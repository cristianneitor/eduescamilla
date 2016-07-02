<?php

namespace Sipp\Form;

use Zend\Form\Form;

class EmployeeForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('employee');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'company_code',
            'type' => 'Text',
            'options' => array(
                'label' => 'Código de la empresa',
            ),
        ));
        $this->add(array(
            'name' => 'state',
            'type' => 'Text',
            'options' => array(
                'label' => 'Estado',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Enviar',
                'id' => 'submitbutton',
            ),
        ));
    }
}