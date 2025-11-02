<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notifications\PasswordChanged;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class PerfilController extends Component
{
    use WithFileUploads;
    public string $layout = 'layouts.site';
    public $fields = [
        'name' => '',
        'lastname' => '',
        'email' => '',
        'username' => '',
        'phone' => '',
        'document_type' => '',
        'document_number' => '',
        'institution' => '',
    ];

    public bool $loading = false;
    public $avatar; // uploaded file
    public $passwords = [
        'current' => '',
        'new' => '',
        'confirm' => '',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        // Seleccionar layout según rol
        $this->layout = in_array((int) ($user->role_id ?? 0), [1, 2]) && request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.site';

        $this->fields['name'] = $user->name;
        $this->fields['lastname'] = $user->lastname;
        $this->fields['email'] = $user->email;
        $this->fields['username'] = $user->username;
        $this->fields['phone'] = $user->phone;
        $this->fields['document_type'] = $user->document_type;
        $this->fields['document_number'] = $user->document_number;
        $this->fields['institution'] = $user->institution;
    }

    public function update()
    {
        $userId = Auth::id();

        $rules = [
            'fields.name' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.lastname' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.email' => [
                'required',
                'email',
                'regex:/^.+@.+\..+$/',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'fields.username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9._]+$/u',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'fields.phone' => 'nullable|string|max:30',
            'fields.document_type' => 'nullable|string|max:50',
            'fields.document_number' => 'nullable|string|max:100',
            'fields.institution' => 'nullable|string|max:150',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es requerido',
            'fields.lastname.required' => 'El apellido es requerido',
            'fields.email.required' => 'El correo electrónico es requerido',
            'fields.username.required' => 'El nombre de usuario es requerido',
        ];

        $this->validate($rules, $messages);

        $this->loading = true;
        try {
            $user = Auth::user();
            $user->fill($this->fields);
            $user->save();

            $this->dispatch('message-success', 'Perfil actualizado correctamente');
        } catch (\Throwable $th) {
            $this->dispatch('message-error', 'Error al actualizar el perfil');
        } finally {
            $this->loading = false;
        }
    }

    public function updateAvatar()
    {
        $this->validate([
            'avatar' => 'required|image|max:4096', // hasta 4MB por procesamiento
        ], [
            'avatar.required' => 'Selecciona una imagen',
            'avatar.image' => 'El archivo debe ser una imagen',
            'avatar.max' => 'La imagen no debe superar 4MB',
        ]);

        try {
            $user = Auth::user();

            // Leer binario y crear imagen GD
            $raw = file_get_contents($this->avatar->getRealPath());
            $src = imagecreatefromstring($raw);
            if ($src === false) {
                throw new \RuntimeException('Imagen inválida');
            }
            $w = imagesx($src);
            $h = imagesy($src);
            // Crop centrado a cuadrado
            $side = min($w, $h);
            $srcX = (int) (($w - $side) / 2);
            $srcY = (int) (($h - $side) / 2);
            $crop = imagecreatetruecolor($side, $side);
            imagecopyresampled($crop, $src, 0, 0, $srcX, $srcY, $side, $side, $side, $side);
            imagedestroy($src);
            // Redimensionar a 512x512
            $dst = imagecreatetruecolor(512, 512);
            imagecopyresampled($dst, $crop, 0, 0, 0, 0, 512, 512, $side, $side);
            imagedestroy($crop);

            // Generar nombre y guardar como jpg de alta calidad
            $filename = 'avatars/' . uniqid() . '.jpg';
            ob_start();
            imagejpeg($dst, null, 90);
            $jpegData = ob_get_clean();
            imagedestroy($dst);

            // Eliminar anterior si existe
            $metadata = is_array($user->metadata) ? $user->metadata : [];
            if (!empty($metadata['avatar']) && Storage::disk('images')->exists($metadata['avatar'])) {
                Storage::disk('images')->delete($metadata['avatar']);
            }

            Storage::disk('images')->put($filename, $jpegData);
            $metadata['avatar'] = 'img/' . $filename;
            $user->metadata = $metadata;
            $user->save();

            $this->reset('avatar');
            $this->dispatch('message-success', 'Foto de perfil actualizada');
        } catch (\Throwable $th) {
            $this->dispatch('message-error', 'No se pudo actualizar la foto');
        }

        if (File::exists(storage_path('app/private'))) {
            File::deleteDirectory(storage_path('app/private'));
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'passwords.current' => 'required',
            'passwords.new' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-={}\[\]:;"\'`~<>,.?\/]).+$/'
            ],
            'passwords.confirm' => 'required|same:passwords.new',
        ], [
            'passwords.current.required' => 'Ingresa tu contraseña actual',
            'passwords.new.required' => 'Ingresa la nueva contraseña',
            'passwords.new.min' => 'Debe tener al menos 8 caracteres',
            'passwords.new.regex' => 'Debe incluir mayúsculas, minúsculas, número y carácter especial',
            'passwords.confirm.same' => 'La confirmación no coincide',
        ]);

        $user = Auth::user();
        if (!Hash::check($this->passwords['current'], $user->password)) {
            $this->addError('passwords.current', 'La contraseña actual no es correcta');
            return;
        }

        try {
            $user->password = $this->passwords['new']; // hashed by cast
            $user->save();
            // Cerrar sesiones activas en otros dispositivos
            Auth::logoutOtherDevices($this->passwords['new']);
            // Limpiar sesiones en tabla sessions excepto la actual
            $currentId = request()->session()->getId();
            DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', $currentId)->delete();
            // Notificar por correo
            try {
                $user->notify(new PasswordChanged());
            } catch (\Throwable $e) {
            }
            $this->passwords = ['current' => '', 'new' => '', 'confirm' => ''];
            $this->dispatch('message-success', 'Contraseña actualizada correctamente');
        } catch (\Throwable $th) {
            $this->dispatch('message-error', 'No se pudo actualizar la contraseña');
        }
    }

    public function deleteAvatar()
    {
        try {
            $user = Auth::user();
            $metadata = is_array($user->metadata) ? $user->metadata : [];
            if (!empty($metadata['avatar']) && Storage::disk('images')->exists($metadata['avatar'])) {
                Storage::disk('images')->delete($metadata['avatar']);
            }
            unset($metadata['avatar']);
            $user->metadata = $metadata;
            $user->save();
            $this->dispatch('message-success', 'Foto de perfil eliminada');
        } catch (\Throwable $th) {
            $this->dispatch('message-error', 'No se pudo eliminar la foto');
        }
    }

    public function resetFields()
    {
        $this->mount();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.perfil')
            ->extends($this->layout)
            ->section('content');
    }
}