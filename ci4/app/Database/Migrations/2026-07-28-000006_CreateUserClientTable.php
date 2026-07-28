<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserClientTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'client_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'firstname'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'mid_name'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'lastname'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'email'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'profile_picture'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'verification_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'password'           => ['type' => 'TEXT', 'null' => false],
            'mobilenum'          => ['type' => 'TEXT', 'null' => false],
            'comp_id_upload'     => ['type' => 'LONGTEXT', 'null' => false],
            'govt_id_upload'     => ['type' => 'LONGTEXT', 'null' => false],
            'auth_letter'        => ['type' => 'LONGTEXT', 'null' => false],
            'password_unhashed'  => ['type' => 'TEXT', 'null' => false],
            'Status'             => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'province'           => ['type' => 'TEXT', 'null' => false],
            'citymun'            => ['type' => 'TEXT', 'null' => false],
            'brgy'               => ['type' => 'TEXT', 'null' => false],
            'zips'               => ['type' => 'TEXT', 'null' => false],
        ]);
        $this->forge->addKey('client_id', true);
        $this->forge->createTable('user_client');
    }

    public function down()
    {
        $this->forge->dropTable('user_client');
    }
}
