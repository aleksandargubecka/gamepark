<?php

class Application_Form_Contact extends Zend_Form
{

    public function init()
    {
        $email = $this->createElement('text','email');
        $email->setLabel('Email: *')
            ->setRequired(false);

        $subject = $this->createElement('text','subject');
        $subject->setLabel('Subject: *')
            ->setRequired(true);

        $message = $this->createElement('textarea','message');
        $message->setLabel('Message: *')
            ->setRequired(true)->setAttrib('rows', 10);

        $submit = $this->createElement('submit','register');
        $submit->setLabel('Send')
            ->setIgnore(true);

        $this->addElements(array(
            $email,
            $subject,
            $message,
            $submit
        ));
    }


}

