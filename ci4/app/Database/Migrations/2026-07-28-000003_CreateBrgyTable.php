<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBrgyTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'brgy_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'brgy_name' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'mun_code'  => ['type' => 'VARCHAR', 'constraint' => 6, 'null' => false],
            'brgy_code' => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => false],
        ]);
        $this->forge->addKey('brgy_id', true);
        $this->forge->addKey('mun_code', false, false, 'municipality');
        $this->forge->addKey('brgy_code', false, false, 'brgy_code');
        $this->forge->addForeignKey('mun_code', 'muncity', 'mun_code');
        $this->forge->createTable('brgy');
    }

    public function down()
    {
        $this->forge->dropTable('brgy');
    }
}
