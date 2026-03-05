<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Usuario;      
use App\Models\Notificacion; 
use App\Events\NuevoReporteEvent; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    /**
     * Lista todos los reportes (Vista del Administrador)
     */
    public function index()
    {
        try {
            $reportes = DB::table('reportes')
                ->join('usuarios', 'reportes.usuario_id', '=', 'usuarios.id_persona')
                ->join('personas', 'usuarios.id_persona', '=', 'personas.id') 
                ->leftJoin('per_dep', 'personas.id', '=', 'per_dep.id_persona')
                ->leftJoin('departamentos', 'per_dep.id_depa', '=', 'departamentos.id')
                ->select(
                    'reportes.id',
                    'reportes.categoria',
                    'reportes.descripcion',
                    'reportes.estado',
                    'reportes.created_at',
                    DB::raw("CONCAT(personas.nombre, ' ', personas.apellido_p) as usuario_nombre"),
                    'departamentos.depa as departamento'
                )
                ->orderBy('reportes.created_at', 'desc')
                ->get();

            return response()->json($reportes);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Guarda un nuevo reporte y notifica a los administradores
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required',
                'categoria' => 'required',
                'descripcion' => 'required'
            ]);

            // 1. Guardar el reporte en la base de datos
            $reporte = Reporte::create([
                'usuario_id' => $request->usuario_id,
                'categoria' => $request->categoria,
                'descripcion' => $request->descripcion,
                'estado' => 'Pendiente'
            ]);

            // 2. Notificar a todos los administradores (admin = 1)
            $admins = Usuario::where('admin', 1)->get();

            foreach ($admins as $admin) {
                // Guardar la notificación
                // NOTA: Se agrega 'tipo' porque tu migración lo pide como obligatorio
                $notif = Notificacion::create([
                    'usuario_id' => $admin->id_persona,
                    'tipo'       => 'reporte', 
                    'titulo'     => 'Nuevo Reporte: ' . $request->categoria,
                    'mensaje'    => 'Un residente ha enviado una nueva incidencia.',
                    'url'        => '/reportes',
                    'leido'      => false
                ]);

                // Disparar evento para tiempo real
                broadcast(new NuevoReporteEvent($notif, $admin->id_persona));
            }

            return response()->json(['res' => true, 'reporte' => $reporte], 201);
        } catch (\Exception $e) {
            Log::error("Error al guardar reporte: " . $e->getMessage());
            return response()->json([
                'res' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza el estado o datos de un reporte
     */
    public function update(Request $request, $id)
    {
        try {
            $reporte = Reporte::findOrFail($id);
            $reporte->update([
                'categoria' => $request->categoria,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado 
            ]);

            return response()->json(['res' => true, 'reporte' => $reporte]);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un reporte
     */
    public function destroy($id)
    {
        try {
            $reporte = Reporte::findOrFail($id);
            $reporte->delete();
            return response()->json(['res' => true]);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lista los reportes de un usuario específico (Vista del Residente)
     */
    public function reportesPorUsuario($id)
    {
        try {
            $reportes = DB::table('reportes')
                ->join('usuarios', 'reportes.usuario_id', '=', 'usuarios.id_persona')
                ->join('personas', 'usuarios.id_persona', '=', 'personas.id')
                ->leftJoin('per_dep', 'personas.id', '=', 'per_dep.id_persona')
                ->leftJoin('departamentos', 'per_dep.id_depa', '=', 'departamentos.id')
                ->where('reportes.usuario_id', $id)
                ->select(
                    'reportes.*', // Trae id, categoria, descripcion, estado, created_at, etc.
                    DB::raw("CONCAT(personas.nombre, ' ', personas.apellido_p) as usuario_nombre"),
                    'departamentos.depa as departamento'
                )
                ->orderBy('reportes.created_at', 'desc')
                ->get();
                
            return response()->json($reportes);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}