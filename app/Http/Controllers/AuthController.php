<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {

    public function register(Request $request) {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'apellido_p' => 'required|string|max:255',
            'apellido_m' => 'required|string|max:255',
            'celular'    => 'required|string',
            'correo'     => 'required|email|unique:personas,correo',
            'pass'       => 'required|min:6',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $persona = Persona::create([
                    'nombre'     => $request->nombre,
                    'apellido_p' => $request->apellido_p,
                    'apellido_m' => $request->apellido_m,
                    'celular'    => $request->celular,
                    'correo'     => $request->correo,
                    'activo'     => true
                ]);

                $usuario = Usuario::create([
                    'id_persona' => $persona->id,
                    'pass'       => Hash::make($request->pass),
                    'admin'      => false
                ]);

                $usuario->sendEmailVerificationNotification();

                return response()->json([
                    'res' => true,
                    'mensaje' => '¡Registro exitoso! Revisa tu correo para verificar tu cuenta.'
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'pass'  => 'required'
        ]);

        $persona = Persona::where('correo', $request->email)->first();

        if ($persona && $persona->usuario) {
            $usuario = $persona->usuario;

            if (Hash::check($request->pass, $usuario->pass)) {

                if (!$usuario->hasVerifiedEmail()) {
                    return response()->json([
                        'res' => false,
                        'mensaje' => 'Debes verificar tu correo.'
                    ], 403);
                }

                $deviceName = $request->header('User-Agent', 'device');
                $token = $usuario->createToken($deviceName)->plainTextToken;

                return response()->json([
                    'res' => true,
                    'token' => $token,
                    'usuario' => [
                        'id_persona' => $usuario->id_persona,
                        'nombre'     => $persona->nombre,
                        'admin'      => $usuario->admin
                    ]
                ]);
            }
        }

        return response()->json([
            'res' => false,
            'mensaje' => 'Credenciales incorrectas'
        ], 401);
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_pass' => 'required',
            'new_pass'     => 'required|min:6|confirmed'
        ]);

        $usuario = Auth::user();

        if (!$usuario) {
            return response()->json([
                'res' => false,
                'mensaje' => 'Usuario no autenticado'
            ], 401);
        }

        if (!Hash::check($request->current_pass, $usuario->pass)) {
            return response()->json([
                'res' => false,
                'mensaje' => 'La contraseña actual no coincide'
            ], 401);
        }

        if (Hash::check($request->new_pass, $usuario->pass)) {
            return response()->json([
                'res' => false,
                'mensaje' => 'La nueva contraseña no puede ser igual a la anterior'
            ], 400);
        }

        $usuario->pass = Hash::make($request->new_pass);
        $usuario->save();

        $usuario->tokens()->delete();

        return response()->json([
            'res' => true,
            'mensaje' => 'Contraseña actualizada. Sesiones cerradas en todos los dispositivos.'
        ]);
    }
}