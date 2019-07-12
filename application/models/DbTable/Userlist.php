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


}

