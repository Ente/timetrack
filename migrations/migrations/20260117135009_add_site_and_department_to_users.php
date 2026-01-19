<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSiteAndDepartmentToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('site', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('department', 'string', ['limit' => 255, 'null' => true])
            ->update();
    }
}
