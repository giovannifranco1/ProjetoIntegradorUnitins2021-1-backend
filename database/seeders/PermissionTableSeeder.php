<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    private static $permissions = array(
        array("name" => "gerenciar-visita", "guard_name" => "web"),
        array("name" => "gerenciar-cooperado", "guard_name" => "web"),
        array("name" => "gerenciar-tecnico", "guard_name" => "web"),
        array("name" => "gerenciar-grupos", "guard_name" => "web"),
        array("name" => "gerenciar-motivos", "guard_name" => "web"),
        array("name" => "gerar-relatorios", "guard_name" => "web"),
        array("name" => "gerenciar-propriedade", "guard_name" => "web")
    );

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(self::$permissions as $permission){
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name']
            ]);
        }
    }
}
