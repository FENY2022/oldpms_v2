<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRequirementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'requirement_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'download_link'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'new_app_status'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'renewal_app_status' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'sequence'           => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('requirements');
    }

    public function down()
    {
        $this->forge->dropTable('requirements');
    }
}
