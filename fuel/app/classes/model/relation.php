<?php
class Model_Relation extends Orm\Model {
    protected static $_table_name = 'user_schedule_relations';
    protected static $_primary_key = array('user_schedule_relations_id');
    protected static $_properties = array(
        'user_schedule_relations_id',
        'user_id',
        'schedules_id',
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
    
    protected static $_belongs_to = array(
        'account' => array(
            'key_from' => 'user_id',
            'model_to' => 'Model_Account',
            'key_to' => 'user_id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
}