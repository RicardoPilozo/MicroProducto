<?php

namespace App\Http\Controllers;

use App\Models\detalle;
use Illuminate\Http\Request;

class DetalleController extends Controller
{
/*************************************************************************** */
    public function index(Request $request)
    {
        // Obtener parámetros de la consulta
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        // Consulta base
        $query = Detalle::query();

        // Aplicar búsqueda especializada si se proporciona
        if ($search) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('id_detalle', 'LIKE', "%$search%")
                    ->orWhere('cantidad', 'LIKE', "%$search%")
                    ->orWhere('valor_unitario', 'LIKE', "%$search%")
                    ->orWhere('id_inventario', 'LIKE', "%$search%")
                    ->orWhere('id_movimiento', 'LIKE', "%$search%")
                    ->orWhere('id_producto', 'LIKE', "%$search%");
            });
        }

        // Obtener los resultados paginados
        $detalles = $query->paginate($perPage);

        return response()->json($detalles);
    }
/*************************************************************************** */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer',
            'valor_unitario' => 'required|numeric',
            'id_inventario' => 'required|integer',
            'id_movimiento' => 'required|integer',
            'id_producto' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Crear el detalle
        $detalle = Detalle::create([
            'cantidad' => $request->input('cantidad'),
            'valor_unitario' => $request->input('valor_unitario'),
            'id_inventario' => $request->input('id_inventario'),
            'id_movimiento' => $request->input('id_movimiento'),
            'id_producto' => $request->input('id_producto'),
        ]);

        if (!$detalle) {
            return response()->json(['error' => 'No se pudo crear el detalle'], 500);
        }

        // Devolver la respuesta exitosa
        return response()->json(['message' => 'Detalle creado correctamente', 'detalle' => $detalle], 201);
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
    public function update(Request $request, detalle $detalle)
    {
        // Comprobar si el detalle existe
        $existingDetalle = Detalle::find($detalle->id_detalle);

        if (!$existingDetalle) {
            return response()->json(['message' => 'El detalle no existe'], 404);
        }

        // Actualizar los datos del detalle
        $existingDetalle->cantidad = $request->input('cantidad');
        $existingDetalle->valor_unitario = $request->input('valor_unitario');
        // Actualizar los otros campos según corresponda

        // Guardar los cambios en la base de datos
        $existingDetalle->save();

        return response()->json(['message' => 'Detalle actualizado correctamente'], 200);
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
