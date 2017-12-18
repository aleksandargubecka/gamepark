<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        $messages = $this->_helper->flashMessenger->getMessages();

        if(!empty($messages))
            $this->view->messages = $messages;
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction()
    {
        $users = new Application_Model_DbTable_Users();
        $form = new Application_Form_Login();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'users');

        $form->setAttrib('id', 'signup');
        $this->view->form = $form;
        $this->view->pageTitle = 'Login';

        if ($auth->hasIdentity()) {
            $this->redirect($baseUrl->baseUrl() . '/');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {

                $data = $form->getValues();
                $authAdapter->setIdentityColumn('username')->setCredentialColumn('password');
                $authAdapter->setIdentity($data['username'])->setCredential(md5($data['password']));

                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $storage = new Zend_Auth_Storage_Session();
                    $users->update(array('last_login' => date('Y-m-d G:i:s')), array('username=?' => $data['username']));
                    $storage->write($authAdapter->getResultRowObject());
                    $this->redirect($baseUrl->baseUrl() . '/');
                } else {
                    $this->view->messages = array(array('message' => "Invalid username or password. Please try again.", 'type' => 'error'));

                    return;
                }
            } else {
                $this->_helper->flashMessenger( array('message' => 'Invalid request.', 'type' => 'error') );
                $this->redirect($baseUrl->baseUrl() . '/');
            }

        }

    }

    public function signupAction()
    {
        $users = new Application_Model_DbTable_Users();
        $form = new Application_Form_SignUp();

        $form->setAttrib('id', 'login');
        $this->view->form = $form;
        $this->view->pageTitle = 'Sing Up';

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                if ($data['password'] != $data['confirmPassword']) {
                    $this->view->messages = "Password and confirm password don't match.";
                    $this->view->messageType = "error";

                    return;
                }

                unset($data['confirmPassword']);

                if (!$users->isUnique($data['username'])) {
                    $this->view->messages = array(array('message' => "User with username \"{$data['username']}\" already exits.", 'type' => 'error'));
                    return;
                }

                if (!$users->isUnique($data['email'])) {
                    $this->view->messages = array(array('message' => "User with email \"{$data['email']}\" already exits.", 'type' => 'error'));
                    return;
                }

                $data['password'] = md5($data['password']);

                try {
                    $users->insert($data);
                    $this->redirect('auth/login');
                } catch (Zend_Exception $e) {
                    $this->view->messages = array(array('message' => $e->getMessage(), 'type' => 'error'));
                }
            }
        }
    }

    public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->redirect('auth/login');
    }

    public function editAction()
    {
        $users = new Application_Model_DbTable_Users();
        $form = new Application_Form_EditUser();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $auth = Zend_Auth::getInstance();

        if(!$auth->hasIdentity())
            $this->redirect($baseUrl->baseUrl() . '/');

        $user = $auth->getIdentity();

        $form->real_name->setValue($user->real_name);
        $form->email->setValue($user->email);
        $form->username->setValue($user->username);

        $form->setAttrib('id', 'login');

        $this->view->form = $form;
        $this->view->pageTitle = 'Edit';


        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();

                if (!$users->isUnique($data['username']) && $data['username'] != $user->username) {
                    $this->view->messages = array(array('message' => "User with username \"{$data['username']}\" already exits.", 'type' => 'error'));

                    return;
                }

                if (!$users->isUnique($data['email']) && $data['email'] != $user->email) {
                    $this->view->messages = array(array('message' => "User with email \"{$data['email']}\" already exits.", 'type' => 'error'));
                    return;
                }

                if(!empty($data['oldPassword'])){

                    if ($data['newPassword'] != $data['confirmNewPassword'] || md5($data['oldPassword']) != $user->password) {
                        $this->view->messages = array(array('message' => "Password and confirm password don't match.", 'type' => 'error'));
                        return;
                    }

                    $data['password'] = md5($data['newPassword']);
                }


                unset($data['oldPassword']);
                unset($data['newPassword']);
                unset($data['confirmNewPassword']);

                try {
                    $users->update($data, array('id=?' => $user->id));
                    $this->view->messages = array(array('message' => "Successfully updated.", 'type' => 'success'));
                } catch (Zend_Exception $e) {
                    $this->view->messages = array(array('message' => $e->getMessage(), 'type' => 'error'));
                }
            }
        }
    }

    public function promoteAction()
    {
        $this->is_allowed();

        $id = $this->_request->getParam('id');
        $role = $this->_request->getParam('role', 0);
        $activities = new Application_Model_DbTable_Users();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $activity = $activities->fetchRow('id = '.$id);

        if(!empty($activity)){
            $activities->update(array('role' => $role), 'id = '.$id);
        }

        $this->redirect($baseUrl->baseUrl() . '/admin/users');
    }

    private function is_allowed()
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loggedIn = $auth->getIdentity();
            if($loggedIn->role > 0){
                return;
            }
        }
        $this->redirect( '/');
    }

}











