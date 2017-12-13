<?php

class Application_Form_EditUser extends Zend_Form
{

    public function init()
    {
        $fullname = $this->createElement('text','real_name');
        $fullname->setLabel('Full Name:');

        $email = $this->createElement('text','email');
        $email->setLabel('Email: *');

        $username = $this->createElement('text','username');
        $username->setLabel('Username: *');

        $oldPassword = $this->createElement('password','oldPassword');
        $oldPassword->setLabel('Old Password:');

        $newPassword = $this->createElement('password','newPassword');
        $newPassword->setLabel('New Password:');

        $confirmNewPassword = $this->createElement('password','confirmNewPassword');
        $confirmNewPassword->setLabel('New Password Confirm:');

        $submit = $this->createElement('submit','edit');
        $submit->setLabel('Submit')->setIgnore(true);

        $this->addElements(array(
            $fullname,
            $email,
            $username,
            $oldPassword,
            $newPassword,
            $confirmNewPassword,
            $submit
        ));
    }


}

