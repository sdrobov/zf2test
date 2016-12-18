<?php

namespace Application\Controller;

use Application\Model\FileTable;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    /**
     * @var FileTable
     */
    protected $fileTable;

    public function indexAction()
    {
        return new ViewModel(['files' => $this->getFileTable()->fetchAll()]);
    }

    /**
     * @return FileTable
     */
    public function getFileTable()
    {
        if (!$this->fileTable) {
            $serviceLocator = $this->getServiceLocator();
            $this->fileTable = $serviceLocator->get('Application\Model\FileTable');
        }

        return $this->fileTable;
    }
}
