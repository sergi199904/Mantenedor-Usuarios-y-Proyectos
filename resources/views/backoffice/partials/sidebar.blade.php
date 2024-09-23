<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ Route('backoffice.dashboard') }}" class="brand-link">
        {{-- <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
        <span class="brand-image img-circle elevation-3"
            style="opacity: .8; margin-top: 0px; margin-left: 16px; font-size: 30px">
            <i class="fas fa-qrcode"></i>
        </span>
        <span class="brand-text font-weight-light">Tech Solutions</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if ($user->imagen)
                    <img src="data:image/jpeg;base64,{{ base64_encode($user->imagen) }}" class="img-circle elevation-1"
                        alt="User"
                        style="margin-top: 8px; margin-left: -4px; width: 2.5rem; background-color: white">
                @else
                    <img src="{{ asset('dist/img/imagen_default.png') }}" class="img-circle elevation-1" alt="User"
                        style="margin-top: 8px; margin-left: -4px; width: 2.5rem; background-color: white">
                @endif
            </div>
            <div class="info">
                <span class="d-block" style="color: white; ">{{ $user->nombre }}<br><small
                        class="badge badge-info">{{ $user->rol_nombre }}</small></span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ Route('backoffice.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <hr style="color: #a9a9a9">
                </li>
                @foreach ($mantenedores as $mantenedor)
                    {{-- 1: Acceder --}}
                    @if ($rolMP[$mantenedor->id][1] == 1)
                        <li class="nav-item">
                            <a href="{{ Route($mantenedor->ruta) }}" class="nav-link">
                                <i class="nav-icon {{ $mantenedor->icono }}"></i>
                                <p>
                                    {{ $mantenedor->nombre }}
                                </p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
