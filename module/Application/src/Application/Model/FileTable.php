<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class FileTable
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * @param int $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getById($id)
    {
        if (!$file = $this->tableGateway->select(['id' => (int)$id])->current()) {
            throw new \Exception('File not found');
        }

        return $file;
    }

    /**
     * @param string $link
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getByLink($link)
    {
        if (!$file = $this->tableGateway->select(['link' => (string)$link])->current()) {
            throw new \Exception('File not found');
        }

        return $file;
    }

    public function save(File $file)
    {
        if (!$file->id) {
            $this->tableGateway->insert($file->toArray());
        } else {
            $this->tableGateway->update($file->toArray(), ['id' => $file->id]);
        }
    }
}
