<?php

class Rest_ActivitiesController extends Zend_Rest_Controller {

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'text/json');
    }

    public function headAction(){}

    public function indexAction()
    {
        try{
            $activities = new Application_Model_DbTable_Activities();
            $rows = $activities->fetchAll();
            if(!empty($rows)){
                $this->getResponse()->setBody(Zend_Json::encode($rows));
                $this->getResponse()->setHttpResponseCode(200);
            }else{
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Request not valid.')));
                $this->getResponse()->setHttpResponseCode(400);
            }
        }catch (Zend_Exception $e){
            $this->getResponse()->setBody(Zend_Json::encode(array('message' => $e->getMessage())));
            $this->getResponse()->setHttpResponseCode(400);
        }
    }

    public function getAction()
    {
        $id = $this->_request->getParam('id');
        try{
            $activities = new Application_Model_DbTable_Activities();
            $rows = $activities->fetchRow('id = '.$id);
            if(!empty($rows)){
                $this->getResponse()->setBody(Zend_Json::encode($rows));
                $this->getResponse()->setHttpResponseCode(200);
            }else{
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Request not valid.')));
                $this->getResponse()->setHttpResponseCode(400);
            }
        }catch (Zend_Exception $e){
            $this->getResponse()->setBody(Zend_Json::encode(array('message' => $e->getMessage())));
            $this->getResponse()->setHttpResponseCode(400);
        }
    }

    public function postAction()
    {
        try{
            $activities = new Application_Model_DbTable_Activities();
            $media = new Application_Model_DbTable_Media();

            $data = $this->_request->getParams();

            if(strtotime($data['starts_at']) > strtotime($data['ends_at'])){
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Start time must be before end date.')));
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            if($media->find($data['featured_image_id'])->count() == 0){
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => "Image with specified id doesn't exist.")));
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            $rows = $activities->insert(array(
                'title' => $data['title'],
                'description' => $data['description'],
                'starts_at' => !empty($data['starts_at']) ? $data['starts_at'] : null,
                'ends_at' => !empty($data['ends_at']) ? $data['ends_at'] : null,
                'featured_image_id' => !empty($data['featured_image_id']) ? $data['featured_image_id'] : null,
            ));

            if(!empty($rows)){
                $this->getResponse()->setBody(Zend_Json::encode(array('id' => $rows)));
                $this->getResponse()->setHttpResponseCode(200);
            }else{
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Request not valid.')));
                $this->getResponse()->setHttpResponseCode(400);
            }
        }catch (Zend_Exception $e){
            $this->getResponse()->setBody(Zend_Json::encode(array('message' => $e->getMessage())));
            $this->getResponse()->setHttpResponseCode(400);
        }
    }

    public function putAction()
    {
        try{
            $activities = new Application_Model_DbTable_Activities();
            $media = new Application_Model_DbTable_Media();
            $data = array();

            if(strtotime($this->_request->getParam('starts_at')) > strtotime($this->_request->getParam('ends_at'))){
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Start time must be before end date.')));
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            if(!empty($this->_request->getParam('title'))){
                $data['title'] = $this->_request->getParam('title');
            }

            if(!empty($this->_request->getParam('description'))){
                $data['description'] = $this->_request->getParam('description');
            }

            if(!empty($this->_request->getParam('starts_at'))){
                $data['starts_at'] = $this->_request->getParam('starts_at');
            }

            if(!empty($this->_request->getParam('ends_at'))){
                $data['ends_at'] = $this->_request->getParam('ends_at');
            }

            if(!empty($this->_request->getParam('featured_image_id'))){

                if($media->find($this->_request->getParam('featured_image_id'))->count() == 0){
                    $this->getResponse()->setBody(Zend_Json::encode(array('message' => "Image with specified id doesn't exist.")));
                    $this->getResponse()->setHttpResponseCode(400);
                    return;
                }
                $data['featured_image_id'] = $this->_request->getParam('featured_image_id');
            }

            if($activities->update($data, 'id =' . $this->_request->getParam('id'))){
                $this->getResponse()->setBody(Zend_Json::encode(array('id' => $this->_request->getParam('id'))));
                $this->getResponse()->setHttpResponseCode(200);
            }else{
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Request not valid.')));
                $this->getResponse()->setHttpResponseCode(400);
            }
        }catch (Zend_Exception $e){
            $this->getResponse()->setBody(Zend_Json::encode(array('message' => $e->getMessage())));
            $this->getResponse()->setHttpResponseCode(400);
        }
    }

    public function deleteAction()
    {
        try{
            $activities = new Application_Model_DbTable_Activities();
            if($activities->delete('id =' . $this->_request->getParam('id'))){
                $this->getResponse()->setBody(Zend_Json::encode(array('id' => $this->_request->getParam('id'))));
                $this->getResponse()->setHttpResponseCode(200);
            }else{
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => 'Request not valid.')));
                $this->getResponse()->setHttpResponseCode(400);
            }
        }catch (Zend_Exception $e){
            $this->getResponse()->setBody(Zend_Json::encode(array('message' => $e->getMessage())));
            $this->getResponse()->setHttpResponseCode(400);
        }
    }
}