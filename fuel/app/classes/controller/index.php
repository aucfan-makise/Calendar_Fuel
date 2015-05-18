<?php
use Calendar\CalendarFunction;
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Index Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Index extends Controller
{

	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
	    session_start();
	    $index_presenter = Presenter::forge('calendar/index');

	    return $index_presenter;
	}
	
	public function action_calendar(){
	    $calendar = Presenter::forge('calendar/calendar');
	    $calendar_function = new CalendarFunction();
	    $table = array();
	    for ($i = $calendar_function->getStartDate(), $interval = new \DateInterval('P1M'); $i <= $calendar_function->getEndDate(); $i->add($interval)){
		    $presenter = Presenter::forge('calendar/table');
    	    $presenter->date = clone $i;
    	    $presenter->start_week_day = $calendar_function->getStartWeekDay();
    	    $presenter->calendar_array = $calendar_function->getMonthCalendarArray($i);
    	    $table[] = $presenter;
	    }
	    $calendar->table = $table;
	    
	    return $calendar;
	}

	/**
	 * A typical "Hello, Bob!" type example.  This uses a Presenter to
	 * show how to use them.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_hello()
	{
		return Response::forge(Presenter::forge('welcome/hello'));
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(Presenter::forge('welcome/404'), 404);
	}
}
