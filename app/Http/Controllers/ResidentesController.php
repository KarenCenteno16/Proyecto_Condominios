<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResidentesController extends Controller
{
    public function index()
    {
        $residentes = Persona::with(['usuario', 'perDeps.departamento'])->get();
        return response()->json($residentes);
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);
        $persona->update($request->only(['nombre', 'apellido_p', 'apellido_m', 'celular', 'activo']));
        
        if ($request->has('id_depa')) {
            $relacion = \App\Models\PerDep::where('id_persona', $id)->first();
            if ($relacion) {
                $relacion->update(['id_depa' => $request->id_depa]);
            } else {
                \App\Models\PerDep::create([
                    'id_persona' => $id,
                    'id_depa' => $request->id_depa,
                    'id_rol' => 1 
                ]);
            }
        }
        return response()->json(['message' => 'Actualizado con éxito']);
    }

   public function destroy($id)
    {
        try {
            $persona = Persona::findOrFail($id);

            $persona->delete();

            return response()->json(['message' => 'Eliminado correctamente']);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar residente',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function getDepartamentos() {
        $departamentos = \App\Models\Departamento::select('id', 'depa')->get();
        return response()->json($departamentos);
    }
}