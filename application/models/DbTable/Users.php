<?php

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';
    protected $_referenceMap    = array(
        'CommentsUser' => array(
            'columns'           => 'id',
            'refTableClass'     => 'Application_Model_DbTable_Comments',
            'refColumns'        => 'user_id'
        ),
    );
    public function isUnique($username)
    {
        $select = $this->_db->select()
            ->from($this->_name,array('username'))
            ->where('username=?',$username);

        $result = $this->getAdapter()->fetchOne($select);

        if(!$result){
            return true;
        }
        return false;
    }

}

