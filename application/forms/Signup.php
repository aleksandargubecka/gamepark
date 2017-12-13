<?php

class Application_Form_Registration extends Zend_Form
{

    public function init()
    {
        $fullname = $this->createElement('text','real_name');
        $fullname->setLabel('Full Name:')
            ->setRequired(false);

        $email = $this->createElement('text','email');
        $email->setLabel('Email: *')
            ->setRequired(false);

        $username = $this->createElement('text','username');
        $username->setLabel('Username: *')
            ->setRequired(true);

        $password = $this->createElement('password','password');
        $password->setLabel('Password: *')
            ->setRequired(true);

        $confirmPassword = $this->createElement('password','confirmPassword');
        $confirmPassword->setLabel('Confirm Password: *')
            ->setRequired(true);

        $submit = $this->createElement('submit','register');
        $submit->setLabel('Sign up')
            ->setIgnore(true);

        $this->addElements(array(
            $fullname,
            $email,
            $username,
            $password,
            $confirmPassword,
            $submit
        ));
    }


}

