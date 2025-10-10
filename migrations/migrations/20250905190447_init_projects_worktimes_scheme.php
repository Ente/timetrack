<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitProjectsWorktimesScheme extends AbstractMigration
{
    public function change(): void
    {
        $exists = $this->hasTable("projects_worktimes");
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }

        $this->execute("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");
        $this->execute("SET time_zone = '+00:00';");

        $this->table("projects_worktimes", ["id" => false])
        ->addColumn("itemid", "integer")
        ->addColumn("worktimeid", "integer")
        ->addColumn("user", "integer")
        ->create();
    }
}
