<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($front, array(), array('rest'));
        $front->getRouter()->addRoute('rest', $restRoute);
    }

    protected function _initFrontController ( )
    {
        $front = Zend_Controller_Front::getInstance();
        $front->setControllerDirectory( array (
            'default' => APPLICATION_PATH . '/controllers',
            'rest' => APPLICATION_PATH . '/modules/rest/controllers'
        ));

        return $front;
    }


}
