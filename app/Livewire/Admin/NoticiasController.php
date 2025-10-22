<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Contenidos;
use App\Models\ContenidoCuerpo;
use App\Models\Imagenes;
use App\Models\LogsSistema;
use Illuminate\Support\Facades\File;

class NoticiasController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [
        'title' => '',
        'slug' => '',
        'description' => '',
        'content_type' => '',
        'status' => '',
    ];
    public $body = '';
    public $file;
    public $search = '';
    public $paginate = 10;
    public $filter_type = '';
    public $filter_status = '';
    public $order_direction = 'desc'; // 'desc' más reciente primero, 'asc' más antiguo primero

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
        $this->dispatch("abrir-modal", ['modal' => $idModal, 'body' => $this->body]);
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
            'fields.content_type' => 'required|in:Evento,Convocatoria,Noticia,Información',
            'fields.status' => 'required|in:draft,published,archived',
            'file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
        ];
        $messages = [
            'fields.title.required' => 'El título es obligatorio.',
            'fields.title.string' => 'El título debe ser un texto válido.',
            'fields.title.max' => 'El título no puede tener más de 250 caracteres.',
            'fields.description.required' => 'La descripción es obligatoria.',
            'fields.description.string' => 'La descripción debe ser un texto válido.',
            'fields.description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'fields.status.required' => 'El estado es obligatorio.',
            'fields.status.in' => 'Estado inválido.',
            'file.image' => 'El archivo debe ser una imagen (jpeg, png).',
            'file.mimes' => 'El archivo debe ser una imagen (jpeg, png).',
            'file.max' => 'El archivo no puede tener más de 10MB.',
        ];
        $this->validate($rules, $messages);

        $path = null;

        try {
            DB::beginTransaction();

            $item = new Contenidos();
            $this->fields['slug'] = Str::slug($this->fields['title']);
            $item->fill([
                'title' => $this->fields['title'],
                'slug' => $this->fields['slug'],
                'description' => $this->fields['description'],
                'content_type' => $this->fields['content_type'],
                'autor_id' => Auth::id(),
                'status' => $this->fields['status'] ?: 'draft',
            ]);
            $item->save();

            // Guardar cuerpo largo si existe
            if (!empty($this->body)) {
                ContenidoCuerpo::updateOrCreate(
                    ['contenido_id' => $item->id],
                    ['body' => $this->body]
                );
            }

            // Imagen principal
            if ($this->file) {
                $extension = $this->file->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";
                $path = $this->file->storeAs('noticias', $filename, 'images');

                Imagenes::create([
                    'related_table' => (new Contenidos())->getTable(),
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
                'action' => 'create Contenidos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de Noticia con ID ' . $item->id,
                'target_table' => (new Contenidos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->cerrarModal('modal-home');
            $this->dispatch("message-success", "Noticia creada correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($path && Storage::disk('images')->exists($path)) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            LogsSistema::create([
                'action' => 'error al crear Contenidos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear Noticia: ' . $th->getMessage(),
                'target_table' => (new Contenidos())->getTable(),
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

        $item = Contenidos::find($id);
        if (!$item) {
            $this->dispatch("message-error", "Noticia no encontrada");
            return;
        }

        $this->record_id = $item->id;
        $this->fields = [
            'title' => $item->title,
            'slug' => $item->slug,
            'description' => $item->description,
            'content_type' => $item->content_type,
            'status' => $item->status,
            'main_image' => $item->main_image,
        ];
        $this->body = optional($item->contenidoCuerpo)->body ?? '';

        $this->abrirModal('modal-home', false);
    }

    public function update()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'fields.title' => 'required|string|max:250',
            'fields.description' => 'required|string|max:1000',
            'fields.content_type' => 'required|in:Evento,Convocatoria,Noticia,Información',
            'fields.status' => 'required|in:draft,published,archived',
            'file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
        ];
        $this->validate($rules);

        $path = null;

        try {
            DB::beginTransaction();
            $item = Contenidos::find($this->record_id);
            if (!$item) {
                throw new \Exception('Noticia no encontrada');
            }

            $item->fill([
                'title' => $this->fields['title'],
                'slug' => Str::slug($this->fields['title']),
                'description' => $this->fields['description'],
                'content_type' => $this->fields['content_type'],
                'status' => $this->fields['status'],
            ]);
            $item->save();

            // Cuerpo
            ContenidoCuerpo::updateOrCreate(
                ['contenido_id' => $item->id],
                ['body' => $this->body]
            );

            // Imagen principal: reemplazo
            if ($this->file) {
                $currentImage = Imagenes::where('related_id', $item->id)
                    ->where('related_table', (new Contenidos())->getTable())
                    ->first();

                if ($currentImage) {
                    if (Storage::disk('images')->exists($currentImage->path)) {
                        Storage::disk('images')->delete($currentImage->path);
                    }
                    $currentImage->delete();
                }

                $extension = $this->file->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";
                $path = $this->file->storeAs('noticias', $filename, 'images');

                Imagenes::create([
                    'related_table' => (new Contenidos())->getTable(),
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
                'action' => 'update Contenidos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización de Noticia con ID ' . $item->id,
                'target_table' => (new Contenidos())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->dispatch("message-success", "Noticia actualizada correctamente");
            $this->cerrarModal('modal-home');
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($path) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            LogsSistema::create([
                'action' => 'error al actualizar Contenidos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al actualizar Noticia ID ' . $this->record_id . ': ' . $th->getMessage(),
                'target_table' => (new Contenidos())->getTable(),
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

    #[On("delete")]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $item = Contenidos::find($id);
            if (!$item) {
                throw new \Exception('Noticia no encontrada');
            }

            // eliminar imágenes
            $images = Imagenes::where('related_id', $item->id)
                ->where('related_table', (new Contenidos())->getTable())
                ->get();
            foreach ($images as $image) {
                Storage::disk('images')->delete($image->path);
                if (file_exists($image->path)) {
                    @unlink($image->path);
                }
                $image->delete();
            }

            // eliminar cuerpo si existe
            ContenidoCuerpo::where('contenido_id', $item->id)->delete();

            $item->delete();
            DB::commit();

            LogsSistema::create([
                'action' => 'delete Contenidos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Eliminación de Noticia con ID ' . $id,
                'target_table' => (new Contenidos())->getTable(),
                'target_id' => $id,
                'status' => 'success',
            ]);

            $this->dispatch("message-success", "Noticia eliminada correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al eliminar Contenidos',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al eliminar Noticia ID ' . $id . ': ' . $th->getMessage(),
                'target_table' => (new Contenidos())->getTable(),
                'target_id' => $id,
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
        $this->fields = [
            'title' => '',
            'slug' => '',
            'description' => '',
            'content_type' => '',
            'status' => '',
        ];
        $this->body = '';
        $this->file = null;
    }

    public function render()
    {
        $query = Contenidos::query();
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }
        if (!empty($this->filter_type)) {
            $query->where('content_type', $this->filter_type);
        }
        if (!empty($this->filter_status)) {
            $query->where('status', $this->filter_status);
        }

        $direction = strtolower($this->order_direction) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $direction)->orderBy('id', $direction);

        $records = $query->paginate($this->paginate);

        return view('livewire.admin.noticias', compact('records'))
            ->extends('layouts.admin')
            ->section('content');
    }
}