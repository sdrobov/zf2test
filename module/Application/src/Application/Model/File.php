<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class File
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $link;

    /**
     * @var string
     */
    public $password;

    /**
     * @var int
     */
    public $downloads;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $deletedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->path = !empty($data['path']) ? $data['path'] : null;
        $this->link = !empty($data['link']) ? $data['link'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->downloads = !empty($data['downloads']) ? $data['downloads'] : null;
        $this->createdAt = !empty($data['created_at']) ? new \DateTime($data['created_at']) : null;
        $this->deletedAt = !empty($data['deleted_at']) ? new \DateTime($data['deleted_at']) : null;
    }

    /**
     * @return bool
     */
    public function hasPassword()
    {
        return (bool)$this->password;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return !$this->deletedAt;
    }

    public function delete()
    {
        if ($this->isDeleted()) {
            return;
        }

        $this->deletedAt = new \DateTime();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        if ($this->id) {
            $result['id'] = $this->id;
        }
        if ($this->name) {
            $result['name'] = $this->name;
        }
        if ($this->path) {
            $result['path'] = $this->path;
        }
        if ($this->link) {
            $result['link'] = $this->link;
        }
        if ($this->password) {
            $result['password'] = $this->password;
        }
        if ($this->downloads) {
            $result['downloads'] = $this->downloads;
        }
        if ($this->createdAt) {
            $result['created_at'] = $this->createdAt->getTimestamp();
        }
        if ($this->deletedAt) {
            $result['deleted_at'] = $this->deletedAt->getTimestamp();
        }

        return $result;
    }
}
