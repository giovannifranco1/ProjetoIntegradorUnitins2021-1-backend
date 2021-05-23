<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = User::create([
          'name' => 'admin',
          'email' => 'admin@coapasimov.com',
          'email_verified_at' => now(),
          'password' =>  bcrypt('admin123'),
          'created_at' => now(),
          'updated_at' => now()
      ]);
      $user->assignRole('Administração');
    }
}
