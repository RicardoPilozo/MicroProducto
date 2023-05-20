<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Número de elementos por página, 10 por defecto
        $search = $request->input('search'); // Término de búsqueda

        $query = Movimiento::query();

        // Aplicar el filtro de búsqueda si se proporciona
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_comprobante', 'LIKE', "%$search%")
                    ->orWhere('descripcion_mov', 'LIKE', "%$search%");
            });
        }

        $movimientos = $query->paginate($perPage);

        return response()->json([
            'data' => $movimientos->items(),
            'current_page' => $movimientos->currentPage(),
            'last_page' => $movimientos->lastPage(),
            'total' => $movimientos->total(),
            'message' => 'Lista de movimientos obtenida correctamente.',
        ]);
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
    public function show(movimiento $movimiento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(movimiento $movimiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, movimiento $movimiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(movimiento $movimiento)
    {
        //
    }
}
