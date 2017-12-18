<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        $messages = $this->_helper->flashMessenger->getMessages();

        if(!empty($messages))
            $this->view->messages = $messages;

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loggedIn = $auth->getIdentity();
            if($loggedIn->role > 0){ return; }
        }
        $this->redirect( '/');

    }

    public function indexAction()
    {
        $users = new Application_Model_DbTable_Users();

        $rows = $users->fetchAll(
            $users->select()
                ->order('last_login DESC')
                ->limit(10, 0)
        );

        $this->view->pageTitle = "Admin";
        $this->view->lastLoggedUsers = $rows;
    }

    public function usersAction()
    {
        $page = $this->_request->getParam('page', 1);
        $this->view->pageTitle = "Admin | Users";
        $users = new Application_Model_DbTable_Users();
        $paginator = Zend_Paginator::factory($users->fetchAll());
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);
        $this->view->users = $paginator;
    }

    public function reservationsAction()
    {
        $page = $this->_request->getParam('page', 1);
        $this->view->pageTitle = "Admin | Reservation";
        $reservations = new Application_Model_DbTable_Reservations();

        $paginator = Zend_Paginator::factory($reservations->fetchAll());
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);
        $this->view->reservations = $paginator;
    }

    public function activitiesAction()
    {
        $page = $this->_request->getParam('page', 1);
        $this->view->pageTitle = "Admin | Activities";
        $activities = new Application_Model_DbTable_Activities();
        $paginator = Zend_Paginator::factory($activities->fetchAll(null, 'starts_at DESC'));
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);
        $this->view->activities = $paginator;
    }


}

