<?php
use Calendar\CalendarFunction;
use Fuel\Core\Response;
use Calendar\ScheduleFunction;
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
	    if (is_null(Session::instance())) Session::create();
	    $index_presenter = Presenter::forge('calendar/index');
	    if (! is_null(Session::get('user_name'))){
	        $index_presenter->user_name = Session::get('user_name');
	    }
	    return Response::forge($index_presenter);
	}
	
	public function post_schedule(){
	    $response = array();
// 	    不正な場合、カレンダーのページを表示する。
	    if (! AccountFunction::identifyUser($_POST['token'])){
	        $response['result'] = false;
	        $response['error_message'] = '不正なアクセス。'.Crypt::encode(Session::key(), false);
	        return json_encode($response);
	    }
// 	    ログインしてない場合、ログインページを表示する。
	    if (! is_null(Session::get('user_name'))){
	        $response['result'] = false;
	        $response['error_message'] = 'ログインしてください。';
	        return json_encode($response);
	    }
	    
	    $schedule_function = new ScheduleFunction();
	    try {
	        $schedule_function->validateMode();
	    } catch(Exception $e) {
	        $response['result'] = false;
	        $response['error_message'] = $e->getMessage();
	        return json_encode($response);
	    }
	    
	    try {
	        $schedule_function->validateSchedule();
	    } catch(Exception $e) {
	        
	    }
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
	
	public function action_login(){
	    if (is_null(Session::instance())) Session::create();
	    return Response::forge(View::forge('calendar/login'));
	}
	
	public function post_login(){
	    if (! AccountFunction::identifyUser($_POST['token'])){
            $return_view = View::forge('calendar/login');
            $return_view->error_msg = '不正なアクセスです。';
            return Response::forge($return_view);
	    }
	    
	    $account_function = new AccountFunction();
	    $return_view;
	    try {
	        $account_function->validateLoginPost();
	        Session::set('user_name', $account_function->getAddress());
	        return $this->action_index();
	    } catch (Exception $e){
	        $return_view = View::forge('calendar/login');
	        $return_view->user_name = $account_function->getAddress();
	        $return_view->error_msg = $e->getMessage();
		    return Response::forge($return_view);
	    }
	}
	
	public function action_logout(){
	    if (! is_null(Session::instance())) Session::destroy();
	    
	    return $this->action_index();
	}
	
    public function action_account_registration(){
	    if (is_null(Session::instance())) Session::create();
        return View::forge('calendar/registration');
    }
    
    public function post_account_registration(){
        if (! AccountFunction::identifyUser($_POST['token'])){
            $return_view = View::forge('calendar/registration');
            $return_view->error_msg = '不正なアクセスです。';
            return Response::forge($return_view);
        }

        $account_function = new AccountFunction();
        $return_view;
        try{
            $account_function->validateRegistrationPost();
            Model_Account::insert($account_function->getAddress(), $account_function->getPassword());
            $return_view = View::forge('calendar/complete_registration');
        } catch (Exception $e){
            $return_view = View::forge('calendar/registration');
            $return_view->user_name = $account_function->getAddress();
            $return_view->error_msg = $e->getMessage();
        }
        return Response::forge($return_view);
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
