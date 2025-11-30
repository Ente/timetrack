<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTelemetryServerTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('telemetry_server');

        $table->addColumn('instance_uuid', 'string', ['limit' => 255])
              ->addColumn('time_track_version', 'string', ['limit' => 255])
              ->addColumn('api_version', 'string', ['limit' => 255])
              ->addColumn('php_version', 'string', ['default' => '', 'limit' => 255])
              ->addColumn('os_version_and_name', 'string', ['null' => true, 'default' => null])
              ->addColumn('total_plugins', 'integer', ['default' => 0])
              ->addColumn('total_users', 'integer', ['default' => 0])
              ->addColumn('total_worktimes', 'integer', ['default' => 0])
              ->addColumn('api_calls_total', 'integer', ['default' => 0])
              ->addColumn('created_at', 'datetime', [
                    'default' => 'CURRENT_TIMESTAMP'
              ])
              ->addColumn('updated_at', 'datetime', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'update'  => 'CURRENT_TIMESTAMP'
              ])

              ->create();
    }
}
