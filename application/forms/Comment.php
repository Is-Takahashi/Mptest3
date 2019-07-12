<?php

class Application_Form_Comment extends Zend_Form
{
//AdminControllerのOtherActionから呼び出される処理。フォームに入力したコメントをDBに追加する
    public function init()
    {
        $this->setName('comment');

      //$auth = Zend_Auth::getInstance();
      //$id = $auth->getIdentity();

      $comment = new Zend_Form_Element_Text('comment');
      $comment->setLabel('Comment')
             ->setRequired(true)
             ->addFilter('StripTags')
             ->addFilter('StringTrim')
             ->addValidator('NotEmpty');

      //$dtime = date("Y/m/d H:i:s");

      $submit = new Zend_Form_Element_Submit('submit');
      $submit->setAttrib('id', 'submitbutton');

      $this->addElements(array(/*$id,*/ $comment, /*$dtime,*/ $submit)); 
    }


}

