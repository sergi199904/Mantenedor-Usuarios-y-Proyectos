{{ $user }}
<form action="{{ Route('usuario.logout') }}" method="POST">
    @csrf
    <button type="submit">Cerrar Sesión</button>
</form>