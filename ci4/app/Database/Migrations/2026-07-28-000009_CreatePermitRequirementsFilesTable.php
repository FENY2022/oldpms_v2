<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermitRequirementsFilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'file_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'app_id'          => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'requirement_id'  => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'file_path'       => ['type' => 'TEXT', 'null' => false],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'Pending'],
            'remarks'         => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('file_id', true);
        $this->forge->createTable('permit_requirements_files');
    }

    public function down()
    {
        $this->forge->dropTable('permit_requirements_files');
    }
}
