<?php

namespace App\Http\Controllers;

use App\Models\detalle;
use Illuminate\Http\Request;

class DetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(detalle $detalle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(detalle $detalle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, detalle $detalle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(detalle $detalle)
    {
        //
    }
}
