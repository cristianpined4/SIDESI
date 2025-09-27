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
                    return 'vendor.livewire.bootstrap';
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

                public function store()
                {
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

                        \$this->resetUI();
                        \$this->dispatch("message-success", "{$name} creado correctamente");
                        \$this->dispatch("cerrar-modal");
                    } catch (\\Throwable \$th) {
                        DB::rollBack();
                        \$this->dispatch("message-error", "Error al crear");
                    }
                }

                public function edit(\$id)
                {
                    \$item = {$name}::find(\$id);
                    if (!\$item) {
                        \$this->dispatch("message-error", "{$name} no encontrado");
                        return;
                    }

                    \$this->record_id = \$item->id;
                    \$this->fields = \$item->toArray();
                    \$this->dispatch("abrir-modal");
                }

                public function update()
                {
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

                        \$this->resetUI();
                        \$this->dispatch("message-success", "{$name} actualizado correctamente");
                        \$this->dispatch("cerrar-modal");
                    } catch (\\Throwable \$th) {
                        DB::rollBack();
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

                        \$this->dispatch("message-success", "{$name} eliminado correctamente");
                    } catch (\\Throwable \$th) {
                        DB::rollBack();
                        \$this->dispatch("message-error", "Error al eliminar");
                    }
                }

                public function resetUI()
                {
                    \$this->record_id = null;
                    \$this->fields = [];
                    \$this->file = null;
                    \$this->resetErrorBag();
                    \$this->resetValidation();
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
            <!-- Contenido - inicio -->
            <h2>Módulo $name</h2>
            <!-- Contenido - fin -->
            </main>

            <script>
                document.addEventListener('livewire:initialized', function () {
                    Livewire.on('cerrar-modal', function () {
                        $('.modal').modal('hide');
                    });

                    Livewire.on('abrir-modal', function () {
                        $('.modal').modal('show');
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

