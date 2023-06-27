<?php

namespace App\Http\Controllers;

use App\Models\cliente;

use Illuminate\Http\Request;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional

        $query = Cliente::query()
        ->orderBy('cliente.id_cliente', 'asc');

        // Aplicar el filtro de búsqueda si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search)  {
                $query->where('cliente.nombre_clie', 'LIKE', "%$search%")
                    ->orWhere('cliente.apellido_clie', 'LIKE', "%$search%")
                    ->orWhere('cliente.cedula_clie', 'LIKE', "%$search%")
                    ->orWhere('cliente.correo_clie', 'LIKE', "%$search%");
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

    public function ObtenerClienteCedula($cedula)
    {

        $cliente = Cliente::where('cedula_clie', $cedula)->first();

        if ($cliente) {
            return response()->json(['data' => $cliente]);
        } else {
            return response()->json(['message' => 'Cliente no encontrado']);
        }
    }

    public function store(Request $request)
    {
        $cliente = new Cliente;
        $cliente->nombre_clie = $request->input('nombre_clie');
        $cliente->apellido_clie = $request->input('apellido_clie');
        $cliente->cedula_clie = $request->input('cedula_clie');
        $cliente->telefono_clie = $request->input('telefono_clie');
        $cliente->correo_clie = $request->input('correo_clie');
        $cliente->direccion_clie = $request->input('direccion_clie');
        $cliente->save();

        return response()->json(['message' => 'Cliente agregado exitosamente', 'data' => $cliente]);
    
    }

    public function update(Request $request, $id_cliente)
    {
        $cliente = Cliente::find($id_cliente);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
        }
    
        // Actualizar los datos del proveedor campo por campo
        $cliente->nombre_clie = $request->input('nombre_clie');
        $cliente->apellido_clie = $request->input('apellido_clie');
        $cliente->cedula_clie = $request->input('cedula_clie');
        $cliente->telefono_clie = $request->input('telefono_clie');
        $cliente->correo_clie = $request->input('correo_clie');
        $cliente->direccion_clie = $request->input('direccion_clie');
        $cliente->save();
    
        return response()->json([
            'success' => true, 'message' => 'Proveedor actualizado exitosamente', 'data' => $cliente
        ]);
    }
    
}
