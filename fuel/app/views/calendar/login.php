<!DOCTYPE html>
    <head>
        <title>ログイン</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <form method="post" action="login">
            E-Mailアドレス
            <br>
            <input type="text" size="30" name="address" value="<?php echo isset($user_name) ? $user_name : ''; ?>">
            <br>
            パスワード
            <br>
            <input type="password" size="30" name="password">
            <br>
            <input type="submit" name="login" value="ログイン">
            <input type="hidden" name="token" value="<?php echo AccountFunction::getToken(); ?>">
        </form>
        <a href="account_registration">新規登録</a>
        <br>
        <?php if (isset($error_msg)) echo $error_msg; ?>
    </body>
</html>