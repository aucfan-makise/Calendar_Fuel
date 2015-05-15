<?php
?>
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
	<td>
	    <div>
		<div>
		</div>
	    </div>
	    <div class="calendar_schedule_div">
	    </div>
	</td>
    </tr>
</table>