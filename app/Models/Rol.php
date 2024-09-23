<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    // Definir la tabla asociada si el nombre de la tabla no sigue la convención de Laravel
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'user_id_create',
        'user_id_last_update',
        'activo',
    ];

    // Relaciones con otros modelos

    // Relación con el modelo Mantenedor
    public function mantenedor()
    {
        return $this->belongsTo(Mantenedor::class, 'mantenedor_id');
    }

    // Relación con el modelo Mantenedor
    public function privilegio()
    {
        return $this->belongsTo(Privilegio::class, 'privilegio_id');
    }
    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_create');
    }
}
