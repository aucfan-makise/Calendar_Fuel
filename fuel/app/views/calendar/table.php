<table class="calendar_table">
    <tr>
	    <td class="calendar_table_title" colspan="7">
	    <?php echo $title; ?>	
	    </td>
    </tr>
    <tr class="calendar_week_row">
        <?php foreach ($week_day_name_array as $name): ?>
            <td class="calendar_week_column">
                <?php echo $name; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
    <?php foreach ($calendar_array as $day): ?>
        <?php if ($day['week_day'] == $start_week_day): ?>
    <tr>
        <?php endif; ?>
	    <td>   
		    <div class=<?php echo $day['div_class']; ?>>
			    <a id="<?php echo $day['datetime']->format('Y-n-j'); ?>" class="schedule_registration"></a>
				<div>
				    <?php echo $day['day']; 
				    echo $isHoliday($day) ? " ".$getHolidayName($day) : ""; ?>
			    </div>
		    </div>
		    <div class="calendar_schedule_div">
		      <?php foreach ($day['aucfan_topic'] as $topic): ?>
		          <a href="<?php echo $topic['link']; ?>"><?php echo $topic['title']; ?></a>
		      <?php endforeach; ?>
		    </div>
	    </td>
	    <?php if ($day['week_day'] == ($start_week_day + 6) % 7): ?>
    </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>