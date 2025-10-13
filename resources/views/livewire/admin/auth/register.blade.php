@section('title', "Crear una cuenta")

<main style="width: 100%; max-width: 600px;padding: 1rem;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    <div class="container" style="margin-top: 1rem; margin-bottom: 1rem; max-width: 100%;" id="registerForm">
        <div class="header">
            <div class="logo" style="font-size: 1rem">SIDESI</div>
            <h2>Crear Cuenta</h2>
        </div>
        <div class="form-container">
            <form>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;width: 100%;justify-content: space-between;">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input wire:model="fields.name" type="text" placeholder="Nombre" id="name"
                            class="form-control @error('fields.name') was-validated is-invalid @enderror"
                            onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                        <div class="invalid-feedback">@error('fields.name') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Apellido</label>
                        <input wire:model="fields.lastname" type="text" placeholder="Apellido" id="lastname"
                            class="form-control @error('fields.lastname') was-validated is-invalid @enderror"
                            onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                        <div class="invalid-feedback">@error('fields.lastname') {{$message}} @enderror</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="username">Nombre de usuario</label>
                    <input wire:model="fields.username" type="text" placeholder="Nombre de usuario" id="username"
                        class="form-control @error('fields.username') was-validated is-invalid @enderror"
                        onkeyup="this.value = this.value.toLowerCase();">
                    <div class="invalid-feedback">@error('fields.username') {{$message}} @enderror</div>
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input wire:model="fields.email" type="email" placeholder="Correo electrónico" id="email"
                        class="form-control @error('fields.email') was-validated is-invalid @enderror"
                        onkeyup="this.value = this.value.toLowerCase();">
                    <div class="invalid-feedback">@error('fields.email') {{$message}} @enderror</div>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input wire:model="fields.password" type="password" placeholder="Contraseña" id="password"
                        class="form-control @error('fields.password') was-validated is-invalid @enderror">
                    <div class="invalid-feedback">@error('fields.password') {{$message}} @enderror</div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input wire:model="fields.password_confirmation" type="password" placeholder="Confirmar Contraseña"
                        id="password_confirmation"
                        class="form-control @error('fields.password_confirmation') was-validated is-invalid @enderror">
                    <div class="invalid-feedback">@error('fields.password_confirmation') {{$message}} @enderror</div>
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input wire:model="fields.phone" type="text" placeholder="Teléfono" id="phone"
                        class="form-control @error('fields.phone') was-validated is-invalid @enderror"
                        onkeyup="return /[0-9]/.test(String.fromCharCode(event.keyCode))">
                    <div class="invalid-feedback">@error('fields.phone') {{$message}} @enderror</div>
                </div>
                <div class="form-group">
                    <label for="role_id">Tipo de usuario</label>
                    <select wire:model="fields.role_id" id="role_id"
                        class="form-control @error('fields.role_id') was-validated is-invalid @enderror">
                        <option value="" disabled>Seleccione una opción</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">@error('fields.role_id') {{$message}} @enderror</div>
                </div>
                <div class="form-group" id="institutionDiv" wire:ignore.self>
                    <label for="institution">Institución</label>
                    <input wire:model="fields.institution" type="text" placeholder="Institución" id="institution"
                        list="universidades"
                        class="form-control @error('fields.institution') was-validated is-invalid @enderror">
                    <div class="invalid-feedback">@error('fields.institution') {{$message}} @enderror</div>

                    {{-- Datalist para autocompletar --}}
                    <datalist id="universidades">
                        @foreach ($universidades as $uni)
                        <option value="{{ $uni }}">
                            {{ $uni }}
                        </option>
                        @endforeach
                    </datalist>

                </div>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;width: 100%;justify-content: space-between;">
                    <div class="form-group" style="flex: 1;">
                        <label for="document_type">Tipo de documento</label>
                        <select wire:model="fields.document_type" id="document_type"
                            class="form-control @error('fields.document_type') was-validated is-invalid @enderror">
                            <option value="" disabled>Seleccione una opción</option>
                            <option value="DUI">DUI</option>
                            <option value="Carnet Estudiantil">Carnet Estudiantil</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Carnet de extranjería">Carnet de extranjería</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.document_type') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="document_number">Número de documento</label>
                        <input wire:model="fields.document_number" type="text" placeholder="Número de documento"
                            id="document_number"
                            class="form-control @error('fields.document_number') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.document_number') {{$message}} @enderror</div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" wire:click="register">Crear Cuenta</button>
                <a class="toggle-link" href="{{ route('login') }}">
                    ¿Ya tienes cuenta? Iniciar sesión
                </a>
                <a class="toggle-link" href="{{ route('home-site') }}">
                    Volver al sitio</a>
            </form>
        </div>
    </div>
</main>

<script>
    document.addEventListener('livewire:initialized', function () {
        const roleSelect = document.getElementById('role_id');
        const institutionDiv = document.getElementById('institutionDiv');

        function toggleInstitutionField() {
            const selectedRole = roleSelect.value;
            if (selectedRole && selectedRole.toLowerCase() !== 'invitado') {
                institutionDiv.style.display = 'block';
            } else {
                institutionDiv.style.display = 'none';
                Livewire.dispatch('setFields', { payload: { field: 'institution', value: '' } });
            }
        }

        // Inicializar el estado del campo al cargar la página
        toggleInstitutionField();

        // Escuchar cambios en el select
        roleSelect.addEventListener('change', toggleInstitutionField);
    });
</script>