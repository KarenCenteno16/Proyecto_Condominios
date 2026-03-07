<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; 
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $usuario = Usuario::where('id_persona', $request->id_persona)->first();

        if ($usuario && Hash::check($request->pass, $usuario->pass)) {
            return response()->json([
                'res' => true,
                'usuario' => [
                    'id_persona' => $usuario->id_persona,
                    'admin' => $usuario->admin 
                ]
            ]);
        }

        return response()->json(['res' => false, 'mensaje' => 'Credenciales incorrectas'], 401);
    }       
}   