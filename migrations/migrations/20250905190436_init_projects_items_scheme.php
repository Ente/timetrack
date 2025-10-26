<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitProjectsItemsScheme extends AbstractMigration
{
    public function change(): void
    {
        $exists = $this->hasTable("projects_items");
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table("projects_items", ["id" => true])
        ->addColumn("pid", "integer") // PROJECT ID
        ->addColumn("title", "string", ["limit" => 255])
        ->addColumn("description", "text", ["null" => true])
        ->addColumn("assignee", "integer") // USER ID
        ->addColumn("itemid", "integer")
        ->addColumn("deadline", "date")
        ->create();
    }
}
