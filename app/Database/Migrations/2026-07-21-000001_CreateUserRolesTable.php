<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'role']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('role');
        $this->forge->createTable('user_roles');

        // Seed existing users.role values into user_roles for backward compatibility
        $db = \Config\Database::connect();
        $users = $db->table('users')->select('id, role')->where('deleted_at IS NULL', null, false)->get()->getResultArray();

        $batch = [];
        foreach ($users as $user) {
            $batch[] = [
                'user_id'    => $user['id'],
                'role'       => $user['role'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($batch)) {
            $db->table('user_roles')->insertBatch($batch);
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
    }
}
