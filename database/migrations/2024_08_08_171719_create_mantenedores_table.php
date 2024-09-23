<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('icono');
            $table->string('ruta');
            $table->boolean('activo')->default(false);
            $table->unsignedBigInteger('user_id_create');
            $table->unsignedBigInteger('user_id_last_update');
            $table->foreign('user_id_create')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_last_update')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Agregar datos a la tabla recién creada
        DB::table('mantenedores')->insert([
            [
                'nombre' => 'Usuarios',
                'icono' => 'fa fa-users',
                'ruta' => 'usuarios.index',
                'user_id_create' => 1,
                'user_id_last_update' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Proyectos',
                'icono' => 'fa fa-cube',
                'ruta' => 'proyectos.index',
                'user_id_create' => 1,
                'user_id_last_update' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Mantenedores',
                'icono' => 'fa fa-cube',
                'ruta' => 'mantenedores.index',
                'user_id_create' => 1,
                'user_id_last_update' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Privilegios',
                'icono' => 'fa fa-cube',
                'ruta' => 'privilegios.index',
                'user_id_create' => 1,
                'user_id_last_update' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Roles',
                'icono' => 'fa fa-cube',
                'ruta' => 'roles.index',
                'user_id_create' => 1,
                'user_id_last_update' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'QRs',
                'icono' => 'fas fa-qrcode',
                'ruta' => 'qrs.index',
                'user_id_create' => 1,
                'user_id_last_update' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenedores');
    }
};
