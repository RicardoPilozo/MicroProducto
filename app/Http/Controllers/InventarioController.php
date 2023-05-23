<?php

namespace App\Http\Controllers;

use App\Models\inventario;
use App\Models\producto;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Cantidad de registros por página, 10 por defecto
        $search = $request->input('search'); // Término de búsqueda
        $page = $request->input('page', 1); // Página actual, 1 por defecto

        $query = Inventario::select(
            'inventario.id_inventario',
            DB::raw("CASE WHEN inventario.estado_inv = 1 THEN 'activo' ELSE 'inactivo' END as estado_inv"),
            'inventario.cantidad_inventario',
            'inventario.descripcion_inv',
            'producto.nombre_producto'
        )
            ->leftJoin('producto', 'inventario.id_producto', '=', 'producto.id_producto');

        // Aplicar filtro de búsqueda si se proporciona un término de búsqueda
        if ($search) {
            $query->where('producto.nombre_producto', 'LIKE', "%$search%");
        }

        $inventarios = $query->orderBy('inventario.id_inventario')
            ->paginate($perPage, ['*'], 'page', $page);

        $response = [
            'status' => 'success',
            'message' => 'Inventarios obtenidos correctamente.',
            'data' => $inventarios,
        ];

        return response()->json($response, 200);
    }


    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nombre_producto = $request->input('nombre_producto');

        $producto = Producto::where('nombre_producto', $nombre_producto)->first();

        if (!$producto) {
            return response()->json(['error' => 'El producto no existe en la base de datos'], 404);
        }

        $inventario_existente = Inventario::where('id_producto', $producto->id_producto)->first();

        if ($inventario_existente) {
            return response()->json(['error' => 'Ya existe un inventario para este producto'], 400);
        }

        $inventario = new Inventario;
        $inventario->estado_inv = $request->input('estado_inv');
        $inventario->cantidad_inventario = $request->input('cantidad_inventario');
        $inventario->descripcion_inv = $request->input('descripcion_inv');
        $inventario->id_producto = $producto->id_producto;

        $inventario->save();

        return response()->json(['message' => 'Inventario agregado correctamente'], 200);
    }

    public function show($idInventario)
    {
        $inventario = Inventario::where('id_inventario', $idInventario)->first();

        if (!$inventario) {
            return response()->json(['message' => 'No se encontró el inventario'], 404);
        }

        $producto = Producto::where('id_producto', $inventario->id_producto)->first();

        if (!$producto) {
            return response()->json(['message' => 'No se encontró el producto asociado al inventario'], 404);
        }

        $inventario->nombre_producto = $producto->nombre_producto;

        return response()->json($inventario);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventario $inventario)
    {
        $producto = Producto::where('nombre_producto', $request->nombre_producto)->first();

        if (!$producto) {
            return response()->json(['message' => 'No se encontró el producto'], 404);
        }

        $inventario->estado_inv = $request->estado_inv;
        $inventario->cantidad_inventario = $request->cantidad_inventario;
        $inventario->descripcion_inv = $request->descripcion_inv;
        $inventario->id_producto = $producto->id_producto;

        $inventario->save();

        return response()->json(['message' => 'Inventario actualizado correctamente', 'inventario' => $inventario]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventario $inventario)
    {
        $inventario = Inventario::find($inventario->id_inventario);

        if (!$inventario) {
            return response()->json(['message' => 'No se encontró el inventario'], 404);
        }

        $inventario->delete();

        return response()->json(['message' => 'El inventario ha sido eliminado correctamente']);
    }


}
