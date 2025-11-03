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
use App\Models\Imagenes;
use App\Models\User;
use App\Models\SessionesEvento;
use App\Models\InscripcionesEvento;
use App\Models\Certificados;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

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
    public $records_users_event;
    public $file;          // archivo temporal
    public $file2;          // archivo temporal
    public $search = '';
    public $search_sesiones = '';
    public $paginate = 10;
    public bool $loading = false;
    public $orden = 'desc';      // 'asc' o 'desc'
    public $modalidad = '';      // '', 'presencial', 'virtual'
    public $estado = ''; // '', 'activo', 'inactivo'
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
        $this->records_users_event = collect();
        $this->records_sesiones = collect();
    }

    public function render()
    {
        $query = Eventos::query();

        // Búsqueda
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('location', 'like', $searchTerm)
                    ->orWhere('contact_email', 'like', $searchTerm);
            });
        }

        // Filtro por modalidad
        if (!empty($this->modalidad)) {
            $query->where('mode', $this->modalidad);
        }
        // Filtro por estado (activo/inactivo)
        if ($this->estado === 'activo') {
            $query->where('is_active', true);
        } elseif ($this->estado === 'inactivo') {
            $query->where('is_active', false);
        }

        // Orden por fecha de inicio
        $query->orderBy('start_time', $this->orden);

        $records = $query->paginate($this->paginate);

        // Usuarios y ponentes (sin cambios)
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            $concatExpression = "TRIM(name || ' ' || lastname)";
        } else {
            $concatExpression = "TRIM(CONCAT_WS(' ', name, lastname))";
        }
        $recordsUsers = User::selectRaw("id, {$concatExpression} as name, is_active")
            ->whereIn('role_id', [1, 2])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $recordsPonentes = User::selectRaw("id, {$concatExpression} as name, is_active")
            ->whereIn('role_id', [1, 2, 3, 4])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.eventos', compact('records', 'recordsUsers', 'recordsPonentes'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function abrirModal($idModal = 'modal-home', $initVoid = true, $newSession = false)
    {
        if ($initVoid) {
            $this->resetUI();
        } else {
            $this->resetErrorBag();
            $this->resetValidation();
        }
        if ($newSession) {
            $this->record_sesion_id = null;
            $this->fieldsSesiones = [
                'evento_id' => $this->record_id,
                'title' => '',
                'description' => '',
                'start_time' => '',
                'end_time' => '',
                'ponente_id' => '',
                'mode' => '',
                'max_participants' => '',
                'require_approval' => '',
            ];
        }
        $this->dispatch("abrir-modal", ['modal' => $idModal]);
    }

    public function cerrarModal($idModal = 'modal-home', $initVoid = true)
    {
        if ($initVoid) {
            $this->resetUI();
        }
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
            'file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
        ];

        if ($this->fields['is_paid'] && ($this->fields['is_paid'] == '1' || $this->fields['is_paid'] == 1)) {
            $rules['fields.price'] = 'required|numeric|min:0.01';
        } else {
            $rules['fields.price'] = 'nullable|numeric|min:0';
            $this->fields['price'] = 0;
        }

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
            'fields.price.required' => 'El precio es obligatorio.',
            'fields.price.numeric' => 'El precio debe ser un número.',
            'fields.price.min' => 'El precio no puede ser negativo y debe ser al menos mayor a 0.',
            'fields.organizer_id.required' => 'El organizador es obligatorio.',
            'fields.organizer_id.exists' => 'El organizador seleccionado no es válido.',

            'file.image' => 'El archivo debe ser una imagen (jpeg, png).',
            'file.mimes' => 'El archivo debe ser una imagen (jpeg, png).',
            'file.max' => 'El archivo no puede tener más de 10MB.',
        ];

        $this->validate($rules, $messages);
        $path = null;

        try {
            DB::beginTransaction();
            $item = new Eventos();
            $this->fields['inscriptions_enabled'] = (bool) $this->fields['inscriptions_enabled'];
            $this->fields['is_active'] = (bool) $this->fields['is_active'];
            $this->fields['is_paid'] = (bool) $this->fields['is_paid'];
            $item->fill($this->fields);
            $item->save();

            if ($this->file) {
                // Crear nombre de archivo: slug_del_titulo + random + extensión
                $extension = $this->file->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";

                // Guardar en el disco configurado ("images")
                $path = $this->file->storeAs('eventos', $filename, 'images');

                // Crear nuevo registro en la tabla de imágenes
                Imagenes::create([
                    'related_table' => (new Eventos())->getTable(),
                    'related_id' => $item->id,
                    'url' => Storage::disk('images')->url($path),
                    'path' => $path,
                    'alt_text' => $item->title,
                    'size' => $this->file->getSize(),
                    'mime_type' => $this->file->getMimeType(),
                    'is_main' => true,
                ]);
            }
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
            /* borrar el archivo */
            if ($path && Storage::disk('images')->exists($path)) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
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
        $this->file = null;
        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
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
            'main_image' => $item->main_image,
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
            'file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
        ];

        if ($this->fields['is_paid'] && ($this->fields['is_paid'] == '1' || $this->fields['is_paid'] == 1)) {
            $rules['fields.price'] = 'required|numeric|min:0.01';
        } else {
            $rules['fields.price'] = 'nullable|numeric|min:0';
            $this->fields['price'] = 0;
        }

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
            'fields.price.required' => 'El precio es obligatorio.',
            'fields.price.numeric' => 'El precio debe ser un número.',
            'fields.price.min' => 'El precio no puede ser negativo y debe ser al menos mayor a 0.',
            'fields.organizer_id.required' => 'El organizador es obligatorio.',
            'fields.organizer_id.exists' => 'El organizador seleccionado no es válido.',

            'file.image' => 'El archivo debe ser una imagen (jpeg, png).',
            'file.max' => 'El archivo no puede tener más de 10MB.',
        ];

        $this->validate($rules, $messages);
        $path = null;

        try {
            DB::beginTransaction();
            $item = Eventos::find($this->record_id);

            $this->fields['inscriptions_enabled'] = (bool) $this->fields['inscriptions_enabled'];
            $this->fields['is_active'] = (bool) $this->fields['is_active'];
            $this->fields['is_paid'] = (bool) $this->fields['is_paid'];
            $item->fill($this->fields);
            $item->save();

            if ($this->file) {
                // Eliminar imagen anterior si existe
                $currentImage = Imagenes::where('related_id', $item->id)
                    ->where('related_table', (new Eventos())->getTable())
                    ->first();

                if ($currentImage) {
                    if (Storage::disk('images')->exists($currentImage->path)) {
                        Storage::disk('images')->delete($currentImage->path);
                    }
                    $currentImage->delete();
                }

                // Crear nombre de archivo: slug_del_titulo + random + extensión
                $extension = $this->file->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";

                // Guardar en el disco configurado ("images")
                $path = $this->file->storeAs('eventos', $filename, 'images');

                // Crear nuevo registro en la tabla de imágenes
                Imagenes::create([
                    'related_table' => (new Eventos())->getTable(),
                    'related_id' => $item->id,
                    'url' => Storage::disk('images')->url($path),
                    'path' => $path,
                    'alt_text' => $item->title,
                    'size' => $this->file->getSize(),
                    'mime_type' => $this->file->getMimeType(),
                    'is_main' => true,
                ]);
            }
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
            /* borrar el archivo */
            if ($path) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
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

        $this->file = null;
        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
        }
    }

    public function sesiones($id)
    {
        $this->resetUI();

        $query = SessionesEvento::query()
            ->where('evento_id', $id)
            ->with('ponente')
            ->orderBy('id', 'asc');

        if (!empty($this->search_sesiones)) {
            $query->where(function ($q) {
                $search = '%' . $this->search_sesiones . '%';
                $connection = $q->getConnection()->getDriverName();

                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhereHas('ponente', function ($q2) use ($search, $connection) {
                        if ($connection === 'pgsql') {
                            // PostgreSQL usa ||
                            $q2->whereRaw("(name || ' ' || lastname) ILIKE ?", [$search]);
                        } else {
                            // MySQL y otros
                            $q2->whereRaw("CONCAT(name, ' ', lastname) LIKE ?", [$search]);
                        }
                    });
            });
        }

        $items = $query->get();

        $this->records_sesiones = $items;
        $this->record_id = $id;
        $this->fieldsSesiones['evento_id'] = $id;

        $evento = Eventos::find($id);
        if ($evento) {
            $this->fields['start_time'] = $evento->start_time;
            $this->fields['end_time'] = $evento->end_time;
        }

        $this->abrirModal('Sesion-modal', false, true);
    }

    public function participantesEventos($idEvento)
    {
        $this->records_users_event = collect();
        $this->records_users_event = User::join('inscripciones_eventos', 'users.id', '=', 'inscripciones_eventos.user_id')
            ->where('inscripciones_eventos.evento_id', $idEvento)
            ->where('inscripciones_eventos.status', '!=', 'rechazado')
            ->select('users.*', 'inscripciones_eventos.status')
            ->get();

        if ($this->records_users_event === null) {
            $this->records_users_event = collect();
        }

        $this->record_id = $idEvento;
        $this->abrirModal('participantes-evento-modal', false);
    }

    protected $listeners = ['confirmarAprobarParticipante', 'confirmarRechazarParticipante'];

    // logica para aprobar los participante de los eventos
    public function aprobarParticipante($idParticipante)
    {
        $this->cerrarModal('participantes-evento-modal', false);
        $this->dispatch('confirmar-inscripcion', idEvento: $this->record_id, idSesion: null, idParticipante: $idParticipante, title: '¿Confirmar solicitud de inscripción?', text: '¿Está seguro que desea confirmar la solicitud de inscripción a este evento?', metodo: 'confirmarAprobarParticipante');
    }

    public function confirmarAprobarParticipante($idEvento, $idParticipante)
    {
        $inscripcion = InscripcionesEvento::where('evento_id', $idEvento)
            ->where('user_id', $idParticipante)
            ->first();

        if ($inscripcion) {
            $inscripcion->status = 'registrado';
            $inscripcion->save();

            $this->dispatch('inscripcion-message', $idEvento, 'Peticion de Inscripción Aprovada', 'participantesEventos');
        } else {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No se encontró la inscripción del participante.'
            ]);
        }
    }

    // logica para Rechazar los participante de los eventos
    public function rechazarParticipante($idParticipante)
    {
        $this->cerrarModal('participantes-evento-modal', false);
        $this->dispatch('confirmar-cancelacion', idEvento: $this->record_id, idSesion: null, idParticipante: $idParticipante, title: 'Rechazar solicitud de inscripción?', text: '¿Está seguro que desea rechazar la solicitud de inscripción a este evento?', metodo: 'confirmarRechazarParticipante');
    }

    public function confirmarRechazarParticipante($idEvento, $idParticipante)
    {
        $inscripcion = InscripcionesEvento::where('evento_id', $idEvento)
            ->where('user_id', $idParticipante)
            ->first();

        if ($inscripcion) {
            $inscripcion->status = 'rechazado';
            $inscripcion->save();

            $certificado = Certificados::where('evento_id', $idEvento)
                ->where('user_id', $idParticipante)
                ->first();
            if ($certificado) {
                $certificado->is_valid = false;
                $certificado->save();
            }

            $this->dispatch('inscripcion-message', $idEvento, 'Peticion de Inscripción Rechazada', 'participantesEventos');
        } else {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No se encontró la inscripción del participante.'
            ]);
        }
    }

    #[On("delete")]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $item = Eventos::find($id);

            $images = Imagenes::where('related_id', $item->id)
                ->where('related_table', (new Eventos())->getTable())
                ->get();

            foreach ($images as $image) {
                Storage::disk('images')->delete($image->path);
                if (file_exists($image->path)) {
                    unlink($image->path);
                }
                $image->delete();
            }

            $sesiones = SessionesEvento::where('evento_id', $item->id)->get();
            foreach ($sesiones as $sesion) {
                $imagenSesion = Imagenes::where('related_id', $sesion->id)
                    ->where('related_table', (new SessionesEvento())->getTable())
                    ->get();
                foreach ($imagenSesion as $imgSes) {
                    Storage::disk('images')->delete($imgSes->path);
                    if (file_exists($imgSes->path)) {
                        unlink($imgSes->path);
                    }
                    $imgSes->delete();
                }
                $sesion->ponente()->dissociate();
                $sesion->delete();
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

    protected $updatesQueryString = ['search_sesiones'];

    public function updatedSearchSesiones()
    {
        if ($this->record_id) {
            $this->sesiones($this->record_id);
        }
    }

    public function resetUI()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->record_id = null;
        $this->record_sesion_id = null;
        $this->records_sesiones = collect();
        $this->records_users_event = collect();
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
        $this->file2 = null;
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
            'file2' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
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
            'fieldsSesiones.require_approval.boolean' => 'El campo de aprobación requerida debe ser verdadero o falso.',
            'file2.image' => 'El archivo debe ser una imagen (jpeg, png).',
            'file2.mimes' => 'El archivo debe ser una imagen (jpeg, png).',
            'file2.max' => 'El archivo no puede tener más de 10MB.',
        ];

        $this->validate($rules, $messages);
        $path = null;

        try {
            DB::beginTransaction();
            $item = new SessionesEvento();
            $this->fieldsSesiones['require_approval'] = (bool) $this->fieldsSesiones['require_approval'];
            $item->fill($this->fieldsSesiones);
            $item->save();

            if ($this->file2) {
                // Crear nombre de archivo: slug_del_titulo + random + extensión
                $extension = $this->file2->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";

                // Guardar en el disco configurado ("images")
                $path = $this->file2->storeAs('sesiones', $filename, 'images');

                // Crear nuevo registro en la tabla de imágenes
                Imagenes::create([
                    'related_table' => (new SessionesEvento())->getTable(),
                    'related_id' => $item->id,
                    'url' => Storage::disk('images')->url($path),
                    'path' => $path,
                    'alt_text' => $item->title,
                    'size' => $this->file2->getSize(),
                    'mime_type' => $this->file2->getMimeType(),
                    'is_main' => true,
                ]);
            }
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
            $this->sesiones($item->evento_id);
            $this->fieldsSesiones['evento_id'] = $item->evento_id;
            $this->record_id = $item->evento_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            /* borrar el archivo */
            if ($path) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
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
        $this->file2 = null;
        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
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
            'file2' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
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
            'file2.image' => 'El archivo debe ser una imagen.',
            'file2.mimes' => 'El archivo debe ser una imagen con extensiones jpeg, jpg o png.',
            'file2.max' => 'El archivo debe tener un tamaño máximo de 10MB.',
        ];

        $this->validate($rules, $messages);
        $path = null;

        try {
            DB::beginTransaction();
            $item = SessionesEvento::find($this->fieldsSesiones['id']);
            $this->fieldsSesiones['require_approval'] = (bool) $this->fieldsSesiones['require_approval'];
            $item->fill($this->fieldsSesiones);
            $item->save();

            if ($this->file2) {
                // Eliminar imagen anterior si existe
                $currentImage = Imagenes::where('related_id', $item->id)
                    ->where('related_table', (new SessionesEvento())->getTable())
                    ->first();

                if ($currentImage) {
                    if (Storage::disk('images')->exists($currentImage->path)) {
                        Storage::disk('images')->delete($currentImage->path);
                        if (file_exists($currentImage->path)) {
                            unlink($currentImage->path);
                        }
                    }
                    $currentImage->delete();
                }

                // Crear nombre de archivo: slug_del_titulo + random + extensión
                $extension = $this->file2->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";

                // Guardar en el disco configurado ("images")
                $path = $this->file2->storeAs('sesiones', $filename, 'images');

                // Crear nuevo registro en la tabla de imágenes
                Imagenes::create([
                    'related_table' => (new SessionesEvento())->getTable(),
                    'related_id' => $item->id,
                    'url' => Storage::disk('images')->url($path),
                    'path' => $path,
                    'alt_text' => $item->title,
                    'size' => $this->file2->getSize(),
                    'mime_type' => $this->file2->getMimeType(),
                    'is_main' => true,
                ]);
            }
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
            $this->fieldsSesiones['evento_id'] = $item->evento_id;
            $this->record_id = $item->evento_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            /* borrar el archivo */
            if ($path) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

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
        $this->file2 = null;
        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
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
            'main_image' => null,
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
        $this->record_id = $item->evento_id;
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
            'main_image' => $item->main_image,
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
            $images = Imagenes::where('related_id', $item->id)
                ->where('related_table', (new SessionesEvento())->getTable())
                ->get();
            foreach ($images as $image) {
                Storage::disk('images')->delete($image->path);
                if (file_exists($image->path)) {
                    unlink($image->path);
                }
                $image->delete();
            }
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

    public function generarDiplomasAll($idEvento)
    {
        $data = [];
        try {
            DB::beginTransaction();
            $evento = Eventos::find($idEvento);
            if (!$evento) {
                throw new \Exception('Evento no encontrado');
            }

            $participantes = User::join('inscripciones_eventos', 'users.id', '=', 'inscripciones_eventos.user_id')
                ->where('inscripciones_eventos.evento_id', $idEvento)
                ->whereNotIn('inscripciones_eventos.status', ['cancelado', 'rechazado', 'pendiente'])
                ->select('users.*', 'inscripciones_eventos.status')
                ->get();

            if ($participantes->isEmpty()) {
                throw new \Exception('No se encontraron participantes registrados para el evento');
            }

            foreach ($participantes as $participante) {
                /* verificar si el participante ya tiene un certificado */
                $existingCertificate = Certificados::where('user_id', $participante->id)
                    ->where('evento_id', $idEvento)
                    ->first();

                if ($existingCertificate) {
                    // Crear el código QR
                    $result = (new Builder(
                        writer: new PngWriter(),
                        data: $existingCertificate->url,
                        encoding: new Encoding('UTF-8'),
                        errorCorrectionLevel: ErrorCorrectionLevel::Low,
                        size: 150,
                        margin: 5,
                        roundBlockSizeMode: RoundBlockSizeMode::Margin,
                    ))->build();

                    // Convertir a Base64 para usar en PDF o Blade
                    $codigoQr = $result->getDataUri();

                    $data[] = [
                        'recipient_name' => $participante->name . ' ' . $participante->lastname,
                        'event_name' => $evento->title,
                        'date' => Carbon::parse($evento->end_time)->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
                        'url' => $existingCertificate->url,
                        'qr_code' => $codigoQr,
                        'code' => $existingCertificate->codigo_qr,
                    ];
                } else {
                    /* generar certificado */
                    $uniqueCode = Str::uuid()->toString();
                    $certificateUrl = route('ver-certificado', ['code' => $uniqueCode]);

                    // Crear el código QR
                    $result = (new Builder(
                        writer: new PngWriter(),
                        data: $certificateUrl,
                        encoding: new Encoding('UTF-8'),
                        errorCorrectionLevel: ErrorCorrectionLevel::Low,
                        size: 150,
                        margin: 5,
                        roundBlockSizeMode: RoundBlockSizeMode::Margin,
                    ))->build();

                    // Convertir a Base64 para usar en PDF o Blade
                    $codigoQr = $result->getDataUri();

                    Certificados::create([
                        'user_id' => $participante->id,
                        'evento_id' => $idEvento,
                        'emitido_en' => now(),
                        'url' => $certificateUrl,
                        'codigo_qr' => $uniqueCode,
                        'is_valid' => true,
                    ]);

                    $data[] = [
                        'recipient_name' => $participante->name . ' ' . $participante->lastname,
                        'event_name' => $evento->title,
                        'date' => Carbon::parse($evento->end_time)->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
                        'url' => $certificateUrl,
                        'qr_code' => $codigoQr,
                        'code' => $uniqueCode,
                    ];
                }
            }
            DB::commit();
            LogsSistema::create([
                'action' => 'generate diplomas',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Generación de diplomas para el evento con ID ' . $idEvento,
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $idEvento,
                'status' => 'success',
            ]);
            $this->dispatch("generate-diplomas", $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al generar diplomas',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al generar diplomas para el evento con ID ' . $idEvento . ': ' . $th->getMessage(),
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $idEvento,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al generar los diplomas");
        } finally {
            $this->records_users_event = User::join('inscripciones_eventos', 'users.id', '=', 'inscripciones_eventos.user_id')
                ->where('inscripciones_eventos.evento_id', $idEvento)
                ->where('inscripciones_eventos.status', '!=', 'rechazado')
                ->select('users.*', 'inscripciones_eventos.status')
                ->get();
        }
    }

    public function generarDiplomaIndividual($idEvento, $idParticipante)
    {
        $data = [];
        try {
            DB::beginTransaction();
            $evento = Eventos::find($idEvento);
            if (!$evento) {
                throw new \Exception('Evento no encontrado');
            }

            $participante = User::find($idParticipante);
            if (!$participante) {
                throw new \Exception('Participante no encontrado');
            }

            /* verificar si el participante ya tiene un certificado */
            $existingCertificate = Certificados::where('user_id', $participante->id)
                ->where('evento_id', $idEvento)
                ->first();

            if ($existingCertificate) {
                // Crear el código QR
                $result = (new Builder(
                    writer: new PngWriter(),
                    data: $existingCertificate->url,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::Low,
                    size: 150,
                    margin: 5,
                    roundBlockSizeMode: RoundBlockSizeMode::Margin,
                ))->build();

                // Convertir a Base64 para usar en PDF o Blade
                $codigoQr = $result->getDataUri();

                $data[] = [
                    'recipient_name' => $participante->name . ' ' . $participante->lastname,
                    'event_name' => $evento->title,
                    'date' => Carbon::parse($evento->end_time)->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
                    'url' => $existingCertificate->url,
                    'qr_code' => $codigoQr,
                    'code' => $existingCertificate->codigo_qr,
                ];
            } else {
                /* generar certificado */
                $uniqueCode = Str::uuid()->toString();
                $certificateUrl = route('ver-certificado', ['code' => $uniqueCode]);

                // Crear el código QR
                $result = (new Builder(
                    writer: new PngWriter(),
                    data: $certificateUrl,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::Low,
                    size: 150,
                    margin: 5,
                    roundBlockSizeMode: RoundBlockSizeMode::Margin,
                ))->build();

                // Convertir a Base64 para usar en PDF o Blade
                $codigoQr = $result->getDataUri();

                Certificados::create([
                    'user_id' => $participante->id,
                    'evento_id' => $idEvento,
                    'emitido_en' => now(),
                    'url' => $certificateUrl,
                    'codigo_qr' => $uniqueCode,
                    'is_valid' => true,
                ]);

                $data[] = [
                    'recipient_name' => $participante->name . ' ' . $participante->lastname,
                    'event_name' => $evento->title,
                    'date' => Carbon::parse($evento->end_time)->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
                    'url' => $certificateUrl,
                    'qr_code' => $codigoQr,
                    'code' => $uniqueCode,
                ];
            }

            DB::commit();

            LogsSistema::create([
                'action' => 'generate diploma individual',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Generación de diploma para el participante con ID ' . $idParticipante . ' en el evento con ID ' . $idEvento,
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $idEvento,
                'status' => 'success',
            ]);

            $this->dispatch("generate-diploma-individual", $data, $idParticipante);
        } catch (\Throwable $th) {
            DB::rollBack();

            LogsSistema::create([
                'action' => 'error al generar diploma individual',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al generar diploma para el participante con ID ' . $idParticipante . ' en el evento con ID ' . $idEvento . ': ' . $th->getMessage(),
                'target_table' => (new Eventos())->getTable(),
                'target_id' => $idEvento,
                'status' => 'error',
            ]);

            $this->dispatch("message-error", "Error al generar el certificado");
        } finally {
            $this->records_users_event = User::join('inscripciones_eventos', 'users.id', '=', 'inscripciones_eventos.user_id')
                ->where('inscripciones_eventos.evento_id', $idEvento)
                ->where('inscripciones_eventos.status', '!=', 'rechazado')
                ->select('users.*', 'inscripciones_eventos.status')
                ->get();
        }
    }
}