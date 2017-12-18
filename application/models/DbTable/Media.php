<?php

class Application_Model_DbTable_Media extends Zend_Db_Table_Abstract
{

    protected $_name = 'media';

    protected $_dependentTables = array('Application_Model_DbTable_Galleries');

    protected $_referenceMap    = array(
        'MediaActivities' => array(
            'columns'           => 'id',
            'refTableClass'     => 'Application_Model_DbTable_Activities',
            'refColumns'        => 'featured_image_id'
        ),
    );

}

