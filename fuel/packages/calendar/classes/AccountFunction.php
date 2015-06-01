<?php
class AccountFunction{
    private $address = '';
    private $password = '';
    
    /**
     * ユーザ名を返す
     * @return string $address
     */
    public function getAddress(){
        return $this->address;
    }

    /**
     * パスワードを返す
     * @return string $password
     */
    public function getPassword(){
        return $this->password;
    }

    public function validateLoginPost(){
        $this->checkAddress();
        
        if (empty($_POST['password'])) {
            throw new Exception('パスワードが入力されていません。');
        }
        $this->password = Crypt::encode($_POST['password'], false);
        
        $pass = Model_Account::selectUserPassword($this->address);
        if (! Model_Account::accountExists($this->address) || $pass != $this->password){
            throw new Exception('アドレス/パスワードが間違っています。');
        }
    }
    /**
     * アカウント登録に関するデータのチェック
     * 
     * @access public
     * @throws Exception
     */
    public function validateRegistrationPost(){
        $this->checkAddress();
           
        if (Model_Account::accountExists($_POST['address'])){
            throw new Exception('既に同じアドレスが登録されています。');
        }
        
        if (empty($_POST['password']) || empty($_POST['check_password'])) {
            throw new Exception('パスワードが入力されていません。');
        }
        if (strlen($_POST['password']) < 8) {
            throw new Exception('パスワードは8文字以上にしてください。');
        }
        if ($_POST['password'] !== $_POST['check_password']){
            throw new Exception('確認パスワードが一致しません。');
        }
        $this->password = Crypt::encode($_POST['password'], false);
    }
    
    /**
     * アドレスをチェックする
     * @access private
     * @throws Exception
     */
    private function checkAddress(){
        if (empty($_POST['address'])) {
            throw new Exception('アドレスが入力されていません。');
        }
        $this->address = $_POST['address'];
        if (! filter_var($_POST['address'], FILTER_VALIDATE_EMAIL)){
            throw new Exception('アドレスが正しくありません。');
        }
    }
    /**
     * 受け取ったtokenとsession_idの暗号化したものを比較する
     * @access protected
     * @param string $token
     * @return boolean
     */
    public static function identifyUser($token){
        return Crypt::encode(Session::key(), false) == $token ? true : false;
    }
    
    /**
     * CSRF対策として受け取ったsession idの暗号化したものを返す
     * @access public
     * @return string
     */
    public static function getToken(){
        return Crypt::encode(Session::key(), false);
    }
}