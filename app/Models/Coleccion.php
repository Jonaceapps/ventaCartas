<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coleccion extends Model
{
    use HasFactory;
    protected $table = 'colecciones';
    public function cartas(){
        return $this->belongsToMany(Carta::class,'cartas_colecciones');
    }
}
