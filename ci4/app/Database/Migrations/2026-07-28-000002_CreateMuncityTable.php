<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMuncityTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'muncity_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'muncity_name'=> ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'prov_code'   => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => false],
            'mun_code'    => ['type' => 'VARCHAR', 'constraint' => 6, 'null' => false],
            'zip_code'    => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => false],
            'office_id'   => ['type' => 'INT', 'constraint' => 20, 'null' => false],
            'office_cover'=> ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
        ]);
        $this->forge->addKey('muncity_id', true);
        $this->forge->addKey('prov_code', false, false, 'region');
        $this->forge->addKey('office_id', false, false, 'office');
        $this->forge->addKey('mun_code', false, false, 'municipality');
        $this->forge->createTable('muncity');
    }

    public function down()
    {
        $this->forge->dropTable('muncity');
    }
}
