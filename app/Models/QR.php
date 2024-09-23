<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QR extends Model
{
    use HasFactory;

    // Definir la tabla asociada si el nombre de la tabla no sigue la convención de Laravel
    protected $table = 'qrs';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'user_id_create',
        'user_id_last_update',
        'proyecto_id',
        'etiqueta',
        'redireccion',
        'activo',
    ];

    // Relaciones con otros modelos

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_create');
    }

    // Relación con el modelo Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

}
