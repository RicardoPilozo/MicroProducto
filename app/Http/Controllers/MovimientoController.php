<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use App\Models\usuario;
use App\Models\cliente;
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
                    ->orWhere('descripcion_mov', 'LIKE', "%$search%")
                    ->orWhere('id_cliente', 'LIKE', "%$search%");
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

    public function store(Request $request)
    {
        // Validar los datos del request
        $validatedData = $request->validate([
            'fecha_mov' => 'required|date',
            'numero_comprobante' => 'required|string|max:100',
            'tipo_mov' => 'required|string|max:50',
            'descripcion_mov' => 'required|string|max:200',
            'valor_total_mov' => 'required|numeric',
            'id_usuario' => 'required|integer|exists:usuario,id_usuario', // Validar la existencia del usuario en la tabla 'usuarios'
            'id_cliente' => 'integer'
        ]);

        // Verificar si el usuario existe en la base de datos
        $usuario = Usuario::find($validatedData['id_usuario']);
        if (!$usuario) {
            return response()->json(['error' => 'El usuario no existe'], 404);
        }
        // Verificar si el usuario existe en la base de datos
        $usuario = Cliente::find($validatedData['id_cliente']);
        if (!$usuario) {
            return response()->json(['error' => 'El cliente no existe'], 404);
        }
        

        // Crear una nueva instancia del modelo Movimiento
        $movimiento = new Movimiento;

        // Asignar los valores validados a las propiedades del modelo
        $movimiento->fecha_mov = $validatedData['fecha_mov'];
        $movimiento->numero_comprobante = $validatedData['numero_comprobante'];
        $movimiento->tipo_mov = $validatedData['tipo_mov'];
        $movimiento->descripcion_mov = $validatedData['descripcion_mov'];
        $movimiento->valor_total_mov = $validatedData['valor_total_mov'];
        $movimiento->id_usuario = $validatedData['id_usuario'];
        $movimiento->id_cliente = $validatedData['id_cliente'];

        // Guardar el movimiento en la base de datos
        $movimiento->save();

        // Retorna o devuelve una respuesta adecuada
        return response()->json(['message' => 'Movimiento creado con éxito'], 201);
    }

    public function show(Movimiento $movimiento)
    {
        $movimientoEncontrado = Movimiento::find($movimiento->id_movimiento);

        if ($movimientoEncontrado) {
            // El movimiento se encontró en la base de datos
            return response()->json($movimientoEncontrado);
        } else {
            // El movimiento no se encontró en la base de datos
            return response()->json(['message' => 'Movimiento no encontrado'], 404);
        }
    }


    public function update(Request $request, Movimiento $movimiento)
    {
        // Validar si el movimiento existe
        if (!$movimiento->exists()) {
            return response()->json(['error' => 'El movimiento no existe'], 404);
        }

        // Validar los datos del request
        $validatedData = $request->validate([
            'fecha_mov' => 'required|date',
            'numero_comprobante' => 'required|string|max:100',
            'tipo_mov' => 'required|string|max:50',
            'descripcion_mov' => 'required|string|max:200',
            'valor_total_mov' => 'required|numeric',
            'id_usuario' => 'required|integer'
        ]);

        // Actualizar los valores del modelo con los datos validados
        $movimiento->fecha_mov = $validatedData['fecha_mov'];
        $movimiento->numero_comprobante = $validatedData['numero_comprobante'];
        $movimiento->tipo_mov = $validatedData['tipo_mov'];
        $movimiento->descripcion_mov = $validatedData['descripcion_mov'];
        $movimiento->valor_total_mov = $validatedData['valor_total_mov'];
        $movimiento->id_usuario = $validatedData['id_usuario'];

        // Guardar los cambios en la base de datos
        $movimiento->save();

        // Retornar o devolver una respuesta 
        return response()->json(['message' => 'Movimiento actualizado con éxito']);
    }

    public function destroy(Movimiento $movimiento)
    {
        // Verificar si el movimiento existe
        if (!$movimiento->exists()) {
            return response()->json(['error' => 'El movimiento no existe'], 404);
        }

        // Eliminar el movimiento de la base de datos
        $movimiento->delete();

        // retorna o devolver una respuesta
        return response()->json(['message' => 'Movimiento eliminado con éxito']);
    }
}
