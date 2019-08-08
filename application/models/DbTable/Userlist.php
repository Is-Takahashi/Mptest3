<?php

class Application_Model_DbTable_Userlist extends Zend_Db_Table_Abstract
{

    protected $_name = 'Userlist';

    public function addUserlist($id, $pass)
    {
        $data = array(
            'id' => $id,
            'pass' => $pass,
        );
        $this->insert($data); //DBに新しいユーザを追加
    }

    public function checkpass($id, $pass)
    {
        //ハッシュ化して記録したDB側のpassと、入力されたpassを比較する

        //キーから選択した行を特定してDBから取り出す
        $targetid = $this->fetchRow('id = \''. $id .'\'');
        //DBに記録されているpassの値(ハッシュ化されている)を取り出す
        $targetpass = $targetid->pass;
        //値を比較。一致していればtrueが出力される
        $passhash = password_verify($pass, $targetpass);
        if($passhash === TRUE) {
            //入力されたpassが正しかった時、DBに記録されている「passのハッシュ値」を返す
            return $targetpass;
        } else {
            //入力されたpassが正しくなかった場合、文字列「missmatch」を返す
            return 'missmatch';
        }
    }
}
