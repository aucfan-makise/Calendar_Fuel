<?php
namespace Calendar;
use Oil\Exception;
class ScheduleFunction {
//     書き込み用
    private $mode;
    private $title;
    private $detail = '';
    private $start_datetime;
    private $end_datetime;
    private $view_id;
    
//     読み込み用
    private $api_start_datetime;
    private $api_end_datetime;
    private $api_id;
    
    public function getTitle(){
        return $this->title;
    }
    public function getDetail(){
        return $this->detail;
    }
    public function getStartTimeStr(){
        return $this->start_datetime->format('Y-n-j G:i');
    }
    public function getEndTimeStr(){
        return $this->end_datetime->format('Y-n-j G:i');
    }
    public function getMode(){
        return $this->mode;
    }
    public function getViewId(){
        return $this->view_id;
    }
    public function __construct(){
        $this->start_datetime = new \DateTime();
        $this->end_datetime = new \DateTime();
    }
    public function validateMode(){
        if ($_POST['mode'] == 'register' || $_POST['mode'] == 'modify' || $_POST['mode'] == 'delete'){
            $this->mode = $_POST["mode"];
        } else{
            throw new \Exception('Mode Error.');
        }
    }
    
    public function validateSchedule(){
        if (empty($_POST['schedule_title'])) throw new \Exception('タイトルが入力されていません。');
                
        $this->dateTimeCheck(
            $_POST['schedule_start_year'],
            $_POST['schedule_start_month'],
            $_POST['schedule_start_day'],
            $_POST['schedule_start_hour'],
            $_POST['schedule_start_minute']
            );
        $this->dateTimeCheck(
            $_POST['schedule_end_year'],
            $_POST['schedule_end_month'],
            $_POST['schedule_end_day'],
            $_POST['schedule_end_hour'],
            $_POST['schedule_end_minute']
            );
        
        $this->title = $_POST['schedule_title'];        
        if (! empty($_POST['schedule_detail'])) $this->detail = $_POST['schedule_detail'];
        
        try {
            $this->start_datetime->setDate(
                $_POST['schedule_start_year'],
                $_POST['schedule_start_month'],
                $_POST['schedule_start_day']
                );
            $this->start_datetime->setTime(
                $_POST['schedule_start_hour'],
                $_POST['schedule_start_minute']
                );
        } catch(Exception $e){
            throw new \Exception('開始日時の不正:'.$e->getMessage());
        }

        try {
            $this->end_datetime->setDate(
                $_POST['schedule_end_year'],
                $_POST['schedule_end_month'],
                $_POST['schedule_end_day']
                );
            $this->end_datetime->setTime(
                $_POST['schedule_end_hour'],
                $_POST['schedule_end_minute']
                );
        } catch (Exception $e){
            throw new \Exception('終了日時の不正:'.$e->getMessage());
        }
        
        if ($this->start_datetime >= $this->end_datetime){
            throw new \Exception('登録時間がおかしいです。');
        }
    }

    /**
     * 日付時間のチェックをする
     *
     * @access private
     * @param DateTime $datetime            
     * @throws Exception
     */
    private function dateTimeCheck($year = null, $month = null, $day = null, $hour = null, $minute = null)
    {
        $this->dateCheck($year, $month, $day);
        if (is_null($hour) || ! preg_match('/^([0-9]|1[0-9]|2[0-3])$/', $hour)){
            throw new \Exception('時が不正です。');
        }
        if (is_null($minute) || ! preg_match('/^[1-5]?[0-9]$/', $minute)){
            throw new \Exception('分が不正です。');
        }
    }
    
    /**
     * 日付のチェックをする
     * @param string $year
     * @param string $month
     * @param string $day
     * @throws \Exception
     */
    private function dateCheck($year, $month, $day){
        if (is_null($year) || is_null($month) || is_null($day)){
            throw new \Exception('入力されていない日付があります。');
        }
        if (! checkdate($month, $day, $year)){
            throw new \Exception('日付が不正です。');
        }
        if ($year < 2015 || 2018 < $year)
            throw new \Exception("年が不正です。");
    }
    
