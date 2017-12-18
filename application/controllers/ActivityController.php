<?php

class ActivityController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $messages = $this->_helper->flashMessenger->getMessages();

        if(!empty($messages)){
            $this->view->messages = $messages;
        }
    }

    public function indexAction()
    {
        $search = $this->_request->getParam('search');
        $where = null;
        $page = $this->_request->getParam('page', 1);
        $this->view->pageTitle = "Activities";
        $activities = new Application_Model_DbTable_Activities();

        try{
            if(!empty($search)){
                $activitiesFetched = $activities->search($search);
                $this->view->search = $search;
            }else{
                $activitiesFetched = $activities->fetchAll(null, 'starts_at DESC');
            }
        }catch (Zend_Exception $e){
            $this->view->messages = array(array('message' => $e->getMessage(), 'type' => 'error'));
        }

        if(!empty($activitiesFetched)){
            $paginator = Zend_Paginator::factory($activitiesFetched);
            $paginator->setItemCountPerPage(9);
            $paginator->setCurrentPageNumber($page);
            $this->view->activities = $paginator;
        }
    }

    public function singleAction()
    {
        $id = $this->_request->getParam('id');
        $auth = Zend_Auth::getInstance();
        $activities = new Application_Model_DbTable_Activities();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $activity = $activities->fetchRow('id = '.$id);
        $form = new Application_Form_Comment();

        $form->setAttrib('id', 'comment');
        $this->view->form = $form;
        $this->view->pageTitle = strlen($activity->title) > 50 ? substr($activity->title, 0, 50) . '...' : $activity->title; ;
        $this->view->is_logged_in = $auth->hasIdentity();
        $this->view->activity = $activity;

        $this->getActivityDependencies($activity);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $comments = new Application_Model_DbTable_Comments();
                $user = $auth->getIdentity();
                $data = $form->getValues();
                $data['activity_id'] = $id;
                $data['user_id'] = $user->id;
                $comments->insert($data);
                $this->redirect($baseUrl->baseUrl() . '/activity/' . $id . '#comments');
            }
        }
    }

    public function createAction()
    {
        $this->is_allowed();

        $activities = new Application_Model_DbTable_Activities();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $form = new Application_Form_CreateActivity();

        $form->setAttrib('id', 'add-new-activity');
        $this->view->form = $form;
        $this->view->pageTitle = 'Add New Activity';


        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                try {
                    $data = $form->getValues();

                    $this->formatDateData($data);

                    if(strtotime($data['starts_at']) > strtotime($data['ends_at'])){
                        $this->view->messages = array(array('message' => "Start time must be before end date.", 'type' => 'error'));

                        return;
                    }

                    if(!empty($form->featured_image))
                        $form->featured_image->receive();

                    $data['featured_image_id'] = $this->saveImage($form);
                    unset($data['featured_image']);
                    unset($data['gallery']);

                    $id = $activities->insert($data);
                    $this->saveGallery($form, $id);
                    $this->_helper->flashMessenger( array('message' => 'Successfully created.', 'type' => 'success') );
                    $this->redirect($baseUrl->baseUrl() . '/admin/activities/edit/' . $id);
                } catch (Zend_Exception $e) {
                    $this->view->messages = array(array('message' => $e->getMessage(), 'type' => 'error'));
                }
            }
        }
    }

    public function editAction()
    {
        $this->is_allowed();

        $id = $this->_request->getParam('id');
        $activities = new Application_Model_DbTable_Activities();
        $media = new Application_Model_DbTable_Media();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $activity = $activities->fetchRow('id = '.$id);

        if(empty($activity))
            $this->redirect($baseUrl->baseUrl() . '/admin/activities/add');

        $form = new Application_Form_CreateActivity();

        $form->setAttrib('id', 'edit-new-activity');
        $this->view->form = $form;
        $this->view->pageTitle = 'Edit New Activity';

        $form->title->setValue($activity->title);
        $form->description->setValue($activity->description);
        $form->starts_at->setValue($this->getDayFromDate($activity->starts_at));
        $form->ends_at->setValue($this->getDayFromDate($activity->ends_at));
        $form->starts_at_hour->setValue($this->getHoursFromDate($activity->starts_at));
        $form->ends_at_hour->setValue($this->getHoursFromDate($activity->ends_at));

        if(!empty($activity->featured_image_id)){
            $this->view->featured_image = $media->fetchRow( 'id = ' . $activity->featured_image_id);
        }

        $gallery = $activity->findManyToManyRowset('Application_Model_DbTable_Media', 'Application_Model_DbTable_Galleries', 'Application_Model_DbTable_Activities');

        if(!empty($gallery)){
            $this->view->gallery = $gallery;
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                try {
                    $data = $form->getValues();

                    $this->formatDateData($data);

                    if(strtotime($data['starts_at']) > strtotime($data['ends_at'])){
                        $this->view->messages = array(array('message' => "Start time must be before end date.", 'type' => 'error'));

                        return;
                    }

                    $data['featured_image_id'] = $this->saveImage($form, $activity);
                    unset($data['featured_image']);
                    unset($data['gallery']);

                    $activities->update($data, array('id=?' => $activity->id));
                    $this->saveGallery($form, $id);

                    $this->_helper->flashMessenger( array('message' => 'Successfully updated.', 'type' => 'success') );
                    $this->redirect($baseUrl->baseUrl() . '/admin/activities/edit/' . $id);
                } catch (Zend_Exception $e) {
                    $this->view->messages = array(array('message' => $e->getMessage(), 'type' => 'error'));
                }
            }
        }
    }

    public function deleteAction()
    {
        $this->is_allowed();

        $id = $this->_request->getParam('id');
        $activities = new Application_Model_DbTable_Activities();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $activity = $activities->fetchRow('id = '.$id);

        if(!empty($activity)){
            $activities->delete('id = '.$id);
        }

        $this->redirect($baseUrl->baseUrl() . '/admin/activities');

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

    private function getActivityDependencies($activity) {

        $activityComments = $activity->findDependentRowset('Application_Model_DbTable_Comments');
        $gallery = $activity->findManyToManyRowset('Application_Model_DbTable_Media', 'Application_Model_DbTable_Galleries', 'Application_Model_DbTable_Activities');

        if(!empty($activity->featured_image_id)){
            $image = $activity->findDependentRowset('Application_Model_DbTable_Media');
            if(!empty($image)){
                $this->view->image = $image[0];
            }
        }
        if(!empty($activityComments)){
            $comments = new Application_Model_DbTable_Comments();
            $this->view->comments = $comments->getCommentsWithUserByActivity($activity->id);
        }
        if(!empty($gallery)){
            $this->view->gallery = $gallery;
        }
    }


    private function saveImage($form, $old = null)
    {
        $media = new Application_Model_DbTable_Media();
        if(empty($form->featured_image->getValue())){
            if(!empty($old)){
                return $old->featured_image_id;
            }else{
                return null;
            }
        }

        $fileInfo = $form->featured_image->getFileInfo();
        $form->featured_image->receive();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $name = $fileInfo['featured_image']['name'];
        $path = $baseUrl->BaseUrl() . '/uploads/' .$name;

        try{

            $exitsImage = $media->fetchRow('path = "' . $path . '"');
            if(!empty($exitsImage)){
                return $exitsImage->id;
            }

            $mediaData = array(
                'path' => $path,
                'title' => $name
            );

            $id = $media->insert($mediaData);

            return $id;
        }catch (Zend_Exception $e){
            $this->_helper->flashMessenger( array('message' => $e->getMessage(), 'type' => 'error') );
        }

        return null;
    }

    private function saveGallery($form, $activity_id) {
        if(!$form->gallery->isUploaded())
            return;

        if ($form->gallery->receive()) {

            $media = new Application_Model_DbTable_Media();
            $gallery = new Application_Model_DbTable_Galleries();
            $baseUrl = new Zend_View_Helper_BaseUrl();

            // Clear old images
            $oldImages = $gallery->fetchAll('activity_id = ' . $activity_id);
            if(!empty($oldImages)){
                foreach ($oldImages as $oldImage) {
                    $gallery->delete('id = ' . $oldImage->id);
                }
            }
            $files = $form->gallery->getFileInfo();

            foreach ($files as $file) {
                $path = $baseUrl->BaseUrl() . '/uploads/' .$file['name'];
                $media_id = $media->insert(array(
                    'title' => $file['name'],
                    'path' => $path
                    )
                );
                $gallery->insert(array(
                    'activity_id' => $activity_id,
                    'media_id' => $media_id
                ));
            }
        }
    }

    private function getHoursFromDate($date)
    {
        return date('G',strtotime($date));
    }

    private function getDayFromDate($date)
    {
        return strtotime(date('Y-m-d', strtotime($date)));
    }

    private function formatDateData(&$data)
    {
        $data['starts_at'] = $this->mergeTimeWithHour($data['starts_at'], $data['starts_at_hour']);
        $data['ends_at'] = $this->mergeTimeWithHour($data['ends_at'], $data['ends_at_hour']);
        unset($data['starts_at_hour']);
        unset($data['ends_at_hour']);
    }

    private function mergeTimeWithHour($date, $time)
    {
        return date("Y-m-d", $date) . ' ' . $time . ':00';
    }
}
