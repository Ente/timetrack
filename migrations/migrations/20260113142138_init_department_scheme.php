<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitDepartmentScheme extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table('departments')
            ->addColumn('name', 'string', ['limit' => 256])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('manager_user_id', 'integer', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('active', 'boolean', ['default' => true])
            ->create();
    }
}
