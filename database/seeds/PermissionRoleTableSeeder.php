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

        $planPermissions = $planPermissions = $permissions->filter(function ($permission) {
            return substr($permission->title, 0, 8) == 'project_'
                && $permission->title != 'project_create';
        });

        $role = Role::where('title', 'Free Plan')->first()->permissions();
        $role->attach($planPermissions);
        $role->attach($permissions->firstWhere('title', 'project_create'), ['max_amount' => 1]);
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

        $role = Role::where('title', 'Basic')->first()->permissions();
        $role->attach($planPermissions);
        $role->attach($permissions->firstWhere('title', 'project_create'), ['max_amount' => 10]);
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

        $role = Role::where('title', 'Plus')->first()->permissions();
        $role->attach($planPermissions);
        $role->attach($permissions->firstWhere('title', 'project_create'), ['max_amount' => 20]);
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

        $role = Role::where('title', 'Premium')->first()->permissions();
        $role->attach($planPermissions);
        $role->attach($permissions->firstWhere('title', 'project_create'));
        $role->attach($permissions->firstWhere('title', 'profile_password_edit'));

    }
}
