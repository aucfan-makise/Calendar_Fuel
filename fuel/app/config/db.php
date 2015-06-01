<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(
    'active' => 'calendar',
    
    'calendar' => array(
        'type' => 'mysqli',
        'connection' => array(
            'hostname' => 'localhost',
		    'database' => 'calendar',
		    'username' => 'makise',
		    'passowrd' => '',
	    ),
    'charset' => 'utf8',
    'table_prefix' => '',
    ),
);
