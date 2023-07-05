<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use App\Models\usuario;
use App\Models\cliente;
use App\Models\detalle;

use Illuminate\Http\Request;


class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional

        $query = Movimiento::query()
        ->orderBy('movimiento.id_movimiento', 'asc');

        // Aplicar el filtro de búsqueda si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search)  {
                $q->where('movimiento.numero_comprobante', 'LIKE', "%$search%")
                    ->orWhere('movimiento.descripcion_mov', 'LIKE', "%$search%")
                    ->orWhere('movimiento.fecha_mov', 'LIKE', "%$search%")
                    ->orWhere('movimiento.id_cliente', 'LIKE', "%$search%");
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

    public function indexSalidas(Request $request)
    {
        $perPage = intval($request->input('per_page', 100)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional


        $query = Movimiento::query()
        ->join('usuario', 'movimiento.id_usuario', '=', 'usuario.id_usuario')
        ->leftJoin('cliente', 'movimiento.id_cliente', '=', 'cliente.id_cliente')
        ->leftJoin('transacciones', 'movimiento.id_transacciones', '=', 'transacciones.id_transacciones')
        ->select(
            'movimiento.id_movimiento',
            'movimiento.fecha_mov',
            'movimiento.numero_comprobante',
            'movimiento.tipo_mov',
            'movimiento.descripcion_mov', 
            'movimiento.valor_total_mov',
            'movimiento.id_cliente', 
            'movimiento.id_usuario',
            'movimiento.id_transacciones',
            'transacciones.tipo_pago',
            \DB::raw("CONCAT(usuario.nombre_usu, ' ', usuario.apellido_usu) AS nombre_usuario"),
            \DB::raw("CONCAT(cliente.nombre_clie, ' ', cliente.apellido_clie) AS nombre_cliente")
        )
        ->orderBy('movimiento.id_movimiento', 'asc')
        ->where('movimiento.tipo_mov', "Salida");
            

        // Aplicar el filtro de búsqueda si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search)  {
                $query->where('movimiento.numero_comprobante', 'LIKE', "%$search%")
                    ->orWhere('movimiento.descripcion_mov', 'LIKE', "%$search%")
                    ->orWhere('movimiento.fecha_mov', 'LIKE', "%$search%")
                    ->orWhere('movimiento.id_cliente', 'LIKE', "%$search%");
            });
        }

        $total = $query->count();

        $registros = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Obtener los detalles agrupados por el ID del movimiento
        $movimientoIds = $registros->pluck('id_movimiento')->toArray();

        $detalles = Detalle::whereIn('id_movimiento', $movimientoIds)
        ->join('producto', 'detalle.id_producto', '=', 'producto.id_producto')
        ->select('detalle.*', 'producto.nombre_producto')
        ->get()
        ->groupBy('id_movimiento');

        // Asignar los detalles a cada registro de movimiento
        $registros->each(function ($registro) use ($detalles) {
            $idMovimiento = $registro->id_movimiento;
            $registro->detalles = $detalles->get($idMovimiento);
        });

        return response()->json([
            'data' => $registros,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
        ]);
    }

    public function indexSalidasFecha(Request $request, $fecha)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional

        $query = Movimiento::query()
            ->join('usuario', 'movimiento.id_usuario', '=', 'usuario.id_usuario')
            ->leftJoin('cliente', 'movimiento.id_cliente', '=', 'cliente.id_cliente')
            ->select(
                'movimiento.id_movimiento',
                'movimiento.fecha_mov',
                'movimiento.numero_comprobante',
                'movimiento.tipo_mov',
                'movimiento.descripcion_mov',
                'movimiento.valor_total_mov',
                'movimiento.id_cliente',
                'movimiento.id_usuario',
                'movimiento.id_transacciones',
                \DB::raw("CONCAT(usuario.nombre_usu, ' ', usuario.apellido_usu) AS nombre_usuario"),
                \DB::raw("CONCAT(cliente.nombre_clie, ' ', cliente.apellido_clie) AS nombre_cliente")
            )
            ->orderBy('movimiento.id_movimiento', 'asc')
            ->where('movimiento.tipo_mov', 'Salida')
            ->where('movimiento.fecha_mov', $fecha);

        // Aplicar el filtro de búsqueda si se proporciona
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('movimiento.numero_comprobante', 'LIKE', "%$search%")
                    ->orWhere('movimiento.descripcion_mov', 'LIKE', "%$search%")
                    ->orWhere('movimiento.id_cliente', 'LIKE', "%$search%");
            });
        }

        $total = $query->count();

        $registros = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Obtener los detalles agrupados por el ID del movimiento
        $movimientoIds = $registros->pluck('id_movimiento')->toArray();

        $detalles = Detalle::whereIn('id_movimiento', $movimientoIds)
            ->get()
            ->groupBy('id_movimiento');

        // Asignar los detalles a cada registro de movimiento
        $registros->each(function ($registro) use ($detalles) {
            $idMovimiento = $registro->id_movimiento;
            $registro->detalles = $detalles->get($idMovimiento);
        });

        return response()->json([
            'data' => $registros,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
        ]);
    }



    public function store(Request $request)
    {
              
        $movimiento = new Movimiento;
        $movimiento->fecha_mov = $request->input('fecha_mov');
        $movimiento->numero_comprobante = $request->input('numero_comprobante');
        $movimiento->tipo_mov = $request->input('tipo_mov');
        $movimiento->descripcion_mov = $request->input('descripcion_mov');
        $movimiento->valor_total_mov = $request->input('valor_total_mov');
        $movimiento->id_usuario = $request->input('id_usuario');
        $movimiento->id_cliente = $request->input('id_cliente');
        $movimiento->id_transacciones = $request->input('id_transacciones');
        $movimiento->ganancia_mov = $request->input('ganancia_mov');

        $movimiento->save();
        return response()->json(['message' => 'Movimiento creado con éxito', 'data' => $movimiento]);
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


    public function productosSalida(Request $request)
    {
        $perPage = intval($request->input('per_page', 10));
        $page = intval($request->input('page', 1));
        $search = $request->input('search');

        $query = Movimiento::where('tipo_mov', 'Salida')
            ->join('detalle', 'movimiento.id_movimiento', '=', 'detalle.id_movimiento')
            ->join('producto', 'detalle.id_producto', '=', 'producto.id_producto')
            ->join('usuario', 'movimiento.id_usuario', '=', 'usuario.id_usuario')
            ->select('producto.nombre_producto', 'producto.modelo_prod', 'producto.marca_prod', 'producto.descripcion_prod', 'movimiento.fecha_mov', 'movimiento.id_usuario', 
            \DB::raw("CONCAT(usuario.nombre_usu, ' ', usuario.apellido_usu) AS nombre_usuario"))
            ->selectRaw('SUM(detalle.cantidad) as total_cantidad')
            ->groupBy('producto.nombre_producto', 'producto.modelo_prod', 'producto.marca_prod', 'producto.descripcion_prod', 'movimiento.fecha_mov', 'movimiento.id_usuario', 'usuario.nombre_usu', 'usuario.apellido_usu')
            ->orderBy('movimiento.fecha_mov', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('producto.nombre_producto', 'like', "%{$search}%")
                    ->orWhere('producto.modelo_prod', 'like', "%{$search}%")
                    ->orWhere('producto.marca_prod', 'like', "%{$search}%")
                    ->orWhere('producto.descripcion_prod', 'like', "%{$search}%")
                    ->orWhere('movimiento.fecha_mov', 'like', "%{$search}%")
                    ->orWhere('movimiento.id_usuario', 'like', "%{$search}%");
            });
        }

        // Obtiene el total de registros sin aplicar la paginación
        $total = $query->count();

        // Aplica la paginación y obtiene los registros
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

    
    




}
