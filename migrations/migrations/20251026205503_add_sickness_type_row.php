<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSicknessTypeRow extends AbstractMigration
{

    public function change(): void
    {
        if(!$this->table("sick")->hasColumn("Stype")){
            $this->table("sick")
            ->addColumn("Stype", "enum", [
                "values" => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"],
                "default" => "0",
                "null" => false,
                "after" => "status"
            ])
            ->update();
            $this->execute('UPDATE sick SET Stype = "0" WHERE Stype IS NULL');
            echo "Added Stype column to sick table\n";
        } else {
            echo "Stype column already exists in sick table\n";
        }
    }
}
