<?php

namespace App\Http\Controllers;

use App\Models\Mantenedor;
use App\Models\Privilegio;
use App\Models\Proyecto;
use App\Models\QR;
use App\Models\Rol;
use App\Models\RolMantenedorPrivilegio;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRController extends Controller
{
    private const SINGULAR_MIN = 'qr';
    private const SINGULAR_MAY = 'QR';
    private const PLURAL_MIN = 'qrs';
    private const PLURAL_MAY = 'QRs';

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
                //select
                'id' => 1,
                'name' => 'user_id',
                'label' => 'Usuario',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => false,
                'inNuevo' => false
            ],
            [
                'id' => 2,
                'name' => 'user_nombre',
                'label' => 'Usuario',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => false,
                'inNuevo' => false
            ],
            [
                //select
                'id' => 3,
                'name' => 'proyecto_id',
                'label' => 'Proyecto',
                'control' => 'select', // si es select: tiene options || si es input: tiene type
                'options' => '', // inicialmente vacío, se llena en index
                'required' => false,
                'inVerEnableDisableDelete' => false,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 4,
                'name' => 'proyecto_nombre',
                'label' => 'Proyecto',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => false,
                'inNuevo' => false
            ],
            [
                'id' => 5,
                'name' => 'etiqueta',
                'label' => 'Etiqueta',
                'control' => 'input',
                'type' => 'text',
                'required' => false,
                'inVerEnableDisableDelete' => true,
                'inEditar' => true,
                'inNuevo' => true
            ],
            [
                'id' => 6,
                'name' => 'redireccion',
                'label' => 'Redirección',
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
        $datos = QR::all();

        //prepara la lista de proyectos para ser asignada a la propiedad
        $posicionProyectos = 2;
        $listaProyectos = [];
        foreach (Proyecto::all() as $p) {
            if ($p->activo) {
                array_push($listaProyectos, [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                ]);
            }
        }
        $this->properties['fields'][$posicionProyectos]['options'] = $listaProyectos;

        foreach ($datos as $registro) {
            $registro->user_nombre = User::findOrFail($registro->user_id_create)->nombre;
            $registro->proyecto_nombre = Proyecto::findOrFail($registro->proyecto_id)->nombre;
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
            'mantenedor_id' => 6,
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
            'qr_proyecto_id' => 'required|int',
            'qr_etiqueta' => 'required',
            'qr_redireccion' => 'required',
        ], $this->mensajes);

        try {
            // Insertar el registro en la base de datos
            QR::create([
                'user_id_create' => $user->id,
                'user_id_last_update' => $user->id,
                'proyecto_id' => $_request->qr_proyecto_id,
                'etiqueta' => $_request->qr_etiqueta,
                'redireccion' => $_request->qr_redireccion,
                'activo' => false,
            ]);
            return redirect()->back()->with('success', 'QR creado con éxito.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el QR: ' . $e->getMessage());
        }
    }

    public function getById($_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }
        if ($_id === null) {
            $datos = QR::all();
        } else {
            $datos = QR::findOrFail($_id);
            // Preparar los datos adicionales
            $datos->user_nombre = User::findOrFail($datos->user_id_create)->nombre;
            $datos->proyecto_nombre = Proyecto::findOrFail($datos->proyecto_id)->nombre;
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
        $registro = QR::findOrFail($_id);
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
        $registro = QR::findOrFail($_id);
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
        $registro = QR::findOrFail($_id);
        try {
            $registro->delete();
            return redirect()->route('qrs.index')->with('success', "[id: $registro->id] eliminado con éxito.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el QR: ' . $e->getMessage());
        }
    }

    public function update(Request $_request, $_id)
    {
        $user = Auth::user();
        if ($user == NULL) {
            return redirect()->route('usuario.login')->withErrors(['message' => 'No existe una sesión activa.']);
        }

        $_request->validate([
            'qr_proyecto_id' => 'required|int',
            'qr_etiqueta' => 'required',
            'qr_redireccion' => 'required',
        ], $this->mensajes);

        //busca el registro
        $registro = QR::findOrFail($_id);

        $datos = $_request->only('_token', 'qr_proyecto_id', 'qr_etiqueta', 'qr_redireccion');

        $cambios = 0;

        // solo si es distinto actualiza
        if ($registro->proyecto_id != $datos['qr_proyecto_id']) {
            $registro->proyecto_id = $datos['qr_proyecto_id'];
            $cambios += 1;
        }
        if ($registro->etiqueta != $datos['qr_etiqueta']) {
            $registro->etiqueta = $datos['qr_etiqueta'];
            $cambios += 1;
        }
        if ($registro->redireccion != $datos['qr_redireccion']) {
            $registro->redireccion = $datos['qr_redireccion'];
            $cambios += 1;
        }

        if ($cambios > 0) {
            try {
                $registro->user_id_last_update = $user->id;
                $registro->save();
                return redirect()->route('qrs.index')->with('success', "[id: $registro->id] actualizado con éxito.");
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Error al actualizar el QR: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', "No se realizaron cambios.");
        }
    }
    //3 en md5 = eccbc87e4b5ce2fe28308fd9f2a7baf3

    public function handleRedireccion(Request $request)
    {
        // $id = $request->query('id'); // Captura el parámetro 'id' de la URL
        // $qr = QR::all()->first(function ($registro) use ($id) {
        //     // echo '<p>' . $registro->id . ' - ' . md5($registro->id) . '</p>';
        //     return md5($registro->id) === $id;
        // });
        // echo '<hr>';
        // echo 'handle: ' . $id;
        // echo '<hr>';
        // print_r($qr);
        try {
            $id = $request->query('id'); // Captura el parámetro 'id' de la URL
            // Busca el QR que corresponde al id recibido (hash md5 del ID)
            $qr = QR::all()->first(function ($registro) use ($id) {
                return md5($registro->id) === $id;
            });
            if ($qr && $qr->activo) {
                // Realiza la redirección a la URL almacenada en el QR
                return redirect($qr->redireccion);
            }
            return abort(404); // Si no se encuentra el QR o no está activo
        } catch (\Throwable $th) {
            // Maneja cualquier excepción inesperada
            return abort(406);
        }
    }
}
