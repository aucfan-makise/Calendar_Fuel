<!DOCTYPE html>
    <head>
        <title>アカウント登録</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <form method="post" action="account_registration">
            E-mailアドレス
            <br>
            <input type="text" size="30" name="address" value="<?php echo isset($user_name) ? $user_name : ''; ?>">
            <br>
            パスワード
            <br>
            <input type="password" size="30" name="password">
            <br>
            もう一度パスワード
            <br>
            <input type="password" size="30" name="check_password">
            <br>
            <input type="submit" name="register" value="登録">
            <input type="hidden" name="token" value="<?php echo AccountFunction::getToken(); ?>">
        </form>
        <?php if (isset($error_msg)) echo $error_msg; ?>
    </body>
</html>