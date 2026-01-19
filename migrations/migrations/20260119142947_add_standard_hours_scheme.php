<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddStandardHoursScheme extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table('standardHours')
            ->addColumn('groupName', 'string', ['limit' => 256])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('workingHours', 'float', ['default' => 40.00])
            ->addColumn('breakPerShift', 'float', ['default' => 1.00, 'null' => true])
            ->addColumn('holidaysGroup', 'integer', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->create();
    }
}
