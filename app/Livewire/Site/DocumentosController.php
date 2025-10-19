<?php

namespace App\Livewire\Site;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Documentos;

class DocumentosController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
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
        $query = Documentos::query()
            ->where('visibility', 'publico')
            ->where('is_valid', true);

        if (!empty($this->search)) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%");
            });
        }

        $records = $query->orderBy('id', 'desc')->paginate($this->paginate);

        return view('livewire.site.documentos', compact('records'))
            ->extends('layouts.site')
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
            $item = new Documentos();
            $item->fill($this->fields);

            if ($this->file) {
                $path = $this->file->store('uploads', 'public');
                $item->file_path = $path;
            }

            $item->save();
            DB::commit();

            $this->resetUI();
            $this->dispatch("message-success", "Documentos creado correctamente");
            $this->dispatch("cerrar-modal");
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch("message-error", "Error al crear");
        }
    }

    public function edit($id)
    {
        $item = Documentos::find($id);
        if (!$item) {
            $this->dispatch("message-error", "Documentos no encontrado");
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
            $item = Documentos::find($this->record_id);
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
            $this->dispatch("message-success", "Documentos actualizado correctamente");
            $this->dispatch("cerrar-modal");
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch("message-error", "Error al actualizar");
        }
    }

     #[On("delete")]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $item = Documentos::find($id);

            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }

            $item->delete();
            DB::commit();

            $this->dispatch("message-success", "Documentos eliminado correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
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