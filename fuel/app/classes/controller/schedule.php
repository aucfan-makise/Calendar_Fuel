<?php
use Calendar\ScheduleFunction;
use Fuel\Core\Response;
use Fuel\Core\Cookie;
use Calendar\Calendar;
class Controller_Schedule extends Controller{
    
    private function access_check(){
	    if (! AccountFunction::identifyUser($_POST['token'])){
	        throw new Exception('不正なアクセス。');
	    }

	    if (is_null(Session::get('user_name'))){
	        throw new Exception('ログインしてください。');
	    }
    }
    public function post_register(){
	    $response = array();
        
	    try {
	        $this->access_check();
    	    $schedule_function = new ScheduleFunction();
	        $schedule_function->validateMode();
	        $schedule_function->validateSchedule();
	    }catch(Exception $e){
	        $response['result'] = false;
	        $response['error_message'] = $e->getMessage();
	        return json_encode($response);
	    }
	    
	    Model_Schedule::insert(
	        Session::get('user_name'),
	        $schedule_function->getTitle(),
	        $schedule_function->getDetail(),
	        $schedule_function->getStartTimeStr(),
	        $schedule_function->getEndTimeStr());
	    $response['result'] = true;
	    $response['mode'] = $schedule_function->getMode();
	    return json_encode($response);
    }
    
    public function post_delete(){
        $response = array();

	    try {
	        $this->access_check();
	        $schedule_function = new ScheduleFunction();
	        $schedule_function->validateMode();
	        $schedule_function->validateSchedule();
	        $schedule_function->validateDeleteId();
	    }catch(Exception $e){
	        $response['result'] = false;
	        $response['error_message'] = $e->getMessage();
	        return json_encode($response);
	    }
	    
        
    }
    
    public function post_refer_schedule_by_id(){
        $response['response'] = array();
        $return = new Response();
        $return->set_header('Content-Type', 'application/json');
        
        try{
            $schedule_function = new ScheduleFunction();
            $schedule_function->validateReadId();
            $response['response']['items'] = $schedule_function->getScheduleById();
            $response['response']['result_count'] = count($response['response']['items']);
        }catch (Exception $e){
            $response['response']['status'] = false;
            $response['response']['error_msg'] = $e->getMessage();
            $return->body(json_encode($response));
            return $return;
        }
        $response['response']['status'] = true;
        $return->body(json_encode($response));
        return $return;
    }
    public function post_refer_schedule(){
        $account_function = new AccountFunction();
        $response['response'] = array();
        $return = new Response();
        $return->set_header('Content-Type', 'application/json');
        
        try{
            if (is_null(Session::instance()) || ! $account_function->identifyUser(Cookie::get('token'))){
                throw new Exception('不正なアクセス。');
            }
            $schedule_function = new ScheduleFunction();
            $schedule_function->validateReadOptions();
            $response['response']['items'] = $schedule_function->getSchedules();
            $response['response']['result_count'] = count($response['response']['items']);
        }catch (Exception $e){
            $response['response']['status'] = false;
            $response['response']['error_msg'] = $e->getMessage();
            $return->body(json_encode($response));
            return $return;
        }
        $response['response']['status'] = true;
        $return->body(json_encode($response));
        return $return;
    }

    public function post_client_login(){
        if (is_null(Session::instance())) Session::create();
        $account_function = new AccountFunction();
        $response['response'] = array();
        try{
            $account_function->validateLoginPost();
            Session::set('user_name', $account_function->getAddress());
            $response['response']['status'] = true;
            Cookie::set('token', $account_function->getToken());
        } catch (Exception $e){
            $response['response']['status'] = false;
            $response['response']['error_msg'] = $e->getMessage();
        }
        $return = Response::forge(json_encode($response));
        $return->set_header('Content-Type', 'application/json');
        return $return;
    }


    public function post_view_schedule(){
        $response = array();
        $schedule_function = new ScheduleFunction();
    }
    
}