@extends('backoffice.layouts.app')

@section('title', 'Proyecto QR | ' . $titulo['singular'])

@section('page-title', 'Mantenedor de ' . $titulo['plural'])

@section('btn-add')
    @if ($rolMP[$mantenedor_id][2] == 1)
        <button class="btn btn-primary" onclick="open_modal(null, 'new')" data-toggle="modal" data-target="#modalAcciones">
            <i class="fas fa-plus"></i>
            @if ($titulo['genero'] == 'f')
                Nueva
            @else
                Nuevo
            @endif
            {{ $titulo['singular'] }}
        </button>
    @endif
@endsection

@section('css')
    <!-- Custom CSS files here -->
    <style>
        .form-floating label:not(.form-check-label):not(.custom-file-label) {
            color: gray;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 50px !important;
        }

        .btn-cerrar i {
            /* color: white; */
            margin-left: -12px;
            margin-top: 0px;
            font-size: 24px;
        }

        .btn-cerrar:hover i {
            color: white;
        }

        .modal {
            --bs-modal-inner-border-radius: 2px
        }
    </style>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <!-- Mensajes de Éxito -->
                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            launchToastMessage({
                                class: 'bg-success',
                                title: '<i class="fas fa-check"></i>',
                                subtitle: 'Acción completada exitosamente',
                                message: '{{ session('success') }}'
                            });
                        });
                    </script>
                @endif
                <!-- Mensajes de Error -->
                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            launchToastMessage({
                                class: 'bg-danger',
                                title: '<i class="fa fa-exclamation-circle"></i>',
                                subtitle: 'La acción no se realizó',
                                message: '{{ session('error') }}'
                            });
                        });
                    </script>
                @endif
                <!-- Errores de Validación -->
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                launchToastMessage({
                                    class: 'bg-danger',
                                    title: '<i class="fa fa-exclamation-circle"></i>',
                                    subtitle: 'La acción no se realizó',
                                    message: '{{ $error }}'
                                });
                            });
                        </script>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table id="roles-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 20px; text-align: center">ID</th>
                            <th>Nombre</th>
                            <th style="width: 200px;">Fecha Creación</th>
                            <th style="width: 200px;">Fecha Actualización</th>
                            <th style="width: 54px; text-align: center">Estado</th>
                            @if (
                                $rolMP[$mantenedor_id][3] == 1 ||
                                    $rolMP[$mantenedor_id][4] == 1 ||
                                    $rolMP[$mantenedor_id][5] == 1 ||
                                    $rolMP[$mantenedor_id][6] == 1 ||
                                    $rolMP[$mantenedor_id][7] == 1)
                                <th style="width: 161px; text-align: center">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $registro)
                            <tr>
                                <th scope="row" style="text-align: center">{{ $registro->id }}</th>
                                <td>
                                    {{ $registro->nombre }}
                                </td>
                                <td>
                                    {{ $registro->created_at }}
                                    <label class="badge badge-dark">{{ $registro->user_id_create_nombre }}</label>
                                </td>
                                <td>
                                    {{ $registro->updated_at }}
                                    <label class="badge badge-dark">{{ $registro->user_id_last_update_nombre }}</label>
                                </td>
                                <td style="text-align: center">
                                    {!! $registro->activo
                                        ? '<div class="badge badge-success">Activo</div>'
                                        : '<div class="badge badge-danger">Inactivo</div>' !!}
                                </td>
                                @if (
                                    $rolMP[$mantenedor_id][3] == 1 ||
                                        $rolMP[$mantenedor_id][4] == 1 ||
                                        $rolMP[$mantenedor_id][5] == 1 ||
                                        $rolMP[$mantenedor_id][6] == 1 ||
                                        $rolMP[$mantenedor_id][7] == 1)
                                    <td style="text-align: center">
                                        {{-- 3: Ver --}}
                                        @if ($rolMP[$mantenedor_id][3] == 1)
                                            <button class="btn btn-{{ $privilegios[2]->color }}"
                                                onclick="open_modal({{ $registro->id }}, 'ver')" data-toggle="modal"
                                                data-target="#modalAcciones"><i
                                                    class="{{ $privilegios[2]->icono }}"></i></button>
                                        @endif
                                        {{-- 4: Actualizar --}}
                                        @if ($rolMP[$mantenedor_id][4] == 1)
                                            <button class="btn btn-{{ $privilegios[3]->color }}"
                                                onclick="open_modal({{ $registro->id }}, 'editar')" data-toggle="modal"
                                                data-target="#modalAcciones"><i
                                                    class="{{ $privilegios[3]->icono }}"></i></button>
                                        @endif
                                        @if ($registro->activo)
                                            {{-- 6: Apagar --}}
                                            @if ($rolMP[$mantenedor_id][6] == 1)
                                                <button class="btn btn-{{ $privilegios[5]->color }}"
                                                    onclick="open_modal({{ $registro->id }}, 'apagar')" data-toggle="modal"
                                                    data-target="#modalAcciones"><i
                                                        class="{{ $privilegios[5]->icono }}"></i></button>
                                            @endif
                                        @else
                                            {{-- 5: Encender --}}
                                            @if ($rolMP[$mantenedor_id][5] == 1)
                                                <button class="btn btn-{{ $privilegios[4]->color }}"
                                                    onclick="open_modal({{ $registro->id }}, 'encender')"
                                                    data-toggle="modal" data-target="#modalAcciones"><i
                                                        class="{{ $privilegios[4]->icono }}"></i></button>
                                            @endif
                                        @endif
                                        {{-- 7: Eliminar --}}
                                        @if ($rolMP[$mantenedor_id][7] == 1)
                                            <button class="btn btn-{{ $privilegios[6]->color }}"
                                                onclick="open_modal({{ $registro->id }}, 'eliminar')" data-toggle="modal"
                                                data-target="#modalAcciones"><i
                                                    class="{{ $privilegios[6]->icono }}"></i></button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Control Sidebar -->
    <aside id="aside-add" class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <div class="row mb-2">
                <div class="col-10">
                    <h5>
                        <i class="fas fa-plus"></i>
                        @if ($titulo['genero'] == 'f')
                            Nueva
                        @else
                            Nuevo
                        @endif
                        {{ $titulo['singular'] }}
                    </h5>
                </div>
                <div class="col-2 text-end btn-cerrar">
                    <i id="close-add-project" class="fa fa-times-circle"></i>
                </div>
            </div>
            <form method="POST" action="{{ $action['new'] }}" enctype="multipart/form-data">
                @csrf
                @foreach ($campos as $item)
                    <div class="form-floating mb-3">
                        {{-- @if ($item['inNuevo'])
                            @switch($item['control'])
                                @case('input')
                                    <input id="floatingInput{{ $item['id'] }}" type="{{ $item['type'] }}" class="form-control"
                                        name="{{ strtolower($titulo['name']) }}_{{ $item['name'] }}" placeholder=""
                                        @if ($item['required']) required="" @endif>
                                @break

                                @case('textarea')
                                    <textarea id="floatingInput{{ $item['id'] }}" class="form-control" placeholder=""
                                        id="floatingTextarea{{ $item['id'] }}" style="height: 300px"
                                        name="{{ strtolower($titulo['name']) }}_{{ $item['name'] }}"
                                        @if ($item['required']) required="" @endif></textarea>
                                @break

                                @case('select')
                                    <select class="form-select" id="floatingInput{{ $item['id'] }}"
                                        aria-label="Floating label select"
                                        name="{{ strtolower($titulo['name']) }}_{{ $item['name'] }}">
                                        @foreach ($item['options'] as $opcion)
                                            <option value="{{ $opcion['id'] }}">{{ $opcion['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                @break
                            @endswitch
                            <label for="floatingInput{{ $item['id'] }}">{{ $item['label'] }}</label>
                        @endif --}}
                    </div>
                @endforeach
                <button type="submit" class="btn btn-primary col-12">Crear</button>
            </form>
        </div>
    </aside>
    <div class="control-sidebar-bg"></div>
    <!-- Modal -->
    <div class="modal fade modal-center" id="modalAcciones" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="action-form" method="#" action="#" enctype="multipart/form-data">
                    @csrf
                    <div id="modal-header" class="modal-header">
                        <h5 class="modal-title" id="modal-title">Título del Modal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalAcciones-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button id="btn_principal" type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#roles-table').DataTable({
                // Opciones de DataTables
                responsive: true,
                autoWidth: false,
                paging: true,
                searching: true,
            });

            // Mostrar control-sidebar al hacer clic en "Agregar Proyecto"
            $('#add-project').on('click', function() {
                $('#aside-add').addClass('control-sidebar-open');
            });
            $('#close-add-project').on('click', function() {
                $('#aside-add').removeClass('control-sidebar-open').css('display', 'none');
            });

        });

        function open_modal(_id, _accion) {
            const modalAcciones = document.getElementById('modalAcciones');
            const modalTitulo = document.getElementById('modal-title');
            const modalHeader = document.getElementById('modal-header');
            const modalContenedor = document.getElementsByClassName('modal-content')[0];
            const modalContenido = document.getElementById('modalAcciones-body');
            const btnPrincipal = document.getElementById('btn_principal');
            const form = document.getElementById('action-form');
            modalContenido.innerHTML = '';

            const clasesBG = [
                'bg-primary',
                'bg-dark',
                'bg-warning',
                'bg-danger',
                'bg-secondary'
            ];
            const clasesBtn = [
                'd-none',
                'btn-primary',
                'btn-dark',
                'btn-warning',
                'btn-danger',
                'btn-secondary'
            ];

            let propiedades = {
                bg: null,
                title: null,
                form: {
                    action: null,
                    method: null,
                },
                btn: {
                    principal: {
                        html: null,
                        display: null,
                        clases: null
                    }
                },
                onlyread: false
            };

            //eliminar bg del modal
            clasesBG.forEach(clase => {
                modalHeader.classList.remove(clase);
            });
            clasesBtn.forEach(clase => {
                btnPrincipal.classList.remove(clase);
            })

            switch (_accion) {
                case 'ver':
                    propiedades.bg = 'bg-dark';
                    propiedades.title = '<i class="fas fa-eye"></i> Ver ' + @json($titulo['singular']);
                    propiedades.form.action = '#';
                    propiedades.form.method = '#';
                    propiedades.btn.principal.display = 'none';
                    propiedades.btn.principal.clases = 'd-none';
                    propiedades.onlyread = true;
                    break;
                case 'editar':
                    propiedades.bg = 'bg-primary';
                    propiedades.title = '<i class="fas fa-edit"></i> Editar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('roles.update', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Guardar Cambios';
                    propiedades.btn.principal.clase = 'btn-primary';
                    break;
                case 'encender':
                    propiedades.bg = 'bg-warning';
                    propiedades.title = '<i class="fas fa-arrow-up"></i> Activar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('roles.enable', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Activar ' +
                        @json($titulo['singular']);
                    propiedades.btn.principal.clase = 'btn-warning';
                    propiedades.onlyread = true;
                    break;
                case 'apagar':
                    propiedades.bg = 'bg-secondary';
                    propiedades.title = '<i class="fas fa-arrow-down"></i> Desactivar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('roles.disable', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Desactivar ' +
                        @json($titulo['singular']);
                    propiedades.btn.principal.clase = 'btn-secondary';
                    propiedades.onlyread = true;
                    break;
                case 'eliminar':
                    propiedades.bg = 'bg-dark';
                    propiedades.title = '<i class="fas fa-arrow-down"></i> Eliminar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('roles.delete', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Eliminar ' +
                        @json($titulo['singular']);
                    propiedades.btn.principal.clase = 'btn-danger';
                    propiedades.onlyread = true;
                    break;
                case 'new':
                    if (_id == null) {
                        propiedades.bg = 'bg-primary';
                        propiedades.title = '<i class="fas fa-plus"></i> Nuevo ' + @json($titulo['singular']);
                        propiedades.form.action = @json(route('roles.create'));
                        propiedades.form.method = 'post';
                        propiedades.btn.principal.html = '<i class="fas fa-save"></i> Agregar ' +
                            @json($titulo['singular']);
                        propiedades.btn.principal.clase = 'btn-primary';
                        propiedades.onlyread = true;
                    } else {
                        alert('Error, esta intentando agregar un ID');
                    }
                    break;
                default:
                    break;
            }

            //aplicar propiedades
            modalHeader.classList.add(propiedades.bg);
            modalTitulo.innerHTML = propiedades.title;
            btnPrincipal.innerHTML = propiedades.btn.principal.html;
            btnPrincipal.style.display = propiedades.btn.principal.display;
            btnPrincipal.classList.add(propiedades.btn.principal.clase);
            form.setAttribute('action', propiedades.form.action);
            form.setAttribute('method', propiedades.form.method);

            let mantenedor = @json($titulo['plural']);

            if (_id != null) {
                $.ajax({
                    url: `/backoffice/${mantenedor.toLowerCase()}/get/${_id}`,
                    type: 'GET',
                    success: function(data) {
                        data = data.data;
                        if (data.activo == true) {
                            modalContenedor.setAttribute('style', 'border-top: #28a745 6px solid');
                        } else if (data.activo == false) {
                            modalContenedor.setAttribute('style', 'border-top: #dc3545 6px solid');
                        }
                        campos = @json($campos);
                        titulo = @json($titulo);
                        registrosMaM = @json($registrosMaM).filter(registro => registro.rol_id ===
                            _id);

                        campos.forEach(campo => {
                            valor = data[campo.name];
                            const row = document.createElement('div');
                            row.classList.add('row');
                            const colD = document.createElement('div');
                            colD.classList.add('col-12');
                            colD.innerHTML = ``;
                            if (_accion == 'ver' || _accion == 'encender' || _accion == 'apagar' ||
                                _accion == 'eliminar') {

                                if (campo.inVerEnableDisableDelete) {
                                    if (campo.control == 'table') {
                                        // Generar encabezados de la tabla
                                        let headers = `<tr>`;
                                        campo.headers.forEach(d => {
                                            headers +=
                                                `<th class="text-center">${d.nombre}</th>`;
                                        });
                                        headers += `</tr>`;

                                        // Generar cuerpo de la tabla
                                        let tbody = '';
                                        campo.listaMantenedores.forEach(m => {
                                            tbody += `<tr>`;
                                            tbody += `<td>${m.nombre}</td>`;

                                            campo.listaPrivilegios.forEach(p => {
                                                const checkeado = registrosMaM.some(r =>
                                                        r.mantenedor_id == m.id && r
                                                        .privilegio_id == p.id && r
                                                        .activo == 1) ?
                                                    'checked' : '';
                                                tbody += `
                                                    <td class="text-center">
                                                        <input type="checkbox" class="btn-check" id="btn-check-outlined-${m.id}-${p.id}" autocomplete="off" name="privilegios[${m.id}][${p.id}]" ${checkeado} disabled>
                                                        <label class="btn btn-outline-${p.color}" for="btn-check-outlined-${m.id}-${p.id}"><i class="${p.icono}"></i></label><br>
                                                    </td>
                                                `;
                                            });

                                            tbody += `</tr>`;
                                        });

                                        // Renderizar la tabla
                                        colD.innerHTML += `
        <h5>${campo.label}</h5>
        <table class="table table-hover">
            <thead>${headers}</thead>
            <tbody>${tbody}</tbody>
        </table>
    `;
                                    } else {
                                        if (valor == 1) {
                                            colD.innerHTML += `
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput${data.id}" value="Si" disabled>
                                                <label for="floatingInput${data.id}">${campo.label}</label>
                                            </div>
                                            `;
                                        } else if (valor == 0) {
                                            colD.innerHTML += `
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput${data.id}" value="No" disabled>
                                                <label for="floatingInput${data.id}">${campo.label}</label>
                                            </div>
                                            `;
                                        } else {
                                            colD.innerHTML += `
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput${data.id}" value="${valor}" disabled>
                                                <label for="floatingInput${data.id}">${campo.label}</label>
                                            </div>
                                            `;
                                        }
                                    }
                                }
                            } else {
                                if (campo.inEditar) {
                                    switch (campo.control) {
                                        case 'input':
                                            if (campo.type == 'text' || campo.type == 'email') {
                                                // colD.innerHTML = `INPUT Campo: ${campo.name} Editar :${campo.inEditar}`;
                                                colD.innerHTML = `
                                                <div class="form-floating mb-3">
                                                    <input type="${campo.type}" class="form-control" id="floatingInput${campo.id}" name="${titulo.singular.toLowerCase()}_${campo.name}" value="${valor}">
                                                    <label for="floatingInput${campo.id}">${campo.label}</label>
                                                </div>
                                                `;

                                            } else if (campo.type == 'password') {
                                                colD.innerHTML = `
                                            <div class="form-floating mb-3">
                                                <input type="${campo.type}" class="form-control" id="floatingInput${campo.id}" name="${titulo.singular.toLowerCase()}_${campo.name}" value="">
                                                <label for="floatingInput${campo.id}">${campo.label}</label>
                                            </div>
                                            `;
                                            } else if (campo.type == 'file') {
                                                colD.innerHTML = `
                                            <div class="form-floating mb-3">
                                                <input type="${campo.type}" class="form-control" id="floatingInput${campo.id}" name="${titulo.singular.toLowerCase()}_${campo.name}" value="${valor}">
                                                <label for="floatingInput${campo.id}">${campo.label}</label>
                                            </div>
                                            `;
                                            }
                                            break;
                                        case 'select':
                                            // colD.innerHTML = `SELECT Campo: ${campo.name} Editar :${campo.inEditar}`;
                                            opciones = '';
                                            campo.options.forEach(opcion => {
                                                seleccionada = valor == opcion.id ? 'selected' :
                                                    '';
                                                opciones +=
                                                    `<option value="${opcion.id}" ${seleccionada}>${opcion.nombre}</option>`;
                                            });
                                            colD.innerHTML = `
                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="floatingInput${campo.id}" aria-label="Floating label select example" name="${titulo.singular.toLowerCase()}_${campo.name}">
                                                    ${opciones}
                                                </select>
                                                <label for="floatingInput${campo.id}">${campo.label}</label>
                                            </div>
                                            `;
                                            break;
                                        case 'table':
                                            if (campo.control == 'table') {
                                                // Generar encabezados de la tabla
                                                let headers = `<tr>`;
                                                campo.headers.forEach(d => {
                                                    headers +=
                                                        `<th class="text-center">${d.nombre}</th>`;
                                                });
                                                headers += `</tr>`;

                                                // Generar cuerpo de la tabla
                                                let tbody = '';
                                                campo.listaMantenedores.forEach(m => {
                                                    tbody += `<tr>`;
                                                    tbody += `<td>${m.nombre}</td>`;

                                                    campo.listaPrivilegios.forEach(p => {
                                                        const checkeado = registrosMaM
                                                            .some(
                                                                r => r.mantenedor_id ==
                                                                m.id && r
                                                                .privilegio_id == p
                                                                .id && r.activo == 1
                                                            ) ? 'checked' : '';

                                                        tbody += `
                                                            <td class="text-center">
                                                                <input type="checkbox" class="btn-check" id="btn-check-outlined-${m.id}-${p.id}" autocomplete="off" name="privilegios[${m.id}][${p.id}]" ${checkeado}>
                                                                <label class="btn btn-outline-${p.color}" for="btn-check-outlined-${m.id}-${p.id}"><i class="${p.icono}"></i></label><br>
                                                            </td>
                                                        `;
                                                    });

                                                    tbody += `</tr>`;
                                                });

                                                // Renderizar la tabla
                                                colD.innerHTML += `
                                                    <h5>${campo.label}</h5>
                                                    <table class="table table-hover">
                                                        <thead>${headers}</thead>
                                                        <tbody>${tbody}</tbody>
                                                    </table>
                                                `;
                                            }
                                            break;

                                        default:
                                            break;
                                    }
                                }
                            }
                            row.appendChild(colD);
                            modalContenido.appendChild(row);
                        });
                    },
                    error: function(e) {
                        alert('Error al obtener los datos del proyecto.');
                        console.log(e);
                    }
                });
            } else {
                //cuando es uno nuevo
                campos = @json($campos);
                titulo = @json($titulo);
                campos.forEach(campo => {
                    const row = document.createElement('div');
                    row.classList.add('row');
                    const colD = document.createElement('div');
                    colD.classList.add('col-12');
                    colD.innerHTML = ``;
                    if (campo.inNuevo) {
                        switch (campo.control) {
                            case 'input':
                                if (campo.type == 'text') {
                                    colD.innerHTML = `
                                    <div class="form-floating mb-3">
                                        <input type="${campo.type}" class="form-control" id="floatingInput${campo.id}" name="${titulo.singular.toLowerCase()}_${campo.name}">
                                        <label for="floatingInput${campo.id}">${campo.label}</label>
                                    </div>
                                    `;
                                }
                                break;
                            case 'table':
                                //titulos
                                headers = `<tr>`;
                                campo.headers.forEach(d => {
                                    headers += `
                                        <th class="text-center">${d.nombre}</th>
                                        `;
                                });
                                headers += `</tr>`;
                                //cuerpo
                                tbody = '';
                                campo.listaMantenedores.forEach(m => {
                                    tbody += `<tr>`;
                                    tbody += `<td>${m.nombre}</td>`;
                                    campo.listaPrivilegios.forEach(p => {
                                        tbody += `
                                            <td class="text-center">
                                                <input type="checkbox" class="btn-check" id="btn-check-outlined-${m.id}-${p.id}" autocomplete="off" name="privilegios[${m.id}][${p.id}]">
                                                <label class="btn btn-outline-${p.color}" for="btn-check-outlined-${m.id}-${p.id}"><i class="${p.icono}"></i></label><br>
                                            </td>
                                            `;
                                    });
                                    tbody += `</tr>`;
                                });
                                //tabla
                                colD.innerHTML = `
                                        <h5>${campo.label}</h5>
                                        <table class="table table-hover">
                                            <thead>${headers}</thead>
                                            <tbody>${tbody}</tbody>
                                        </table>
                                        `;
                                break;
                            default:
                                break;
                        }
                    }
                    row.appendChild(colD);
                    modalContenido.appendChild(row);
                });
            }


        }

        function launchToastMessage(_params) {
            $(document).Toasts('create', {
                class: _params.class,
                title: _params.title,
                subtitle: _params.subtitle,
                body: _params.message,
                autohide: true,
                delay: 10000,
            });
        }
    </script>
@endsection
