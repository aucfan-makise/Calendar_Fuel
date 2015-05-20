<?php
namespace Calendar;
use Fuel\Core\Config;
class CalendarFunction {
    private $errorMessage = array();
    private $today_date;
    private $today_date_array;
    private $start_week_day = 0;
    private $calendar_size = 3;
    private $calendar_div;
    
    private $selected_date_datetime;
    private $start_datetime;
    private $end_datetime;
    
    private $calendar_array;
    
    private function setErrorMessage($msg){
        $this->errorMessage[] = $msg;
    }
    public function __construct(){
        $this->checkGetData();
        $this->calendar_div = Config::get('calendar.calendar_div_array');
        $this->today_date = new \DateTime('NOW');
        $this->today_date_array = date_parse($this->today_date->format('Y-n-j'));
        
        if (isset($_GET['select_date'])){
            $this->selected_date_datetime = new \DateTime($_GET['select_date']);
        } else {
            $this->selected_date_datetime = new \DateTime($this->today_date->format('Y-n'));
        }
        $start_calendar = date('Y-n', strtotime($this->selected_date_datetime->format('Y-n') . ' -' . floor($this->calendar_size / 2) . ' month'));
        $this->start_datetime = new \DateTime($start_calendar);
        $end_calendar = date('Y-n', strtotime($this->selected_date_datetime->format('Y-n') . ' +' . ceil($this->calendar_size / 2) . ' month -1 month'));
        $this->end_datetime = new \DateTime($end_calendar);
	    $this->calendar_array = $this->createCalendarArray();
    }
    
    protected function checkGetData(){
        try {
            if (isset($_GET['start_week_day'])){
			    if (! ctype_digit($_GET['start_week_day']) || $_GET['start_week_day'] > 6 || $_GET < 0) {
			        throw new \Exception('開始の曜日の選択が不正です。');
			    }
			    if (isset($_GET['start_week_day'])) {
				    $this->start_week_day = $_GET['start_week_day'];
			    }
            }
            if (isset($_GET['calendar_size'])) {
                if (! ctype_digit($_GET['calendar_size']) || $_GET['calendar_size'] > 9 || $_GET['calendar_size'] < 0) {
                    throw new \Exception('カレンダーのサイズが大きすぎるか小さすぎます。');
                }
                $this->calendar_size = $_GET['calendar_size'];
            }
        } catch (Exception $e) {
            $this->setErrorMessage('パラメータの値が不正です。' . $e->getMessage());
        }
    }
    /**
     * 週初めの曜日かどうか調べる
     *
     * @param string $week_day            
     * @return boolean
     */
    public function isStartWeekDay($week_day)
    {
        return $this->start_week_day == $week_day ? true : false;
    }
    
    /**
     * カレンダーのサイズを返す
     * @return number
     */
    public function getCalendarSize(){
        return $this->calendar_size;
    }
    
    /**
     * 週の初めの曜日を返す
     *
     * @access public
     * @return Ambigous <number, array>
     */
    public function getStartWeekDay()
    {
        return $this->start_week_day;
    }
    
    /**
     * 本日のDateを返す
     * @access public
     * @return DateTime
     */
    public function getTodayDate(){
        return $this->today_date;
    }
    
    /**
     * カレンダーの始まりのDateTimeを返す
     * @access public
     * @return DateTime
     */
    public function getStartDate(){
        return $this->start_datetime;
    }
    
    /**
     * カレンダーの終わりのDateTimeを返す
     * @access public
     * @return DateTime
     */
    public function getEndDate(){
        return $this->end_datetime;
    }
    
    /**
     * 引数は取得する祝日のはじめの年、月、終わりの年、月
     * xmlをパースしたものを返す
     *
     * @access private
     * @param array $start_datetime_array            
     * @param array $end_datetime_array            
     * @return SimpleXMLElement
     */
    private function getPublicHolidayData($start_datetime_array, $end_datetime_array)
    {
        $conf_array = Config::get('calendar.public_holiday_api');
        $year_start_option = $conf_array['year_start_option_name'] . '=' . $start_datetime_array['year'];
        $year_end_option = $conf_array['year_end_option_name'] . '=' . $end_datetime_array['year'];
        $month_start_option = $conf_array['month_start_option_name'] . '=' . $start_datetime_array['month'];
        $month_end_option = $conf_array['month_end_option_name'] . '=' . $end_datetime_array['month'];
        $url = $conf_array['server'] . '?' . $year_start_option . '&' . $month_start_option . '&' . $year_end_option . '&' . $month_end_option . '&' . $conf_array['fixed_option'];
        $res = null;
        try {
            $res = simplexml_load_file($url);
        } catch (Exception $e) {
            $this->setErrorMessage('祝日が読み込めませんでした。');
        }
        return $res;
    }

