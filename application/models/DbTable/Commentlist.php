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

        //DBに新しいコメントを追加
        $this->insert($data);
    }

    public function addGood($num)
    {

        //プライマリキーから選択した行を特定してDBから取り出す
        $row = $this->fetchRow('Num = ' . $num);
        //現在のいいねの数
        $goodplus = $row->good;
        $goodplus++;
        $data = array(
            'good' => $goodplus,
        );
        //いいねの値を1加算してからDBを更新
        $this->update($data, 'num ='. (int)$num);

    }

}
