<?php
use Fuel\Core\Presenter;
use Calendar\CalendarFunction;
class Presenter_Calendar_Table extends Presenter{
    public function view(){
        $week_day_array = Config::get('calendar.week_day_array');            
        
        $calendar_function = new CalendarFunction();
        
//         カレンダーのタイトルの生成 
        $date_array = date_parse($calendar_function->getTodayDate()->format('mysql'));
        $this->title = $date_array['year'].'年'.$date_array['month'].'月';

//         曜日の列の表示
        $array = array();
        foreach (range($calendar_function->getStartWeekDay(), 6) as $week_day_num){
            $array[] = $week_day_array[$week_day_num];
        }
        if ($calendar_function->getStartWeekDay() != 0){
            foreach (range(0, $calendar_function->getStartWeekDay()) as $week_day_num){
                $array[] = $week_day_array[$week_day_num];    
            }
        }
        $this->week_day_name_array = $array;
    }
}