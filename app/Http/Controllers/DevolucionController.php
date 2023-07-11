<?php

namespace App\Http\Controllers;


use App\Models\devolucion;
use App\Models\inventario;

use Illuminate\Http\Request;

class DevolucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional


        $query = Devolucion::query()
        ->join('usuario', 'devolucion.id_usuario', '=', 'usuario.id_usuario')
        ->leftJoin('producto', 'devolucion.id_producto', '=', 'producto.id_producto')
        ->select(
            'devolucion.id_devolucion',
            'devolucion.fecha_dev',
            'devolucion.cantidad_dev',
            'devolucion.descripcion_dev',
            'devolucion.id_producto',
            'producto.nombre_producto', 
            'devolucion.id_usuario',
            \DB::raw("CONCAT(usuario.nombre_usu, ' ', usuario.apellido_usu) AS nombre_usuario")
        )
        ->orderBy('devolucion.id_devolucion', 'asc');
            

        // Aplicar el filtro de búsqueda si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search)  {
                $query->where('devolucion.fecha_dev', 'LIKE', "%$search%")
                    ->orWhere('devolucion.descripcion_dev', 'LIKE', "%$search%");
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $devolucion = new Devolucion;
        $devolucion->fecha_dev = $request->input('fecha_dev');
        $devolucion->cantidad_dev = $request->input('cantidad_dev');
        $devolucion->descripcion_dev = $request->input('descripcion_dev');
        $devolucion->id_producto = $request->input('id_producto');
        $devolucion->id_usuario = $request->input('id_usuario');
        $devolucion->save();

        // Obtener el inventario correspondiente al producto
        $inventario = Inventario::where('id_producto', $devolucion->id_producto)->first();

        if ($inventario) {
            // Restar la cantidad_dev al valor de cantidad_inventario
            $inventario->cantidad_inventario -= $devolucion->cantidad_dev;
            $inventario->save();
        }

        return response()->json(['message' => 'Devolucion generada exitosamente', 'data' => $devolucion], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
