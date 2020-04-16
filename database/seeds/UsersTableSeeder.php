<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$1g1u0eStm.kthizHN.Kmaet7aO1KFNvsYnL1vFSxqIdMXEsdg11Gu',
                'remember_token' => null,
            ],
        ];

        User::insert($users);

    }
}
