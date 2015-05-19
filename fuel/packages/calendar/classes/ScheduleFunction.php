<?php
namespace Calendar;
class ScheduleFunction {
    private $mode;
    private $title;
    private $detail = '';
    private $start_datetime;
    private $end_datetime;
    
    public function __construct(){
        $this->start_datetime = new \DateTime();
        $this->end_datetime = new \DateTime();
    }
    public function validateMode(){
        if ($_POST["mode"] == 'register' || $_POST["modify"] || $_POST["delete"]){
            $this->mode = $_POST["mode"];
        } else{
            throw new \Exception('Mode Error.');
        }
    }
    
    public function validateSchedule(){
        if (empty($_POST['title'])) throw new \Exception('タイトルが入力されていません。');
                
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
        
        $this->title = $_POST['title'];        
        if (! empty($_POST['detail'])) $this->detail = $_POST['detail'];
        
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
    private function dateTimeCheck($year, $month, $day, $hour, $minute)
    {
        if (! checkdate($month, $day, $year)){
            throw new \Exception('日付が不正です。');
        }
        if ($year < 2015 || 2018 < $year)
            throw new \Exception("年が不正です。");
        if (! preg_match('/^([0-9]|1[0-9]|2[0-3])$/', $hour)){
            throw new \Exception('時が不正です。');
        }
        if (! preg_match('/^[1-5]?[0-9]$/', $minute)){
            throw new \Exception('分が不正です。');
        }
    }
}