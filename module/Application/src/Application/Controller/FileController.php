<?php

namespace Application\Controller;

use Application\Form\FileForm;
use Application\Model\File;
use Application\Model\FileTable;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FileController extends AbstractActionController
{
    /**
     * @var FileTable
     */
    protected $fileTable;

    public function addAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('/');
        }

        $postData = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );
        $form = new FileForm();

        $form->setData($postData);
        if ($form->isValid()) {
            $data = $form->getData();
        } else {
            die(var_dump($form->getData()));
        }

        $fileName = sha1_file($data['upload']['tmp_name']);
        $dstName = PROJECT_ROOT . 'upload/' . $fileName;
        move_uploaded_file($data['upload']['tmp_name'], $dstName);

        $file = new File();
        $file->name = $data['upload']['name'];
        $file->path = $dstName;

        if (!empty($postData['passwd'])) {
            $file->password = password_hash($postData['passwd'], PASSWORD_DEFAULT);
        }

        $file->link = substr($fileName, 0, 7);

        $this->getFileTable()->save($file);

        return new ViewModel(['link' => $file->link]);
    }

    public function getAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request->isGet()) {
            return $this->redirect()->toRoute('/');
        }

        $queryParams = $request->getQuery()->toArray();

        $link = $this->params()->fromRoute('link');

        /** @var File $file */
        if (!$file = $this->getFileTable()->getByLink($link)) {
            return $this->notFoundAction();
        }

        if (!file_exists($file->path)) {
            return $this->notFoundAction();
        }

        if ($file->hasPassword() && (empty($queryParams['passwd']) || !password_verify($queryParams['passwd'], $file->password))) {
            return new ViewModel(['password' => true]);
        }

        $file->downloads++;
        $this->getFileTable()->save($file);

        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream', true);
        header('Content-Disposition: attachment; filename=' . $file->name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file->path));

        readfile($file->path);
        exit();
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
