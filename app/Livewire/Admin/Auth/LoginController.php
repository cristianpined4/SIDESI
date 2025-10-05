<?php

namespace App\Livewire\Admin\Auth;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LogsSistema;
use Livewire\Attributes\Validate;

class LoginController extends Component
{
    use WithPagination, WithFileUploads;

    public $username;
    public $password;
    public bool $loading = false;

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function mount()
    {
        if (Auth::check()) {
            if (in_array(Auth::user()->role_id, [1, 2])) {
                return redirect()->route('dashboard-admin');
            } else {
                return redirect()->route('home-site');
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.auth.login')->extends('layouts.loginAndRegister')->section('content');
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

    public function login()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $rules = [
            'username' => 'required|string|max:50|exists:users,username',
            'password' => 'required|min:6|max:30',
        ];

        $messages = [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'username.exists' => 'El nombre de usuario no existe en nuestros registros.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.max' => 'La contraseña debe tener máximo 30 caracteres.',
        ];

        $validatedData = Validator::make($this->all(), $rules, $messages);
        if ($validatedData->fails()) {
            $this->setErrorBag($validatedData->errors());
            LogsSistema::create([
                'action' => 'LOGIN',
                'user_id' => null,
                'ip_address' => request()->ip(),
                'description' => 'Intento de inicio de sesión fallido en el sistema - Validación fallida',
                'target_table' => 'users',
                'target_id' => null,
                'status' => 'error',
            ]);
            return;
        }

        $user = DB::table('users')->where('username', $this->username)->first();

        if ($user->is_active == 0) {
            LogsSistema::create([
                'action' => 'LOGIN',
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'description' => 'Intento de inicio de sesión fallido en el sistema - Usuario inactivo',
                'target_table' => 'users',
                'target_id' => $user->id,
                'status' => 'error',
            ]);
            $this->addError('username', 'El usuario se encuentra inactivo.');
            return;
        }

        if (!password_verify($this->password, $user->password)) {
            LogsSistema::create([
                'action' => 'LOGIN',
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'description' => 'Intento de inicio de sesión fallido en el sistema',
                'target_table' => 'users',
                'target_id' => $user->id,
                'status' => 'error',
            ]);
            $this->addError('password', 'La contraseña es incorrecta.');
            return;
        }

        try {

            Auth::loginUsingId($user->id);

            LogsSistema::create([
                'action' => 'LOGIN',
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'description' => 'Inicio de sesión en el sistema',
                'target_table' => 'users',
                'target_id' => $user->id,
                'status' => 'success',
            ]);

            if (in_array($user->role_id, [1, 2])) {
                return redirect()->route('dashboard-admin');
            } else {
                return redirect()->route('home-site');
            }

        } catch (\Exception $e) {
            LogsSistema::create([
                'action' => 'LOGIN',
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'description' => 'Error inesperado al intentar iniciar sesión en el sistema',
                'target_table' => 'users',
                'target_id' => $user->id,
                'status' => 'error',
            ]);
            $this->addError('username', 'Ocurrió un error al intentar iniciar sesión. Por favor, inténtelo de nuevo.');
            return;
        }
    }

    public function resetUI()
    {
        $this->username = '';
        $this->password = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->back();
    }
}