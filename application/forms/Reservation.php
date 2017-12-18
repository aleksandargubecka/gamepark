<?php
class Application_Form_Reservation extends Zend_Form {

    public function init() {

        $date = $this->createElement('select', 'date');
        $date->setLabel('Date: *')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addMultiOptions($this->getSaturdaysAndSundays());

        $period = $this->createElement('radio', 'period');
        $period->setLabel('Period: *')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addMultiOptions($this->getPeriods());

        $cName = $this->createElement('text', 'cName');
        $cName->setLabel('Name of the celebrator:')
            ->setRequired(false);

        $cAge = $this->createElement('text', 'cAge');
        $cAge->setLabel('Age of the celebrator:')
            ->setRequired(false);

        $oName = $this->createElement('text', 'oName');
        $oName->setLabel('Name of the organizer: *')
            ->addValidator('NotEmpty', true)
            ->setRequired(true);

        $oTelephone = $this->createElement('text', 'oTelephone');
        $oTelephone->setLabel('Telephone: *')
            ->addValidator('NotEmpty', true)
            ->setRequired(true);


        $email = $this->createElement('text', 'oEmail');
        $email->setLabel('Email: *')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress');

        $submit = $this->createElement('submit', 'reserve');
        $submit->setLabel('Reserve')
            ->setIgnore(true);


        $this->addElements(array(
            $date,
            $period,
            $cName,
            $cAge,
            $oName,
            $oTelephone,
            $email,
            $submit,
        ));
    }

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
}

