<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddActiveUsersColumn extends AbstractMigration
{
    public function change(): void
    {
        if(!$this->table('users')->hasColumn('active')) {
            $this->table('users')
                ->addColumn('active', 'boolean', [
                    'default' => true,
                    'null' => false,
                    'after' => 'password'
                ])
                ->update();
            $this->execute('UPDATE users SET active = 1 WHERE active IS NULL');
            echo "Added active column to users table\n";
        } else {
            echo "active column already exists in users table\n";
        }
    }
}
