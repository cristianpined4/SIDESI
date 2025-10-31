<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\LogsSistema;
use App\Models\Configuracion; // <- IMPORTANTE

class ConfiguracionController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id = null;
    public $fields = [];         // Debe mapear columnas reales (ej: name, value, etc.)
    public $file = null;         // archivo temporal
    public $search = '';
    public $paginate = 10;
    public bool $loading = false;

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function mount()
    {
        if (Auth::check() && !in_array(Auth::user()->role_id, [1, 2])) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        $query = Configuracion::query();

        if (!empty($this->search)) {
            $search = $this->search;
            $driver = DB::getDriverName();

            $query->where(function ($q) use ($search, $driver) {
                $likeOp = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
                // Recorremos sólo columnas rellenables
                foreach ((new Configuracion())->getFillable() as $field) {
                    // Evita orWhere en campos no textuales si tu esquema lo requiere
                    $q->orWhere($field, $likeOp, "%{$search}%");
                }
            });
        }

        $records = $query->orderByDesc('id')->paginate($this->paginate);

        return view('livewire.admin.configuracion', compact('records')) // <-- Asegura que existe
            ->extends('layouts.admin')
            ->section('content');
    }

    public function abrirModal($idModal = 'modal-home', $initVoid = true)
    {
        if ($initVoid) {
            $this->resetUI();
        } else {
            $this->resetErrorBag();
            $this->resetValidation();
        }
        $this->dispatch('abrir-modal', ['modal' => $idModal]);
    }

    public function cerrarModal($idModal = 'modal-home')
    {
        $this->resetUI();
        $this->dispatch('cerrar-modal', ['modal' => $idModal]);
    }

    public function store()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        // Ajusta las reglas a tus columnas reales
        $rules = [
            'fields.name' => 'required|string|max:255',
            'file'        => 'nullable|file|max:2048',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es obligatorio.',
            'fields.name.string'   => 'El nombre debe ser un texto válido.',
            'fields.name.max'      => 'El nombre no puede tener más de 255 caracteres.',
            'file.file'            => 'El archivo debe ser válido.',
            'file.max'             => 'El archivo no puede superar los 2 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $item = new Configuracion();
            $item->fill($this->fields);

            if ($this->file) {
                $path = $this->file->store('uploads', 'public'); // requiere storage:link
                $item->file_path = $path; // <-- asegúrate que existe esta columna y en $fillable
            }

            $item->save();
            DB::commit();

            LogsSistema::create([
                'action'       => 'create Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Creación de Configuración ID ' . $item->id,
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => $item->id,
                'status'       => 'success',
            ]);

            $this->resetUI();
            $this->dispatch('message-success', 'Configuración creada correctamente');
            $this->abrirModal('modal-home');
        } catch (\Throwable $th) {
            DB::rollBack();

            LogsSistema::create([
                'action'       => 'error al crear Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Error al crear Configuración: ' . $th->getMessage(),
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => null,
                'status'       => 'error',
            ]);

            $this->dispatch('message-error', 'Error al crear');
        }
    }

    public function edit($id)
    {
        $this->resetUI();

        $item = Configuracion::find($id);
        if (!$item) {
            LogsSistema::create([
                'action'       => 'error al editar Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Intento de edición inexistente ID ' . $id,
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => $id,
                'status'       => 'error',
            ]);

            $this->dispatch('message-error', 'Configuración no encontrada');
            return;
        }

        $this->record_id = $item->id;
        $this->fields = $item->toArray();
        $this->abrirModal('modal-home', false);
    }

    public function update()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'fields.name' => 'required|string|max:255',
            'file'        => 'nullable|file|max:2048',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es obligatorio.',
            'fields.name.string'   => 'El nombre debe ser un texto válido.',
            'fields.name.max'      => 'El nombre no puede tener más de 255 caracteres.',
            'file.file'            => 'El archivo debe ser válido.',
            'file.max'             => 'El archivo no puede superar los 2 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $item = Configuracion::find($this->record_id);
            if (!$item) {
                throw new \Exception('Configuración no encontrada.');
            }

            $item->fill($this->fields);

            if ($this->file) {
                if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                    Storage::disk('public')->delete($item->file_path);
                }
                $path = $this->file->store('uploads', 'public');
                $item->file_path = $path;
            }

            $item->save();
            DB::commit();

            LogsSistema::create([
                'action'       => 'update Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Actualización de Configuración ID ' . $item->id,
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => $item->id,
                'status'       => 'success',
            ]);

            $this->resetUI();
            $this->dispatch('message-success', 'Configuración actualizada correctamente');
            $this->cerrarModal('modal-home');
        } catch (\Throwable $th) {
            DB::rollBack();

            LogsSistema::create([
                'action'       => 'error al actualizar Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Error al actualizar Configuración ID ' . $this->record_id . ': ' . $th->getMessage(),
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => $this->record_id,
                'status'       => 'error',
            ]);

            $this->dispatch('message-error', 'Error al actualizar');
        }
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $item = Configuracion::find($id);
            if (!$item) {
                throw new \Exception('Configuración no encontrada.');
            }

            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }

            $item->delete();
            DB::commit();

            LogsSistema::create([
                'action'       => 'delete Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Eliminación de Configuración ID ' . $item->id,
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => $item->id,
                'status'       => 'success',
            ]);

            $this->dispatch('message-success', 'Configuración eliminada correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();

            LogsSistema::create([
                'action'       => 'error al eliminar Configuracion',
                'user_id'      => auth()->id(),
                'ip_address'   => request()->ip(),
                'description'  => 'Error al eliminar Configuración ID ' . $id . ': ' . $th->getMessage(),
                'target_table' => (new Configuracion())->getTable(),
                'target_id'    => $id,
                'status'       => 'error',
            ]);

            $this->dispatch('message-error', 'Error al eliminar');
        }
    }

    public function resetUI()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->record_id = null;
        $this->fields = [];
        $this->file = null;
    }
}
