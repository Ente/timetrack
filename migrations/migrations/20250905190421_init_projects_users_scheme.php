<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitProjectsUsersScheme extends AbstractMigration
{
    public function change(): void
    {
        $exists = $this->hasTable("projects_users");
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table("projects_users", ["id" => false, "primary_key" => "id"])
        ->addColumn("id", "integer", ["identity" => true])
        ->addColumn("userid", "integer", ["limit" => 255])
        ->addColumn("projectid", "integer", ["null" => true])
        ->addColumn("permissions", "integer")
        ->addColumn("role", "text")
        ->addColumn("is_owner", "boolean")
        ->create();
    }
}
