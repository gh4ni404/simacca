<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Profile Tracking Fields
 * 
 * Adds fields to track when users change their password, email, and profile photo
 * to enforce profile completion on first login.
 * 
 * Fields:
 * - password_changed_at: Track when password was last changed
 * - email_changed_at: Track when email was last set/changed
 * - profile_photo_uploaded_at: Track when profile photo was uploaded
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class AddProfileTrackingFields extends Migration
{
    public function up()
    {
        $fields = [
            'password_changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when password was last changed by user'
            ],
            'email_changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when email was last set/changed by user'
            ],
            'profile_photo_uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when profile photo was uploaded'
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['password_changed_at', 'email_changed_at', 'profile_photo_uploaded_at']);
    }
}
