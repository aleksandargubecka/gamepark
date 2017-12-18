<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    public function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($front, array(), array('rest'));
        $front->getRouter()->addRoute('rest', $restRoute);
        $sms = new Zend_Controller_Router_Route
        (
            "smsgateway/reservations/:phone/:smscenter/:text", array
            (
                "module"     => "smsgateway",
                "controller" => "reservations",
                "action"     => "index",
            )
        );
        $front->getRouter()->addRoute("smsgateway/reservations/:phone/:smscenter/:text", $sms);
    }

    protected function _initFrontController() {
        $front = Zend_Controller_Front::getInstance();
        $front->setControllerDirectory(array(
            'default'    => APPLICATION_PATH . '/controllers',
            'rest'       => APPLICATION_PATH . '/modules/rest/controllers',
            'smsgateway' => APPLICATION_PATH . '/modules/smsgateway/controllers',
        ));

        return $front;
    }


}
