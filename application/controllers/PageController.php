<?php

class PageController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function contactAction()
    {
        $form = new Application_Form_Contact();
        $form->setAttrib('id', 'contact');
        $this->view->form = $form;
        $this->view->pageTitle = 'Contact';
        $this->view->active = 'contact';

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {

                try{
                    // Get super admin's mail
                    $superAdmin = new Application_Model_DbTable_Users();
                    $adminsRow = $superAdmin->fetchRow($superAdmin->select()->where('role = ?', 1));

                    // Send mail
                    $mail = new Zend_Mail();
                    $mail->setBodyText($_POST['message']);
                    $mail->setFrom($_POST['email'], 'Some Sender');
                    $mail->addTo($adminsRow->email, 'Some Recipient');
                    $mail->setSubject($_POST['subject']);
                    $mail->send();
                    $this->view->errorMessage = "Message successfully sent.";
                    $this->view->messageType = "success";
                }catch (Zend_Exception $e){
                    $this->view->errorMessage = $e->getMessage();
                    $this->view->messageType = "error";
                }
            }
        }
    }


}


