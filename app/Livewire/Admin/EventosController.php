<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\LogsSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Eventos;
use App\Models\User;

class EventosController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [
        'title' => '',
        'description' => '',
        'start_time' => '',
        'end_time' => '',
        'location' => '',
        'inscriptions_enabled' => '',
        'max_participants' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'is_active' => '',
        'mode' => '',
        'is_paid' => '',
        'price' => '',
        'organizer_id' => '',
];   // inputs normales
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
        $query = Eventos::query();

        if (!empty($this->search)) {
            foreach ((new Eventos())->getFillable() as $field) {
                $query->orWhere($field, 'like', '%' . $this->search . '%');
            }
        }

        $records = $query->orderBy('id', 'asc')->paginate($this->paginate);
        $recordsUsers = User::whereIn('role_id', [1, 2])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

        return view('livewire.admin.eventos', compact('records', 'recordsUsers'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function abrirModal($idModal = 'modal-home', $initVoid = true)
    {
        if ($initVoid) {
            $this->resetUI();
        } else{
            $this->resetErrorBag();
            $this->resetValidation();
        }
        $this->dispatch("abrir-modal", ['modal' => $idModal]);
    }

    public function cerrarModal($idModal = 'modal-home')
    {
        $this->resetUI();
        $this->dispatch("cerrar-modal", ['modal' => $idModal]);
    }

    public function store()
    {   
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'fields.title' => 'required|string|max:250',
            'fields.description' => 'required|string|max:1000',
            'fields.start_time' => 'required|date',
            'fields.end_time' => 'required|date|after:fields.start_time',
            'fields.location' => 'required|string|max:255',
            'fields.inscriptions_enabled' => 'required|boolean',
            'fields.max_participants' => 'required|integer|min:1',
            'fields.contact_email' => 'required|email|max:250',
            'fields.contact_phone' => 'required|string|max:15',
            'fields.is_active' => 'required|boolean',
            'fields.mode' => 'required|string|max:50',
            'fields.is_paid' => 'required|boolean',
            'fields.price' => 'numeric|min:0',
            'fields.organizer_id' => 'required|exists:users,id',
            'file' => 'nullable|file|max:2048',
        ];

        $messages = [
            'fields.title.required' => 'El título es obligatorio.',
            'fields.title.string' => 'El título debe ser un texto válido.',
            'fields.title.max' => 'El título no puede tener más de 250 caracteres.',
            'fields.description.required' => 'La descripción es obligatoria.',
            'fields.description.string' => 'La descripción debe ser un texto válido.',
            'fields.description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'fields.start_time.required' => 'La fecha y hora de inicio son obligatorias.',
            'fields.start_time.date' => 'La fecha y hora de inicio deben ser una fecha válida.',
            'fields.end_time.required' => 'La fecha y hora de fin son obligatorias.',
            'fields.end_time.date' => 'La fecha y hora de fin deben ser una fecha válida.',
            'fields.end_time.after' => 'La fecha y hora de fin deben ser posteriores a la de inicio.',
            'fields.location.required' => 'La ubicación es obligatoria.',
            'fields.location.string' => 'La ubicación debe ser un texto válido.',
            'fields.location.max' => 'La ubicación no puede tener más de 255 caracteres.',
            'fields.inscriptions_enabled.required' => 'El campo de inscripciones habilitadas es obligatorio.',
            'fields.inscriptions_enabled.boolean' => 'El campo de inscripciones habilitadas debe ser verdadero o falso.',
            'fields.max_participants.required' => 'El número máximo de participantes es obligatorio.',
            'fields.max_participants.integer' => 'El número máximo de participantes debe ser un entero.',
            'fields.max_participants.min' => 'El número máximo de participantes debe ser al menos 1.',
            'fields.contact_email.required' => 'El correo de contacto es obligatorio.',
            'fields.contact_email.email' => 'El correo de contacto debe ser una dirección de correo válida.',
            'fields.contact_email.max' => 'El correo de contacto no puede tener más de 250 caracteres.',
            'fields.contact_phone.required' => 'El teléfono de contacto es obligatorio.',
            'fields.contact_phone.string' => 'El teléfono de contacto debe ser un texto válido.',
            'fields.contact_phone.max' => 'El teléfono de contacto no puede tener más de 15 caracteres.',
            'fields.is_active.required' => 'El campo de activo es obligatorio.',
            'fields.is_active.boolean' => 'El campo de activo debe ser verdadero o falso.',
            'fields.mode.required' => 'El modo es obligatorio.',
            'fields.mode.string' => 'El modo debe ser un texto válido.',
            'fields.mode.max' => 'El modo no puede tener más de 50 caracteres.',
            'fields.is_paid.required' => 'El campo de pagado es obligatorio.',
            'fields.is_paid.boolean' => 'El campo de pagado debe ser verdadero o falso.',
            'fields.price.numeric' => 'El precio debe ser un número.',
            'fields.price.min' => 'El precio no puede ser negativo.',
            'fields.organizer_id.required' => 'El organizador es obligatorio.',
            'fields.organizer_id.exists' => 'El organizador seleccionado no es válido.',

            'file.file' => 'El archivo debe ser válido.',
            'file.max' => 'El archivo no puede superar los 2 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = new Eventos();
            $item->fill($this->fields);

            if ($this->file) {
                $path = $this->file->store('uploads', 'public');
                $item->file_path = $path;
            }

            $item->save();
            DB::commit();

            LogsSistema::create([
                'action' => 'create Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de un nuevo Eventos con ID ' . $item->id,
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);
            $this->resetUI();
            $this->dispatch("message-success", "Eventos creado correctamente");
            $this->abrirModal('modal-home');
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al crear Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear un nuevo Eventos: ' . $th->getMessage(),
                'target_table' => (new Eventos())->getTable(),
                'target_id' => null,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al crear");
        }
    }

    public function edit($id)
    {
        $this->resetUI();
        
        $item = Eventos::find($id);
        if (!$item) {
            LogsSistema::create([
                'action' => 'error al editar Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Intento de edición de un Eventos inexistente con ID ' . $id,
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Eventos no encontrado");
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
            'fields.title' => 'required|string|max:250',
            'fields.description' => 'required|string|max:1000',
            'fields.start_time' => 'required|date',
            'fields.end_time' => 'required|date|after:fields.start_time',
            'fields.location' => 'required|string|max:255',
            'fields.inscriptions_enabled' => 'required|boolean',
            'fields.max_participants' => 'required|integer|min:1',
            'fields.contact_email' => 'required|email|max:250',
            'fields.contact_phone' => 'required|string|max:15',
            'fields.is_active' => 'required|boolean',
            'fields.mode' => 'required|string|max:50',
            'fields.is_paid' => 'required|boolean',
            'fields.price' => 'numeric|min:0',
            'fields.organizer_id' => 'required|exists:users,id',
            'file' => 'nullable|file|max:2048',
        ];

        $messages = [
            'fields.title.required' => 'El título es obligatorio.',
            'fields.title.string' => 'El título debe ser un texto válido.',
            'fields.title.max' => 'El título no puede tener más de 250 caracteres.',
            'fields.description.required' => 'La descripción es obligatoria.',
            'fields.description.string' => 'La descripción debe ser un texto válido.',
            'fields.description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'fields.start_time.required' => 'La fecha y hora de inicio son obligatorias.',
            'fields.start_time.date' => 'La fecha y hora de inicio deben ser una fecha válida.',
            'fields.end_time.required' => 'La fecha y hora de fin son obligatorias.',
            'fields.end_time.date' => 'La fecha y hora de fin deben ser una fecha válida.',
            'fields.end_time.after' => 'La fecha y hora de fin deben ser posteriores a la de inicio.',
            'fields.location.required' => 'La ubicación es obligatoria.',
            'fields.location.string' => 'La ubicación debe ser un texto válido.',
            'fields.location.max' => 'La ubicación no puede tener más de 255 caracteres.',
            'fields.inscriptions_enabled.required' => 'El campo de inscripciones habilitadas es obligatorio.',
            'fields.inscriptions_enabled.boolean' => 'El campo de inscripciones habilitadas debe ser verdadero o falso.',
            'fields.max_participants.required' => 'El número máximo de participantes es obligatorio.',
            'fields.max_participants.integer' => 'El número máximo de participantes debe ser un entero.',
            'fields.max_participants.min' => 'El número máximo de participantes debe ser al menos 1.',
            'fields.contact_email.required' => 'El correo de contacto es obligatorio.',
            'fields.contact_email.email' => 'El correo de contacto debe ser una dirección de correo válida.',
            'fields.contact_email.max' => 'El correo de contacto no puede tener más de 250 caracteres.',
            'fields.contact_phone.required' => 'El teléfono de contacto es obligatorio.',
            'fields.contact_phone.string' => 'El teléfono de contacto debe ser un texto válido.',
            'fields.contact_phone.max' => 'El teléfono de contacto no puede tener más de 15 caracteres.',
            'fields.is_active.required' => 'El campo de activo es obligatorio.',
            'fields.is_active.boolean' => 'El campo de activo debe ser verdadero o falso.',
            'fields.mode.required' => 'El modo es obligatorio.',
            'fields.mode.string' => 'El modo debe ser un texto válido.',
            'fields.mode.max' => 'El modo no puede tener más de 50 caracteres.',
            'fields.is_paid.required' => 'El campo de pagado es obligatorio.',
            'fields.is_paid.boolean' => 'El campo de pagado debe ser verdadero o falso.',
            'fields.price.numeric' => 'El precio debe ser un número.',
            'fields.price.min' => 'El precio no puede ser negativo.',
            'fields.organizer_id.required' => 'El organizador es obligatorio.',
            'fields.organizer_id.exists' => 'El organizador seleccionado no es válido.',

            'file.file' => 'El archivo debe ser válido.',
            'file.max' => 'El archivo no puede superar los 2 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = Eventos::find($this->record_id);
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
                'action' => 'update Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización del Eventos con ID ' . $item->id,
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->dispatch("message-success", "Eventos actualizado correctamente");
            $this->cerrarModal('modal-home');
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al actualizar Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al actualizar el Eventos con ID ' . $this->record_id . ': ' . $th->getMessage(),
                'target_table' => (new Eventos())->getTable(),
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
            $item = Eventos::find($id);

            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }

            $item->delete();
            DB::commit();

            LogsSistema::create([
                'action' => 'delete Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Eliminación del Eventos con ID ' . $item->id,
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->dispatch("message-success", "Eventos eliminado correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al eliminar Eventos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al eliminar el Eventos con ID ' . $item->id . ': ' . $th->getMessage(),
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $item->id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al eliminar");
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