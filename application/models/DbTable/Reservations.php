<?php

class Application_Model_DbTable_Reservations extends Zend_Db_Table_Abstract
{

    protected $_name = 'reservations';

    public function exists($date, $period) {

        $select = $this->_db->select()
            ->from($this->_name,array('date', 'period'))
            ->where('date=?',$date)
            ->where('period=?',$period);

        $result = $this->getAdapter()->fetchOne($select);

        if($result){
            return true;
        }
        return false;
    }

    public function getReserved($date, $period, $telephone) {

        $select = $this->_db->select()
            ->from($this->_name,array('id'))
            ->where('date=?',$date)
            ->where('period=?',$period)
            ->where('oTelephone=?', $telephone);

       return $this->getAdapter()->fetchOne($select);
    }
}

