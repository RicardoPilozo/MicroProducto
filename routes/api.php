<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\DetalleController;


Route::get('/Producto', [ProductoController::class, 'index']);
Route::post('/AggProducto', [ProductoController::class, 'store']);
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





Route::get('/detalle', [DetalleController::class, 'index']);
