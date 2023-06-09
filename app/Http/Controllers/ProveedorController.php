<?php

namespace App\Http\Controllers;

use App\Models\proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional

        $query = Proveedor::query()
        ->orderBy('proveedor.nombre_prove', 'asc')
        ->where('proveedor.estado_prove', 1);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('proveedor.nombre_prove', 'LIKE', "%$search%")
                    ->orWhere('proveedor.apellido_prove', 'LIKE', "%$search%")
                    ->orWhere('proveedor.empresa_prove', 'LIKE', "%$search%")
                    ->orWhere('proveedor.cargo_prove', 'LIKE', "%$search%")
                    ->orWhere('proveedor.ciudad_prove', 'LIKE', "%$search%")
                    ->orWhere('proveedor.celular_prove', 'LIKE', "%$search%");
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

    
    public function store(Request $request)
    {
        $rules = [
            'nombre_prove' => 'required|string|max:150',
            'apellido_prove' => 'required|string|max:150',
            'empresa_prove' => 'required|string|max:200',
            'cargo_prove' => 'required|string|max:150',
            'ciudad_prove' => 'required|string|max:150',
            'celular_prove' => 'required|string|max:10',
            'estado_prove' => ['required', Rule::in([0, 1])],
        ];

        $validatedData = $request->validate($rules);

        // Validar si ya existe un proveedor con el mismo nombre y apellido
        $existingProveedor = Proveedor::where('nombre_prove', $validatedData['nombre_prove'])
            ->where('apellido_prove', $validatedData['apellido_prove'])
            ->first();
        if ($existingProveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un proveedor con el mismo nombre y apellido'
            ], 409);
        }

        $proveedor = new Proveedor();
        $proveedor->nombre_prove = $validatedData['nombre_prove'];
        $proveedor->apellido_prove = $validatedData['apellido_prove'];
        $proveedor->empresa_prove = $validatedData['empresa_prove'];
        $proveedor->cargo_prove = $validatedData['cargo_prove'];
        $proveedor->ciudad_prove = $validatedData['ciudad_prove'];
        $proveedor->celular_prove = $validatedData['celular_prove'];
        $proveedor->estado_prove = $validatedData['estado_prove'];
        $proveedor->save();

        return response()->json([
            'success' => true,
            'data' => $proveedor
        ]);
    }


    public function show(Proveedor $proveedor)
    {
        // Verificar si el proveedor existe
        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $proveedor
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_proveedor)
    {
        $proveedor = Proveedor::find($id_proveedor);

        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ]);
        }
    
        // Actualizar los datos del proveedor campo por campo
        $proveedor->nombre_prove = $request->input('nombre_prove');
        $proveedor->apellido_prove = $request->input('apellido_prove');
        $proveedor->empresa_prove = $request->input('empresa_prove');
        $proveedor->cargo_prove = $request->input('cargo_prove');
        $proveedor->ciudad_prove = $request->input('ciudad_prove');
        $proveedor->celular_prove = $request->input('celular_prove');
        $proveedor->estado_prove = $request->input('estado_prove');
    
        $proveedor->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente'
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json([
                'message' => 'El producto no existe'
            ], 404);
        }


        $proveedor->estado_prove = 0;
        $proveedor->save();

        return response()->json([
            'message' => 'Proveedor desactivado correctamente'
        ], 200);
    }

}
