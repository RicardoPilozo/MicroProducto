<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalle extends Model
{
    use HasFactory;

    protected $table = 'detalle';
    
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'cantidad',
        'valor_unitario',
        'id_inventario',
        'id_movimiento',
        'id_producto',
    ];
    
}
