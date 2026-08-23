<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Expand guru.nip column to VARCHAR(50) to support long NIP / NUPTK / formatted strings
 */
class ExpandGuruNipToVarchar50 extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE guru MODIFY COLUMN nip VARCHAR(50) NOT NULL");
    }

    public function down()
    {
        // No action needed
    }
}
