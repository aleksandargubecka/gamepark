<?php
class Application_Form_Reservation extends Zend_Form {

    public function init() {

        $reservations = new Application_Model_DbTable_Reservations();

        $date = $this->createElement('select', 'date');
        $date->setLabel('Date: *')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addMultiOptions($reservations->getSaturdaysAndSundays());

        $period = $this->createElement('radio', 'period');
        $period->setLabel('Period: *')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addMultiOptions($reservations->getPeriods());

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


}

