<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $user = Role::create(['name' => 'user']);

        // Create permissions
        $permissions = [
            'dashboard', 'profile.*', 'projects.*', 'boards.*', 'tasks.*', 'reports.*', 'admin.*'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign to roles
        $admin->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['projects.*', 'boards.*', 'tasks.*']);
        $user->givePermissionTo(['dashboard', 'profile.*']);

        // Assign to default user
        \App\Models\User::find(1)?->assignRole('admin');
    }
}
