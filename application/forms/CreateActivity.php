<?php

class Application_Form_CreateActivity extends Zend_Form {

    public function init() {
        $title = $this->createElement('text', 'title');
        $title->setLabel('Title:')->setRequired(true);

        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description:');

        $workingDays = $this->getWorkingDays();
        $workingHours = $this->getWorkingHours();
        $starts_at = $this->createElement('select', 'starts_at');
        $starts_at->setLabel('Starts at:')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->setRegisterInArrayValidator(false)
            ->addMultiOptions($workingDays);

        $starts_at_hour = $this->createElement('select', 'starts_at_hour');
        $starts_at_hour->setLabel('Starts at hour:')
            ->setRequired(true)
            ->setRegisterInArrayValidator(false)
            ->addValidator('NotEmpty', true)
            ->addMultiOptions($workingHours);

        $ends_at = $this->createElement('select', 'ends_at');
        $ends_at->setLabel('Ends at:')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->setRegisterInArrayValidator(false)
            ->addMultiOptions($workingDays);

        $ends_at_hour = $this->createElement('select', 'ends_at_hour');
        $ends_at_hour->setLabel('Ends at hour:')
            ->setRequired(true)
            ->setRegisterInArrayValidator(false)
            ->addValidator('NotEmpty', true)
            ->addMultiOptions($workingHours);

        $featured_image = new Zend_Form_Element_File('featured_image');
        $featured_image->setLabel('Upload an image:')
            ->setDestination(APPLICATION_UPLOADS_DIR)
            ->addValidator('Size', false, 1000000)
            ->addValidator('Extension', false, 'jpg,png,gif');

        $gallery = new Zend_Form_Element_File('gallery');
        $gallery->setLabel('Gallery images:')
            ->setDestination(APPLICATION_UPLOADS_DIR)
            ->setIsArray(true)
            ->setAttrib('multiple', true)
            ->addValidator('Size', false, 10000000)
            ->addValidator('Extension', false, 'jpg,png,gif');

        $submit = $this->createElement('submit', 'save');
        $submit->setLabel('Save')->setIgnore(true);

        $this->addElements(array(
            $title,
            $description,
            $starts_at,
            $starts_at_hour,
            $ends_at,
            $ends_at_hour,
            $featured_image,
            $gallery,
            $submit,
        ));
    }


    public function getWorkingDays() {

        $currentDate = time();
        $monthFromNow = strtotime('+2 months', $currentDate);;

        $workingDays = array();
        $j = 1;
        for ($i = $currentDate; $i <= $monthFromNow; $i = $i + 86400) {
            $dayInWeek = date("w", $i);
            if ($dayInWeek != 0 && $dayInWeek != 6) {
                $day = strtotime(date("Y-m-d",  $i));
                $workingDays[$day] = date("l", $i) . ' ' . date("Y-m-d", $i);
            }
            if ($dayInWeek == 0) {
                $j++;
            }
        }

        return $workingDays;
    }

    public function getWorkingHours() {
        $hours = array();
        for ($i = 9; $i < 22; $i++){
            $hours[$i] = $i . ' : 00' ;
        }
        return $hours;

    }
}

