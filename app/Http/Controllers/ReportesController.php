<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use App\Models\servicio_tecnico;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function obtenerGananciasSemanales(Request $request)
    {
        $anio = $request->input('anio');

        $gananciasMovimientos = DB::table('movimiento')
            ->select(DB::raw('YEAR(fecha_mov) AS anio'), DB::raw('MONTH(fecha_mov) AS mes'), DB::raw('WEEK(fecha_mov) AS semana'), DB::raw('SUM(ganancia_mov) as ganancia_total'))
            ->whereYear('fecha_mov', $anio) // Filtrar por el año proporcionado
            ->groupBy('anio', 'mes', 'semana')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->orderBy('semana', 'asc')
            ->get();

        $gananciasServicios = DB::table('servicio_tecnico')
            ->select(DB::raw('YEAR(fecha_ingreso_serv) AS anio'), DB::raw('MONTH(fecha_ingreso_serv) AS mes'), DB::raw('WEEK(fecha_ingreso_serv) AS semana'), DB::raw('SUM(ganancia_serv) as ganancia_total'))
            ->whereYear('fecha_ingreso_serv', $anio) // Filtrar por el año proporcionado
            ->groupBy('anio', 'mes', 'semana')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->orderBy('semana', 'asc')
            ->get();

        $gananciasTotales = $gananciasMovimientos->concat($gananciasServicios);

        $gananciasPorMes = $gananciasTotales->groupBy(function ($ganancia) {
            return $ganancia->anio . '-' . $ganancia->mes;
        })->map(function ($ganancias) {
            $semanas = $ganancias->pluck('ganancia_total')->toArray();
            $numSemanas = $semanas ? max(array_keys($semanas)) + 1 : 0;
            return array_pad($semanas, $numSemanas, 0);
        })->values()->toArray();

        $gananciasFlattened = collect($gananciasPorMes)->flatten()->filter(function ($ganancia) {
            return !is_null($ganancia);
        });
        
        $promedioGanancias = $gananciasFlattened->avg();
        
        return response()->json([
            'data' => $gananciasPorMes,
            'promedioSemanal' => $promedioGanancias
        ]);
        
    }

    public function obtenerGananciasMes(Request $request)
    {
        $anio = $request->input('anio');

        // Obtener ganancias de movimientos por mes y año
        $gananciasMovimientos = DB::table('movimiento')
            ->select(DB::raw('MONTH(fecha_mov) as mes'), DB::raw('SUM(ganancia_mov) as ganancia_total'))
            ->whereYear('fecha_mov', $anio)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Obtener ganancias de servicios técnicos por mes y año
        $gananciasServicios = DB::table('servicio_tecnico')
            ->select(DB::raw('MONTH(fecha_ingreso_serv) as mes'), DB::raw('SUM(ganancia_serv) as ganancia_total'))
            ->whereYear('fecha_ingreso_serv', $anio)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Combinar los resultados de los movimientos y servicios técnicos
        $gananciasTotales = $gananciasMovimientos->concat($gananciasServicios);

        // Agrupar las ganancias por mes
        $gananciasPorMes = $gananciasTotales->groupBy('mes')->values();

        // Crear un arreglo con las sumas de las ganancias por mes
        $sumaGananciasPorMes = $gananciasPorMes->map(function ($ganancias) {
            return [$ganancias->sum('ganancia_total')];
        });

        // Calcular la suma de las ganancias de todo el año
        $gananciaAnio = $gananciasTotales->sum('ganancia_total');

        // Calcular el promedio mensual del año si hay datos disponibles
        $promedioMensual = 0;
        if ($gananciasPorMes->count() > 0) {
            $promedioMensual = $gananciaAnio / $gananciasPorMes->count();
        }

        // Retornar las sumas de las ganancias por mes, la ganancia del año y el promedio mensual
        return response()->json([
            'data' => $sumaGananciasPorMes,
            'gananciaAnio' => $gananciaAnio,
            'promedioMensual' => $promedioMensual
        ]);
    }

    public function topProductosVendidosPorMes(Request $request)
    {
        $mes = $request->input('mes');
        $productos = DB::table('detalle')
            ->join('producto', 'detalle.id_producto', '=', 'producto.id_producto')
            ->select('producto.nombre_producto', DB::raw('SUM(detalle.cantidad) as total_vendido'))
            ->join('movimiento', 'detalle.id_movimiento', '=', 'movimiento.id_movimiento')
            ->whereMonth('movimiento.fecha_mov', $mes)
            ->groupBy('detalle.id_producto', 'producto.nombre_producto') // Agregar 'producto.nombre_producto' al GROUP BY
            ->orderByDesc('total_vendido')
            ->take(5)
            ->get();

        $labels = $productos->pluck('nombre_producto')->toArray();
        $data = $productos->pluck('total_vendido')->toArray();

        return response()->json(['labels' => $labels, 'data' => $data]);
    }



        
}
