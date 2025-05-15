<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddWorktimeTypeRow extends AbstractMigration
{

    public function change(): void
    {
        if (!$this->table('arbeitszeiten')->hasColumn('Wtype')) {
            $this->table('arbeitszeiten')
            ->addColumn('Wtype', 'enum', [
                'values' => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                'default' => '0',
                'null' => false,
                'after' => 'pause_end'
            ])
            ->update();
            $this->execute('UPDATE arbeitszeiten SET Wtype = "0" WHERE type IS NULL');
            echo "Added Wtype column to arbeitszeiten table\n";
        } else {
            echo "Wtype column already exists in arbeitszeiten table\n";
        }
    }
}
