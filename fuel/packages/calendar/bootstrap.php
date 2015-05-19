<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */


Autoloader::add_core_namespace('Calendar');

Autoloader::add_classes(array(
    'Calendar\\CalendarFunction'   =>  __DIR__.'/classes/CalendarFunction.php',
    'Calendar\\AccountFunction' =>  __DIR__.'/classes/AccountFunction.php',
    'Calendar\\ScheduleFunction' => __DIR__.'/classes/ScheduleFunction.php',
));