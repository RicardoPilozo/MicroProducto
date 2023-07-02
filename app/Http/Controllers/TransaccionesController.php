<?php

namespace App\Http\Controllers;

use App\Models\transacciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




class TransaccionesController extends Controller
{
    /*************************************************************************** */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional


        // Consulta base
        $query = Transacciones::query()
        //->join('movimiento', 'movimiento.id_transacciones', '=', 'transacciones.id_transacciones')
        ->select(
            'transacciones.id_transacciones',
            'transacciones.tipo_transaccion',
            'transacciones.tipo_pago',
            'transacciones.monto_transaccion',
            'transacciones.descripción',
            'transacciones.created_at',
            'transacciones.updated_at',
            'transacciones.fecha_t'
            )
        ->orderBy('transacciones.id_transacciones', 'asc');

        // Aplicar búsqueda especializada si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search)  {
                $innerQuery->where('tipo_transaccion', 'LIKE', "%$search%")
                    ->orWhere('tipo_pago', 'LIKE', "%$search%");
            });
        }
        $total = $query->count();
        $registros = $query->skip(($page - 1) * $perPage)
        ->take($perPage)
        ->get();

        return response()->json([
            'data' => $registros,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
        ]);
    }
    /*************************************************************************** */
    public function store(Request $request)
    {
        $transacciones = new Transacciones;
        $transacciones->tipo_transaccion = $request->input('tipo_transaccion');
        $transacciones->tipo_pago = $request->input('tipo_pago');
        $transacciones->monto_transaccion = $request->input('monto_transaccion');
        $transacciones->descripción = $request->input('descripción');
        $transacciones->fecha_t = $request->input('fecha_t');
        $transacciones->save();

        return response()->json(['message' => 'Transaccion agregada exitosamente', 'data' => $transacciones]);
    }
    /*************************************************************************** */

    public function getTransaccionesporSemana(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $startDate = Carbon::createFromDate($year, $month)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth();

        $transacciones = DB::table('transacciones')
            ->select(
                DB::raw('WEEK(fecha_t) as week'),
                'id_transacciones',
                DB::raw('SUM(monto_transaccion) as total')
            )
            ->where('tipo_transaccion', 'entrada')
            ->whereBetween('fecha_t', [$startDate, $endDate])
            ->groupBy('week', 'id_transacciones')
            ->get();

        $result = [];

        foreach ($transacciones as $transaccion) {
            $week = $transaccion->week;

            if (!isset($result[$week])) {
                $result[$week] = [
                    'week' => $week,
                    'id_transacciones' => [],
                    'total' => 0,
                ];
            }

            $result[$week]['id_transacciones'][] = $transaccion->id_transacciones;
            $result[$week]['total'] += $transaccion->total; // Accedemos a la propiedad 'total' en lugar de 'monto_transaccion'
        }

        $response = array_values($result);

        return response()->json($response);
    }


    public function obtenerSemanasMeses(Request $request)
    {
        $year = $request->input('year'); // Obtén el año desde la solicitud
        
        $result = DB::table('transacciones')
            ->select(DB::raw('SUM(monto_transaccion) as totalMontoSemana, WEEK(fecha_t) as semana, MONTH(fecha_t) as mes'))
            ->where('tipo_transaccion', 'entrada')
            ->whereYear('fecha_t', $year)
            ->groupBy('mes', 'semana')
            ->orderBy('mes', 'asc')
            ->orderBy('semana', 'asc')
            ->get();

        $data = [];
        
        foreach ($result as $row) {
            $data[$row->mes][] = $row->totalMontoSemana;
        }
        
        $response = [];
        
        for ($mes = 1; $mes <= 12; $mes++) {
            if (isset($data[$mes])) {
                $response[] = $data[$mes];
            } else {
                $response[] = [];
            }
        }
        
        return $response;
    }






}


