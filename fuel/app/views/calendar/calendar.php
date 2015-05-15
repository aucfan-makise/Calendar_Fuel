<?php
$table_count = 0;
$max_row_count = 3;
?>
<table id="calendar">
    <tr>
	    <?php foreach ($table as $single): ?>
	    <td valign="top">
		    <?php echo $single; ?>
	    </td>
		    <?php $table_count++; ?>
            <?php if ($table_count % $max_row_count == 0): ?>
		    </tr>
		   	<tr>
		    <?php endif; ?>
	    <?php endforeach; ?>
    </tr>
</table>