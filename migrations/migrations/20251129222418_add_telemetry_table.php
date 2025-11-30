<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTelemetryTable extends AbstractMigration
{
    public function change(): void
    {
        if (!$this->hasTable('telemetry')) {
            $table = $this->table('telemetry');
            $table->addColumn('instance_uuid', 'string', ['limit' => 36])
                  ->addColumn("api_calls_total", "integer", ["default" => 0])
                  ->create();
        }
    }
}