    /**
     * オークションのトピックを取ってくる
     *
     * @access private
     * @return Ambigous <multitype:, multitype:string >
     */
    private function getAucfanTopicData()
    {
        $topics_array = array();
        foreach (range(1, 1) as $page) {
            $topic = array();
            $url = Config::get('calendar.auctopic_api_server') . '?paged=' . $page;
            $res = null;
            try {
                $res = simplexml_load_file($url);
            } catch (Exception $e) {
                $this->setErrorMessage('オークショントピックが読み込めませんでした。');
                return array();
            }
            foreach ($res->channel->item as $topics) {
                if ($this->end_datetime <= new \DateTime($topics->time))
                    break 2;
                    // 0:year 1:month 2:day 3:hour 4:minute 5:second
                $time = explode('-', date('Y-n-j-H-i-s', strtotime($topics->pubDate)));
                
                if (! in_array($time[0], $topics_array))
                    $topics_array[$time[0]][] = array();
                if (! in_array($time[1], $topics_array[$time[0]]))
                    $topics_array[$time[0]][$time[1]][] = array();
                if (! in_array($time[2], $topics_array[$time[0]][$time[1]]))
                    $topics_array[$time[0]][$time[1]][$time[2]][] = array();
                
                $topic_array = array();
                $topic_array['time'] = $time[3] . '-' . $time[4] . '-' . $time[5];
                $topic_array['title'] = (string) $topics->title;
                $topic_array['link'] = (string) $topics->link;
                $topics_array[$time[0]][$time[1]][$time[2]][] = $topic_array;
            }
        }
        return $topics_array;
    }

    /**
     * 引数は表示するカレンダーの最初と最後の年月
     * カレンダーの配列を返す
     * 最初の月−１から最後の月＋１で作成
     * year => month => day => 'weekday' =>
     * '...' =>
     *
     * @access private
     * @param DateTime $start_date_array            
     * @param DateTime $end_date_array            
     * @return multitype:NULL multitype:multitype:mixed boolean multitype:unknown
     */
    private function createCalendarArray(){
        $array = array();
        // 指定された最初の月と最後の月の範囲を広げる
        $interval = new \DateInterval('P1M');
        $read_start_date = clone $this->start_datetime;
        $read_start_date->sub($interval);
        $start_datetime_array = date_parse($read_start_date->format('Y-n'));
        $read_end_date = clone $this->end_datetime;
        $read_end_date->add($interval);
        $end_datetime_array = date_parse($read_end_date->format('Y-n'));
        
        $this->public_holiday_array = $this->getPublicHolidayData($start_datetime_array, $end_datetime_array);
        $this->auction_topic_array = $this->getAucfanTopicData();
        
//         $this->schedule_function->getSchedule($start_datetime, $end_datetime);
//         $this->schedules_array = $this->schedule_function->getSchedulesArray();
        for ($year = $start_datetime_array['year']; $year <= $end_datetime_array['year']; ++ $year) {
            if ($year == $start_datetime_array['year'] && $year == $end_datetime_array['year']) {
                $array[$year] = $this->createYearCarendarArray($year, $start_datetime_array['month'], $end_datetime_array['month']);
            } elseif ($year == $start_datetime_array['year'] && $year < $end_datetime_array['year']) {
                $array[$year] = $this->createYearCarendarArray($year, $start_datetime_array['month'], 12);
            } elseif ($year > $start_datetime_array['year'] && $year < $end_datetime_array['year']) {
                $array[$year] = $this->createYearCarendarArray($year, 1, 12);
            } elseif ($year > $start_datetime_array['year'] && $year == $end_datetime_array['year']) {
                $array[$year] = $this->createYearCarendarArray($year, 1, $end_datetime_array['month']);
            }
        }
        
        return $array;
    }
    
