<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MigrateProjectsIDToPID extends AbstractMigration
{
    public function change(): void
    {
        if(!$this->table("projects_items")->hasColumn("pid")){
            $this->table("projects_items")->addColumn("pid", "integer", [
                "null" => true,
                "after" => "id"
            ])->update();
            $this->execute('UPDATE projects_items SET pid = id WHERE pid IS NULL');
        } else {
            echo "pid column already exists in projects_items table\n";
        }
    }
}
