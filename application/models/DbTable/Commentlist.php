<?php

class Application_Model_DbTable_Commentlist extends Zend_Db_Table_Abstract
{

    protected $_name = 'Commentlist';

    public function getCommentlist($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('id = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row->toArray();
    }

    public function addCommentlist($id, $comment, $dtime, $good)
    {

        $data = array(
            'id' => $id,
            'comment' => $comment,
            'dtime' => $dtime,
            'good' => $good,
        );

        $this->insert($data); //DBに新しいコメントを追加
    }

    public function addGood($num)
    {

        $row = $this->fetchRow('Num = ' . $num); //プライマリキーから選択した行を特定してDBから取り出す
        $goodplus = $row->good; //現在のいいねの数
        $goodplus++;
        $data = array(
            'good' => $goodplus,
        );
        $this->update($data, 'num ='. (int)$num); //1加算してからDBを更新

    }

}