    /**
     * 引数は生成するカレンダーの年、最初の月、最後の月、祝日のデータ
     * 一年分位内のカレンダーの配列を生成する
     *
     * @access private
     * @param string $year            
     * @param string $start_month            
     * @param string $end_month            
     * @return multitype:multitype:mixed boolean multitype:unknown
     */
    private function createYearCarendarArray($year, $start_month, $end_month)
    {
        $outer_array = array();
        for ($month = $start_month; $month <= $end_month; ++ $month) {
            $inner_array = array();
            $day_array = array();
            for ($day = 1; $day <= date('t', strtotime($year . '-' . $month)); ++ $day) {
//                 $array['schedules'] = $this->schedules_array[$year][$month][$day];
                $array['week_day'] = (string) date('w', strtotime($year . '-' . $month . '-' . $day));
                $array['day'] = $day;
                if ($day == $this->today_date_array['day'] && $month == $this->today_date_array['month'] && $year == $this->today_date_array['year']) {
                    $array['div_class'] = $this->calendar_div['today'];
                } elseif ($array['week_day'] == 0) {
                    $array['div_class'] = $this->calendar_div['0'];
                } elseif ($array['week_day'] == 6) {
                    $array['div_class'] = $this->calendar_div['6'];
                } else {
                    $array['div_class'] = $this->calendar_div['default'];
                }
                $holiday_name = '';
                foreach ($this->public_holiday_array->response->month as $month_array) {
                    if ($month_array->attributes()->year == $year && $month_array->attributes()->month == $month) {
                        foreach ($month_array->mday as $day_array) {
                            if ($day_array->attributes()->mday == $day) {
                                $holiday_name = (string) $day_array['holiday_name'];
                                $array['div_class'] = $this->calendar_div['public_holiday'];
                                break 2;
                            }
                        }
                    }
                }
                $array['holiday_name'] = $holiday_name;
                $aucfan_topics = array();
                if (isset($this->auction_topic_array[$year][$month][$day])) {
                    foreach ($this->auction_topic_array[$year][$month][$day] as $topic) {
                        if (empty($topic))
                            continue;
                        
                        $aucfan_topics[] = $topic;
                    }
                }
                $array['aucfan_topic'] = $aucfan_topics;
                $inner_array[$day] = $array;
                $inner_array['last_day'] = end($inner_array);
                $inner_array['in_range'] = $this->inRange(new \DateTime($year . '-' . $month)) ? true : false;
            }
            $outer_array[$month] = $inner_array;
        }
        return $outer_array;
    }

    /**
     * 一月の表示用のカレンダーの配列を返す
     *
     * @param DateTime $calendar_datetime            
     * @return multitype:NULL unknown
     */
    public function getMonthCalendarArray($calendar_datetime)
    {
        $array = array();
        $calendar_datetime_array = date_parse($calendar_datetime->format('Y-n'));
        
        $interval = new \DateInterval('P1M');
        
        $before_month_datetime = clone $calendar_datetime;
        $before_month_datetime->sub($interval);
        $before_month_datetime_array = date_parse($before_month_datetime->format('Y-n'));
        
        $next_month_datetime = clone $calendar_datetime;
        $next_month_datetime->add($interval);
        $next_month_datetime_array = date_parse($next_month_datetime->format('Y-n'));
        
        // 前月
        if ($this->calendar_array[$calendar_datetime_array['year']][$calendar_datetime_array['month']][1]['week_day'] != $this->start_week_day) {
            $before_month_end_day = cal_days_in_month(CAL_GREGORIAN, $before_month_datetime_array['month'], $before_month_datetime_array['year']);
            $before_month_start_day = $before_month_end_day - ($this->calendar_array[$calendar_datetime_array['year']][$calendar_datetime_array['month']][1]['week_day'] + 7 - $this->start_week_day) % 7 + 1;
            foreach (range($before_month_start_day, $before_month_end_day) as $day) {
                $input = $this->calendar_array[$before_month_datetime_array['year']][$before_month_datetime_array['month']][$day];
                $input_date = new \DateTime();
                $input['datetime'] = $input_date->setDate($before_month_datetime_array['year'], $before_month_datetime_array['month'], $input['day']);
                $array[] = $input;
            }
        }
        // 今月
        foreach ($this->calendar_array[$calendar_datetime_array['year']][$calendar_datetime_array['month']] as $key => $value) {
            if (is_integer($key)) {
                $input = $value;
                $input_date = new \DateTime();
                $input['datetime'] = $input_date->setDate($calendar_datetime_array['year'], $calendar_datetime_array['month'], $input['day']);
                $array[] = $input;
            }
        }
        // 次月
        if ($this->calendar_array[$next_month_datetime_array['year']][$next_month_datetime_array['month']][1]['week_day'] != $this->start_week_day) {
            $next_month_end_day = 6 - $this->calendar_array[$calendar_datetime_array['year']][$calendar_datetime_array['month']]['last_day']['week_day'] + $this->start_week_day;
            foreach (range(1, $next_month_end_day) as $day) {
                $input = $this->calendar_array[$next_month_datetime_array['year']][$next_month_datetime_array['month']][$day];
                $input_date = new \DateTime();
                $input['datetime'] = $input_date->setDate($next_month_datetime_array['year'], $next_month_datetime_array['month'], $input['day']);
                $array[] = $input;
            }
        }
        
        return $array;
    }


    /**
     * 表示する範囲内かどうか調べる
     *
     * @param DateTime $date            
     * @return boolean
     */
    private function inRange($date)
    {
        return $this->start_datetime <= $date && $this->end_datetime >= $date ? true : false;
    }
}