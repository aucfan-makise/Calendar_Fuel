<?php
use Fuel\Core\Presenter;
use Calendar\CalendarFunction;
class Presenter_Calendar_Table extends Presenter{
    public function view(){
        $week_day_array = Config::get('calendar.week_day_array');            

//         カレンダーのタイトルの生成 
        $date_array = date_parse($this->date->format('Y-n'));
        $this->title = $date_array['year'].'年'.$date_array['month'].'月';

//         曜日の列の表示
        $array = array();
        foreach (range($this->start_week_day, 6) as $week_day_num){
            $array[] = $week_day_array[$week_day_num];
        }
        if ($this->start_week_day != 0){
            foreach (range(0, $this->start_week_day - 1) as $week_day_num){
                $array[] = $week_day_array[$week_day_num];    
            }
        }
        $this->week_day_name_array = $array;
        
//         日付の配列
        $this->calendar_array = $this->calendar_array;
        
//         祝日かどうかを調べる
        $this->isHoliday = function($day) {
            return empty($day['holiday_name']) ? false : true;
        };
        
//         祝日名を返す
        $this->getHolidayName = function($day) {
            return $day['holiday_name'];
        };
    }
}