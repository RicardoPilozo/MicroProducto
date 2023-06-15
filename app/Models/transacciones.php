<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transacciones extends Model
{
    use HasFactory;

    protected $table = 'transacciones';
    
    protected $primaryKey = 'id_transacciones';
}