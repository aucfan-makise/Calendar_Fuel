(function(){
	$(function(){
		initialize();
		$(document).load(function(){
		    objectCentering();
		});
		$(window).resize(function(){
		    objectCentering(); 
		});
		$('[name=select_date_before]').click(function(){
		    $('#loading, #overlay').css('visibility', 'visible');
			$.ajax({
				type: 'GET',
				url: '/index/calendar',
				dataType: 'html',
				data: {select_date: $(this).val(),
					start_week_day : $('[name=start_week_day]').val(),
		            calendar_size : $('[name=calendar_size]').val()},
				success: function(data){
					if($('[name=select_date_before]').val() != ''){
					    tableReload(data);
						var date = $('[name=select_date_before]').val();
						changeMoveButtonValue(date);
						$('[name=select_date_combo]').val(date);
					}
				},
				error:function() {
					alert('Ajax error.');
				},
				complete: function(data) {
				    $('#loading, #overlay').css('visibility', 'hidden');
				}
			})
		})
		$('[name=select_date_next]').click(function(){
		    $('#loading, #overlay').css('visibility', 'visible');
			$.ajax({
				type: 'GET',
				url: '/index/calendar',
				dataType: 'html',
				data: {select_date: $(this).val(),
					start_week_day : $('[name=start_week_day]').val(),
		            calendar_size : $('[name=calendar_size]').val()},
				success: function(data){
					if($('[name=select_date_next]').val() != ''){
					    tableReload(data);
						var date = $('[name=select_date_next]').val();
						changeMoveButtonValue(date);
						$('[name=select_date_combo]').val(date);
					}
				},
				error:function() {
					alert('Ajax error.');
				},
				complete: function(data) {
				    $('#loading, #overlay').css('visibility', 'hidden');
				}
			})
		});
		$('[name=select_date_combo]').change(function(){
		    $('#loading, #overlay').css('visibility', 'visible');
			$.ajax({
				type: 'GET',
				url: '/index/calendar',
				dataType: 'html',
				data: {select_date : $('[name=select_date_combo]').val(),
					start_week_day : $('[name=start_week_day]').val(),
		            calendar_size : $('[name=calendar_size]').val()},
				success: function(data){
				    tableReload(data);
					var date = $('[name=select_date_combo]').val();
					changeMoveButtonValue(date);
				},
				error:function() {
					alert('Ajax error.');
				},
				complete: function(data) {
				    $('#loading, #overlay').css('visibility', 'hidden');
				}
			})
		});
		$('[name=start_week_day]').change(function(){
		    $('#loading, #overlay').css('visibility', 'visible');
			$.ajax({
				type: 'GET',
				url: '/index/calendar',
				dataType: 'html',
				data: {select_date : $('[name=select_date_combo]').val(),
					start_week_day : $('[name=start_week_day]').val(),
		            calendar_size : $('[name=calendar_size]').val()},
				success: function(data){
				    tableReload(data);
				},
				error:function() {
					alert('Ajax error.');
				},
				complete: function(data) {
				    $('#loading, #overlay').css('visibility', 'hidden');
				}
			})
		});
		$('#change_calendar_size').click(function(event){
            event.preventDefault();
		    $('#loading, #overlay').css('visibility', 'visible');
		    $.ajax({
		        type: 'GET',
		        url: '/index/calendar',
		        dataType: 'html',
		        data: {select_date : $('[name=select_date_combo]').val(),
		            start_week_day : $('[name=start_week_day]').val(),
		            calendar_size : $('[name=calendar_size]').val()},
		        success: function(data){
				    tableReload(data);
		        },
		        error:function() {
		            alert('Ajax error.');
		        },
				complete: function(data) {
				    $('#loading, #overlay').css('visibility', 'hidden');
				}
		    })
		});
		
		$('#schedule_form_finish_div_close').click(function(){
			$('#overlay, #schedule_form_finish_div').css('visibility', 'hidden');
		    $('#loading, #overlay').css('visibility', 'visible');
			$.ajax({
				type: 'GET',
				url: '/index/calendar',
				dataType: 'html',
				data: {select_date : $('[name=select_date_combo]').val(),
					start_week_day : $('[name=start_week_day]').val(),
		            calendar_size : $('[name=calendar_size]').val()},
				success: function(data){
				    tableReload(data);
					$('#schedule_title').val('');
					$('#schedule_detail').val('');
				},
				error:function() {
					alert('Ajax error.');
				},
				complete: function(data) {
				    $('#loading, #overlay').css('visibility', 'hidden');
				}
			});
			$('#main-window').css('opacity', '1,0');
		});
		$('#schedule_form button').click(function(event) {
            event.preventDefault();
            var $url = '/schedule/' + $(this).attr('id');
            var $form = $('#schedule_form');
            var param = $form.serializeArray();
            $.ajax({
                url: $url,
                type: 'post',
                dataType : 'json',
                data: {schedule_start_year : param[0].value,
                    schedule_start_month : param[1].value,
                    schedule_start_day : param[2].value,
                    schedule_start_hour : param[3].value,
                    schedule_start_minute : param[4].value,
                    schedule_end_year : param[5].value,
                    schedule_end_month : param[6].value,
                    schedule_end_day : param[7].value,
                    schedule_end_hour : param[8].value,
                    schedule_end_minute : param[9].value,
                    schedule_title : param[10].value,
                    schedule_detail : param[11].value,
                    token : param[12].value,
                    view_id : $('#view_id').val(),
                    mode : $(this).attr('id'),
                    },
                
                beforeSend: function(xhr, settings){
                    $('#register, #modify, #delete').attr('disabled', true);
                },
                success: function(data){
                    if(data.result === true){
                        if(data.mode === 'register'){
                            $('#schedule_form_finish_message').text('登録しました。');
                        }else if(data.mode === 'modify'){
                            $('#schedule_form_finish_message').text('編集しました。');
                        }else if(data.mode === 'delete'){
                            $('#schedule_form_finish_message').text('削除しました。');
                        }
                        $('#schedule_form_div, #register, #modify, #delete').css('visibility', 'hidden');
                        $('#schedule_form_finish_div').css('visibility', 'visible');
                        $('#error_message').text('');
                        $('#schedule_form_div, #schedule_form_div *').css('visibility', 'hidden');
                    }else {
                        $('#error_message').text(data.error_message);
                    }
                    $('#register, #modify, #delete').attr('disabled', false);
                },
                error: function(xhr, textStatus, error){
                    alert('Ajax Error.'+error);
                    $('#register, #modify, #delete').attr('disabled', false);
                }
            });
        });
	});
		
	function initialize(){
	    initializeMoveButton();
	    $('[name=start_week_day]').val('0');
	    appendComboBox();
	    $('[name=calendar_size]').val('3');
	    objectCentering();
	}
	function objectCentering(){
	    setPosition('#overlay');
	    setPosition('#schedule_form_div');
	    setPosition('#schedule_form_finish_div');
	    setPosition('#loading');
	}
	function setPosition(target){
	    var left = Math.floor(($(window).width() - $(target).width()) / 2);
	    var top = Math.floor(($(window).height() - $(target).height()) / 2);
	    
	    $(target).css('left', left);
	    $(target).css('top', top);
	}
	function initializeMoveButton(){
		var date = new Date();
		next_month = new Date();
		before_month = new Date();
		next_month.setMonth(date.getMonth() + Number(1));
		before_month.setMonth(date.getMonth() - Number(1));
		
		$('[name=select_date_before]').val(before_month.getFullYear() + '-' + (before_month.getMonth() + 1));
		$('[name=select_date_next]').val(next_month.getFullYear() + '-' + (next_month.getMonth() + 1));
		
	}
	function tableReload(table){
		$('#calendar_div').find('tr:gt(0)').remove();
		$('#calendar_div').append(table);
	    
	}
	function changeMoveButtonValue(date){
		var date_array = date.split('-');
		var date_obj = new Date(date_array[0], date_array[1], 1);
        var over_limit = new Date();
        over_limit.setMonth(over_limit.getMonth() + Number(10));
        var under_limit = new Date();
        under_limit.setMonth(under_limit.getMonth() - Number(11));
        date_obj.setMonth(date_obj.getMonth());
        date_obj.setMonth(date_obj.getMonth() - Number(2));
        if (date_obj < under_limit) {
            $('[name=select_date_before]').val('').attr('disabled', true);
        } else {
            $('[name=select_date_before]').attr('disabled', false).val(
                    date_obj.getFullYear() + '-' + (date_obj.getMonth() + 1));
        }
        date_obj.setMonth(date_obj.getMonth() + Number(2));
        if (date_obj > over_limit) {
            $('[name=select_date_next]').val('').attr('disabled', true);
        } else {
            $('[name=select_date_next]').attr('disabled', false).val(
                    date_obj.getFullYear() + '-' + (date_obj.getMonth() + 1));
        }
    }

    function appendComboBox() {
        var date = new Date();
        for (var i = -10; i <= 10; ++i) {
            var append_date = new Date();
            append_date.setMonth(date.getMonth() + Number(i));
            $('[name=select_date_combo]').append(
                    $('<option>').html(
                            append_date.getFullYear() + '年'
                                    + (append_date.getMonth() + 1) + '月').val(
                            append_date.getFullYear() + '-'
                                    + (append_date.getMonth() + 1)))
        }
        $('[name=select_date_combo]').val(
                date.getFullYear() + '-' + (date.getMonth() + 1));
    }

})();
