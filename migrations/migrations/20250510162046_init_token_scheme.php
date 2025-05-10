<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitTokenScheme extends AbstractMigration
{
    
    public function change(): void
    {
        if (!$this->hasTable("tokens")) {
            $table = $this->table('tokens', ['id' => false, 'primary_key' => 'id']);
            $table->addColumn('id', 'integer', ['identity' => true])
                ->addColumn('user_id', 'integer')
                ->addColumn('access_token', 'string', ['limit' => 255])
                ->addColumn('refresh_token', 'string', ['limit' => 255])
                ->addColumn('expires_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->create();
        } else {
            echo "Table tokens already exists\n";
        }
    }
}
