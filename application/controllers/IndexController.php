<?php

class IndexController extends Zend_Controller_Action
{
    const AUTH_TABLE_NAME = 'Userlist';
    const AUTH_ID_NAME    = 'id';
    const AUTH_PASS_NAME  = 'pass';

    private $errmsg = array();

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //ログインページに移動
        return $this->_forward('login-page');
    }

    public function loginPageAction()
    {
        //ログインページのビュースクリプトを指定
        $this->renderScript('/index/login.php');
    }

    public function loginAction()
    {
        //Bootstrap.phpに書いた処理とあわせて、application.iniのdbの設定を読み込む
        $dbAdapter = Zend_Registry::get('db');

        $authAdapter = new Zend_Auth_Adapter_DbTable(
                               $dbAdapter,
                               self::AUTH_TABLE_NAME,
                               self::AUTH_ID_NAME,
                               self::AUTH_PASS_NAME);

        //入力したidとpassを取得
        $id  = $this->getRequest()->getParam('id', '');
        $pass = $this->getRequest()->getParam('pass', '');

        //id、もしくはpassが入力されていない場合はログインページに戻す
        if ($id === '' || $pass === '') {
            return $this->_forward('login-page');
        }
        //idとpassが英数字のみを使っているか確認し、それ以外の文字を使っていた場合はログインページに戻す
        if (!Zend_Validate::is($id,    'Alnum') || !Zend_Validate::is($pass,    'Alnum')){
            echo('idとpassは英数字のみ使えます');
            return $this->_forward('login-page');
        }

        //idとpassをセット
        $authAdapter->setIdentity($id);
        $authAdapter->setCredential($pass);

        //DBに登録されているidとpassが、今回入力されたidとpassと一致するか調べる(passをハッシュ化してDBに登録しているユーザidの場合、ここの認証は失敗する)
        $result = $authAdapter->authenticate();

        //idまたはpassが一致しなかった場合の処理
        if ($result->isValid() === FALSE) {
            //getCodeが-1のときはidが一致しなかった時である
            if($result->getCode() == -1){
                //入力されたidを新規ユーザとして登録する処理のactionを呼び出す
                return $this->_forward('adduser');
            }

            //ここからpassの照合。
            $DBpass = new Application_Model_DbTable_Userlist();
            $passresult = $DBpass->checkpass($id, $pass);

            //$passresultに返ってくる値は、passが正しかった場合は「DBに記録されている、そのpassのハッシュ値」で、passが誤っていた場合は文字列「missmatch」である

            if($passresult == 'missmatch'){
                //idはあっているがpassが間違っているということなので、ログインページに戻す
                echo('passがちがいます');
                return $this->_forward('login-page');
            }

            //入力されたidもpassも正しいことが判明したので、DBに登録されたpassを利用して再度認証を行う(当然成功する)
            //認証が成功することによって、下記の「getResultRowObject(array('id'))」などの処理が予定通り行われ、ユーザのidがストレージに登録される
            $authAdapter->setCredential($passresult);
            $result = $authAdapter->authenticate();
        }

        //認証が完了したので、ストレージ(セッション)に値(id)を入れる
        $storage = Zend_Auth::getInstance()->getStorage();
        $resultRow = $authAdapter->getResultRowObject(array('id'));
        $storage->write($resultRow);

        // session_idを再び生成
        $ret = session_regenerate_id(true);
        // 認証が完了したのでloginページに移動
        return $this->_forward('index', 'admin');
    }

    public function logoutAction()
    {
        //ストレージに入れていた値を削除
        $authStorage = Zend_Auth::getInstance()->getStorage();
        $authStorage->clear();
        //そのままログインページに移動
        return $this->_forward('login-page');
    }

    public function adduserAction()
    {
        //このactionはログイン時に入力したidが既存のidと一致しなかった場合に、新規ユーザとして登録する処理

        //Bootstrap.phpに書いた処理とあわせて、application.iniのdbの設定を読み込む
        $dbAdapter = Zend_Registry::get('db');

        $authAdapter = new Zend_Auth_Adapter_DbTable(
                               $dbAdapter,
                               self::AUTH_TABLE_NAME,
                               self::AUTH_ID_NAME,
                               self::AUTH_PASS_NAME);

        //改めて、入力したidとpassを取得し直す
        $id  = $this->getParam('id', '');
        $pass = $this->getParam('pass', '');

        //入力されたpassの値をhash化
        $passhash = password_hash($pass, PASSWORD_DEFAULT);

        //DBに新しいidとpassを追加
        $users = new Application_Model_DbTable_Userlist();
        $users->addUserlist($id, $passhash);

        //あとは通常のログイン成功時と同じく、セッションに値を入れてからログイン後の画面へ移動

        //idとpassをセット
        $authAdapter->setIdentity($id);
        $authAdapter->setCredential($passhash);

        //DBに登録したidとpassをそのまま認証に利用して、認証を完了させる
        $result = $authAdapter->authenticate();

        //なぜかid、もしくはpassが間違っていた場合の処理
        if ($result->isValid() === FALSE) {
            //もし正しくログインできなかった場合、ログインページに戻す
            echo('もう一度やってみてください');
            return $this->_forward('login-page');
        }

        //認証が完了したので、ストレージ(セッション)に値を入れる
        $storage = Zend_Auth::getInstance()->getStorage();
        $resultRow = $authAdapter->getResultRowObject(array('id'));
        $storage->write($resultRow);
        //session_idを再び生成
        $ret = session_regenerate_id(true);
        //loginページに移動
        return $this->_forward('index', 'admin');
    }
}
