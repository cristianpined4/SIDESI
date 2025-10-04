<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LogsSistema;

class DashboardController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [];   // inputs normales
    public $file;          // archivo temporal
    public $search = '';
    public $paginate = 10;
    public bool $loading = false;

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function mount()
    {
        if (Auth::check()) {
            if (!in_array(Auth::user()->role_id, [1, 2])) {
                return redirect()->route('login');
            }
        }
    }

    public function render()
    {
        $current_user = Auth::user();

        return view('livewire.admin.dashboard', compact('current_user'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function abrirModal($idModal = 'modal-home')
    {
        $this->resetUI();
        $this->dispatch("abrir-modal", ['modal' => $idModal]);
    }

    public function cerrarModal($idModal = 'modal-home')
    {
        $this->resetUI();
        $this->dispatch("cerrar-modal", ['modal' => $idModal]);
    }

    public function store()
    {
        $rules = [
            'fields.name' => 'required|string|max:255',
            'file' => 'nullable|file|max:2048',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es obligatorio.',
            'fields.name.string' => 'El nombre debe ser un texto válido.',
            'fields.name.max' => 'El nombre no puede tener más de 255 caracteres.',

            'file.file' => 'El archivo debe ser válido.',
            'file.max' => 'El archivo no puede superar los 2 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = new Dashboard();
            $item->fill($this->fields);

            if ($this->file) {
                $path = $this->file->store('uploads', 'public');
                $item->file_path = $path;
            }

            $item->save();
            DB::commit();

            LogsSistema::create([
                'action' => 'create Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de un nuevo Dashboard con ID ' . $item->id,
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);
            $this->resetUI();
            $this->dispatch("message-success", "Dashboard creado correctamente");
            $this->dispatch("cerrar-modal");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al crear Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear un nuevo Dashboard: ' . $th->getMessage(),
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => null,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al crear");
        }
    }

    public function edit($id)
    {
        $item = Dashboard::find($id);
        if (!$item) {
            LogsSistema::create([
                'action' => 'error al editar Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Intento de edición de un Dashboard inexistente con ID ' . $id,
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => $id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Dashboard no encontrado");
            return;
        }

        $this->record_id = $item->id;
        $this->fields = $item->toArray();
        $this->dispatch("abrir-modal");
    }

    public function update()
    {
        $rules = [
            'fields.name' => 'required|string|max:255',
            'file' => 'nullable|file|max:2048',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es obligatorio.',
            'fields.name.string' => 'El nombre debe ser un texto válido.',
            'fields.name.max' => 'El nombre no puede tener más de 255 caracteres.',

            'file.file' => 'El archivo debe ser válido.',
            'file.max' => 'El archivo no puede superar los 2 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = Dashboard::find($this->record_id);
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
                'action' => 'update Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización del Dashboard con ID ' . $item->id,
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->dispatch("message-success", "Dashboard actualizado correctamente");
            $this->dispatch("cerrar-modal");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al actualizar Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al actualizar el Dashboard con ID ' . $this->record_id . ': ' . $th->getMessage(),
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => $this->record_id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al actualizar");
        }
    }

    #[On("delete")]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $item = Dashboard::find($id);

            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }

            $item->delete();
            DB::commit();

            LogsSistema::create([
                'action' => 'delete Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Eliminación del Dashboard con ID ' . $item->id,
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->dispatch("message-success", "Dashboard eliminado correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al eliminar Dashboard',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al eliminar el Dashboard con ID ' . $item->id . ': ' . $th->getMessage(),
                'target_table' => (new Dashboard())->getTable(),
                'target_id' => $item->id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al eliminar");
        }
    }

    public function resetUI()
    {
        $this->record_id = null;
        $this->fields = [];
        $this->file = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}