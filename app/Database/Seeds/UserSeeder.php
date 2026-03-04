<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Users data
        $users = [
            [
                'name'    => 'aslainie maruhom',
                'email'       => 'aslainie@gmail.com',
                'password'    => password_hash('aslainie', PASSWORD_DEFAULT),
                'role'        => 'admin',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'    => 'norami marohombsar',
                'email'       => 'norami@gmail.com',
                'password'    => password_hash('norami', PASSWORD_DEFAULT),
                'role'        => 'teacher',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'    => 'baikan lampac',
                'email'       => 'baikan@gmail.com',
                'password'    => password_hash('baikan', PASSWORD_DEFAULT),
                'role'        => 'student',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'    => 'amarah tador',
                'email'       => 'amarah@gmail.com',
                'password'    => password_hash('amarah', PASSWORD_DEFAULT),
                'role'        => 'teacher',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'    => 'anisah lampac',
                'email'       => 'anisah@gmail.com',
                'password'    => password_hash('anisah', PASSWORD_DEFAULT),
                'role'        => 'student',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'    => 'fatima librarian',
                'email'       => 'fatima@gmail.com',
                'password'    => password_hash('fatima', PASSWORD_DEFAULT),
                'role'        => 'librarian',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],

        ];

        $this->db->table('users')->insertBatch($users);
    }
}