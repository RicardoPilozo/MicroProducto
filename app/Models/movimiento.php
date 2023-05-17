<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimiento extends Model
{
    use HasFactory;
    protected $table = 'movimiento';
    
    protected $primaryKey = 'id_movimiento';
}
