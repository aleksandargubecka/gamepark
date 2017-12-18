<?php

class Application_Form_Comment extends Zend_Form
{

    public function init()
    {
        $comment = $this->createElement('textarea','text');
        $comment->setLabel('Leave a Comment:')
            ->setRequired(true)->setAttrib('rows', 10);

        $submit = $this->createElement('submit','register');
        $submit->setLabel('Send')
            ->setIgnore(true);

        $this->addElements(array(
            $comment,
            $submit
        ));
    }


}

