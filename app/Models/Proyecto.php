<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'user_id_create',
        'user_id_last_update',
        'activo',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_create');
    }

    public function qrs(){
        // return $this->hasMany(QR::class)->where('activo', true);
        return $this->hasMany(QR::class); //los obtiene todos
    }
}
