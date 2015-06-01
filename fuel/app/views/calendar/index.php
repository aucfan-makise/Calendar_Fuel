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
    <?php echo Asset::js('schedule.js'); ?>
</head>
<body>
    <?php if (isset($user_name)): ?>
        User:<?php echo $user_name; ?>
        <a href="/index/logout">ログアウト</a>
    <?php else: ?>
        <a id="login_link" href="/index/login">ログイン</a>
        <a href="/index/account_registration">新規登録</a>
    <?php endif; ?>
    <br>
    <button name="select_date_before">前</button>
    <button name="select_date_next">次</button>
    <p>
        <select name="select_date_combo"></select>
        週の始まり
        <select name="start_week_day">
            <?php foreach (Config::get('calendar.week_day_array') as $key => $value): ?>
                <option value="<?php echo $key; ?>">
                    <?php echo $value; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <form>
            <p>
            表示するカレンダーの数
                <input type="text" size="2" maxlength="1" name="calendar_size">
                <button id="change_calendar_size">change</button>
            </p>
        </form>
        <div id="calendar_div"><?php echo $calendar; ?></div>
        <div id="overlay"></div>
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
                    <input type="hidden" id="view_id">
                    <button id="modify">修正</button>
                    <button id="delete">削除</button>
                </p>
                <input type="hidden" name="token" value="<?php echo AccountFunction::getToken(); ?>">
            </form>
            <button id="schedule_form_close">キャンセル</button>
            <div id="error_message"></div>
        </div>

        <div id="schedule_form_finish_div">
            <div id="schedule_form_finish_message"></div>
            <button id="schedule_form_finish_div_close">閉じる</button>
        </div>
        <div id="loading"><?php echo Asset::img('loading.gif', array('id' => 'loading_img')); ?></div>
</body>
</html>
