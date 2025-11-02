<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\OfertasDeEmpleo;
use App\Models\LogsSistema;
use App\Models\Imagenes;
use Illuminate\Support\Facades\File;

class OfertasDeEmpleoController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [
        'title' => '',
        'description' => '',
        'location' => '',
        'company_name' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'is_active' => false,
        'salary' => null,
        'vacancies' => 1,
        'application_deadline' => null,
    ];
    public $file;

    public $search = '';
    public $paginate = 10;
    public $filter_active = '';
    public $order_direction = 'desc';

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

        $rules = [
            'fields.title' => 'required|string|max:250',
            'fields.description' => 'required|string',
            'fields.location' => 'required|string|max:150',
            'fields.company_name' => 'required|string|max:150',
            'fields.contact_email' => 'required|email|max:150',
            'fields.contact_phone' => 'nullable|string|max:50',
            'fields.is_active' => 'boolean',
            'fields.salary' => 'nullable|numeric|min:0',
            'fields.vacancies' => 'required|integer|min:1',
            'fields.application_deadline' => 'nullable|date',
            'file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
        ];
        $this->validate($rules);

        try {
            DB::beginTransaction();

            $item = new OfertasDeEmpleo();
            $deadline = $this->fields['application_deadline'] ?? null;
            if (!empty($deadline)) {
                // Acepta valores como "2025-11-01T10:30"
                $deadline = Carbon::parse(str_replace('T', ' ', $deadline));
            } else {
                $deadline = null;
            }
            $item->fill([
                'title' => $this->fields['title'],
                'description' => $this->fields['description'],
                'location' => $this->fields['location'],
                'company_name' => $this->fields['company_name'],
                'contact_email' => $this->fields['contact_email'],
                'contact_phone' => $this->fields['contact_phone'],
                'is_active' => (bool) ($this->fields['is_active'] ?? false),
                'salary' => $this->fields['salary'],
                'vacancies' => $this->fields['vacancies'] ?: 1,
                'posted_by' => Auth::id(),
                'application_deadline' => $deadline,
            ]);
            $item->save();

            // Imagen principal opcional
            $path = null;
            if ($this->file) {
                $extension = $this->file->getClientOriginalExtension();
                $slugTitle = Str::slug($item->title, '-');
                $randomCode = Str::random(8);
                $filename = "{$slugTitle}-{$randomCode}.{$extension}";
                $path = $this->file->storeAs('empleos', $filename, 'images');

                Imagenes::create([
                    'related_table' => (new OfertasDeEmpleo())->getTable(),
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
                'action' => 'create OfertasDeEmpleo',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de Oferta de Empleo con ID ' . $item->id,
                'target_table' => (new OfertasDeEmpleo())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->cerrarModal('modal-home');
            $this->dispatch('message-success', 'Oferta creada correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (isset($path) && $path && Storage::disk('images')->exists($path)) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            LogsSistema::create([
                'action' => 'error al crear OfertasDeEmpleo',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear Oferta: ' . $th->getMessage(),
                'target_table' => (new OfertasDeEmpleo())->getTable(),
                'target_id' => null,
                'status' => 'error',
            ]);
            $this->dispatch('message-error', 'Error al crear');
        }

        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
        }
    }

    public function edit($id)
    {
        $this->resetUI();

        $item = OfertasDeEmpleo::find($id);
        if (!$item) {
            $this->dispatch('message-error', 'Oferta no encontrada');
            return;
        }

        $this->record_id = $item->id;
        $this->fields = [
            'title' => $item->title,
            'description' => $item->description,
            'location' => $item->location,
            'company_name' => $item->company_name,
            'contact_email' => $item->contact_email,
            'contact_phone' => $item->contact_phone,
            'is_active' => (bool) $item->is_active,
            'salary' => $item->salary,
            'vacancies' => $item->vacancies,
            'application_deadline' => optional($item->application_deadline)->format('Y-m-d\TH:i'),
            'main_image' => Imagenes::where('related_id', $item->id)
                ->where('related_table', (new OfertasDeEmpleo())->getTable())
                ->where('is_main', true)
                ->value('url'),
        ];

        $this->abrirModal('modal-home', false);
    }

    public function update()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'fields.title' => 'required|string|max:250',
            'fields.description' => 'required|string',
            'fields.location' => 'required|string|max:150',
            'fields.company_name' => 'required|string|max:150',
            'fields.contact_email' => 'required|email|max:150',
            'fields.contact_phone' => 'nullable|string|max:50',
            'fields.is_active' => 'boolean',
            'fields.salary' => 'nullable|numeric|min:0',
            'fields.vacancies' => 'required|integer|min:1',
            'fields.application_deadline' => 'nullable|date',
        ];
        $this->validate($rules);

        try {
            DB::beginTransaction();
            $item = OfertasDeEmpleo::find($this->record_id);
            if (!$item) {
                throw new \Exception('Oferta no encontrada');
            }

            $deadline = $this->fields['application_deadline'] ?? null;
            if (!empty($deadline)) {
                $deadline = Carbon::parse(str_replace('T', ' ', $deadline));
            } else {
                $deadline = null;
            }

            $item->fill([
                'title' => $this->fields['title'],
                'description' => $this->fields['description'],
                'location' => $this->fields['location'],
                'company_name' => $this->fields['company_name'],
                'contact_email' => $this->fields['contact_email'],
                'contact_phone' => $this->fields['contact_phone'],
                'is_active' => (bool) ($this->fields['is_active'] ?? false),
                'salary' => $this->fields['salary'],
                'vacancies' => $this->fields['vacancies'] ?: 1,
                'application_deadline' => $deadline,
            ]);
            $item->save();

            // Imagen principal: reemplazo
            if ($this->file) {
                $currentImage = Imagenes::where('related_id', $item->id)
                    ->where('related_table', (new OfertasDeEmpleo())->getTable())
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
                $path = $this->file->storeAs('empleos', $filename, 'images');

                Imagenes::create([
                    'related_table' => (new OfertasDeEmpleo())->getTable(),
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
                'action' => 'update OfertasDeEmpleo',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización de Oferta con ID ' . $item->id,
                'target_table' => (new OfertasDeEmpleo())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->dispatch('message-success', 'Oferta actualizada correctamente');
            $this->cerrarModal('modal-home');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (isset($path) && $path) {
                Storage::disk('images')->delete($path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            LogsSistema::create([
                'action' => 'error al actualizar OfertasDeEmpleo',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al actualizar Oferta ID ' . $this->record_id . ': ' . $th->getMessage(),
                'target_table' => (new OfertasDeEmpleo())->getTable(),
                'target_id' => $this->record_id,
                'status' => 'error',
            ]);
            $this->dispatch('message-error', 'Error al actualizar');
        }

        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
        }
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $item = OfertasDeEmpleo::find($id);
            if (!$item) {
                throw new \Exception('Oferta no encontrada');
            }

            // eliminar imágenes
            $images = Imagenes::where('related_id', $item->id)
                ->where('related_table', (new OfertasDeEmpleo())->getTable())
                ->get();
            foreach ($images as $image) {
                Storage::disk('images')->delete($image->path);
                if (file_exists($image->path)) {
                    @unlink($image->path);
                }
                $image->delete();
            }

            $item->delete();
            DB::commit();

            LogsSistema::create([
                'action' => 'delete OfertasDeEmpleo',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Eliminación de Oferta con ID ' . $id,
                'target_table' => (new OfertasDeEmpleo())->getTable(),
                'target_id' => $id,
                'status' => 'success',
            ]);

            $this->dispatch('message-success', 'Oferta eliminada correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al eliminar OfertasDeEmpleo',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al eliminar Oferta ID ' . $id . ': ' . $th->getMessage(),
                'target_table' => (new OfertasDeEmpleo())->getTable(),
                'target_id' => $id,
                'status' => 'error',
            ]);
            $this->dispatch('message-error', 'Error al eliminar');
        }
    }

    public function resetUI()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->record_id = null;
        $this->fields = [
            'title' => '',
            'description' => '',
            'location' => '',
            'company_name' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'is_active' => false,
            'salary' => null,
            'vacancies' => 1,
            'application_deadline' => null,
        ];
        $this->file = null;
    }

    public function render()
    {
        $query = OfertasDeEmpleo::query();
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('company_name', 'like', $search)
                    ->orWhere('location', 'like', $search);
            });
        }
        if ($this->filter_active !== '') {
            $query->where('is_active', (bool) $this->filter_active);
        }

        $direction = strtolower($this->order_direction) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $direction)->orderBy('id', $direction);

        $records = $query->paginate($this->paginate);

        return view('livewire.admin.ofertas', compact('records'))
            ->extends('layouts.admin')
            ->section('content');
    }
}