<?php

namespace App\Http\Controllers;

use App\Models\Mantenedor;
use App\Models\Privilegio;
use App\Models\Rol;
use App\Models\RolMantenedorPrivilegio;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MantenedorController extends Controller
{

    private const SINGULAR_MIN = 'mantenedor';
    private const SINGULAR_MAY = 'Mantenedor';
    private const PLURAL_MIN = 'mantenedores';
    private const PLURAL_MAY = 'Mantenedores';

    private $properties = [
        'title' => [
            'genero' => 'm',
            'name' =>  self::SINGULAR_MAY,
            'singular' => self::SINGULAR_MAY,
            'plural' => self::PLURAL_MAY,
        ],
        'view' => [
            'index' => 'backoffice.mantenedor.' . self::SINGULAR_MIN
        ],
        'actions' => [
            'new' => '/backoffice/' . self::PLURAL_MIN . '/new',
        ],
        'routes' => [
            'index' => self::PLURAL_MIN . '.index'
        ],
        'fields' => [
            [
                'id' => 1,
                'name' => 'nombre',
                'label' => 'Nombre',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 2,
                'name' => 'icono',
                'label' => 'Icono',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 3,
                'name' => 'ruta',
                'label' => 'Ruta',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => true,
                'inNuevo' => true
            ],
        ]
    ];

    public function index()
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $datos = Mantenedor::all();
        foreach ($datos as $registro) {
            $registro->user_id_create_nombre = User::findOrFail($registro->user_id_create)->nombre;
            $registro->user_id_last_update_nombre = User::findOrFail($registro->user_id_last_update)->nombre;
        }
        $user->rol_nombre = Rol::findOrFail($user->rol_id)->nombre;
        //privilegios del Rol en Mantenedor y sus Privilegios
        $allRolMantenedorPrivilegio = RolMantenedorPrivilegio::all()->where('rol_id', $user->rol_id);
        $rolMP = [];
        foreach ($allRolMantenedorPrivilegio as $rmp) {
            $rolMP[$rmp->mantenedor_id][$rmp->privilegio_id] = $rmp->activo;
        }
        return view($this->properties['view']['index'], [
            'user' => $user,
            'registros' => $datos,
            'action' => $this->properties['actions'],
            'titulo' => $this->properties['title'],
            'campos' => $this->properties['fields'],
            'mantenedor_id' => 3,
            'mantenedores' => Mantenedor::all(),
            'privilegios' => Privilegio::all(),
            'rolMP' => $rolMP,
        ]);
    }

    public function create(Request $_request)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        // Validar la solicitud. USO DE UNIQUE: unique:tabla,campo
        $_request->validate([
            'mantenedor_nombre' => 'required|string|max:255|unique:mantenedores,nombre',
        ], $this->mensajes);

        try {
            // Insertar el registro en la base de datos
            Mantenedor::create([
                'nombre' => $_request->mantenedor_nombre,
                'activo' => false,
                'user_id_create' => $user->id,
                'user_id_last_update' => $user->id,
            ]);
            return redirect()->back()->with('success', 'Proyecto creado con éxito.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el proyecto: ' . $e->getMessage());
        }
    }

    public function getById($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        if ($_id === null) {
            $data = Mantenedor::all();
        } else {
            $data = Mantenedor::findOrFail($_id);
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function enable($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $registro = Mantenedor::findOrFail($_id);
        $registro->user_id_last_update = $user->id;
        $registro->activo = true;
        try {
            $registro->save();
            return redirect()->route($this->properties['routes']['index'])->with('success', $this->getTextToast($this->properties['title']['singular'], 'enable', 'success', $registro->nombre, null));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, null) . $e->getMessage());
        }
    }

    public function disable($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $registro = Mantenedor::findOrFail($_id);
        $registro->user_id_last_update = $user->id;
        $registro->activo = false;
        try {
            $registro->save();
            return redirect()->route($this->properties['routes']['index'])->with('success', $this->getTextToast($this->properties['title']['singular'], 'disable', 'success', $registro->nombre, null));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'disable', 'error', $registro->nombre, null) . $e->getMessage());
        }
    }

    public function delete($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $registro = Mantenedor::findOrFail($_id);
        try {
            $registro->delete();
            return redirect()->route('mantenedores.index')->with('success', "[id: $registro->id] [Registro: $registro->nombre] eliminado con éxito.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'disable', 'error', $registro->nombre, null) . $e->getMessage());
        }
    }

    public function update(Request $_request, $_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }

        $_request->validate([
            'mantenedor_nombre' => 'required|string|max:255',
        ], $this->mensajes);

        //busca el registro
        $registro = Mantenedor::findOrFail($_id);

        $datos = $_request->only('_token', 'mantenedor_nombre');

        $cambios = 0;

        // solo si es distinto actualiza
        if ($registro->nombre != $datos['mantenedor_nombre']) {
            $registro->nombre = $datos['mantenedor_nombre'];
            $cambios += 1;
        }

        if ($cambios > 0) {
            try {
                $registro->user_id_last_update = $user->id;
                $registro->save();
                return redirect()->back()->with('success', 'Registro actualizado con éxito.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', "[id: $registro->id] [Registro: $registro->nombre] no se realizaron cambios.");
        }
    }
}
