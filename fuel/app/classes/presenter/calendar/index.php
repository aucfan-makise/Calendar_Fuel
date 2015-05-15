<?php
use Fuel\Core\Presenter;
use Calendar\CalendarFunction;
class Presenter_Calendar_Index extends Presenter{
    public function view(){
        $calendar_function = new CalendarFunction();
        $this->start_week_day = $calendar_function->getStartWeekDay();
        
    }
}