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
                'stripe_plan_id' => env('STRIPE_PLAN_BASIC_ID', null),
                'price'          => 1000,
            ],
            [
                'id'             => 4,
                'title'          => 'Plus',
                'stripe_plan_id' => env('STRIPE_PLAN_PLUS_ID', null),
                'price'          => 2000,
            ],
            [
                'id'             => 5,
                'title'          => 'Premium',
                'stripe_plan_id' => env('STRIPE_PLAN_PREMIUM_ID', null),
                'price'          => 5000,
            ],
        ];

        Role::insert($roles);

    }
}
