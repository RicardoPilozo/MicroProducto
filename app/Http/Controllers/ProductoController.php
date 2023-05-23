<?php

namespace App\Http\Controllers;

use App\Models\producto;
use App\Models\proveedor;
use App\Models\inventario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 10)); // Número de elementos por página, valor por defecto: 10
        $page = intval($request->input('page', 1)); // Página actual, valor por defecto: 1
        $search = $request->input('search'); // Término de búsqueda, opcional

        $query = producto::join('proveedor', 'producto.id_proveedor', '=', 'proveedor.id_proveedor')
            ->join('categoria', 'producto.id_categoria', '=', 'categoria.id_categoria')
            ->leftJoin('inventario', 'producto.id_producto', '=', 'inventario.id_producto')
            ->select('producto.id_producto', 'producto.nombre_producto', 'producto.precio_compra', 'producto.precio_venta1', 'producto.precio_venta2', 'producto.precio_venta3', 'producto.precio_venta4', 'producto.codigo_qr', 'producto.marca_prod', 'producto.modelo_prod', 'producto.codigo_prod', 'producto.descripcion_prod', 'producto.estado_prod', \DB::raw("CONCAT(proveedor.nombre_prove, ' ', proveedor.apellido_prove) AS nombre_proveedor"), 'categoria.nombre_cat', 'inventario.cantidad_inventario')
            ->orderBy('producto.id_producto', 'asc');

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('producto.modelo_prod', 'LIKE', "%$search%")
                    ->orWhere('producto.marca_prod', 'LIKE', "%$search%")
                    ->orWhere('producto.codigo_prod', 'LIKE', "%$search%")
                    ->orWhere('producto.nombre_producto', 'LIKE', "%$search%");
            });
        }

        $total = $query->count();

        if ($total === 0) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

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
        $producto = new Producto;
        $producto->nombre_producto = $request->input('nombre_producto');
        $producto->precio_compra = $request->input('precio_compra');
        $producto->precio_venta1 = $request->input('precio_venta1');
        $producto->precio_venta2 = $request->input('precio_venta2');
        $producto->precio_venta3 = $request->input('precio_venta3');
        $producto->precio_venta4 = $request->input('precio_venta4');
        $producto->codigo_qr = $request->input('codigo_qr');
        $producto->marca_prod = $request->input('marca_prod');
        $producto->modelo_prod = $request->input('modelo_prod');
        $producto->codigo_prod = $request->input('codigo_prod');
        $producto->descripcion_prod = $request->input('descripcion_prod');
        $producto->estado_prod = $request->input('estado_prod');
        $producto->id_proveedor = $request->input('id_proveedor');
        $producto->id_categoria = $request->input('id_categoria');

        $producto->save();
        
        $idProductoCreado = $producto->id_producto;

        $inventario = new Inventario;
        $inventario->estado_inv = 1;
        $inventario->cantidad_inventario = $request->input('cantidad_inventario');
        $inventario->descripcion_inv = $request->input('descripcion_prod');
        $inventario->id_producto = $idProductoCreado;

        $inventario->save();

        return response()->json(['message' => 'Producto agregado exitosamente'], 201);
    
    }
    
    public function buscarPorNombre(Request $request)
    {
        $nombreProducto = $request->input('nombre_producto');

        $productos = Producto::where('nombre_producto', 'LIKE', "%$nombreProducto%")
                    ->get();

        if ($productos->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron productos con el nombre buscado'
            ], 404);
        }

        return response()->json($productos);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_producto)
    {
        $producto = Producto::find($id_producto);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $producto->nombre_producto = $request->input('nombre_producto');
        $producto->precio_compra = $request->input('precio_compra');
        $producto->precio_venta1 = $request->input('precio_venta1');
        $producto->precio_venta2 = $request->input('precio_venta2');
        $producto->precio_venta3 = $request->input('precio_venta3');
        $producto->precio_venta4 = $request->input('precio_venta4');
        $producto->codigo_qr = $request->input('codigo_qr');
        $producto->marca_prod = $request->input('marca_prod');
        $producto->modelo_prod = $request->input('modelo_prod');
        $producto->codigo_prod = $request->input('codigo_prod');
        $producto->descripcion_prod = $request->input('descripcion_prod');
        $producto->estado_prod = $request->input('estado_prod');
        $producto->id_proveedor = $request->input('id_proveedor');
        $producto->id_categoria = $request->input('id_categoria');

        $producto->save();

        return response()->json(['message' => 'Producto actualizado con éxito', 'data' => $producto], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'El producto no existe'
            ], 404);
        }

        $inventario = Inventario::where('id_producto', $id)->get();
        foreach($inventario as $item){
            $item->delete();
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ], 200);
    }
}
