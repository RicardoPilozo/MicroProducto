<?php

namespace App\Http\Controllers;

use App\Models\categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Categoria::query();

        // Búsqueda por nombre_cat
        if ($request->has('nombre_cat')) {
            $query->where('nombre_cat', 'LIKE', '%' . $request->input('nombre_cat') . '%');
        }

        // Búsqueda por estado_cat
        if ($request->has('estado_cat')) {
            $query->where('estado_cat', 'LIKE', '%' . $request->input('estado_cat') . '%');
        }

        // Paginación
        $perPage = $request->has('per_page') ? $request->input('per_page') : 10;
        $categories = $query->paginate($perPage);

        return response()->json([
            'data' => $categories->items(),
            'current_page' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
        ]);
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
    public function show(categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(categoria $categoria)
    {
        //
    }
}
