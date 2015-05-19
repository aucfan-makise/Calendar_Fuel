<?php
class Model_Account extends Orm\Model {
    protected static $_table_name = 'user_accounts';
    protected static $_primary_key = array('user_id');
    protected static $_properties = array(
        'user_id',
        'user_name',
        'user_passwd',
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
    
    public static function selectUserPassword($user_name){
        $select = Model_Account::find('first', array(
            'select' => array(
                'user_passwd',
            ),
            'where' => array(
                array('user_name', $user_name),
            ),
        ));
        
        return $select['user_passwd'];
    }
    public static function insert($user_name, $password){
        $insert = new Model_Account();
        $insert->user_name = $user_name;
        $insert->user_passwd = $password;
        $insert->save();
    }
    
    public static function accountExists($user_name){
        $query = Model_Account::query()->where('user_name', $user_name)->where('deleted_at', null);

        return $query->count() === 1 ? true : false;
    }
}