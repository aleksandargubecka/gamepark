<?php

class Rest_ReservationsController extends Zend_Rest_Controller{

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
            $reservations = new Application_Model_DbTable_Reservations();
            $rows = $reservations->fetchAll();
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
            $reservations = new Application_Model_DbTable_Reservations();
            $rows = $reservations->fetchRow('id = '.$id);
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
            $reservations = new Application_Model_DbTable_Reservations();

            $data = $this->_request->getParams();

            if($reservations->exists($data['date'], $data['period'])){
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => "Already taken.")));
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            $rows = $reservations->insert(array(
                'date' => $data['date'],
                'period' => $data['period'],
                'cName' => !empty($data['cName']) ? $data['cName'] : null,
                'cAge' => !empty($data['cAge']) ? $data['cAge'] : null,
                'oName' => $data['oName'],
                'oTelephone' => $data['oTelephone'],
                'oEmail' => $data['oEmail'],
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
            $reservations = new Application_Model_DbTable_Reservations();
            $data = array();

            if(!empty($this->_request->getParam('date')) && !empty($this->_request->getParam('period')) && $reservations->exists($this->_request->getParam('date'), $this->_request->getParam('period'))){
                $this->getResponse()->setBody(Zend_Json::encode(array('message' => "Already taken.")));
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            if(!empty($this->_request->getParam('date'))){
                $data['date'] = $this->_request->getParam('date');
            }

            if(!empty($this->_request->getParam('period'))){
                $data['period'] = $this->_request->getParam('period');
            }

            if(!empty($this->_request->getParam('cName'))){
                $data['cName'] = $this->_request->getParam('cName');
            }

            if(!empty($this->_request->getParam('cAge'))){
                $data['cAge'] = $this->_request->getParam('cAge');
            }

            if(!empty($this->_request->getParam('oName'))){
                $data['oName'] = $this->_request->getParam('oName');
            }

            if(!empty($this->_request->getParam('oEmail'))){
                $data['oEmail'] = $this->_request->getParam('oEmail');
            }

            if($reservations->update($data, 'id = ' . $this->_request->getParam('id'))){
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
            $reservations = new Application_Model_DbTable_Reservations();
            if($reservations->delete('id =' . $this->_request->getParam('id'))){
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