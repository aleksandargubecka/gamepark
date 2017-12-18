<?php

class Application_Model_DbTable_Galleries extends Zend_Db_Table_Abstract {

    protected $_name = 'galleries';

    protected $_referenceMap = array(
        'Application_Model_DbTable_Media' => array(
            'columns'       => array('media_id'),
            'refTableClass' => 'Application_Model_DbTable_Media',
            'refColumns'    => array('id'),
        ),
        'Application_Model_DbTable_Activities' => array(
            'columns'       => array('activity_id'),
            'refTableClass' => 'Application_Model_DbTable_Activities',
            'refColumns'    => array('id'),
        ),
    );

}

