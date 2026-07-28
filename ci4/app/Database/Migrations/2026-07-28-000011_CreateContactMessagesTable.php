<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'subject'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'message'    => ['type' => 'TEXT', 'null' => false],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('contact_messages');
    }

    public function down()
    {
        $this->forge->dropTable('contact_messages');
    }
}
