<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProvinceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'prov_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'prov_name' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'reg_code'  => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => false],
            'prov_code' => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => false],
            'Suffix'    => ['type' => 'TEXT', 'null' => false],
        ]);
        $this->forge->addKey('prov_id', true);
        $this->forge->addKey('reg_code', false, false, 'province');
        $this->forge->addKey('prov_code', false, false, 'prov_code');
        $this->forge->createTable('province');
    }

    public function down()
    {
        $this->forge->dropTable('province');
    }
}
