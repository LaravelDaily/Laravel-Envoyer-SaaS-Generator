<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'             => 1,
                'title'          => 'Admin',
                'stripe_plan_id' => null,
                'price'          => null,
            ],
            [
                'id'             => 2,
                'title'          => 'Free Plan',
                'stripe_plan_id' => null,
                'price'          => null,
            ],
            [
                'id'             => 3,
                'title'          => 'Basic',
                'stripe_plan_id' => 'plan_xxxxxxxxxxxxxx',
                'price'          => 1000,
            ],
            [
                'id'             => 4,
                'title'          => 'Plus',
                'stripe_plan_id' => 'plan_xxxxxxxxxxxxxx',
                'price'          => 2000,
            ],
            [
                'id'             => 5,
                'title'          => 'Premium',
                'stripe_plan_id' => 'plan_xxxxxxxxxxxxxx',
                'price'          => 5000,
            ],
        ];

        Role::insert($roles);

    }
}
