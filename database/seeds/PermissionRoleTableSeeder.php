<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = Permission::all();
        $roles       = Role::all();

        $roles->firstWhere('title', 'Admin')->permissions()->attach($permissions);

        $role = Role::where('title', 'Free Plan')->first()->permissions();
        $role->attach($permissions->firstWhere('title', 'project_access'), ['max_amount' => 1]);
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

        $role = Role::where('title', 'Basic')->first()->permissions();
        $role->attach($permissions->firstWhere('title', 'project_access'), ['max_amount' => 10]);
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

        $role = Role::where('title', 'Plus')->first()->permissions();
        $role->attach($permissions->firstWhere('title', 'project_access'), ['max_amount' => 20]);
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

        $role = Role::where('title', 'Premium')->first()->permissions();
        $role->attach($permissions->firstWhere('title', 'project_access'));
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

    }
}
