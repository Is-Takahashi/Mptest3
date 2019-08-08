<?php

class Application_Form_Comment extends Zend_Form
{
//AdminControllerのOtherActionから呼び出される処理。フォームに入力したコメントをDBに追加する
    public function init()
    {
        $this->setName('comment');

      $comment = new Zend_Form_Element_Text('comment');
      $comment->setLabel('Comment')
             ->setRequired(true)
             ->addFilter('StripTags')
             ->addFilter('StringTrim')
             ->addValidator('NotEmpty');

      $submit = new Zend_Form_Element_Submit('submit');
      $submit->setAttrib('id', 'submitbutton');

      $this->addElements(array($comment, $submit)); 
    }


}