    public function validateReadOptions(\DateTime $schedule_start = null, \DateTime $schedule_end = null){
        if (is_null($schedule_start) && is_null($schedule_end) && isset($_POST['schedule_start']) && isset($_POST['schedule_end'])){
            $schedule_start = new \DateTime($_POST['schedule_start']);
            $schedule_end = new \DateTime($_POST['schedule_end']);
        }
        $date_array = date_parse($schedule_start->format('Y-n'));
        $this->dateCheck($date_array['year'], $date_array['month'], $date_array['day']);
        $date_array = date_parse($schedule_end->format('Y-n'));
        $this->dateCheck($date_array['year'], $date_array['month'], $date_array['day']);
        $this->api_start_datetime = $schedule_start;
        $this->api_end_datetime = $schedule_end;
        if ($this->api_start_datetime > $this->api_end_datetime) {
            throw new \Exception('指定した期間が正しくありません。');
        }
        
        $this->api_start_datetime = new \DateTime($this->api_start_datetime->format('Y-n-' . '1'));
        $this->api_start_datetime->setTime(0, 0, 0);
        $this->api_end_datetime = new \DateTime(
            $this->api_end_datetime->format(
                'Y-n-' . date('t', strtotime($this->api_end_datetime->format('Y-n')))));
        $this->api_end_datetime->setTime(23, 59, 59);
    }
    
    public function validateReadId(){
        if (! ctype_digit($_POST['id']) || $_POST['id'] < 0){
            throw new \Exception('Id error.');
        }
        $this->api_id = $_POST['id'];
    }
    
    public function validateViewId(){
        if (! ctype_digit($_POST['view_id']) || $_POST['view_id'] < 0){
            throw new \Exception('Id error.');
        }
        if (! \Model_Schedule::existSchedule(\Session::get('user_name'), $_POST['view_id'])){
            throw new \Exception('No schedule.');
        }
        $this->view_id = $_POST['view_id'];
    }
    
    public function getScheduleById(){
        $schedule = \Model_Schedule::selectScheduleById(\Session::get('user_name'), $this->api_id);
        $items = array();
        
        $item = array(
            'id' => $schedule['schedules_id'],
            'title' => $schedule['title'],
            'detail' => $schedule['detail'],
            'start_time' => $schedule['start_time'],
            'end_time' => $schedule['end_time']
        );
        $items[] = $item;
        return $items;
    }
    
    public function getSchedules(){
        $schedule_array = \Model_Schedule::selectSchedules(\Session::get('user_name'),
            $this->api_start_datetime->format('Y-n-j H:i:s'),
            $this->api_end_datetime->format('Y-n-j H:i:s'));
        $items = array();
        foreach ($schedule_array as $schedule){
            $item = array(
                'id' => $schedule['schedules_id'],
                'title' => $schedule['title'],
                'detail' => $schedule['detail'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time']
            );
            $items[] = $item;
        }
        return $items;
    }
    
    public function fetchSchedules(){
        $tmp_path = tempnam(sys_get_temp_dir(), 'CKI');
        $url = 'http://makky.aucfan.com/schedule/client_login';
        $params = array(
            'address' => \Session::get('user_name'),
            'password' => \Crypt::decode(\Model_Account::selectUserPassword(\Session::get('user_name')))
        );
        try {
            $this->executeCurl($url, $tmp_path, $params);
        }catch (Exception $e){
            unlink($tmp_path);
            throw new Exception('API login error.');
        }

        $url = 'http://makky.aucfan.com/schedule/refer_schedule';
        $params = array(
            'schedule_start' => $this->api_start_datetime->format('Y-n'),
            'schedule_end' => $this->api_end_datetime->format('Y-n')
        );
        try {
            $result = $this->executeCurl($url, $tmp_path, $params);
        }catch (Exception $e){
            unlink($tmp_path);
            throw new Exception('API parameter error.');
        }
        unlink($tmp_path);
        return $result;
    }
    
    private function executeCurl($url, $tmp_path, array $params){
        $curl = \Request::forge($url, 'curl');
        $curl->set_method('post');
        $curl->set_options(array(
            CURLOPT_COOKIEFILE => $tmp_path,
            CURLOPT_COOKIEJAR => $tmp_path
        ));
        $curl->set_params($params);
        $curl->execute();
        $response = $curl->response()->body;
        $decoded_response = json_decode($response);
        if ($decoded_response->response->status === false){
            throw new Exception();
        }
        
        return $response;
    }
}