<?php

namespace App\Http\Controllers;

use App\Models\proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedores = Proveedor::all();

        return response()->json([
            'data' => $proveedores
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
    public function update(Request $request, Proveedor $proveedor)
    {
        // Validar si ya existe un proveedor con el mismo ID
        $existingProveedor = Proveedor::find($proveedor->id_proveedor);
        if (!$existingProveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }
    
        // Actualizar los datos del proveedor campo por campo
        $existingProveedor->nombre_prove = $request->input('nombre_prove', $existingProveedor->nombre_prove);
        $existingProveedor->apellido_prove = $request->input('apellido_prove', $existingProveedor->apellido_prove);
        $existingProveedor->empresa_prove = $request->input('empresa_prove', $existingProveedor->empresa_prove);
        $existingProveedor->cargo_prove = $request->input('cargo_prove', $existingProveedor->cargo_prove);
        $existingProveedor->ciudad_prove = $request->input('ciudad_prove', $existingProveedor->ciudad_prove);
        $existingProveedor->celular_prove = $request->input('celular_prove', $existingProveedor->celular_prove);
        $existingProveedor->estado_prove = $request->input('estado_prove', $existingProveedor->estado_prove);
    
        $existingProveedor->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente'
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        // Verificar si el proveedor existe
        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }

        // Eliminar el proveedor
        $proveedor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proveedor eliminado exitosamente'
        ]);
    }

}
