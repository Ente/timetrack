<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddHolidaysScheme extends AbstractMigration
{
    public function change(): void
    {
        $this->table("holidays")
            ->addColumn('name', 'string', ['limit' => 256])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('holidays', 'text')
            ->create();
    }
}
