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

class RolController extends Controller
{
    private const SINGULAR_MIN = 'rol';
    private const SINGULAR_MAY = 'Rol';
    private const PLURAL_MIN = 'roles';
    private const PLURAL_MAY = 'Roles';

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
            'new' => '/backoffice/roles/new',
        ],
        'routes' => [
            'index' => self::PLURAL_MIN . '.index'
        ],
        'fields' => [
            [
                'id' => 1,
                'name' => 'nombre',
                'label' => 'Nombre del ' . self::SINGULAR_MAY,
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 2,
                'control' => 'table',
                'label' => 'Privilegios del ' . self::SINGULAR_MAY,
                'headers' => [
                    [
                        'id' => 0,
                        'nombre' => 'Mantenedores'
                    ],
                ],
                'listaMantenedores' => [],
                'listaPrivilegios' => [],
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
        $datos = Rol::all();
        //prepara la lista para ser asignada a la propiedad
        $mantenedor = [
            "posicion" => 1,
            "lista" => []
        ];
        foreach (Mantenedor::all() as $registro) {
            if ($registro->activo) {
                array_push($mantenedor['lista'], [
                    'id' => $registro->id,
                    'nombre' => $registro->nombre,
                ]);
            }
        }
        $this->properties['fields'][$mantenedor['posicion']]['listaMantenedores'] = $mantenedor['lista'];
        //prepara la lista para ser asignada a la propiedad
        $privilegio = [
            'lista' => []
        ];
        foreach (Privilegio::all() as $registro) {
            if ($registro->activo) {
                array_push($this->properties['fields'][$mantenedor['posicion']]['listaPrivilegios'], [
                    'id' => $registro->id,
                    'nombre' => $registro->nombre,
                    'icono' => $registro->icono,
                    'color' => $registro->color,
                ]);
                array_push($this->properties['fields'][$mantenedor['posicion']]['headers'], [
                    'id' => $registro->id,
                    'nombre' => $registro->nombre,
                ]);
            }
        }

        //recupera datos del usuario
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
        //es distinto a los demas
        return view($this->properties['view']['index'], [
            'user' => $user,
            'registros' => $datos,
            'registrosMaM' => RolMantenedorPrivilegio::all(), //registros muchos a muchos
            'action' => $this->properties['actions'],
            'titulo' => $this->properties['title'],
            'campos' => $this->properties['fields'],
            'mantenedor_id' => 5,
            'mantenedores' => Mantenedor::all(),
            'privilegios' => Privilegio::all(),
            'rolMP' => $rolMP,
        ]);
    }

    public function getById($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }

        if ($_id === null) {
            // Si $_id es nulo, obtén todos los roles
            $datos = Rol::all();
        } else {
            // Encuentra el rol por su ID
            $datos = Rol::findOrFail($_id);

            // Obtener los registros asociados en RolMantenedorPrivilegio
            $datos->registrosMaM = RolMantenedorPrivilegio::where('rol_id', $_id)->get();

            // Obtener nombres de los usuarios que crearon y actualizaron el rol
            $datos->user_id_create_nombre = User::find($datos->user_id_create)->nombre ?? 'Desconocido';
            $datos->user_id_last_update_nombre = User::find($datos->user_id_last_update)->nombre ?? 'Desconocido';
        }

        return response()->json([
            'data' => $datos
        ]);
    }


    public function enable($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $registro = Rol::findOrFail($_id);
        $registro->user_id_create = $user->id;
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
        $registro = Rol::findOrFail($_id);
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
        $registro = Rol::findOrFail($_id);
        try {
            $registro->delete();
            return redirect()->route('roles.index')->with('success', "[id: $registro->id] [Registro: $registro->nombre] eliminado con éxito.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'delete', 'error', $registro->nombre, null) . $e->getMessage());
        }
    }

    public function create(Request $_request)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }

        // Validar la solicitud. USO DE UNIQUE: unique:tabla,campo
        $_request->validate([
            'rol_nombre' => 'required|unique:roles,nombre',
            'privilegios' => 'required'
        ], $this->mensajes);

        $nombreRol = $_request->input('rol_nombre');
        $privilegios = $_request->input('privilegios', []);

        try {
            // Crear el nuevo rol
            $rol = Rol::create([
                'nombre' => $nombreRol,
                'user_id_create' => $user->id,
                'user_id_last_update' => $user->id,
                'activo' => true
            ]);

            // Recorrer todos los mantenedores y privilegios
            $allMantenedores = Mantenedor::all();
            $allPrivilegios = Privilegio::all();

            foreach ($allMantenedores as $mantenedor) {
                foreach ($allPrivilegios as $privilegio) {
                    $activo = isset($privilegios[$mantenedor->id][$privilegio->id]);
                    try {
                        // Insertar el registro en la tabla `rol_mantenedor_privilegio`
                        RolMantenedorPrivilegio::create([
                            'rol_id' => $rol->id,
                            'mantenedor_id' => $mantenedor->id,
                            'privilegio_id' => $privilegio->id,
                            'user_id_create' => $user->id,
                            'user_id_last_update' => $user->id,
                            'activo' => $activo
                        ]);
                    } catch (Exception $e) {
                        return redirect()->back()->with('error', 'Error al crear el rol: ' . $e->getMessage());
                    }
                }
            }
            return redirect()->back()->with('success', 'Rol creado con éxito.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el rol: ' . $e->getMessage());
        }
    }


    public function update(Request $_request, $_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }

        $_request->validate([
            'rol_nombre' => 'required',
            'privilegios' => 'required'
        ], $this->mensajes);

        // Buscar el registro
        $registro = Rol::findOrFail($_id);

        $nombreRol = $_request->input('rol_nombre');
        $privilegios = $_request->input('privilegios', []);

        // Verificar si hay cambios en el nombre del rol
        if ($registro->nombre != $nombreRol) {
            $registro->nombre = $nombreRol;
        }

        try {
            // Actualizar el registro
            $registro->user_id_last_update = $user->id;
            $registro->save();

            // Recorrer todos los mantenedores y privilegios
            $allMantenedores = Mantenedor::all();
            $allPrivilegios = Privilegio::all();

            foreach ($allMantenedores as $mantenedor) {
                foreach ($allPrivilegios as $privilegio) {
                    $activo = false;

                    // Verificar si el privilegio está en los datos enviados
                    if (isset($privilegios[$mantenedor->id][$privilegio->id])) {
                        $activo = true;
                    }

                    // Buscar o crear el registro en la tabla rol_mantenedor_privilegio
                    $rolMantenedorPrivilegio = RolMantenedorPrivilegio::updateOrCreate(
                        [
                            'rol_id' => $_id,
                            'mantenedor_id' => $mantenedor->id,
                            'privilegio_id' => $privilegio->id
                        ],
                        [
                            'user_id_last_update' => $user->id,
                            'activo' => $activo
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', "[id: $registro->id] [Registro: $registro->nombre] actualizado con éxito.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }
}
