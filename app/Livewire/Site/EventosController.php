<?php

namespace App\Livewire\Site;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Eventos;
use App\Models\SessionesEvento;
use App\Models\User;

class EventosController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $records_sesiones;
    public $records_event;
    public $records_sesion;
    public $records_ponente;
    public $fields = [];   // inputs normales
    public $file;          // archivo temporal
    public $search = '';
    public $paginate = 10;

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function render()
    {
        $query = Eventos::query();

        if (!empty($this->search)) {
            foreach ((new Eventos())->getFillable() as $field) {
                $query->orWhere($field, 'like', '%' . $this->search . '%');
            }
        }

        $records = $query->orderBy('id', 'desc')->paginate($this->paginate);

        return view('livewire.site.eventos', compact('records'))
            ->extends('layouts.site')
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
            $item = new Eventos();
            $item->fill($this->fields);

            if ($this->file) {
                $path = $this->file->store('uploads', 'public');
                $item->file_path = $path;
            }

            $item->save();
            DB::commit();

            $this->resetUI();
            $this->dispatch("message-success", "Eventos creado correctamente");
            $this->dispatch("cerrar-modal");
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch("message-error", "Error al crear");
        }
    }

    public function edit($id)
    {
        $item = Eventos::find($id);
        if (!$item) {
            $this->dispatch("message-error", "Eventos no encontrado");
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

            $this->resetUI();
            $this->dispatch("message-success", "Eventos actualizado correctamente");
            $this->dispatch("cerrar-modal");
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch("message-error", "Error al actualizar");
        }
    }

    public function sesiones($id)
    {

        $query = SessionesEvento::query()
            ->where('evento_id', $id)
            ->with('ponente')
            ->orderBy('id', 'asc');

        $item = Eventos::find($id);
        
        $this->records_event = $item;

        // if (!empty($this->search_sesiones)) {
        //     $query->where(function ($q) {
        //         $search = '%' . $this->search_sesiones . '%';
        //         $connection = $q->getConnection()->getDriverName();

        //         $q->where('title', 'like', $search)
        //             ->orWhere('description', 'like', $search)
        //             ->orWhereHas('ponente', function ($q2) use ($search, $connection) {
        //                 if ($connection === 'pgsql') {
        //                     // PostgreSQL usa ||
        //                     $q2->whereRaw("(name || ' ' || lastname) ILIKE ?", [$search]);
        //                 } else {
        //                     // MySQL y otros
        //                     $q2->whereRaw("CONCAT(name, ' ', lastname) LIKE ?", [$search]);
        //                 }
        //             });
        //     });
        // }

        $items = $query->get();

        $this->records_sesiones = $items;
        $this->record_id = $id;

        // $evento = Eventos::find($id);
        // if ($evento) {
        //     $this->fields['start_time'] = $evento->start_time;
        //     $this->fields['end_time'] = $evento->end_time;
        // }

        $this->abrirModal('event-modal', false);
    }

    public function sesion($id){
        // sesion seleccionada
        $item = SessionesEvento::find($id); 

        $this->records_sesion = $item;

        // ponente de la sesion
        $ponente = User::find($item['ponente_id']);

        $this->records_ponente = $ponente;

        $this->abrirModal('sesion-modal', false);
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

            $this->dispatch("message-success", "Eventos eliminado correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch("message-error", "Error al eliminar");
        }
    }

    public function resetUI()
    {
        $this->record_id = null;
        $this->records_sesiones = collect();
        $this->records_event = collect();
        $this->records_sesion = collect();
        $this->records_ponente = collect();
        $this->fields = [];
        $this->file = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}