<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class FileForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->addElements();
    }

    public function addElements()
    {
        $file = new Element\File('upload');
        $file->setLabel('upload');

        $password = new Element\Password('passwd');
        $password->setLabel('passwd');

        $this->add($file)->add($password);
    }
}
