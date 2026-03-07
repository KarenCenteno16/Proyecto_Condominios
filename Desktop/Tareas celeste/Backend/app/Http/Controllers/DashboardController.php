<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Pago;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        try {
            // 1. Total de residentes (Tabla: personas)
            $totalResidentes = Persona::count();

            // 2. Pagos Pendientes (Tabla: pagos, Columna: efectuado = false)
            $pagosPendientes = Pago::where('efectuado', false)->count();

            // 3. Pagos del Mes (Tabla: pagos, Columna: monto y fecha)
            $pagosMes = Pago::where('efectuado', true)
                ->whereMonth('fecha', Carbon::now()->month)
                ->whereYear('fecha', Carbon::now()->year)
                ->sum('monto');

            return response()->json([
                'total_residentes' => $totalResidentes,
                'pagos_pendientes' => $pagosPendientes,
                'pagos_mes' => $pagosMes,
                'visitantes_hoy' => 0 // Lo dejamos en 0 por ahora ya que no hay tabla de visitas
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la base de datos: ' . $e->getMessage()], 500);
        }
    }
}