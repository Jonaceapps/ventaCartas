<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carta extends Model
{
    use HasFactory;
    protected $table = 'cartas';
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function colecciones(){
        return $this->belongsToMany(Coleccion::class,'cartas_colecciones');
    }
}
