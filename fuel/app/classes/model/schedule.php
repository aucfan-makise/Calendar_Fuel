<?php
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
}