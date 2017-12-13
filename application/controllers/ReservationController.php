<?php

class ReservationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $reservations = new Application_Model_DbTable_Reservations();
        $form = new Application_Form_Reservation();
        $form->setAttrib('id', 'reservation');
        $this->view->form = $form;
        $this->view->pageTitle = 'Reservation';

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();

                if($reservations->exists($data['date'], $data['period'])){
                    $this->view->errorMessage = "Already taken.";
                    $this->view->messageType = "error";
                    return;
                }

                try {
                    $reservations->insert($data);
                    $this->view->errorMessage = "Successfully reserved.";
                    $this->view->messageType = "success";
                    return;
                } catch (Zend_Exception $e) {
                    $this->view->messageType = "error";
                    $this->view->errorMessage = $e->getMessage();
                }

            }

        }
    }

}

