<?php

class Application_Model_DbTable_Reservations extends Zend_Db_Table_Abstract
{

    protected $_name = 'reservations';


    public function getSaturdaysAndSundays() {

        $currentDate = time();
        $monthFromNow = strtotime('+1 months', $currentDate);;

        $saturdaysAndSundays = array();
        $j = 1;
        for($i = $currentDate; $i <= $monthFromNow; $i = $i + 86400) {
            $dayInWeek = date("w",$i);
            if($dayInWeek == 0 || $dayInWeek == 6) {
                $saturdaysAndSundays[date("Y-m-d", $i)] = date("l", $i) . ' ' . date("Y-m-d", $i);
            }
            if($dayInWeek == 0) {
                $j++;
            }
        }

        return $saturdaysAndSundays;
    }

    public function getPeriods() {
        return array(
            '12-15' => '12-15h',
            '15-18' => '15-18h',
            '18-21' => '18-21h',
        );
    }

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

}

