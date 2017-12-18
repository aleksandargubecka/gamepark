<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->pageTitle = "Home";
        $activities = new Application_Model_DbTable_Activities();
        $slides = $activities->fetchAll('featured_image_id IS NOT NULL', 'starts_at DESC', 3);
        $slidesArr = array();
        $i = 0;
        foreach ($slides as $slide) {
            $slidesArr[$i]['slide'] = $slide;
            $slidesArr[$i]['image'] = $slide->findDependentRowset('Application_Model_DbTable_Media');
            $i++;
        }
        $this->view->slides = $slidesArr;
        $this->view->activities = $activities->fetchAll(null, 'starts_at DESC', 6, 3);
    }



}



