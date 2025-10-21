<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Documentos;
use App\Models\LogsSistema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DocumentosController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [
        'title' => '',
        'description' => '',
        'visibility' => '',
    ];
    public $file;
    public $search = '';
    public $paginate = 10;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'fields.title' => 'required|string|max:255',
        'fields.description' => 'required|string|max:255',
        'fields.visibility' => 'required|in:publico,privado',
        'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,png,jpg,jpeg|max:5120',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
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
            'fields.title' => 'required|string|max:255',
            'fields.description' => 'required|string|max:255',
            'fields.visibility' => 'required|in:publico,privado',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,png,jpg,jpeg|max:5120',
        ];
        $this->validate($rules);

        $path = null;
        try {
            DB::beginTransaction();
            $extension = $this->file->getClientOriginalExtension();
            $baseName = Str::slug($this->fields['title']);
            // Evita colisiones: agrega un sufijo único si ya existe
            $fileName = $baseName . '.' . $extension;

            if (Storage::disk('documents')->exists($fileName)) {
                $fileName = $baseName . '-' . uniqid() . '.' . $extension;
            }

            $path = $this->file->storeAs('', $fileName, 'documents');
            $storagePath = 'documents/' . $path;

            $item = Documentos::create([
                'user_id' => auth()->id(),
                'name' => $this->fields['title'],
                'description' => $this->fields['description'],
                'path' => $storagePath,
                'type' => $extension,
                'is_valid' => true,
                'visibility' => $this->fields['visibility'],
            ]);
            DB::commit();

            LogsSistema::create([
                'action' => 'create Documentos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de un nuevo Documento con ID ' . $item->id,
                'target_table' => (new Documentos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->cerrarModal('modal-home');
            $this->dispatch("message-success", "Documento creado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('documents')->delete($path);
            if (file_exists($path)) {
                unlink($path);
            }
            LogsSistema::create([
                'action' => 'create Documentos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear un nuevo Documento: ' . $e->getMessage(),
                'target_table' => (new Documentos())->getTable(),
                'target_id' => null,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al subir el documento");
        }

        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
        }
    }

    public function edit($id)
    {
        $this->resetUI();
        $item = Documentos::find($id);
        if (!$item) {
            session()->flash('error', 'Documento no encontrado.');
            return;
        }
        $this->record_id = $item->id;
        $this->fields = [
            'title' => $item->name,
            'description' => $item->description,
            'visibility' => $item->visibility,
        ];
        $this->abrirModal('modal-home', false);
    }

    public function update()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $rules = [
            'fields.title' => 'required|string|max:255',
            'fields.description' => 'required|string|max:255',
            'fields.visibility' => 'required|in:publico,privado',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,png,jpg,jpeg|max:5120',
        ];
        $this->validate($rules);

        $path = null;
        try {
            DB::beginTransaction();
            $item = Documentos::find($this->record_id);
            if (!$item) {
                session()->flash('error', 'Documento no encontrado.');
                return;
            }

            $item->name = $this->fields['title'];
            $item->description = $this->fields['description'];
            $item->visibility = $this->fields['visibility'];

            if ($this->file) {
                // Reemplazo del archivo: borrar el anterior en el disco 'documents' y subir el nuevo
                if ($item->path) {
                    $oldName = basename($item->path);
                    if (Storage::disk('documents')->exists($oldName)) {
                        Storage::disk('documents')->delete($oldName);
                    }
                }

                $extension = $this->file->getClientOriginalExtension();
                $baseName = Str::slug($this->fields['title']);
                $fileName = $baseName . '.' . $extension;
                if (Storage::disk('documents')->exists($fileName)) {
                    $fileName = $baseName . '-' . uniqid() . '.' . $extension;
                }
                $stored = $this->file->storeAs('', $fileName, 'documents'); // devuelve nombre de archivo
                $item->path = 'documents/' . $stored;
                $item->type = $extension;
            } else {
                // Sin nuevo archivo: si solo cambió el nombre, renombrar el archivo físico
                if ($item->path) {
                    $oldName = basename($item->path);
                    $currentExt = $item->type ?: pathinfo($oldName, PATHINFO_EXTENSION);
                    $newBase = Str::slug($this->fields['title']);
                    $newName = $newBase . '.' . $currentExt;

                    if ($oldName !== $newName) {
                        // Evitar colisión
                        $finalName = $newName;
                        if (Storage::disk('documents')->exists($finalName)) {
                            $finalName = $newBase . '-' . uniqid() . '.' . $currentExt;
                        }
                        if (Storage::disk('documents')->exists($oldName)) {
                            Storage::disk('documents')->move($oldName, $finalName);
                            $item->path = 'documents/' . $finalName;
                        } else {
                            // Si por alguna razón no existe, no fallar: mantener path actual
                        }
                    }
                }
            }

            $item->save();
            DB::commit();

            LogsSistema::create([
                'action' => 'update Documentos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización del Documento con ID ' . $item->id,
                'target_table' => (new Documentos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->cerrarModal('modal-home');
            $this->dispatch("message-success", "Documento actualizado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('documents')->delete($path);
            if (file_exists($this->file->getRealPath())) {
                unlink($this->file->getRealPath());
            }
            LogsSistema::create([
                'action' => 'update Documentos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización del Documento con ID ' . $item->id,
                'target_table' => (new Documentos())->getTable(),
                'target_id' => $item->id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", $e->getMessage());
        }

        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
        }
    }

    #[On("delete")]
    public function destroy($id)
    {
        $item = Documentos::find($id);
        if (!$item) {
            session()->flash('error', 'Documento no encontrado.');
            return;
        }
        // Eliminar del disco 'documents' usando el nombre base
        if ($item->path) {
            $base = basename($item->path);
            if (Storage::disk('documents')->exists($base)) {
                Storage::disk('documents')->delete($base);
            }
        }
        $item->delete();
        LogsSistema::create([
            'action' => 'delete Documentos',
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'description' => 'Eliminación del Documento con ID ' . $id,
            'target_table' => (new Documentos())->getTable(),
            'target_id' => $id,
            'status' => 'success',
        ]);
        $this->dispatch("message-success", "Documento eliminado correctamente");
    }

    public function resetUI()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->record_id = null;
        $this->fields = [
            'title' => '',
            'visibility' => '',
        ];
        $this->file = null;
    }

    public function render()
    {
        $query = Documentos::query();
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        $records = $query->orderBy('id', 'asc')->paginate($this->paginate);
        return view('livewire.admin.documentos', compact('records'))
            ->extends('layouts.admin')
            ->section('content');
        /*return view('livewire.admin.documentos', [
            'documentos' => $documentos,
        ])->extends('layouts.admin')
        ->section('content');*/
    }
}