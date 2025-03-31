<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitSickScheme extends AbstractMigration
{

    public function change(): void
    {
        $exists = $this->hasTable('sick');
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");
        $this->table("sick")
            ->addColumn("username", "string", ["limit" => 255])
            ->addColumn("start", "text", ["null" => false])
            ->addColumn("stop", "text", ["null" => false])
            ->addColumn("status", "text", ["null" => false])
            ->changePrimaryKey(["id"])
            ->create();
    }
}
