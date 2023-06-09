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
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional

        $query = Categoria::query()
        ->orderBy('categoria.nombre_cat', 'asc')
        ->where('categoria.estado_cat', 1);

        
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('categoria.nombre_cat', 'LIKE', "%$search%")
                    ->orWhere('categoria.estado_cat', 'LIKE', "%$search%");
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
            'total' =>  $total,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $categoria = new Categoria;
        $categoria->nombre_cat = $request->input('nombre_cat');
        $categoria->descripcion_cat = $request->input('descripcion_cat');
        $categoria->estado_cat = 1;

        $categoria->save();
        return response()->json(['message' => 'Categoría agregado exitosamente'], 201);
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
    public function update(Request $request, $id_categoria)
    {
        //
        $categoria = Categoria::find($id_categoria);

        if (!$categoria) {
            return response()->json(['message' => 'Categoria no encontrada'], 404);
        }

        $categoria->nombre_cat = $request->input('nombre_cat');
        $categoria->descripcion_cat = $request->input('descripcion_cat');
        $confirmacion = true;
        $categoria->save();
        return response()->json(['message' => 'Categoria actualizada con éxito', 'data' => $categoria, 'confirmacion' => $confirmacion], 200);

    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json([
                'message' => 'La categoria no existe'
            ], 404);
        }

        $categoria->estado_cat = 0;
        $categoria->save();

        return response()->json([
            'message' => 'Categoria desactivada correctamente'
        ], 200);
    }

    
}
