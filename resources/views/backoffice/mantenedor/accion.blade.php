@extends('backoffice.layouts.app')

@section('title', 'Proyecto QR | ' . $titulo['singular'])

@section('page-title', 'Mantenedor de ' . $titulo['plural'])

@section('btn-add')
    <button class="btn btn-primary" data-widget="control-sidebar" data-controlsidebar-slide="true" id="add-project">
        <i class="fas fa-plus"></i>
        @if ($titulo['genero'] == 'f')
            Nueva
        @else
            Nuevo
        @endif
        {{ $titulo['singular'] }}
    </button>
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
                <table id="projects-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 20px; text-align: center">ID</th>
                            <th>Nombre</th>
                            <th style="width: 54px; text-align: center">Estado</th>
                            <th style="width: 161px; text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $registro)
                            <tr>
                                <th scope="row" style="text-align: center">{{ $registro->id }}</th>
                                <td>{{ $registro->nombre }}</td>
                                <td style="text-align: center">
                                    {!! $registro->activo
                                        ? '<div class="badge badge-success">Activo</div>'
                                        : '<div class="badge badge-danger">Inactivo</div>' !!}
                                </td>
                                <td style="text-align: center">
                                    <button class="btn btn-dark" onclick="open_modal({{ $registro->id }}, 'ver')"
                                        data-toggle="modal" data-target="#modalAcciones"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-primary" onclick="open_modal({{ $registro->id }}, 'editar')"
                                        data-toggle="modal" data-target="#modalAcciones"><i
                                            class="fas fa-edit"></i></button>
                                    @if ($registro->activo)
                                        <button class="btn btn-secondary"
                                            onclick="open_modal({{ $registro->id }}, 'apagar')" data-toggle="modal"
                                            data-target="#modalAcciones"><i class="fas fa-arrow-down"></i></button>
                                    @else
                                        <button class="btn btn-warning"
                                            onclick="open_modal({{ $registro->id }}, 'encender')" data-toggle="modal"
                                            data-target="#modalAcciones"><i class="fas fa-arrow-up"></i></button>
                                    @endif
                                    <button class="btn btn-danger" onclick="open_modal({{ $registro->id }}, 'eliminar')"
                                        data-toggle="modal" data-target="#modalAcciones"><i
                                            class="fas fa-trash"></i></button>
                                </td>
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
                        @if ($item['inForm'])
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

                                @default
                            @endswitch
                            <label for="floatingInput{{ $item['id'] }}">{{ $item['label'] }}</label>
                        @endif
                    </div>
                @endforeach
                <button type="submit" class="btn btn-primary col-12">Crear</button>
            </form>
        </div>
    </aside>
    <div class="control-sidebar-bg"></div>
    <!-- Modal -->
    <div class="modal fade " id="modalAcciones" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
            $('#projects-table').DataTable({
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
                    propiedades.form.action = @json(route('acciones.update', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Guardar Cambios';
                    propiedades.btn.principal.clase = 'btn-primary';
                    break;
                case 'encender':
                    propiedades.bg = 'bg-warning';
                    propiedades.title = '<i class="fas fa-arrow-up"></i> Activar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('acciones.enable', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Activar ' + @json($titulo['singular']);
                    propiedades.btn.principal.clase = 'btn-warning';
                    propiedades.onlyread = true;
                    break;
                case 'apagar':
                    propiedades.bg = 'bg-secondary';
                    propiedades.title = '<i class="fas fa-arrow-down"></i> Desactivar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('acciones.disable', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Desactivar ' + @json($titulo['singular']);
                    propiedades.btn.principal.clase = 'btn-secondary';
                    propiedades.onlyread = true;
                    break;
                case 'eliminar':
                    propiedades.bg = 'bg-dark';
                    propiedades.title = '<i class="fas fa-arrow-down"></i> Eliminar ' + @json($titulo['singular']);
                    propiedades.form.action = @json(route('acciones.delete', 'xxx')).split('xxx')[0] + _id;
                    propiedades.form.method = 'post';
                    propiedades.btn.principal.html = '<i class="fas fa-save"></i> Eliminar ' + @json($titulo['singular']);
                    propiedades.btn.principal.clase = 'btn-danger';
                    propiedades.onlyread = true;
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

            $.ajax({
                url: `/backoffice/acciones/get/${_id}`,
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
                    // console.log('data');
                    // console.log(data);
                    campos.forEach(campo => {
                        valor = data[campo.name];
                        const row = document.createElement('div');
                        row.classList.add('row');
                        const colD = document.createElement('div');
                        colD.classList.add('col-12');
                        colD.innerHTML = ``;

                        switch (campo.control) {
                            case 'input':
                                if (campo.type == 'file') {
                                    if (propiedades.onlyread) {
                                        if (data.imagen) {
                                            colD.innerHTML +=
                                                `<div class="mb-3" style="background-color: #e9ecef; border: 1px solid lightgray; border-radius: 5px; padding-top: 5px !important; padding-left: 7px; color: gray; font-size: 0.9rem">
                                                    <label style="display: block; background-color: white; width: fit-content; border-radius: 6px; padding-left: 4px; padding-right: 4px">${campo.label}</label>
                                                    <img class="mb-3" style="width: auto; height: 100px;" src="data:image/jpeg;base64, ${data.imagen}" >
                                                </div>`;
                                        }
                                    } else {
                                        if (data.imagen) {
                                            colD.innerHTML +=
                                                `<div class="mb-3" style="background-color: white; border: 1px solid lightgray; border-radius: 5px; padding-top: 5px !important; padding-left: 7px; color: gray; font-size: 0.9rem">
                                                    <label style="display: block;  border-radius: 6px; padding-left: 4px; padding-right: 4px">${campo.label}</label>
                                                    <img class="mb-3" style="width: auto; height: 100px;" src="data:image/jpeg;base64, ${data.imagen}" >
                                                </div>`;
                                        }
                                        colD.innerHTML += `
                                            <div class="form-floating mb-3">
                                                <input id="floatingInput${campo.id}" type="${campo.type}" class="form-control" name="${titulo.singular.toLowerCase()}_${campo.name}" placeholder="${campo.label}"  value="${valor}" ${propiedades.onlyread == true ? 'readonly' : ''} >
                                                <label for="floatingInput${campo.id}">${campo.label}</label></div>
                                            </div>`;
                                    }
                                } else {
                                    colD.innerHTML += `
                                        <div class="form-floating mb-3">
                                            <input id="floatingInput${campo.id}" type="${campo.type}" class="form-control" name="${titulo.name.toLowerCase()}_${campo.name}" placeholder="${campo.label}"  value="${valor}" ${propiedades.onlyread == true ? 'readonly' : ''} >
                                            <label for="floatingInput${campo.id}">${campo.label}</label></div>
                                        </div>`;
                                }

                                break;
                            case 'textarea':
                                colD.innerHTML += `
                                <div class="form-floating mb-3">
                                    <textarea id="floatingInput${campo.id}" class="form-control" placeholder="${campo.label}" style="height: 100px" name="${titulo.name.toLowerCase()}_${campo.name}" ${propiedades.onlyread == true ? 'readonly' : ''} >${data['descripcion']}</textarea>
                                    <label for="floatingInput3">Descripción</label>
                                </div>
                            `;
                                break;
                            default:
                                break;
                        }
                        row.appendChild(colD);
                        modalContenido.appendChild(row);
                    });
                    console.log(data);
                },
                error: function() {
                    alert('Error al obtener los datos del proyecto.');
                }
            });
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

        $(document).ready(function() {
            $('#close-edit-project').on('click', function() {
                $('#aside-edit').controlSidebar('close');
            });
        });
    </script>
@endsection
