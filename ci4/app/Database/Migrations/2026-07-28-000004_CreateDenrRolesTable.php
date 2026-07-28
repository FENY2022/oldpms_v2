<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDenrRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'role_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'office_level' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'comment' => 'CENRO, PENRO, REGIONAL, or ADMIN'],
            'role_name'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'description'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('role_id', true);
        $this->forge->createTable('denr_roles');
    }

    public function down()
    {
        $this->forge->dropTable('denr_roles');
    }
}
