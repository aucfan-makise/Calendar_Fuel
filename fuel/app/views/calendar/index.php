<?php 
    use Fuel\Core\Config;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Calendar</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF8">
    <?php echo Asset::css('calendar.css'); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <?php echo Asset::js('calendar.js'); ?>
</head>
<body>
    <button name="select_date_before">前</button>
    <button name="select_date_next">次</button>
    <p>
        <select name="select_date_combo"></select>
        週の始まり
        <select name="start_week_day">
            <?php foreach (Config::get('calendar.week_day_array') as $key => $value): ?>
                <option value="<?php echo $key; ?>"<?php  echo $week_selectbox_selected_param; ?>>
                    <?php echo $value; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <form action="calendar.php" method="get">
            <p>
            表示するカレンダーの数
                <input type="text" size="2" maxlength="2" name="calendar_size" value="3">
                <input type="submit" value="change">
            </p>
        </form>
        
        <table id="calendar"></table>
        
        <div id="schedule_form_div">
            <form id="schedule_form">
                <p>
                予定の編集<br>
                開始日
                    <select name="schedule_start_year"></select>年
                    <select name="schedule_start_month"></select>月
                    <select name="schedule_start_day"></select>日
                    <select name="schedule_start_hour"></select>時
                    <select name="schedule_start_minute"></select>分
                    <br>
               終了日
                    <select name="schedule_end_year"></select>年
                    <select name="schedule_end_month"></select>月
                    <select name="schedule_end_day"></select>日
                    <select name="schedule_end_hour"></select>時
                    <select name="schedule_end_minute"></select>分
                    <br>
                    タイトル:
                    <input type="text" size="10" maxlength="100" id="schedule_title" name="schedule_title"><br>
                    詳細  :
                    <input type="text" size="100" maxlength="500" id="schedule_detail" name="schedule_detail">
                    
                    <button id="register">登録</button>
                    <input type="hidden" id="mode">
                    <input type="hidden" id="view_id">
                    <button id="modify">修正</button>
                    <button id="delete">削除</button>
                </p>
<!--                 hiddenのセッションID -->
            </form>
            <button id="schedule_form_close">キャンセル</button>
            <div id="error_message"></div>
        </div>

        <div id="schedule_form_finish_div">
            <div id="schedule_form_finish_message"></div>
            <button id="schedule_form_finish_div_close">閉じる</button>
        </div>
</body>
</html>
