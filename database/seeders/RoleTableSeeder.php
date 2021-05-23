<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $role = Role::firstOrCreate([
        'name' => 'Administração',
        'guard_name' => 'api']);
      $role->permissions()->sync(Permission::all());
    }
}
