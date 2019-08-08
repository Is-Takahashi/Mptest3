<?php

class AdminController extends Zend_Controller_Action
{

    private $authInfo = array();

    public function preDispatch()
    {
        //ログインしたユーザの値を取得
        $authStorage = Zend_Auth::getInstance()->getStorage();
        if ($authStorage->isEmpty()) {
            // もしidかpassがなかったら、ログインページに戻る
            return $this->_forward('login-page', 'index');
        }
        $this->authInfo = (array)$authStorage->read();
    }

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //ログイン後のトップページを表示するためのaction

        $auth = Zend_Auth::getInstance();
        $id = $auth->getIdentity()->id;

        echo('現在のユーザidは'. $id .'です');

        $comment = new Application_Model_DbTable_Commentlist();
        $this->view->comment = $comment->fetchAll();
    }

    public function addcommentAction()
    {
        //コメントを追加するためのaction

        //コメント追加用formの呼び出し
        $form = new Application_Form_Comment();
        $form->submit->setLabel('コメント追加');
        $this->view->form = $form;

        //コメントが入力された場合は以下の処理をおこない、コメントをDBに追加
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            //コメントの入力値を確認して、大丈夫そうならコメント追加処理
            if ($form->isValid($formData)) {
                //入力されたコメントを取得
                $commentl = $form->getValue('comment');

                //idは現在ログインしているものを使用
                $auth = Zend_Auth::getInstance();
                $id = $auth->getIdentity()->id;

                //現在時刻も取得
                $dtime = date("Y/m/d H:i:s");
                //いいねの初期値はもちろん0
                $good = 0;

                //コメントに必要な値を全てそろえたので、DBに接続するためにmodelのインスタンスを作成し、
                //addCommentlistのファンクションを呼び出す
                $comments = new Application_Model_DbTable_Commentlist();
                $comments->addCommentlist($id, $commentl, $dtime, $good);

                //addCommentlistの処理が終わり、コメントを追加できたので、元のindexのページに戻る
                $this->_helper->redirector('index');
            }else{
                $form->populate($formData);
            }
        }
    }

    public function goodAction()
    {
        //いいねを押された時、いいねに1加算するための処理

        //現在のいいねの数を取得して、addGoodファンクションを呼び出す。終わったら元のindexのページに戻る
        $goodnum = $this->getParam('Num');//いいねしたいコメントの番号を取得
        $goods = new Application_Model_DbTable_Commentlist();
        $goods->addGood($goodnum);
        $this->_helper->redirector('index');
    }
}
