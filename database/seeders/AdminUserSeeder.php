<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            
            $persona = Persona::create([
                'nombre'     => 'Admin',
                'apellido_p' => 'Sistema',
                'apellido_m' => 'General',
                'correo'     => 'admin_sistema@gmail.com', 
                'celular'    => '123456789',
            ]);

            $id = $persona->id_persona ?? $persona->id;

            Usuario::create([
                'id_persona' => $id, 
                'pass'       => Hash::make('admin123'), 
                'admin'      => true,
                'email_verified_at' => now(),
            ]);
        });
    }
}