@section('title', 'Iniciar Sesión')

<div class="container" id="loginForm">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>
    <div class="header">
        <div class="logo" style="font-size: 1rem">SIDESI</div>
        <h2>Iniciar Sesión</h2>
    </div>
    <div class="form-container">
        <form onsubmit="login(event)">
            <div class="form-group">
                <label for="loginEmail">Usuario</label>
                <input wire:model="username" type="text" placeholder="Usuario" id="loginEmail"
                    class="form-control @error('username') was-validated is-invalid @enderror">
                <div class="invalid-feedback">@error('username') {{$message}} @enderror</div>
            </div>
            <div class="form-group">
                <label for="loginPassword">Contraseña</label>
                <input wire:model="password" type="password" placeholder="Contraseña" id="loginPassword"
                    class="form-control @error('password') was-validated is-invalid @enderror">
                <div class="invalid-feedback">@error('password') {{$message}} @enderror</div>
            </div>
            <button type="button" class="btn btn-primary" wire:click="login">Iniciar Sesión</button>
            <a class="toggle-link" href="{{ route('register') }}">
                ¿No tienes cuenta? Crear cuenta
            </a>
            <a class="toggle-link" href="{{ route('home-site') }}">
                Volver al sitio
            </a>
        </form>
    </div>
</div>