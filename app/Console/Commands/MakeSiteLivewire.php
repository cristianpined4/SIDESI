<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakeSiteLivewire extends Command
{
    /**
     * Nombre y firma del comando
     */
    protected $signature = 'make:site-livewire {name}';

    /**
     * Descripción del comando
     */
    protected $description = 'Crear un componente Livewire para site con layout y controlador básico';

    /**
     * Ejecutar el comando
     */
    public function handle()
    {
        $name = $this->argument('name');

        // 1. Crear componente Livewire
        Artisan::call("make:livewire Site/$name");
        File::delete(app_path("Livewire/Site/{$name}.php"));

        // 2. Crear controlador en namespace Site
        $controllerPath = app_path("Livewire/Site/{$name}Controller.php");
        if (!File::exists($controllerPath)) {
            File::put($controllerPath, $this->controllerStub($name));
            $this->info("Controlador creado: Site/{$name}Controller");
        }

        // 3. Modificar la vista Livewire para usar layout de site
        $viewPath = resource_path("views/livewire/site/" . strtolower($name) . ".blade.php");
        if (File::exists($viewPath)) {
            File::put($viewPath, $this->viewStub($name));
            $this->info("Vista modificada con layout de site: livewire/site/{$name}.blade.php");
        }

        $this->info("Componente Site/$name generado correctamente 🚀");
    }

    /**
     * Plantilla del controlador
     */
    protected function controllerStub($name)
    {
        return <<<PHP
            <?php

            namespace App\Livewire\Site;

            use Livewire\Attributes\On;
            use Livewire\Component;
            use Livewire\WithPagination;
            use Livewire\WithFileUploads;
            use Illuminate\Support\Facades\DB;
            use Illuminate\Support\Facades\Storage;

            class {$name}Controller extends Component
            {
                use WithPagination, WithFileUploads;

                public \$record_id;
                public \$fields = [];   // inputs normales
                public \$file;          // archivo temporal
                public \$search = '';
                public \$paginate = 10;

                public function paginationView()
                {
                    return 'vendor.livewire.tailwind';
                }

                public function render()
                {
                    \$query = {$name}::query();

                    if (!empty(\$this->search)) {
                        foreach ((new {$name}())->getFillable() as \$field) {
                            \$query->orWhere(\$field, 'like', '%' . \$this->search . '%');
                        }
                    }

                    \$records = \$query->orderBy('id', 'desc')->paginate(\$this->paginate);

                    return view('livewire.site.{$this->toSnakeCase($name)}', compact('records'))
                        ->extends('layouts.site')
                        ->section('content');
                }

                public function abrirModal(\$idModal = 'modal-home', \$initVoid = true)
                {
                    if (\$initVoid) {
                        \$this->resetUI();
                    } else{
                        \$this->resetErrorBag();
                        \$this->resetValidation();
                    }
                    \$this->dispatch("abrir-modal", ['modal' => \$idModal]);
                }

                public function cerrarModal(\$idModal = 'modal-home')
                {
                    \$this->resetUI();
                    \$this->dispatch("cerrar-modal", ['modal' => \$idModal]);
                }

                public function store()
                {   
                    \$this->resetErrorBag();
                    \$this->resetValidation();

                    \$rules = [
                        'fields.name' => 'required|string|max:255',
                        'file' => 'nullable|file|max:2048',
                    ];

                    \$messages = [
                        'fields.name.required' => 'El nombre es obligatorio.',
                        'fields.name.string' => 'El nombre debe ser un texto válido.',
                        'fields.name.max' => 'El nombre no puede tener más de 255 caracteres.',

                        'file.file' => 'El archivo debe ser válido.',
                        'file.max' => 'El archivo no puede superar los 2 MB.',
                    ];

                    \$this->validate(\$rules, \$messages);

                    try {
                        DB::beginTransaction();
                        \$item = new {$name}();
                        \$item->fill(\$this->fields);

                        if (\$this->file) {
                            \$path = \$this->file->store('uploads', 'public');
                            \$item->file_path = \$path;
                        }

                        \$item->save();
                        DB::commit();

                        LogsSistema::create([
                            'action' => 'create {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Creación de un nuevo {$name} con ID ' . \$item->id,
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => \$item->id,
                            'status' => 'success',
                        ]);
                        \$this->resetUI();
                        \$this->dispatch("message-success", "{$name} creado correctamente");
                        \$this->abrirModal('modal-home');
                    } catch (\\Throwable \$th) {
                        DB::rollBack();
                        LogsSistema::create([
                            'action' => 'error al crear {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Error al crear un nuevo {$name}: ' . \$th->getMessage(),
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => null,
                            'status' => 'error',
                        ]);
                        \$this->dispatch("message-error", "Error al crear");
                    }
                }

                public function edit(\$id)
                {
                    \$this->resetUI();
                    
                    \$item = {$name}::find(\$id);
                    if (!\$item) {
                        LogsSistema::create([
                            'action' => 'error al editar {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Intento de edición de un {$name} inexistente con ID ' . \$id,
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => \$id,
                            'status' => 'error',
                        ]);
                        \$this->dispatch("message-error", "{$name} no encontrado");
                        return;
                    }

                    \$this->record_id = \$item->id;
                    \$this->fields = \$item->toArray();
                    \$this->abrirModal('modal-home', false);
                }

                public function update()
                {
                    \$this->resetErrorBag();
                    \$this->resetValidation();

                    \$rules = [
                        'fields.name' => 'required|string|max:255',
                        'file' => 'nullable|file|max:2048',
                    ];

                    \$messages = [
                        'fields.name.required' => 'El nombre es obligatorio.',
                        'fields.name.string' => 'El nombre debe ser un texto válido.',
                        'fields.name.max' => 'El nombre no puede tener más de 255 caracteres.',

                        'file.file' => 'El archivo debe ser válido.',
                        'file.max' => 'El archivo no puede superar los 2 MB.',
                    ];

                    \$this->validate(\$rules, \$messages);

                    try {
                        DB::beginTransaction();
                        \$item = {$name}::find(\$this->record_id);
                        \$item->fill(\$this->fields);

                        if (\$this->file) {
                            if (\$item->file_path && Storage::disk('public')->exists(\$item->file_path)) {
                                Storage::disk('public')->delete(\$item->file_path);
                            }
                            \$path = \$this->file->store('uploads', 'public');
                            \$item->file_path = \$path;
                        }

                        \$item->save();
                        DB::commit();

                        LogsSistema::create([
                            'action' => 'update {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Actualización del {$name} con ID ' . \$item->id,
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => \$item->id,
                            'status' => 'success',
                        ]);

                        \$this->resetUI();
                        \$this->dispatch("message-success", "{$name} actualizado correctamente");
                        \$this->cerrarModal('modal-home');
                    } catch (\\Throwable \$th) {
                        DB::rollBack();
                        LogsSistema::create([
                            'action' => 'error al actualizar {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Error al actualizar el {$name} con ID ' . \$this->record_id . ': ' . \$th->getMessage(),
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => \$this->record_id,
                            'status' => 'error',
                        ]);
                        \$this->dispatch("message-error", "Error al actualizar");
                    }
                }

                #[On("delete")]
                public function destroy(\$id)
                {
                    try {
                        DB::beginTransaction();
                        \$item = {$name}::find(\$id);

                        if (\$item->file_path && Storage::disk('public')->exists(\$item->file_path)) {
                            Storage::disk('public')->delete(\$item->file_path);
                        }

                        \$item->delete();
                        DB::commit();

                        LogsSistema::create([
                            'action' => 'delete {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Eliminación del {$name} con ID ' . \$item->id,
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => \$item->id,
                            'status' => 'success',
                        ]);

                        \$this->dispatch("message-success", "{$name} eliminado correctamente");
                    } catch (\\Throwable \$th) {
                        DB::rollBack();
                        LogsSistema::create([
                            'action' => 'error al eliminar {$name}',
                            'user_id' => auth()->id(),
                            'ip_address' => request()->ip(),
                            'description' => 'Error al eliminar el {$name} con ID ' . \$item->id . ': ' . \$th->getMessage(),
                            'target_table' => (new {$name}())->getTable(),
                            'target_id' => \$item->id,
                            'status' => 'error',
                        ]);
                        \$this->dispatch("message-error", "Error al eliminar");
                    }
                }

                public function resetUI()
                {
                    \$this->resetErrorBag();
                    \$this->resetValidation();
                    \$this->record_id = null;
                    \$this->fields = [];
                    \$this->file = null;
                }
            }
PHP;
    }

    /**
     * Plantilla de vista Livewire con layout site
     */
    protected function viewStub($name)
    {
        return <<<BLADE
            @section('title', "$name")

            <main>
            <!-- modales -->
            <div id="modal-home" class="modal" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userLabel">{{ \$record_id ? 'Editar usuario' : 'Nuevo usuario' }}</h5>
                            <button type="button" class="btn-close" aria-label="Cerrar" onclick="closeModal(this.closest('.modal'))">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Nombre Completo</label>
                                <input wire:model="fields.name" type="text" placeholder="Nombre" id="nombre"
                                    class="form-control @error('fields.name') was-validated is-invalid @enderror"
                                    oninput="this.value = this.value.toUpperCase();">
                                <div class="invalid-feedback">@error('fields.name') {{\$message}} @enderror</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            @if (\$record_id)
                            <button type="button" class="btn btn-warning" wire:click="update">Actualizar</button>
                            @else
                            <button type="button" class="btn btn-primary" wire:click="store">Guardar</button>
                            @endif
                            <button type="button" class="btn btn-secondary" onclick="closeModal(this.closest('.modal'))">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin modales -->

            <!-- Contenido - inicio -->
            <h2>Módulo $name</h2>
            <!-- Contenido - fin -->
            </main>

            <script>
                document.addEventListener('livewire:initialized', function () {
                    Livewire.on('cerrar-modal', function (modal) {
                        let modalElement = document.getElementById(modal[0].modal);
                        if (modalElement) {
                            closeModal(modalElement);
                        }
                    });

                    Livewire.on('abrir-modal', function (modal) {
                        let modalElement = document.getElementById(modal[0].modal);
                        if (modalElement) {
                            openModal(modalElement);
                            let modelDialog = modalElement.querySelector('.modal-dialog');
                            if (modelDialog) {
                                modelDialog.scrollTop = 0;
                            }
                        }
                    });
                });

                const confirmarEliminar = async id => {
                    if (await window.Confirm(
                        'Eliminar',
                        '¿Estas seguro de eliminar este $name?',
                        'warning',
                        'Si, eliminar',
                        'Cancelar'
                    )) {
                        Livewire.dispatch('delete', { id });
                    }
                }
            </script>
        BLADE;
    }

    /**
     * Convierte PascalCase a snake_case
     */
    private function toSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}