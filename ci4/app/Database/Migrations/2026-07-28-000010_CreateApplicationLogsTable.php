<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'log_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'app_id'     => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'action'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'remarks'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('log_id', true);
        $this->forge->createTable('application_logs');
    }

    public function down()
    {
        $this->forge->dropTable('application_logs');
    }
}
