<?php

class Application_Model_DbTable_Activities extends Zend_Db_Table_Abstract {

    protected $_name = 'activities';

    protected $_dependentTables = array('Application_Model_DbTable_Galleries');

    public function search($search) {
        $select = $this->select();
        $select->where('title LIKE ?', '%' . $search . '%');
        $select->orWhere('description LIKE ?', '%' . $search . '%');

        return $this->fetchAll($select);
    }
}

