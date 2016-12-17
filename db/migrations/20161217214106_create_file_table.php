<?php

use Phinx\Migration\AbstractMigration;

class CreateFileTable extends AbstractMigration
{
    public function change()
    {
        $this->table('file')
            ->addColumn('name', 'string')
            ->addColumn('path', 'string')
            ->addColumn('link', 'string')
            ->addColumn('password', 'string', ['null' => true])
            ->addColumn('downloads', 'biginteger', ['default' => 0])
            ->addColumn('created_at', 'integer')
            ->addColumn('deleted_at', 'integer', ['null' => true])
            ->addIndex(['link'], ['unique' => true])
            ->addIndex(['created_at'])
            ->addIndex(['deleted_at'])
            ->create()
        ;
    }
}
