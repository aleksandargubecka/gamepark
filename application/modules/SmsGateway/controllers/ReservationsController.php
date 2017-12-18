<?php

class SmsGateway_ReservationsController extends Zend_Controller_Action {

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender(true);
    }

    public function indexAction()
    {
        $response = $this->switchAction();
        $this->getResponse()->setBody($response);
        $this->getResponse()->setHttpResponseCode(200);
        return;
    }

    private function switchAction() {

        $phone = $this->_request->getParam('phone');
        if(empty($phone))
            return 'Something went wrong!';

        $text = $this->_request->getParam('text');
        $textArray = explode('*', $text);
        if($text == 'info' || empty($textArray))
            return $this->getInfo();

        switch ($textArray[0]){
            case 'make':
                return $this->makeReservation($textArray);
                break;
            case 'cancel':
                return $this->cancelReservation($textArray);
                break;
            default:
                return $this->getInfo();
                break;
        }
    }

    private function getInfo() {
        return 'To make reservation, message text should be separated by * and put in order (action*Date (YYYY-MM-DD HH:MM)*Period*Name of celebrator*Age of celebrator*Organizer name*Organizer email) like in example: "make*2017-12-23 00:00:00*12-15*Name of celebrator*Age of celebrator*Organizer name*Organizer email". Allowed actions are "make" and "cancel" reservation.' . PHP_EOL .
            'To cancel reservation send message in order like in this example ""';
    }

    private function makeReservation($textArray) {

        if(!empty($textArray[1])){
            $dayInWeek = date('w', strtotime($textArray[1]));
            if($dayInWeek == 0 || $dayInWeek == 6){
                $data['date'] = $textArray[1];
            }else{
                return "Date must be saturday or sunday.";
            }
        }else{
            return "Date can't be empty. To see info how to properly write message send \"info\" in message.";
        }

        $periods = array('12-15', '15-18', '18-21');
        if(!empty($textArray[2])){
            if(in_array($textArray[2], $periods)){
                $data['period'] = $textArray[2];
            }else{
                return "Period must be one of these " . implode(', ', $periods) . ". To see info how to properly write message send \"info\" in message.";
            }
        }else{
            return "Period can't be empty. To see info how to properly write message send \"info\" in message.";
        }

        if(!empty($textArray[3])){
            $data['cName'] = $textArray[3];
        }

        if(!empty($textArray[4])){
            $data['cAge'] = $textArray[4];
        }

        if(!empty($textArray[4])){
            $data['cAge'] = $textArray[4];
        }

        if(!empty($textArray[5])){
            $data['oName'] = $textArray[5];
        }else{
            return "Organizer name can't be empty. To see info how to properly write message send \"info\" in message.";
        }

        if(!empty($textArray[6])){
            $data['oEmail'] = $textArray[6];
        }else{
            return "Organizer email can't be empty. To see info how to properly write message send \"info\" in message.";
        }

        $data['oTelephone'] = $this->_request->getParam('phone');

        try {
            $reservations = new Application_Model_DbTable_Reservations();

            if($reservations->exists($data['date'], $data['period'])){
                return "Already taken.";
            }

            $reservations->insert($data);
            return 'Successfully reserved!';
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }

    private function cancelReservation($textArray) {

        if(!empty($textArray[1])){
            $data['date'] = $textArray[1];
        }else{
            return "Date can't be empty. To see info how to properly write message send \"info\" in message.";
        }

        $periods = array('12-15', '15-18', '18-21');
        if(!empty($textArray[2])){
            if(in_array($textArray[2], $periods)){
                $data['period'] = $textArray[2];
            }else{
                return "Period must be one of these " . implode(', ', $periods) . ". To see info how to properly write message send \"info\" in message.";
            }
        }else{
            return "Period can't be empty. To see info how to properly write message send \"info\" in message.";
        }

        try {
            $reservations = new Application_Model_DbTable_Reservations();

            if(!$reservations->exists($data['date'], $data['period'])){
                return "This period is not reserved yet.";
            }

            $id = $reservations->getReserved($data['date'], $data['period'], $this->_request->getParam('phone'));
            if(empty($id))
                return 'Note that period must be canceled from same phone that reservation was made.';

            $reservations->delete('id = ' . $id);
            return 'Successfully deleted!';
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }

}