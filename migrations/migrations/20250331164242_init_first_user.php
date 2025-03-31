<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitFirstUser extends AbstractMigration
{
    public function change(): void
    {
        // create default user INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `email_confirmed`, `isAdmin`, `state`, `easymode`) VALUES (NULL, 'admin','admin', 'admin@admin.com', '$2y$10$5cmvKFvDl07C0QJJRMtG4OPSS56n.7p7VOw9UAHoIIGJTvvqp/HKG', 1, 1, NULL, 0);

        if ($this->hasTable("users")) {

            $data = [
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => '$2y$10$5cmvKFvDl07C0QJJRMtG4OPSS56n.7p7VOw9UAHoIIGJTvvqp/HKG',
                'email_confirmed' => true,
                'isAdmin' => true,
                'state' => null,
                'easymode' => false,
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
