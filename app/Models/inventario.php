<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventario extends Model
{
    use HasFactory; 
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventario';
    
    protected $primaryKey = 'id_inventario';
}
