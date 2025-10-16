<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitProjectsScheme extends AbstractMigration
{
    public function change(): void
    {
        $exists = $this->hasTable("projects");
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table("projects", ["id" => false, "primary_key" => "id"])
        ->addColumn("id", "integer", ["identity" => true])
        ->addColumn("name", "string", ["limit" => 255])
        ->addColumn("description", "text", ["null" => true])
        ->addColumn("members", "text", ["null" => true])
        ->addColumn("items_assoc", "string", ["limit" => 64])
        ->addColumn("deadline", "datetime", ["null" => true])
        ->addColumn("owner", "integer")
        ->create();
    }
}
