<?php

namespace App\Http\Controllers;

use App\Models\detalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DetalleController extends Controller
{
/*************************************************************************** */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional


        // Consulta base
        $query = Detalle::query()
        ->orderBy('detalle.id_movimiento', 'asc');

        // Aplicar búsqueda especializada si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search)  {
                $innerQuery->where('id_detalle', 'LIKE', "%$search%")
                    ->orWhere('cantidad', 'LIKE', "%$search%")
                    ->orWhere('valor_unitario', 'LIKE', "%$search%")
                    ->orWhere('id_inventario', 'LIKE', "%$search%")
                    ->orWhere('id_movimiento', 'LIKE', "%$search%")
                    ->orWhere('id_producto', 'LIKE', "%$search%");
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
        $detalle = new Detalle;
        $detalle->cantidad = $request->input('cantidad');
        $detalle->valor_unitario = $request->input('valor_unitario');
        $detalle->id_inventario = $request->input('id_inventario');
        $detalle->id_movimiento = $request->input('id_movimiento');
        $detalle->id_producto = $request->input('id_producto');

        $detalle->save();

        return response()->json(['message' => 'Detalle agregado exitosamente'], 201);
    }
/*************************************************************************** */
    public function show(Detalle $detalle)
    {
        try {
            $detalle = Detalle::findOrFail($detalle->id_detalle);
        } catch (\Exception $e) {
            return response()->json(['error' => 'El detalle no existe'], 404);
        }

        // Realizar las operaciones necesarias o retornar la información requerida
        // ...

        // Ejemplo: Retornar el detalle como respuesta en formato JSON
        return response()->json($detalle);
    }
/*************************************************************************** */
    public function update(Request $request,  $id_detalle)
    {
        $detalle = Detalle::find($id_detalle);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle no encontrado'], 404);
        }

        $detalle = new Detalle;
        $detalle->cantidad = $request->input('cantidad');
        $detalle->valor_unitario = $request->input('valor_unitario');
        $detalle->id_inventario = $request->input('id_inventario');
        $detalle->id_movimiento = $request->input('id_movimiento');
        $detalle->id_producto = $request->input('id_producto');
        $confirmacion = true;
        $detalle->save();

        return response()->json(['message' => 'Detalle actualizado con éxito', 'data' => $detalle, 'confirmacion' => $confirmacion], 200);
    }
/*************************************************************************** */
    public function destroy(Detalle $detalle)
    {
        try {
            $detalle = Detalle::findOrFail($detalle->id_detalle);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'El detalle no existe'], 404);
        }
        // Eliminar el detalle de la base de datos
        $detalle->delete();
        // Opcional: Redireccionar o devolver una respuesta adecuada
        return response()->json(['message' => 'Detalle eliminado con éxito']);
    }
/*************************************************************************** */
}
