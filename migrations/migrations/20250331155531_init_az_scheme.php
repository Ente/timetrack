<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class InitAzScheme extends AbstractMigration
{
    public function change(): void
    {
        $exists = $this->hasTable('arbeitszeiten');
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table('arbeitszeiten')
            ->addColumn('name', 'string', ['limit' => 256])
            ->addColumn('email', 'string', ['limit' => 256])
            ->addColumn('schicht_tag', 'string', ['limit' => 256])
            ->addColumn('schicht_anfang', 'string', ['limit' => 256])
            ->addColumn('schicht_ende', 'string', ['limit' => 256])
            ->addColumn('username', 'string', ['limit' => 255])
            ->addColumn('ort', 'text', ['null' => true])
            ->addColumn('active', 'boolean', ['null' => true])
            ->addColumn('review', 'boolean', ['null' => true])
            ->addColumn('type', 'string', ['limit' => 11, 'null' => true])
            ->addColumn('pause_start', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('pause_end', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('attachements', 'text', ['null' => true])
            ->addColumn('project', 'string', ['limit' => 255, 'null' => true])
            ->create();

    }
}