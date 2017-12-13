<?php

class AuthController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function loginAction() {
        $users = new Application_Model_DbTable_Users();
        $form = new Application_Form_Login();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'users');

        $form->setAttrib('id', 'signup');
        $this->view->form = $form;
        $this->view->pageTitle = 'Login';

        if ($auth->hasIdentity()) {
            $this->_redirect($baseUrl->baseUrl() . '/');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {

                $data = $form->getValues();
                $authAdapter->setIdentityColumn('username')->setCredentialColumn('password');
                $authAdapter->setIdentity($data['username'])->setCredential($data['password']);

                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $storage = new Zend_Auth_Storage_Session();
                    $storage->write($authAdapter->getResultRowObject());
                    $this->_redirect($baseUrl->baseUrl() . '/');
                } else {
                    $this->view->errorMessage = "Invalid username or password. Please try again.";
                    $this->view->messageType = "error";
                    return;
                }
            } else {
                $this->view->errorMessage = "Invalid request.";
                $this->view->messageType = "error";
                $this->_redirect($baseUrl->baseUrl() . '/');
            }

        }

    }

    public function signupAction() {
        $users = new Application_Model_DbTable_Users();
        $form = new Application_Form_Registration();

        $form->setAttrib('id', 'login');
        $this->view->form = $form;
        $this->view->pageTitle = 'Sing Up';

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                if ($data['password'] != $data['confirmPassword']) {
                    $this->view->errorMessage = "Password and confirm password don't match.";
                    $this->view->messageType = "error";
                    return;
                }
                unset($data['confirmPassword']);

                if (!$users->isUnique($data['username'])) {
                    $this->view->errorMessage = "User with username \"{$data['username']}\" already exits.";
                    $this->view->messageType = "error";
                    return;
                }

                if (!$users->isUnique($data['email'])) {
                    $this->view->errorMessage = "User with email \"{$data['email']}\" already exits.";
                    $this->view->messageType = "error";
                    return;
                }

                try {
                    $users->insert($data);
                    $this->_redirect('auth/login');
                } catch (Zend_Exception $e) {
                    $this->view->messageType = "error";
                    $this->view->errorMessage = $e->getMessage();
                }
            }
        }
    }

    public function logoutAction() {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->_redirect('auth/login');
    }


}







