<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitSchichtScheme extends AbstractMigration
{

    public function change(): void
    {
        $exists = $this->hasTable('schicht');
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->table('schicht')
            ->addColumn('name', 'string', ['limit' => 256])
            ->addColumn('email', 'string', ['limit' => 256])
            ->addColumn('schicht_gestartet_zeit', 'string', ['limit' => 256, 'null' => true])
            ->addColumn('schicht_ende_zeit', 'string', ['limit' => 256, 'null' => true])
            ->addColumn('schicht_datum', 'string', ['limit' => 256, 'null' => true])
            ->changePrimaryKey(['id'])
            ->create();
    }
}
