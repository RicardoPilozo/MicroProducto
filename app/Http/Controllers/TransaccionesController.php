<?php

namespace App\Http\Controllers;

use App\Models\transacciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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

        $transacciones->save();

        return response()->json(['message' => 'Transaccion agregada exitosamente', 'data' => $transacciones]);
    }
    /*************************************************************************** */

    





}
