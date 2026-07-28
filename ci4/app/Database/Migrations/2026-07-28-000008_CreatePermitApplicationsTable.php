<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermitApplicationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'app_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id'         => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'app_type'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'applicant_type'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'business_name'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'tin_number'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'reference_number'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'province_id'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'muncity_id'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'brgy_id'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'zip_code'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'street_address'    => ['type' => 'TEXT', 'null' => true],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'Pending Review'],
            'date_submitted'    => ['type' => 'DATETIME', 'null' => true, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('app_id', true);
        $this->forge->createTable('permit_applications');
    }

    public function down()
    {
        $this->forge->dropTable('permit_applications');
    }
}
