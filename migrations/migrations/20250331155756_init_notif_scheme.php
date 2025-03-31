<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitNotifScheme extends AbstractMigration
{

    public function change(): void
    {
        $exists = $this->hasTable("kalender");
        if($exists){
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->table("kalender")
            ->addColumn("datum", "string", ["limit" => 255])
            ->addColumn("uhrzeit", "string", ["limit" => 255])
            ->addColumn("ort", "string", ["limit" => 255])
            ->addColumn("notiz", "string", ["limit" => 255])
            ->changePrimaryKey(["id"])
            ->create();
    }
}
