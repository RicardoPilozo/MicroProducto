<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class producto extends Model
{
    use HasFactory; 
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'producto';
    
    protected $primaryKey = 'id_producto';
}
