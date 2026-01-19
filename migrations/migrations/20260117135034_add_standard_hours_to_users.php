<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddStandardHoursToUsers extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users')->addColumn('standardHours_group_id', 'integer', ['null' => true])->update();

    }
}
