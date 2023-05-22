<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\DetalleController;


Route::get('/producto', [ProductoController::class, 'index']);
Route::post('/aggProducto', [ProductoController::class, 'store']);
Route::get('/buscarProducto', [ProductoController::class, 'buscarPorNombre']);
Route::put('/actualizarProducto/{id_producto}', [ProductoController::class, 'update']);
Route::delete('/eliminarProducto/{id_producto}', [ProductoController::class, 'destroy']);


Route::get('/inventario', [InventarioController::class, 'index']);
Route::post('/inventario', [InventarioController::class, 'store']);
Route::get('/inventario/{idInventario}', [InventarioController::class, 'show']);
Route::put('/inventario/{inventario}', [InventarioController::class, 'update']);
Route::delete('/inventario/{inventario}', [InventarioController::class, 'destroy']);


Route::get('/proveedores', [ProveedorController::class, 'index']);
Route::post('/proveedores', [ProveedorController::class, 'store']);
Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show']);
Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy']);


Route::get('/movimientos', [MovimientoController::class, 'index']);
Route::post('/movimientos', [MovimientoController::class, 'store']);
Route::get('/movimientos/{movimiento}', [MovimientoController::class, 'show']);
Route::put('/movimientos/{movimiento}', [MovimientoController::class, 'update']);
Route::delete('/movimientos/{movimiento}', [MovimientoController::class, 'destroy']);


Route::get('/detalle', [DetalleController::class, 'index']);
Route::post('/detalle', [DetalleController::class, 'store']);
Route::get('/detalle/{detalle}', [DetalleController::class, 'show']);
Route::put('/detalle/{detalle}', [DetalleController::class, 'update']);
Route::delete('/detalle/{detalle}', [DetalleController::class, 'destroy']);




