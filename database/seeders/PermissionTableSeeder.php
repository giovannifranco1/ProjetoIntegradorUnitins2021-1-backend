<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    private static $permissions = array(
        array("name" => "gerenciar_visita", "guard_name" => "api"),
        array("name" => "gerenciar_cooperado", "guard_name" => "api"),
        array("name" => "gerenciar_tecnico", "guard_name" => "api"),
        array("name" => "gerenciar_grupos", "guard_name" => "api"),
        array("name" => "gerenciar_motivos", "guard_name" => "api"),
        array("name" => "gerar_relatorios", "guard_name" => "api"),
        array("name" => "gerenciar_propriedade", "guard_name" => "api")
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
