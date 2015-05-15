<?php
namespace Calendar;
use Fuel\Core\Config;
class CalendarFunction {
    private $today_date;
    private $start_week_day = 0;
    
    
    public function __construct(){
        $this->today_date = new DateTime('NOW');
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
     * 引数は表示するカレンダーの最初と最後の年月
     * カレンダーの配列を返す
     * 最初の月−１から最後の月＋１で作成
     * year => month => day => 'weekday' =>
     * '...' =>
     *
     * @access public 
     * @param DateTime $start_date_array            
     * @param DateTime $end_date_array            
     * @return multitype:NULL multitype:multitype:mixed boolean multitype:unknown
     */
    public function createCalendarArray($start_datetime, $end_datetime){
        $array = array();
        // 指定された最初の月と最後の月の範囲を広げる
        $interval = new DateInterval('P1M');
        $start_datetime->sub($interval);
        $start_datetime_array = date_parse($start_datetime->format('Y-n'));
        $end_datetime->add($interval);
        $end_datetime_array = date_parse($end_datetime->format('Y-n'));
        
//         $this->public_holiday_array = $this->getPublicHolidayData($start_datetime_array, $end_datetime_array);
//         $this->auction_topic_array = $this->getAucfanTopicData();
        
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
}