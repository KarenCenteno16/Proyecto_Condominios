<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; 
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller {
    public function register(Request $request) {
    // 1. Validamos los datos que vienen del formulario de React
    $request->validate([
        'nombre'     => 'required|string|max:255',
        'apellido_p' => 'required|string|max:255',
        'apellido_m' => 'required|string|max:255',
        'celular'    => 'required|string',
        'correo'     => 'required|email|unique:personas,correo',
        'pass'       => 'required|min:6',
    ]);

    try {
        // 2. Usamos una transacción para que si algo falla en la segunda tabla, 
        // no se guarde nada en la primera (integridad de datos).
        return DB::transaction(function () use ($request) {
            
            // 3. Crear el registro en la tabla 'personas'
            $persona = Persona::create([
                'nombre'     => $request->nombre,
                'apellido_p' => $request->apellido_p,
                'apellido_m' => $request->apellido_m,
                'celular'    => $request->celular,
                'correo'     => $request->correo,
                'activo'     => true // Valor por defecto
            ]);

            // 4. Crear el registro en la tabla 'usuarios'
            // Usamos $persona->id porque es el ID autoincremental que Postgres acaba de generar
            $usuario = Usuario::create([
                'id_persona' => $persona->id, 
                'pass'       => Hash::make($request->pass), // Encriptamos la contraseña
                'admin'      => false // Por defecto es residente
            ]);

            // 5. Disparar el evento de verificación de email (Usa Google SMTP del .env)
            $usuario->sendEmailVerificationNotification();

            // 6. Respuesta de éxito al Frontend
            return response()->json([
                'res' => true, 
                'mensaje' => '¡Registro exitoso! Por favor, revisa tu correo para verificar tu cuenta.'
            ], 201);
        });

        } catch (\Exception $e) {
            // En caso de error (ej. error de base de datos), devolvemos el mensaje real para debug
            return response()->json([
                'res' => false, 
                'mensaje' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request) {
        // Validamos que lleguen los datos
        $request->validate([
            'email' => 'required|email',
            'pass'  => 'required'
        ]);

        // Buscamos a la persona por correo
        $persona = Persona::where('correo', $request->email)->first();

        if ($persona && $persona->usuario) {
            $usuario = $persona->usuario;

            // Verificamos contraseña
            if (Hash::check($request->pass, $usuario->pass)) {
                
                // OPCIONAL: Bloquear login si no ha verificado correo
                if (!$usuario->hasVerifiedEmail()) {
                    return response()->json([
                        'res' => false, 
                        'mensaje' => 'Debes verificar tu correo antes de iniciar sesión.'
                    ], 403);
                }

                $token = $usuario->createToken('auth_token')->plainTextToken;

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

        return response()->json(['res' => false, 'mensaje' => 'Correo o contraseña incorrectos'], 401);
    }
}