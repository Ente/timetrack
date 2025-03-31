<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitUserScheme extends AbstractMigration
{
    public function change(): void
    {
        $exists = $this->hasTable('users');
        if ($exists) {
            echo "\nSkipping. Table already exists.\n";
            return;
        }
        
        $this->table("users")
            ->addColumn("name", "string", ["limit" => 256])
            ->addColumn("username", "string", ["limit" => 255])
            ->addColumn("email", "string", ["limit" => 256])
            ->addColumn("password", "string", ["limit" => 256])
            ->addColumn("email_confirmed", "boolean")
            ->addColumn("isAdmin", "string", ["limit" => 256])
            ->addColumn("state", "text", ["null" => true])
            ->addColumn("easymode", "boolean", ["null" => true])
            ->changePrimaryKey(["id"])
            ->create();
    }   
}
