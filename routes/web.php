<?php

use App\Http\Controllers\MantenedorController;
use App\Http\Controllers\PrivilegioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Models\Mantenedor;
use App\Models\Privilegio;
use App\Models\Proyecto;
use App\Models\QR;
use App\Models\Rol;
use App\Models\RolMantenedorPrivilegio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index', [
        'proyectos' => Proyecto::all()->where('activo', true)
    ]);
})->name('raiz');

Route::get('/login', [UserController::class, 'formularioLogin'])->name('usuario.login');
Route::post('/login', [UserController::class, 'login'])->name('usuario.validar');

Route::post('/logout', [UserController::class, 'logout'])->name('usuario.logout');

//sin backoffice
Route::get('/users/register', [UserController::class, 'formularioNuevo'])->name('usuario.registrar');
Route::post('/users/register', [UserController::class, 'registrar'])->name('usuario.registrar');
//con backoffice
Route::get('/backoffice/users', [UserController::class, 'index'])->name('usuarios.index');
Route::get('/backoffice/users/get/{_id}', [UserController::class, 'getById']);
Route::post('/backoffice/users/new', [UserController::class, 'create'])->name('usuarios.create');
Route::post('/backoffice/users/down/{_id}', [UserController::class, 'disable'])->name('usuarios.disable');
Route::post('/backoffice/users/up/{_id}', [UserController::class, 'enable'])->name('usuarios.enable');
Route::post('/backoffice/users/update/{_id}', [UserController::class, 'update'])->name('usuarios.update');
Route::post('/backoffice/users/delete/{_id}', [UserController::class, 'delete'])->name('usuarios.delete');

Route::get('/backoffice', function () {
    $user = Auth::user();
    if ($user == NULL) {
        return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
    }
    $proyectosConQRs = Proyecto::with('qrs')->get();
    $user->rol_nombre = Rol::findOrFail($user->rol_id)->nombre;
    //privilegios del Rol en Mantenedor y sus Privilegios
    $allRolMantenedorPrivilegio = RolMantenedorPrivilegio::all()->where('rol_id', $user->rol_id);
    $rolMP = [];
    foreach ($allRolMantenedorPrivilegio as $rmp) {
        $rolMP[$rmp->mantenedor_id][$rmp->privilegio_id] = $rmp->activo;
    }

    return view('backoffice.dashboard', [
        'user' => $user,
        'proyectos' => $proyectosConQRs,
        'mantenedores' => Mantenedor::all(),
        'privilegios' => Privilegio::all(),
        'rolMP' => $rolMP,
    ]);
})->name('backoffice.dashboard');

Route::get('/backoffice/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
Route::get('/backoffice/proyectos/get/{_id}', [ProyectoController::class, 'getById']);
Route::post('/backoffice/proyectos/new', [ProyectoController::class, 'create'])->name('proyectos.create');
Route::post('/backoffice/proyectos/down/{_id}', [ProyectoController::class, 'disable'])->name('proyectos.disable');
Route::post('/backoffice/proyectos/up/{_id}', [ProyectoController::class, 'enable'])->name('proyectos.enable');
Route::post('/backoffice/proyectos/update/{_id}', [ProyectoController::class, 'update'])->name('proyectos.update');
Route::post('/backoffice/proyectos/delete/{_id}', [ProyectoController::class, 'delete'])->name('proyectos.delete');

Route::get('/backoffice/mantenedores', [MantenedorController::class, 'index'])->name('mantenedores.index');
Route::get('/backoffice/mantenedores/get/{_id}', [MantenedorController::class, 'getById']);
Route::post('/backoffice/mantenedores/new', [MantenedorController::class, 'create'])->name('mantenedores.create');
Route::post('/backoffice/mantenedores/down/{_id}', [MantenedorController::class, 'disable'])->name('mantenedores.disable');
Route::post('/backoffice/mantenedores/up/{_id}', [MantenedorController::class, 'enable'])->name('mantenedores.enable');
Route::post('/backoffice/mantenedores/update/{_id}', [MantenedorController::class, 'update'])->name('mantenedores.update');
Route::post('/backoffice/mantenedores/delete/{_id}', [MantenedorController::class, 'delete'])->name('mantenedores.delete');

Route::get('/backoffice/privilegios', [PrivilegioController::class, 'index'])->name('privilegios.index');
Route::get('/backoffice/privilegios/get/{_id}', [PrivilegioController::class, 'getById']);
Route::post('/backoffice/privilegios/new', [PrivilegioController::class, 'create'])->name('privilegios.create');
Route::post('/backoffice/privilegios/down/{_id}', [PrivilegioController::class, 'disable'])->name('privilegios.disable');
Route::post('/backoffice/privilegios/up/{_id}', [PrivilegioController::class, 'enable'])->name('privilegios.enable');
Route::post('/backoffice/privilegios/update/{_id}', [PrivilegioController::class, 'update'])->name('privilegios.update');
Route::post('/backoffice/privilegios/delete/{_id}', [PrivilegioController::class, 'delete'])->name('privilegios.delete');

Route::get('/backoffice/roles', [RolController::class, 'index'])->name('roles.index');
Route::get('/backoffice/roles/get/{_id}', [RolController::class, 'getById']);
Route::post('/backoffice/roles/new', [RolController::class, 'create'])->name('roles.create');
Route::post('/backoffice/roles/down/{_id}', [RolController::class, 'disable'])->name('roles.disable');
Route::post('/backoffice/roles/up/{_id}', [RolController::class, 'enable'])->name('roles.enable');
Route::post('/backoffice/roles/update/{_id}', [RolController::class, 'update'])->name('roles.update');
Route::post('/backoffice/roles/delete/{_id}', [RolController::class, 'delete'])->name('roles.delete');


Route::get('/backoffice/qrs', [QRController::class, 'index'])->name('qrs.index');
Route::get('/backoffice/qrs/get/{_id}', [QRController::class, 'getById']);
Route::post('/backoffice/qrs/new', [QRController::class, 'create'])->name('qrs.create');
Route::post('/backoffice/qrs/down/{_id}', [QRController::class, 'disable'])->name('qrs.disable');
Route::post('/backoffice/qrs/up/{_id}', [QRController::class, 'enable'])->name('qrs.enable');
Route::post('/backoffice/qrs/update/{_id}', [QRController::class, 'update'])->name('qrs.update');
Route::post('/backoffice/qrs/delete/{_id}', [QRController::class, 'delete'])->name('qrs.delete');

Route::get('/redireccion', [QRController::class, 'handleRedireccion'])->name('redireccion');
