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
use App\Models\SessionesEvento;

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
    public $record_sesion_id;
    public $fieldsSesiones = [
        'evento_id' => '',
        'title' => '',
        'description' => '',
        'start_time' => '',
        'end_time' => '',
        'ponente_id' => '',
        'mode' => '',
        'max_participants' => '',
        'require_approval' => '',
    ];
    public $records_sesiones;
    public $file;          // archivo temporal
    public $search = '';
    public $search_sesiones = '';
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
        $this->records_sesiones = collect();
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
        $recordsUsers = User::selectRaw('id, CONCAT(name, " ", lastname) as name, is_active')->whereIn('role_id', [1, 2])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $recordsPonentes = User::selectRaw('id, CONCAT(name, " ", lastname) as name, is_active')->whereIn('role_id', [1, 2, 3, 4])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.eventos', compact('records', 'recordsUsers', 'recordsPonentes'))
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
            'fields.inscriptions_enabled' => 'required|in:0,1',
            'fields.max_participants' => 'required|integer|min:1',
            'fields.contact_email' => 'required|email|max:250',
            'fields.contact_phone' => 'required|string|max:15',
            'fields.is_active' => 'required|in:0,1',
            'fields.mode' => 'required|string|max:50',
            'fields.is_paid' => 'required|in:0,1',
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
            $this->fields['inscriptions_enabled'] = (bool) $this->fields['inscriptions_enabled'];
            $this->fields['is_active'] = (bool) $this->fields['is_active'];
            $this->fields['is_paid'] = (bool) $this->fields['is_paid'];
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
            $this->cerrarModal('modal-home');
            $this->dispatch("message-success", "Eventos creado correctamente");
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
        $this->fields = [
            'title' => $item->title,
            'description' => $item->description,
            'start_time' => $item->start_time,
            'end_time' => $item->end_time,
            'location' => $item->location,
            'inscriptions_enabled' => $item->inscriptions_enabled ? '1' : '0',
            'max_participants' => $item->max_participants,
            'contact_email' => $item->contact_email,
            'contact_phone' => $item->contact_phone,
            'is_active' => $item->is_active ? '1' : '0',
            'mode' => $item->mode,
            'is_paid' => $item->is_paid ? '1' : '0',
            'price' => $item->price,
            'organizer_id' => $item->organizer_id,
        ];

        // $this->fields = $item->toArray();
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
            'fields.inscriptions_enabled' => 'required|in:0,1',
            'fields.max_participants' => 'required|integer|min:1',
            'fields.contact_email' => 'required|email|max:250',
            'fields.contact_phone' => 'required|string|max:15',
            'fields.is_active' => 'required|in:0,1',
            'fields.mode' => 'required|string|max:50',
            'fields.is_paid' => 'required|in:0,1',
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

            $this->fields['inscriptions_enabled'] = (bool) $this->fields['inscriptions_enabled'];
            $this->fields['is_active'] = (bool) $this->fields['is_active'];
            $this->fields['is_paid'] = (bool) $this->fields['is_paid'];

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

    public function sesiones($id)
    {

        $this->resetUI();

        $items = SessionesEvento::where('evento_id', $id)
            ->with('ponente')
            ->where(function ($query) {
                if (!empty($this->search_sesiones)) {
                    $query->where('title', 'like', '%' . $this->search_sesiones . '%')
                        ->orWhere('description', 'like', '%' . $this->search_sesiones . '%')
                        ->orWhere('location', 'like', '%' . $this->search_sesiones . '%')
                        ->orWhereHas('ponente', function ($q) {
                            $q->whereRaw("CONCAT(name, ' ', lastname) LIKE ?", ['%' . $this->search_sesiones . '%']);
                        });
                }
            })
            ->orderBy('id', 'asc')
            ->get();


        $this->abrirModal('Sesion-modal');
        $this->records_sesiones = $items;
        $this->record_id = $id;
        $this->fieldsSesiones['evento_id'] = $id;
        $evento = Eventos::find($id);
        $this->fields['start_time'] = $evento->start_time;
        $this->fields['end_time'] = $evento->end_time;
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
        $this->record_sesion_id = null;
        $this->records_sesiones = collect();
        $this->fields = [
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
        ];
        $this->fieldsSesiones = [
            'evento_id' => '',
            'title' => '',
            'description' => '',
            'start_time' => '',
            'end_time' => '',
            'ponente_id' => '',
            'mode' => '',
            'max_participants' => '',
            'require_approval' => '',
        ];
        $this->file = null;
    }

    #[On('setFields')]
    public function updateField($payload)
    {
        $c = explode('.', $payload['field']);
        $variable = $c[0];
        $field = $c[1];
        if (isset($payload['field']) && array_key_exists($field, $this->$variable)) {
            if (isset($payload['value'])) {
                $this->$variable[$field] = $payload['value'];
            }
        }
    }

    public function storeSesion()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'fieldsSesiones.evento_id' => 'required|exists:eventos,id',
            'fieldsSesiones.title' => 'required|string|max:250',
            'fieldsSesiones.description' => 'required|string|max:1000',
            'fieldsSesiones.start_time' => 'required|date',
            'fieldsSesiones.end_time' => 'required|date|after:fieldsSesiones.start_time',
            'fieldsSesiones.ponente_id' => 'required|exists:users,id',
            'fieldsSesiones.mode' => 'required|string|max:50',
            'fieldsSesiones.max_participants' => 'required|integer|min:1',
            'fieldsSesiones.require_approval' => 'required|in:0,1',
        ];

        $messages = [
            'fieldsSesiones.evento_id.required' => 'El evento es obligatorio.',
            'fieldsSesiones.evento_id.exists' => 'El evento seleccionado no es válido.',
            'fieldsSesiones.title.required' => 'El título es obligatorio.',
            'fieldsSesiones.title.string' => 'El título debe ser un texto válido.',
            'fieldsSesiones.title.max' => 'El título no puede tener más de 250 caracteres.',
            'fieldsSesiones.description.required' => 'La descripción es obligatoria.',
            'fieldsSesiones.description.string' => 'La descripción debe ser un texto válido.',
            'fieldsSesiones.description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'fieldsSesiones.start_time.required' => 'La fecha y hora de inicio son obligatorias.',
            'fieldsSesiones.start_time.date' => 'La fecha y hora de inicio deben ser una fecha válida.',
            'fieldsSesiones.end_time.required' => 'La fecha y hora de fin son obligatorias.',
            'fieldsSesiones.end_time.date' => 'La fecha y hora de fin deben ser una fecha válida.',
            'fieldsSesiones.end_time.after' => 'La fecha y hora de fin deben ser posteriores a la de inicio.',
            'fieldsSesiones.ponente_id.required' => 'El ponente es obligatorio.',
            'fieldsSesiones.ponente_id.exists' => 'El ponente seleccionado no es válido.',
            'fieldsSesiones.mode.required' => 'El modo es obligatorio.',
            'fieldsSesiones.mode.string' => 'El modo debe ser un texto válido.',
            'fieldsSesiones.mode.max' => 'El modo no puede tener más de 50 caracteres.',
            'fieldsSesiones.max_participants.required' => 'El número máximo de participantes es obligatorio.',
            'fieldsSesiones.max_participants.integer' => 'El número máximo de participantes debe ser un entero.',
            'fieldsSesiones.max_participants.min' => 'El número máximo de participantes debe ser al menos 1.',
            'fieldsSesiones.require_approval.required' => 'El campo de aprobación requerida es obligatorio.',
            'fieldsSesiones.require_approval.boolean' => 'El campo de aprobación requerida debe ser verdadero o falso.
            ',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = new SessionesEvento();
            $this->fieldsSesiones['require_approval'] = (bool) $this->fieldsSesiones['require_approval'];
            $item->fill($this->fieldsSesiones);
            $item->save();
            DB::commit();

            LogsSistema::create([
                'action' => 'create SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de una nueva sesión con ID ' . $item->id,
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->dispatch("message-success", "Sesión creada correctamente");
            $this->cerrarModal('Sesion-modal-form');
            $this->sesiones($this->record_id);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al crear SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear una nueva sesión: ' . $th->getMessage(),
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => null,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al crear la sesión");
        }
    }

    public function updateSesion()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'fieldsSesiones.title' => 'required|string|max:250',
            'fieldsSesiones.description' => 'required|string|max:1000',
            'fieldsSesiones.start_time' => 'required|date',
            'fieldsSesiones.end_time' => 'required|date|after:fieldsSesiones.start_time',
            'fieldsSesiones.ponente_id' => 'required|exists:users,id',
            'fieldsSesiones.mode' => 'required|string|max:50',
            'fieldsSesiones.max_participants' => 'required|integer|min:1',
            'fieldsSesiones.require_approval' => 'required|in:0,1',
        ];

        $messages = [
            'fieldsSesiones.title.required' => 'El título es obligatorio.',
            'fieldsSesiones.title.string' => 'El título debe ser un texto válido.',
            'fieldsSesiones.title.max' => 'El título no puede tener más de 250 caracteres.',
            'fieldsSesiones.description.required' => 'La descripción es obligatoria.',
            'fieldsSesiones.description.string' => 'La descripción debe ser un texto válido.',
            'fieldsSesiones.description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'fieldsSesiones.start_time.required' => 'La fecha y hora de inicio son obligatorias.',
            'fieldsSesiones.start_time.date' => 'La fecha y hora de inicio deben ser una fecha válida.',
            'fieldsSesiones.end_time.required' => 'La fecha y hora de fin son obligatorias.',
            'fieldsSesiones.end_time.date' => 'La fecha y hora de fin deben ser una fecha válida.',
            'fieldsSesiones.end_time.after' => 'La fecha y hora de fin deben ser posteriores a la de inicio.',
            'fieldsSesiones.ponente_id.required' => 'El ponente es obligatorio.',
            'fieldsSesiones.ponente_id.exists' => 'El ponente seleccionado no es válido.',
            'fieldsSesiones.mode.required' => 'El modo es obligatorio.',
            'fieldsSesiones.mode.string' => 'El modo debe ser un texto válido.',
            'fieldsSesiones.mode.max' => 'El modo no puede tener más de 50 caracteres.',
            'fieldsSesiones.max_participants.required' => 'El número.maxcdn de participantes es obligatorio.',
            'fieldsSesiones.max_participants.integer' => 'El número.maxcdn de participantes debe ser un entero.',
            'fieldsSesiones.max_participants.min' => 'El número.maxcdn de participantes debe ser al menos 1.',
            'fieldsSesiones.require_approval.required' => 'El campo de aprobación requerida es obligatorio.',
            'fieldsSesiones.require_approval.boolean' => 'El campo de aprobación requerida debe ser verdadero o falso.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = SessionesEvento::find($this->fieldsSesiones['id']);
            $this->fieldsSesiones['require_approval'] = (bool) $this->fieldsSesiones['require_approval'];
            $item->fill($this->fieldsSesiones);
            $item->save();
            DB::commit();

            LogsSistema::create([
                'action' => 'update SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización de la sesión con ID ' . $item->id,
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->dispatch("message-success", "Sesión actualizada correctamente");
            $this->cerrarModal('Sesion-modal-form');

            $this->sesiones($item->evento_id);
        } catch (\Throwable $th) {
            DB::rollBack();

            LogsSistema::create([
                'action' => 'error al actualizar SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al actualizar la sesión con ID ' . $this->fieldsSesiones['id'] . ': ' . $th->getMessage(),
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => $this->fieldsSesiones['id'],
                'status' => 'error',
            ]);

            $this->dispatch("message-error", "Error al actualizar la sesión");
        }
    }

    public function editSesion($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->fieldsSesiones = [
            'evento_id' => '',
            'title' => '',
            'description' => '',
            'start_time' => '',
            'end_time' => '',
            'ponente_id' => '',
            'mode' => '',
            'max_participants' => '',
            'require_approval' => '',
        ];

        $item = SessionesEvento::find($id);
        if (!$item) {
            LogsSistema::create([
                'action' => 'error al editar SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Intento de edición de una sesión inexistente con ID ' . $id,
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => $id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Sesión no encontrada");
            return;
        }

        $this->record_sesion_id = $item->id;
        $this->fieldsSesiones = [
            'id' => $item->id,
            'evento_id' => $item->evento_id,
            'title' => $item->title,
            'description' => $item->description,
            'start_time' => $item->start_time,
            'end_time' => $item->end_time,
            'ponente_id' => $item->ponente_id,
            'mode' => $item->mode,
            'max_participants' => $item->max_participants,
            'require_approval' => $item->require_approval ? '1' : '0',
        ];

        $evento = Eventos::find($item->evento_id);
        $this->fields['start_time'] = $evento->start_time;
        $this->fields['end_time'] = $evento->end_time;

        $this->abrirModal('Sesion-modal-form', false);
    }

    #[On("deleteSesion")]
    public function destroySesion($id)
    {
        try {
            DB::beginTransaction();
            $item = SessionesEvento::find($id);
            $item->delete();
            DB::commit();
            LogsSistema::create([
                'action' => 'delete SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Eliminación de la sesión con ID ' . $item->id,
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);
            $this->dispatch("message-success", "Sesión eliminada correctamente");
            $this->sesiones($this->record_id);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al eliminar SessionesEvento',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al eliminar la sesión con ID ' . $item->id . ': ' . $th->getMessage(),
                'target_table' => (new SessionesEvento())->getTable(),
                'target_id' => $item->id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al eliminar la sesión");
        }
    }
}