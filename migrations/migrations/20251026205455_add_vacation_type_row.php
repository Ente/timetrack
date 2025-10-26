<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddVacationTypeRow extends AbstractMigration
{
    public function change(): void
    {
        if(!$this->table("vacation")->hasColumn("Vtype")){
            $this->table("vacation")
            ->addColumn("Vtype", "enum", [
                "values" => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"],
                "default" => "0",
                "null" => false,
                "after" => "status"
            ])
            ->update();
            $this->execute('UPDATE vacation SET Vtype = "0" WHERE Vtype IS NULL');
            echo "Added Vtype column to vacation table\n";
        } else {
            echo "Vtype column already exists in vacation table\n";
        }
    }
}
