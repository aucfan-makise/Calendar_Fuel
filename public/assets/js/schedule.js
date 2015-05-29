(function(){
		$(function(){
			
		$('#calendar_div').on('click', '.schedule_registration', function(){
		    if($('#login_link')[0]) {
		        window.location.href = '/index/login';
		        return;
		    }
			$('#overlay, #schedule_form_div, #schedule_form_div *:not(#delete, #modify)').css('visibility', 'visible');
			$('#modify, #delete').css('visibility', 'hidden');
			var date = $(this).attr('id');
			initializeSelectBox(date);
		});
		$('#calendar_div').on('mouseenter', '.day_column', function(){
			$(this).css('background-color', 'yellow');
		});
		$('#calendar_div').on('mouseleave', '.day_column', function(){
			$(this).css('background-color', '');
		});
		$('#calendar_div').on('click', '.schedule_link', function(){
			view_id = $(this).attr('id');
			$.ajax({
				url: '/schedule/refer_schedule_by_id',
				type: 'post',
				dataType: 'json',
				data: {id : $(this).attr('id')},
				beforeSend: function(){
				    $('#schedule_form button').prop('disabled', true);
				    $('#overlay, #schedule_form_div, #schedule_form_div *:not(#register)').css('visibility', 'visible');
				    $('#register').css('visibility', 'hidden');
				},
				success: function(data){
				    var item = data['response']['items'][0];
				    appendDateTimeSelectBox('[name=schedule_start', item['start_time']);
				    appendDateTimeSelectBox('[name=schedule_end', item['end_time']);
				    
				    $('#schedule_title').val(item['title']);
				    $('#schedule_detail').val(item['detail']);
					$('#view_id').val(view_id);
				},
				error: function(xhr, textStatus, error){
					alert('Ajax Error.'+param);
				},
				complete: function(data){
					$('#schedule_form button').prop('disabled', false);
				} 
			})
		});
		
		$('[name=schedule_start_year]').change(function(){
			changeSelectBox('start');
		});
		$('[name=schedule_start_month]').change(function(){
			changeSelectBox('start');
		});
		$('[name=schedule_end_year]').change(function(){
			changeSelectBox('end');
		});
		$('[name=schedule_end_month]').change(function(){
			changeSelectBox('end');
		});
		

		$('#schedule_form_close').click(function(){
		    scheduleWindowClose();
		});
		$('#overlay').click(function(){
		    if($('#schedule_form_finish_div').css('visibility') != 'visible'){
		        scheduleWindowClose();
		    }
		});
	});
	function initializeSelectBox(selected_date){
	    var date_array = parseDateTimeStr(selected_date);
	    appendDateSelectBox('[name=schedule_start', date_array[0], date_array[1], date_array[2]);
	    appendDateSelectBox('[name=schedule_end', date_array[0], date_array[1], date_array[2]);
	    
	    var now = new Date();
	    appendTimeSelectBox('[name=schedule_start', now.getHours(), now.getMinutes());
	    appendTimeSelectBox('[name=schedule_end', now.getHours(), now.getMinutes());
	};
	function parseDateTimeStr(datetime_str){
	    var datetime_array = datetime_str.split(' ');
	    var date_array = datetime_array[0].split('-');
	    
	    if(typeof datetime_array[1] !== 'undefined'){
		    var time_array = datetime_array[1].split(':');
	    }
	    return date_array.concat(time_array);
	}
	function scheduleWindowClose(){
		$('#overlay, #schedule_form_div, #schedule_form_div *').css('visibility', 'hidden');
		$('#schedule_title, #schedule_detail').val('');
		$('#register').prop('disabled', false);
		$('#error_message').text('');
	}

	function changeSelectBox(name){
		name = '[name=schedule_' + name;
		appendDaySelectBox(name, $(name+'_year]').val(), $(name+'_month]').val(), $(name+'_day]').val());
	}

	function appendDateTimeSelectBox(name, datetime_str){
	    var date_array = parseDateTimeStr(datetime_str);
	    appendDateSelectBox(name, date_array[0], date_array[1], date_array[2]);
	    appendTimeSelectBox(name, date_array[3], date_array[4]);
	}
	function appendDateSelectBox(name, year, month, day){
		var selected_year = Number(year);
		var selected_month = Number(month);
		
	    appendSelectBox(name, '_year]', selected_year, 2014, 2019);
	    appendSelectBox(name, '_month]', selected_month, 1, 12);
		appendDaySelectBox(name, year, month, day);
	};
	
	function appendDaySelectBox(name, year, month, day){
	    var before_selected_day = Number(day);
		var date = new Date(year, month, 0);
		var last_day = date.getDate();
		var selected_day = before_selected_day > last_day ? last_day : before_selected_day;
		
		appendSelectBox(name, '_day]', selected_day, 1, last_day);
	};
	
	function appendTimeSelectBox(name, selected_hour, selected_minute){
		appendSelectBox(name, '_hour]', selected_hour, 0, 23);
		appendSelectBox(name, '_minute]', selected_minute, 0, 59);
	}
	function appendSelectBox(name, plus_name, select, start, end){
	    var selecter = name + plus_name;
	    $(selecter).children().remove();
	    $(selecter).append(createOptionsArray(start, end));
	    $(selecter).val(select);
	}
	
	function createOptionsArray(start, end){
	    var array = [$('<option>').html(start).val(start)];
	    return start == end ? array : array.concat(createOptionsArray(start + 1, end));
	}
})();