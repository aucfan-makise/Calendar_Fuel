<?php
use Psr\Log\NullLogger;
class Model_Schedule extends Orm\Model {
    protected static $_table_name = 'schedules';
    protected static $_primary_key = array('schedules_id');
    protected static $_properties = array(
        'schedules_id',
        'title',
        'detail',
        'start_time',
        'end_time',
        'created_at',
        'update_at',
        'deleted_at',
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_insert', 'before_save'),
            'mysql_timestamp' => true,
            'property' => 'update_at',
        ),
    );
    
    protected static $_has_many = array(
        'relations' => array(
            'key_from' => 'schedules_id',
            'model_to' => 'Model_Relation',
            'key_to' => 'schedules_id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    
    public static function insert($user, $title, $detail, $start, $end){
        $account = Model_Account::query()->select('user_id')->where('user_name', $user)->get_one();
        $relation = new Model_Relation();
        $relation->user_id = $account['user_id'];

        $insert = new Model_Schedule();
        $insert->relations[] = $relation;
        $insert->title = $title;
        $insert->detail = $detail;
        $insert->start_time = $start;
        $insert->end_time = $end;
        $insert->save();
    }
    
    public static function selectSchedules($user, $start, $end){
        $schedules = Model_Schedule::query()
            ->select('schedules_id', 'title', 'detail', 'start_time', 'end_time')
            ->related('relations')
            ->related('relations.account')
            ->where('relations.account.user_name', $user)
            ->where('deleted_at', null)
            ->and_where_open()
                ->where_open()
                    ->where('start_time', '>', $start)
                    ->where('start_time', '<', $end)
                ->where_close()
                ->or_where_open()
                    ->where('end_time', '>', $start)
                    ->where('end_time', '<', $end)
                ->or_where_close()
            ->and_where_close()
            ->get();
        
        $return_array = array();
        foreach ($schedules as $schedule){
            $r = $schedule->to_array();
            unset($r['relations']);
            unset($r['account']);
            $return_array[] = $r;
        }
        
        return $return_array;
    }
    
    public static function selectScheduleById($user, $id){
        $schedule = Model_Schedule::query()
            ->select('schedules_id', 'title', 'detail', 'start_time', 'end_time')
            ->related('relations')
            ->related('relations.account')
            ->where('relations.account.user_name', $user)
            ->where('deleted_at', null)
            ->where('schedules_id', $id)
            ->get_one();
        return $schedule->to_array();
    }
    
    public static function existSchedule($user, $id){
        $schedule_count = Model_Schedule::query()
            ->select('schedules_id')
            ->related('relations')
            ->related('relations.account')
            ->where('relations.account.user_name', $user)
            ->where('deleted_at', null)
            ->where('schedules_id', $id)
            ->count();
        
        return $schedule_count === 1 ? true : false;
    }
    
    public static function deleteSchedule($user, $id){
        $delete_time = \Date::Time()->format('mysql');
        $account = Model_Account::query()
            ->select('user_id')
            ->where('user_name', $user)
            ->get_one();
        $selected_schedule = Model_Schedule::find('first', array(
            'where' => array(
                'schedules_id' => $id,
                'deleted_at' => null
            )));
        $relations = Model_Relation::find('first', array(
            'where' => array(
                'user_id' => $account['user_id'],
                'schedules_id' => $id,
                'deleted_at' => null
            )
        ));
        $relations->user_id = $account['user_id'];
        $relations->schedules_id = $id;
        $relations->deleted_at = $delete_time;
        $selected_schedule->deleted_at = $delete_time;
        $selected_schedule->save();
        $relations->save();
    }
    
    public static function modifySchedule($user, $id, $title, $detail, $start, $end){
        $selected_schedule = Model_Schedule::find('first', array(
            'where' => array(
                'schedules_id' => $id,
                'deleted_at' => null
            )));
        $selected_schedule->title = $title;
        $selected_schedule->detail = $detail;
        $selected_schedule->start_time = $start;
        $selected_schedule->end_time = $end;
        $selected_schedule->save();
    }
}