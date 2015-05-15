<?php

return array(
    'calendar_div_array' => array(
        'today' => 'calendar_today_div',
        'default' => 'calendar_date_div',
        '0' => 'calendar_sunday_div',
        '6' => 'calendar_saturday_div',
        'public_holiday' => 'calendar_public_holiday_div'
    ),
    'week_day_array' => array(
        0 => 'Sun',
        1 => 'Mon',
        2 => 'Tue',
        3 => 'Wed',
        4 => 'Thu',
        5 => 'Fri',
        6 => 'Sat'        
    ),    
    'public_holiday_api' => array(
        'server' => 'http://calendar-service.net/cal',
        'fixed_option' => 'year_style=normal&month_style=numeric&wday_style=none&format=xml&holiday_only=1',
        'year_start_option_name' => 'start_year',
        'year_end_option_name' => 'end_year',
        'month_start_option_name' => 'end_year',
        'month_end_option_name' => 'end_mon',        
    ),
    'auctopic_api_server' => 'http://aucfan.com/article/feed/', 
);