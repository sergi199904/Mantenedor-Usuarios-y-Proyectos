<?php

namespace App\Http\Controllers;

use App\Models\Mantenedor;
use App\Models\Privilegio;
use App\Models\Rol;
use App\Models\RolMantenedorPrivilegio;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    //firs user: update users set activo = true where id = 1

    private const SINGULAR_MIN = 'usuario';
    private const SINGULAR_MAY = 'Usuario';
    private const PLURAL_MIN = 'usuarios';
    private const PLURAL_MAY = 'Usuarios';

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
            'new' => '/backoffice/users/new',
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
                'name' => 'email',
                'label' => 'Correo',
                'control' => 'input',
                'type' => 'email',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 3,
                'name' => 'password',
                'label' => 'Contraseña',
                'control' => 'input',
                'type' => 'password',
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 4,
                'name' => 'rePassword',
                'label' => 'Reingrese Contraseña',
                'control' => 'input', // si es select: tiene options || si es input: tiene type
                'type' => 'password', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 5,
                'name' => 'dayCode',
                'label' => 'Código del Día',
                'control' => 'input', // si es select: tiene options || si es input: tiene type
                'type' => 'password', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => false,
                'inNuevo' => false
            ],
            [
                'id' => 6,
                'name' => 'imagen',
                'label' => 'Imagen',
                'control' => 'input', // si es select: tiene options || si es input: tiene type
                'type' => 'file', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => true,
                'inNuevo' => false
            ],
            [
                //select
                'id' => 7,
                'name' => 'rol_id',
                'label' => 'Rol',
                'control' => 'select', // si es select: tiene options || si es input: tiene type
                'options' => '', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                //select
                'id' => 8,
                'name' => 'rol_id_nombre',
                'label' => 'Rol',
                'control' => 'input', // si es select: tiene options || si es input: tiene type
                'type' => 'text', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => false,
                'inNuevo' => false
            ],
            [
                'id' => 9,
                'name' => 'updated_at',
                'label' => 'Actualizado',
                'control' => 'input', // si es select: tiene options || si es input: tiene type
                'type' => 'text', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => false,
                'inNuevo' => false
            ],
        ]
    ];

    public function formularioLogin()
    {
        if (Auth::check()) {
            return redirect()->route('backoffice.dashboard');
        }
        return view('usuario.login');
    }

    public function formularioNuevo()
    {
        if (Auth::check()) {
            return redirect()->route('backoffice.dashboard');
        }
        return view('usuario.create');
    }

    public function login(Request $_request)
    {

        $_request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ], $this->mensajes);

        $credenciales = $_request->only('email', 'password');

        // var_dump($credenciales);

        if (Auth::attempt($credenciales)) {
            //verifica el usuario activo
            $user = Auth::user();
            if (!$user->activo) {
                Auth::logout();
                return redirect()->route('usuario.login')->withErrors(['email' => 'El usuario se encuentra desactivado.']);
            }
            //Autenticacion exitosa
            $_request->session()->regenerate();
            return redirect()->route('backoffice.dashboard');
        }
        // echo 'siempre';
        return redirect()->back()->withErrors(['email' => 'El usuario o contraseña son incorrectos.']);
    }

    public function logout(Request $_request)
    {
        Auth::logout();
        $_request->session()->invalidate();
        $_request->session()->regenerateToken();
        return redirect()->route('usuario.login');
        // return redirect()->route('raiz');
    }

    public function registrar(Request $_request)
    {
        $_request->validate([
            'nombre' => 'required|string|max:50',
            'email' => 'required|unique:users,email',
            'password' => 'required|string',
            'rePassword' => 'required|string',
            'dayCode' => 'required|string',
            'terms' => 'required',
        ], $this->mensajes);

        $datos = $_request->only('nombre', 'email', 'password', 'rePassword', 'dayCode', 'terms');

        if ($datos['password'] != $datos['rePassword']) {
            return back()->withErrors(['message' => 'Las contraseñas ingresadas no son iguales.']);
        }

        //código de la semana o día
        date_default_timezone_set('UTC');

        if ($datos['dayCode'] != date("d") . '-abcD') {
            return back()->withErrors(['message' => 'El código del día no corresponde.']);
        }

        try {
            User::create([
                'nombre' => $datos['nombre'],
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'activo' => true,
            ]);
            return redirect()->route('usuario.login')->with('success', 'Usuario creado con éxito.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return back()->withErrors(['message' => 'Error al crear el miembro, el email ya existe.']);
            }
            return back()->withErrors(['message' => 'Error desconocido: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $datos = User::all();

        foreach ($datos as $registro) {
            $registro->rol_nombre = Rol::findOrFail($registro->rol_id)->nombre;
        }

        //prepara la lista de Roles para ser asignada a la propiedad
        $posicionRoles = 6;
        $listaRoles = [];
        foreach (Rol::all() as $r) {
            if ($r->activo) {
                array_push($listaRoles, [
                    'id' => $r->id,
                    'nombre' => $r->nombre,
                ]);
            }
        }
        $this->properties['fields'][$posicionRoles]['options'] = $listaRoles;
        $user->rol_nombre = Rol::findOrFail($user->rol_id)->nombre;
        //privilegios del Rol en Mantenedor y sus Privilegios
        $allRolMantenedorPrivilegio = RolMantenedorPrivilegio::all()->where('rol_id', $user->rol_id);
        $rolMP = [];
        foreach ($allRolMantenedorPrivilegio as $rmp) {
            $rolMP[$rmp->mantenedor_id][$rmp->privilegio_id] = $rmp->activo;
        }
        //si tengo permisos para ver, se muestra, sino error
        return view($this->properties['view']['index'], [
            'user' => $user,
            'registros' => $datos,
            'action' => $this->properties['actions'],
            'titulo' => $this->properties['title'],
            'campos' => $this->properties['fields'],
            'mantenedor_id' => 1,
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
            $datos = User::all();
            $datos->each(function ($item) {
                if ($item->imagen) {
                    $item->imagen = base64_encode($item->imagen);
                }
            });
        } else {
            $datos = User::findOrFail($_id);
            if ($datos->imagen) {
                $datos->imagen = base64_encode($datos->imagen);
            }
        }
        $datos->rol_id_nombre = Rol::find($datos->rol_id)->nombre;
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
        $registro = User::findOrFail($_id);

        if ($registro->id != $user->id) {
            $registro->activo = true;
            try {
                $registro->save();
                return redirect()->route($this->properties['routes']['index'])->with('success', $this->getTextToast($this->properties['title']['singular'], 'enable', 'success', $registro->nombre, null));
            } catch (Exception $e) {
                return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, null) . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, 'El usuario no puede realizar acciones sobre si mismo'));
        }
    }

    public function disable($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $registro = User::findOrFail($_id);
        if ($registro->id != $user->id) {
            $registro->activo = false;
            try {
                $registro->save();
                return redirect()->route($this->properties['routes']['index'])->with('success', $this->getTextToast($this->properties['title']['singular'], 'disable', 'success', $registro->nombre, null));
            } catch (Exception $e) {
                return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, null) . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, 'El usuario no puede realizar acciones sobre si mismo'));
        }
    }

    public function delete($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        $registro = User::findOrFail($_id);
        if ($registro->id != $user->id) {
            try {
                $registro->delete();
                return redirect()->route($this->properties['routes']['index'])->with('success', $this->getTextToast($this->properties['title']['singular'], 'delete', 'success', $registro->nombre, null));
            } catch (Exception $e) {
                return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, null) . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'enable', 'error', $registro->nombre, 'El usuario no puede realizar acciones sobre si mismo'));
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
            'usuario_nombre' => 'required',
            'usuario_email' => 'required|unique:users,email',
            'usuario_password' => 'required',
            'usuario_rol_id' => 'required',
        ], $this->mensajes);

        $datos = $_request->only('usuario_nombre', 'usuario_email', 'usuario_password', 'usuario_rePassword', 'usuario_rol_id');

        if ($datos['usuario_password'] != $datos['usuario_rePassword']) {
            return back()->withErrors(['message' => 'Las contraseñas ingresadas no son iguales.']);
        }

        try {
            // Insertar el registro en la base de datos
            User::create([
                'nombre' => $_request->usuario_nombre,
                'email' => $_request->usuario_email,
                'password' => Hash::make($_request->usuario_password),
                'rol_id' => $_request->usuario_rol_id,
            ]);
            return redirect()->back()->with('success', $this->getTextToast($this->properties['title']['singular'], 'create', 'success', $_request->usuario_nombre, null));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $this->getTextToast($this->properties['title']['singular'], 'create', 'error', $_request->usuario_nombre, null) . $e->getMessage());
        }
    }

    public function update(Request $_request, $_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }

        $_request->validate([
            'usuario_nombre' => 'required|string',
            'usuario_email' => 'required|string',
            // 'usuario_password' => 'required|string',
            'usuario_imagen' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'usuario_rol_id' => 'required',
        ], $this->mensajes);

        //busca el proyecto
        $registro = User::findOrFail($_id);

        $datos = $_request->only('_token', 'usuario_nombre', 'usuario_email', 'usuario_password', 'usuario_rePassword', 'usuario_imagen', 'usuario_rol_id');

        $cambios = 0;

        // solo si es distinto actualiza
        if ($registro->nombre != $datos['usuario_nombre']) {
            $registro->nombre = $datos['usuario_nombre'];
            $cambios++;
        }
        if ($registro->email != $datos['usuario_email']) {
            $registro->email = $datos['usuario_email'];
            $cambios++;
        }
        if ($datos['usuario_password'] != '') {
            if ($datos['usuario_password'] != $datos['usuario_rePassword']) {
                return back()->withErrors(['message' => 'Las contraseñas ingresadas no son iguales.']);
            } else {
                $registro->password = $datos['usuario_password'];
                $cambios++;
            }
        }
        if ($registro->rol_id != $datos['usuario_rol_id']) {
            $registro->rol_id = $datos['usuario_rol_id'];
            $cambios++;
        }

        try {
            if ($registro->imagen != $datos['usuario_imagen']) {
                // Manejar la carga de la imagen
                $image = $_request->file('usuario_imagen');
                $imageData = file_get_contents($image);
                $registro->imagen = $imageData;
                $cambios += 1;
            }
        } catch (\Throwable $th) {
        }

        if ($cambios > 0) {
            try {
                $registro->save();
                return redirect()->route('usuarios.index')->with('success', "[id: $registro->id] [Usuario: $registro->nombre] actualizado con éxito.");
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Error al actualizar el proyecto: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', "[id: $registro->id] [Usuario: $registro->nombre] no se realizaron cambios.");
        }
    }
}
