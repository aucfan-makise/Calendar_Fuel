<?php
use Fuel\Core\Presenter;
use Calendar\CalendarFunction;
class Presenter_Calendar_Index extends Presenter{
    public function view(){
        session_start();
        
        $calendar_function = new CalendarFunction();
//         週の始まりの曜日を指定するセレクトボックス 
        $this->week_selectbox_selected_param = function($week_day) {
            return $calendar_function($week_day) ? ' selected' : '';
        };
    }
}