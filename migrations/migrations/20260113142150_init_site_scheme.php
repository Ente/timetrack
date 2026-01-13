<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitSiteScheme extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table('sites') # Location sites are meant here
            ->addColumn('name', 'string', ['limit' => 256])
            ->addColumn('address', 'text', ['null' => true])
            ->addColumn('city', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('country', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('postal_code', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('hr_contacts', 'text', ['null' => true])
            ->addColumn('branch_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('active', 'boolean', ['default' => true])
            ->create();
    }
}
