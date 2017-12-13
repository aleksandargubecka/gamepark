<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loggedIn = $auth->getIdentity();
            if($loggedIn->role > 0){
                return;
            }
        }
        $this->_redirect( '/');

    }

    public function indexAction()
    {
        $users = new Application_Model_DbTable_Users();

        $rows = $users->fetchAll(
            $users->select()
                ->order('last_login ASC')
                ->limit(10, 0)
        );

        $this->view->lastLoggedUsers = $rows;
    }

    public function usersAction()
    {
        $users = new Application_Model_DbTable_Users();
        $this->view->users = $users->fetchAll();
    }

    public function reservationsAction()
    {
        $users = new Application_Model_DbTable_Reservations();
        $this->view->reservations = $users->fetchAll();
    }


}





