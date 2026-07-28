<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDenrUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'         => ['type' => 'INT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'username'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'password'        => ['type' => 'TEXT', 'null' => false],
            'usertype'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'contact_no'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'office_id'       => ['type' => 'INT', 'constraint' => 20, 'null' => false],
            'role_id'         => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'user_role_id'    => ['type' => 'TEXT', 'null' => false],
            'uploadSignature' => ['type' => 'TEXT', 'null' => false],
            'unhashPassword'  => ['type' => 'TEXT', 'null' => false],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->addKey('office_id', false, false, 'office');
        $this->forge->addKey('role_id', false, false, 'fk_user_role');
        $this->forge->addForeignKey('role_id', 'denr_roles', 'role_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('denr_users');
    }

    public function down()
    {
        $this->forge->dropTable('denr_users');
    }
}
