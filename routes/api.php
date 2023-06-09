<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\DetalleController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TransaccionesController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ReportesController;

Route::get('/producto', [ProductoController::class, 'index']);
Route::post('/aggProducto', [ProductoController::class, 'store']);
Route::get('/buscarProducto', [ProductoController::class, 'buscarPorNombre']);
Route::put('/actualizarProducto/{id_producto}', [ProductoController::class, 'update']);
Route::delete('/eliminarProducto/{id_producto}', [ProductoController::class, 'destroy']);
Route::get('marcasProductos', [ProductoController::class, 'marcas']);


Route::get('/inventario', [InventarioController::class, 'index']);
Route::post('/inventario', [InventarioController::class, 'store']);
Route::get('/inventario/{idInventario}', [InventarioController::class, 'show']);
Route::put('/inventario/{inventario}', [InventarioController::class, 'update']);
Route::delete('/inventario/{inventario}', [InventarioController::class, 'destroy']);
Route::patch('/inventario/actualizar', [InventarioController::class, 'actualizarCantidadInventario']);


Route::get('/proveedores', [ProveedorController::class, 'index']);
Route::post('/proveedores', [ProveedorController::class, 'store']);
Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show']);
Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy']);


Route::get('/movimientos', [MovimientoController::class, 'index']);
Route::get('/movimientosSalida', [MovimientoController::class, 'indexSalidas']);
Route::get('/movimientosSalidaFecha/{fecha}', [MovimientoController::class, 'indexSalidasFecha']);
Route::post('/movimientos', [MovimientoController::class, 'store']);
Route::get('/movimientos/{movimiento}', [MovimientoController::class, 'show']);
Route::put('/movimientos/{movimiento}', [MovimientoController::class, 'update']);
Route::delete('/movimientos/{movimiento}', [MovimientoController::class, 'destroy']);
Route::get('/MovProductosSalida', [MovimientoController::class, 'productosSalida']);


Route::get('/detalle', [DetalleController::class, 'index']);
Route::post('/detalle', [DetalleController::class, 'store']);
Route::get('/detalle/{detalle}', [DetalleController::class, 'show']);
Route::put('/detalle/{id_detalle}', [DetalleController::class, 'update']);
Route::delete('/detalle/{detalle}', [DetalleController::class, 'destroy']);


Route::get('/categoria', [CategoriaController::class, 'index']);
Route::post('/categoria', [CategoriaController::class, 'store']);
Route::put('/categoria/{id_categoria}', [CategoriaController::class, 'update']);
Route::delete('/categoria/{id_categoria}', [CategoriaController::class, 'destroy']);


Route::get('/clientes', [ClienteController::class, 'index']);
Route::get('/cliente/{cedula}', [ClienteController::class, 'ObtenerClienteCedula']);
Route::post('/clientes', [ClienteController::class, 'store']);
Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);

Route::get('/transacciones', [TransaccionesController::class, 'index']);
Route::post('/transacciones', [TransaccionesController::class, 'store']);
Route::get('/transacciones/por-semana', [TransaccionesController::class, 'getTransaccionesporSemana']);
Route::get('/transacciones/semanas-meses', [TransaccionesController::class, 'obtenerSemanasMeses']);


Route::get('/devolucion', [DevolucionController::class, 'index']);
Route::post('/devolucion', [DevolucionController::class, 'store']);


Route::get('/reporteGanancia', [ReportesController::class, 'obtenerGananciasSemanales']);
Route::get('/reporteGananciaMes', [ReportesController::class, 'obtenerGananciasMes']);
Route::get('/topProductosVendidosPorMes', [ReportesController::class, 'topProductosVendidosPorMes']);