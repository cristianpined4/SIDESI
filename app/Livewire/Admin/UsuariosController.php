<?php

namespace App\Livewire\Admin;

use App\Models\Roles;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\LogsSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User as Usuarios;

class UsuariosController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [
        'name' => '',
        'lastname' => '',
        'email' => '',
        'password' => '',
        'is_active' => '1',
        'role_id' => '',
        'username' => '',
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
        $query = Usuarios::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        $records = $query->orderBy('id', 'asc')->paginate($this->paginate);
        $roles = Roles::where('is_active', true)->get();

        return view('livewire.admin.usuarios', compact('records', 'roles'))
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

        $this->fields['role_id'] = intval($this->fields['role_id']);

        $rules = [
            'fields.name' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.lastname' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.email' => 'required|email|regex:/^.+@.+\..+$/|unique:users,email',
            'fields.password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?]).+$/',
            'fields.is_active' => 'required|boolean',
            'fields.role_id' => 'required|integer|exists:roles,id',
            'fields.username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9._]+$/u|unique:users,username',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es requerido',
            'fields.lastname.required' => 'El apellido es requerido',
            'fields.email.required' => 'El correo electrónico es requerido',
            'fields.password.required' => 'La contraseña es requerida',
            'fields.is_active.required' => 'El estado es requerido',
            'fields.role_id.required' => 'El rol es requerido',
            'fields.username.required' => 'El nombre de usuario es requerido',

            'fields.name.string' => 'El nombre debe ser un texto válido.',
            'fields.name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'fields.name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'fields.name.regex' => 'El nombre solo puede contener letras y espacios.',

            'fields.lastname.string' => 'El apellido debe ser un texto válido.',
            'fields.lastname.min' => 'El apellido debe tener al menos 3 caracteres.',
            'fields.lastname.max' => 'El apellido no puede tener más de 50 caracteres.',
            'fields.lastname.regex' => 'El apellido solo puede contener letras y espacios.',

            'fields.email.email' => 'El correo electr&oacute;nico debe ser una direcci&oacute;n v&aacute;lida.',
            'fields.email.regex' => 'El correo electr&oacute;nico debe ser una direcci&oacute;n v&aacute;lida.',
            'fields.email.unique' => 'El correo electr&oacute;nico ya existe.',

            'fields.password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'fields.password.regex' => 'La contraseña debe contener al menos una letra may&uacute;scula, una letra min&uacute;scula, un n&uacute;mero y un car&aacute;cter especial.',

            'fields.is_active.boolean' => 'El estado debe ser verdadero o falso.',

            'fields.role_id.exists' => 'El rol no existe.',

            'fields.username.string' => 'El nombre de usuario debe ser un texto válido.',
            'fields.username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'fields.username.max' => 'El nombre de usuario no puede tener más de 50 caracteres.',
            'fields.username.regex' => 'El nombre de usuario solo puede contener letras, números y puntos.',
            'fields.username.unique' => 'El nombre de usuario ya existe.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = new Usuarios();
            $item->fill($this->fields);
            $item->password = bcrypt($this->fields['password']);
            $item->save();
            DB::commit();

            LogsSistema::create([
                'action' => 'create Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Creación de un nuevo Usuario con ID ' . $item->id,
                'target_table' => (new Usuarios())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);
            $this->resetUI();
            $this->dispatch("message-success", "Usuario creado correctamente");
            $this->cerrarModal('form-usuarios');
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al crear Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al crear un nuevo Usuario: ' . $th->getMessage(),
                'target_table' => (new Usuarios())->getTable(),
                'target_id' => null,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Error al crear");
        }
    }

    public function edit($id)
    {
        $this->resetUI();
        $item = Usuarios::find($id);
        if (!$item) {
            LogsSistema::create([
                'action' => 'error al editar Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Intento de edición de un Usuario inexistente con ID ' . $id,
                'target_table' => (new Usuario())->getTable(),
                'target_id' => $id,
                'status' => 'error',
            ]);
            $this->dispatch("message-error", "Usuario no encontrado");
            return;
        }

        $this->record_id = $item->id;
        $this->fields = $item->toArray();
        $this->abrirModal('form-usuarios', false);
    }

    public function update()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if (!empty($this->fields['password'])) {
            $this->fields['password'] = bcrypt($this->fields['password']);
        } else {
            /* quitar la contraseña del objeto */
            unset($this->fields['password']);
        }
        $this->fields['role_id'] = intval($this->fields['role_id']);

        $rules = [
            'fields.name' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.lastname' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.email' => 'required|email|regex:/^.+@.+\..+$/|unique:users,email,' . $this->record_id,
            'fields.password' => 'min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?]).+$/',
            'fields.is_active' => 'required|boolean',
            'fields.role_id' => 'required|integer|exists:roles,id',
            'fields.username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9._]+$/u|unique:users,username,' . $this->record_id,
        ];

        $messages = [
            'fields.name.required' => 'El nombre es requerido',
            'fields.lastname.required' => 'El apellido es requerido',
            'fields.email.required' => 'El correo electrónico es requerido',
            'fields.password.required' => 'La contraseña es requerida',
            'fields.is_active.required' => 'El estado es requerido',
            'fields.role_id.required' => 'El rol es requerido',
            'fields.username.required' => 'El nombre de usuario es requerido',

            'fields.name.string' => 'El nombre debe ser un texto válido.',
            'fields.name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'fields.name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'fields.name.regex' => 'El nombre solo puede contener letras y espacios.',

            'fields.lastname.string' => 'El apellido debe ser un texto válido.',
            'fields.lastname.min' => 'El apellido debe tener al menos 3 caracteres.',
            'fields.lastname.max' => 'El apellido no puede tener más de 50 caracteres.',
            'fields.lastname.regex' => 'El apellido solo puede contener letras y espacios.',

            'fields.email.email' => 'El correo electr&oacute;nico debe ser una direcci&oacute;n v&aacute;lida.',
            'fields.email.regex' => 'El correo electr&oacute;nico debe ser una direcci&oacute;n v&aacute;lida.',
            'fields.email.unique' => 'El correo electr&oacute;nico ya existe.',

            'fields.password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'fields.password.regex' => 'La contraseña debe contener al menos una letra may&uacute;scula, una letra min&uacute;scula, un n&uacute;mero y un car&aacute;cter especial.',

            'fields.is_active.boolean' => 'El estado debe ser verdadero o falso.',

            'fields.role_id.exists' => 'El rol no existe.',

            'fields.username.string' => 'El nombre de usuario debe ser un texto válido.',
            'fields.username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'fields.username.max' => 'El nombre de usuario no puede tener más de 50 caracteres.',
            'fields.username.regex' => 'El nombre de usuario solo puede contener letras, números y puntos.',
            'fields.username.unique' => 'El nombre de usuario ya existe.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::beginTransaction();
            $item = Usuarios::find($this->record_id);
            $item->fill($this->fields);
            $item->save();
            DB::commit();

            if (!empty($this->fields['password']) && $item->id === auth()->id()) {
                Auth::loginUsingId($this->record_id);
            }

            LogsSistema::create([
                'action' => 'update Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Actualización del Usuario con ID ' . $item->id,
                'target_table' => (new Usuarios())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->resetUI();
            $this->cerrarModal('form-usuarios');
            $this->dispatch("message-success", "Usuario actualizado correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al actualizar Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al actualizar el Usuario con ID ' . $this->record_id . ': ' . $th->getMessage(),
                'target_table' => (new Usuarios())->getTable(),
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
            $item = Usuarios::find($id);

            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }

            $item->delete();
            DB::commit();

            LogsSistema::create([
                'action' => 'delete Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Eliminación del Usuario con ID ' . $item->id,
                'target_table' => (new Usuarios())->getTable(),
                'target_id' => $item->id,
                'status' => 'success',
            ]);

            $this->dispatch("message-success", "Usuario eliminado correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'error al eliminar Usuario',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => 'Error al eliminar el Usuario con ID ' . $item->id . ': ' . $th->getMessage(),
                'target_table' => (new Usuarios())->getTable(),
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
        $this->fields = [
            'name' => '',
            'lastname' => '',
            'email' => '',
            'password' => '',
            'is_active' => '1',
            'role_id' => '',
            'username' => '',
        ];
        $this->file = null;
    }
}