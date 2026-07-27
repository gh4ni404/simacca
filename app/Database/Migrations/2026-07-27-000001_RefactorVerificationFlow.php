<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorVerificationFlow extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Add revision_requested_by column
        $this->forge->addColumn('pkl_progress', [
            'revision_requested_by' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'status'],
        ]);

        // 2. Rename existing status data: 'verified_by_instruktur' → 'verified'
        $db->table('pkl_progress')
            ->where('status', 'verified_by_instruktur')
            ->update(['status' => 'verified']);

        // 3. Shorten status column (verified is shorter than verified_by_instruktur)
        $this->forge->modifyColumn('pkl_progress', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
        ]);
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Revert status data
        $db->table('pkl_progress')
            ->where('status', 'verified')
            ->update(['status' => 'verified_by_instruktur']);

        // Drop column
        $this->forge->dropColumn('pkl_progress', ['revision_requested_by']);

        // Restore column length
        $this->forge->modifyColumn('pkl_progress', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'draft'],
        ]);
    }
}
