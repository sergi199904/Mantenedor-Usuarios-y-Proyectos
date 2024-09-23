@extends('backoffice.layouts.app')

@section('title', 'Proyecto QR | Dashboard')

@section('page-title', 'Dashboard | PROYECTOS')

@section('css')
    <!-- Custom CSS files here -->
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .swal2-styled.swal2-confirm {
            margin-top: 5px !important;
            background-color: var(--success);
            width: 100%;
        }

        .swal2-styled.swal2-confirm:hover {
            background-color: var(--green);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            @if (count($proyectos) > 0)
                @foreach ($proyectos as $proyecto)
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-3">
                        <div class="card 
                @if ($proyecto->activo) card-success @else card-danger @endif
                card-outline"
                            style="height: 350px;">
                            <div class="card-header text-center">
                                <h5 style="margin-top: 13px">{{ $proyecto->nombre }}</h5>
                            </div>
                            <div class="card-body" style="overflow-y: auto;">
                                <div class="scroll-container">
                                    <div class="text-center">
                                        <img src="data:image/jpeg;base64,{{ base64_encode($proyecto->imagen) }}"
                                            alt="logo" style="width: auto; height: 75px; background-color: white;"><br>
                                        <label class="bg-primary"
                                            style="margin-top: 5px; width: 40px; height: 40px; border-radius: 50%; padding-top: 8px; text-align: center">{{ count($proyecto->qrs) }}</label>
                                    </div>
                                    <p class="card-text text-justify scroll-container">{{ $proyecto->descripcion }}</p>
                                    {{-- @foreach ($proyecto->qrs as $qr)
                                <li><label class="badge badge-primary"> {{ $qr->etiqueta }}</label> - <a
                                        href="{{ $qr->redireccion }}" target="_blank"><i class="fa fa-link"></i>
                                        Revisar Link</a></li>
                            @endforeach --}}
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary w-100" onclick="datosModal({{ $proyecto->id }})"
                                    data-toggle="modal" @if (count($proyecto->qrs) == 0) disabled @endif
                                    data-target="#modal-xl">Ver Códigos Generados</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    No hay proyectos registrados
                </div>
            @endif


        </div>
    </div>
    <!-- modal -->
    <div class="modal fade" id="modal-xl">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">{titulo}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    <p>{contenido}</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@section('scripts')
    <!-- Custom JS files here -->

    <!-- QR -->
    <script src="{{ asset('dist/js/md5.min.js') }}"></script>
    <script src="{{ asset('dist/js/qr.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // Inicializa el arreglo que contendrá los proyectos
        const listaProyectos = [];

        @foreach ($proyectos as $proyecto)
            // Agrega cada proyecto al arreglo, incluyendo la cuenta de QRs
            listaProyectos.push({
                id: '{{ $proyecto->id }}',
                nombre: '{{ $proyecto->nombre }}',
                activo: {{ $proyecto->activo == 1 ? 'true' : 'false' }},
                qrs: [
                    @foreach ($proyecto->qrs as $qr)
                        {
                            id: '{{ $qr->id }}',
                            etiqueta: '{{ $qr->etiqueta }}',
                            redireccion: '{{ $qr->redireccion }}',
                            fecha_creado: '{{ $qr->created_at }}',
                            fecha_actualizado: '{{ $qr->updated_at }}',
                            activo: {{ $qr->activo == 1 ? 'true' : 'false' }},
                        },
                    @endforeach
                ]
            });
        @endforeach
    </script>

    <script>
        function datosModal(_id) {
            const titulo = document.getElementById('modal-title');
            const body = document.getElementById('modal-body');
            const proyecto = listaProyectos.find(p => p.id == _id);

            titulo.innerText = `Detalle del Proyecto ${proyecto.nombre}`;
            body.innerHTML = '';
            proyecto.qrs.forEach(qr => {
                const colorTarjeta = qr['activo'] == true ? 'success' : 'danger';

                //acciones q queramos en el mostrar el qr
                body.innerHTML += `
                    <div class="card card-${colorTarjeta} card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-3 text-center">
                                    <img id="QR${qr['id']}" style="width: 150px; height: 150px">
                                </div>
                                <div class="col-9">
                                    <p style="margin-bottom: 8px; ">Etiqueta: <label class="badge badge-primary">${qr['etiqueta']}</label></p>
                                    <p>Creado: <b>${qr['fecha_creado']}</b></p>
                                    <p>Actualizado: <b>${qr['fecha_actualizado']}</b></p>
                                    <p>Redirección: <b><a href="${qr['redireccion']}" target="_blank"></b>${qr['redireccion']}</a></p>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex">
                            <button class="btn btn-primary w-100" style="margin-right: 5px" onclick="descargarContenido('${qr.id}', '${proyecto.nombre}-${qr.etiqueta}')"><i
                                    class="fas fa-download"></i> Descargar</button>
                            <button class="btn btn-primary w-100" style="margin-left: 5px" onclick="copiarContenido('${qr.id}')"><i
                                    class="fas fa-copy"></i> Copiar en Portapapeles</button>
                        </div>
                    </div>
                    `;
                generarQR(qr['id']);
            });
        }

        function generarQR(_id) {
            new QRious({
                element: document.querySelector("#QR" + _id),
                value: window.location.protocol + "//" + window.location.host + "/redireccion/?id=" + md5(
                    _id), // La URL o el texto
                size: 1200,
                backgroundAlpha: 0, // 0 para fondo transparente
                foreground: "#000", // Color del QR
                level: "L", // Puede ser L,M,Q y H (L es el de menor nivel, H el mayor)
            });
        }

        async function descargarContenido(_id, _nombre) {
            _nombre = textToSlug(_nombre);
            const img = document.querySelector(`#QR${_id}`);
            //simular clic
            let link = document.createElement('a');
            link.href = img.src;
            link.download = 'qr_' + _nombre + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        async function copiarContenido(_id) {
            const img = document.querySelector(`#QR${_id}`);
            // console.log(img);
            const blob = await fetch(img.src).then(r => r.blob())
            const item = new ClipboardItem({
                'image/png': blob
            })

            navigator.clipboard.write([item])
                .then(() => {
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        showConfirmButton: true,
                        timer: 500000
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Imagen copiada en el portapapeles, podrás pegar directamente usando CTRL+V'
                    })
                })
                .catch(err => {
                    console.error('Error al copiar al portapapeles:', err)
                })
        }

        function textToSlug(_text) {
            // Convertir a minúsculas
            let slug = _text.toLowerCase();

            // Primero eliminar caracteres especiales y puntuación, pero dejar guiones
            slug = slug.replace(/[\!\?\¿\.,\/#!$%\^&\*;:{}=_`~()]/g, "");

            // Convertir guiones a guiones bajos
            slug = slug.replace(/-/g, '_');

            // Eliminar espacios extras y reemplazarlos por guiones bajos
            slug = slug.replace(/\s+/g, '_');

            return slug;
        }
    </script>
@endsection
