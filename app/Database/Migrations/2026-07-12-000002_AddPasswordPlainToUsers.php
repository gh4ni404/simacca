<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordPlainToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'password_plain' => [
                'type'   => 'VARCHAR',
                'constraint' => '255',
                'null'   => true,
                'after'  => 'password',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'password_plain');
    }
}
