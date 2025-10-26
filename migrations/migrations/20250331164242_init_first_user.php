<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitFirstUser extends AbstractMigration
{
    public function change(): void
    {
        if ($this->hasTable("users")) {

            $data = [
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'email_confirmed' => true,
                'isAdmin' => true,
                'state' => null,
                'easymode' => false,
                "active" => 1
            ];

            $users = $this->fetchRow("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
            if ($users['count'] == 0) {
                $this->table('users')->insert($data)->saveData();
            } else {
                echo "User count > 0, not inserting default user\n";
            }

        } else {
            echo "Table users does not exist\n";
        }
    }
}
