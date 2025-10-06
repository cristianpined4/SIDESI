<?php

namespace App\Livewire\Admin\Auth;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\LogsSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class RegisterController extends Component
{
    use WithPagination, WithFileUploads;
    public bool $loading = false;
    public $fields = [
        'name' => null,
        'email' => null,
        'password' => null,
        'password_confirmation' => null,
        'role_id' => 'Docente',
        'document_type' => 'DUI',
        'document_number' => null,
        'institution' => null,
        'phone' => null,
        'username' => null,
        'lastname' => null
    ];

    private $universidades = [

        // GUATEMALA
        "Universidad de San Carlos de Guatemala (USAC) — Guatemala (Ciudad de Guatemala)",
        "Universidad del Valle de Guatemala (UVG) — Guatemala (Ciudad de Guatemala)",
        "Universidad Rafael Landívar (URL) — Guatemala (Ciudad de Guatemala)",
        "Universidad Francisco Marroquín (UFM) — Guatemala (Ciudad de Guatemala)",
        "Universidad Mariano Gálvez (UMG) — Guatemala (Ciudad de Guatemala)",
        "Universidad Galileo — Guatemala (Ciudad de Guatemala)",
        "Universidad del Istmo (UNIS) — Guatemala (Ciudad de Guatemala)",
        "Universidad Panamericana (UPANA) — Guatemala (Ciudad de Guatemala)",
        "Universidad Mesoamericana — Guatemala (Quetzaltenango)",
        "Universidad Regional de Guatemala (URG) — Guatemala (Mazatenango)",
        "Universidad San Pablo de Guatemala (USP) — Guatemala (Ciudad de Guatemala)",
        "Universidad Da Vinci — Guatemala (Mixco)",
        "Universidad Rural de Guatemala — Guatemala (Jalapa)",

        // EL SALVADOR
        "Universidad de El Salvador (UES) — El Salvador (San Salvador)",
        "Universidad Centroamericana José Simeón Cañas (UCA) — El Salvador (San Salvador)",
        "Universidad Tecnológica de El Salvador (UTEC) — El Salvador (San Salvador)",
        "Universidad Don Bosco (UDB) — El Salvador (Soyapango)",
        "Universidad Dr. José Matías Delgado (UJMD) — El Salvador (Antiguo Cuscatlán)",
        "Universidad Francisco Gavidia (UFG) — El Salvador (San Salvador)",
        "Universidad Evangélica de El Salvador (UEES) — El Salvador (San Salvador y San Miguel)",
        "Universidad Salvadoreña Alberto Masferrer (USAM) — El Salvador (San Salvador)",
        "Universidad Andrés Bello (UNAB) — El Salvador (San Salvador y San Miguel)",
        "Universidad Pedagógica de El Salvador (UPES) — El Salvador (San Salvador y San Miguel)",
        "Universidad Politécnica de El Salvador (UPES) — El Salvador (San Salvador)",
        "Escuela Superior de Economía y Negocios (ESEN) — El Salvador (La Libertad)",
        "Universidad Gerardo Barrios (UGB) — El Salvador (San Miguel)",
        "Universidad de Oriente (UNIVO) — El Salvador (San Miguel)",
        "Universidad Modular Abierta (UMA) — El Salvador (San Salvador y San Miguel)",
        "Universidad Nueva San Salvador (UNSSA) — El Salvador (La Libertad)",
        "Universidad Monseñor Oscar Arnulfo Romero (UMOAR) — El Salvador (San Salvador)",
        "Facultad Multidisciplinaria de Occidente (FMO) — El Salvador (Santa Ana)",
        "Facultad Multidisciplinaria de Paracentral (FMP) — El Salvador (Zacatecoluca)",
        "Facultad Multidisciplinaria de Oriente (FMO) — El Salvador (San Miguel)",

        // HONDURAS
        "Universidad Nacional Autónoma de Honduras (UNAH) — Honduras (Tegucigalpa)",
        "Universidad Nacional de Agricultura (UNA) — Honduras (Catacamas)",
        "Universidad Pedagógica Nacional Francisco Morazán (UPNFM) — Honduras (Tegucigalpa)",
        "Universidad Tecnológica de Honduras (UTH) — Honduras (San Pedro Sula y Tegucigalpa)",
        "Universidad Tecnológica Centroamericana (UNITEC) — Honduras (Tegucigalpa y San Pedro Sula)",
        "Universidad Católica de Honduras (UNICAH) — Honduras (Tegucigalpa y San Pedro Sula)",
        "Universidad José Cecilio del Valle (UJCV) — Honduras (Tegucigalpa)",
        "Universidad Metropolitana de Honduras (UMH) — Honduras (Tegucigalpa)",
        "Universidad San Pedro Sula (USAP) — Honduras (San Pedro Sula)",
        "Universidad Cristiana Evangélica Nuevo Milenio (UCENM) — Honduras (San Pedro Sula)",
        "Escuela Agrícola Panamericana Zamorano — Honduras (Francisco Morazán)",

        // NICARAGUA
        "Universidad Nacional Autónoma de Nicaragua (UNAN) — Nicaragua (Managua y León)",
        "Universidad Centroamericana (UCA) — Nicaragua (Managua)",
        "Universidad Nacional Agraria (UNA) — Nicaragua (Managua)",
        "Universidad Politécnica de Nicaragua (UPOLI) — Nicaragua (Managua)",
        "Universidad Nacional de Ingeniería (UNI) — Nicaragua (Managua)",
        "Universidad Americana (UAM) — Nicaragua (Managua)",
        "Universidad del Valle (UNIVALLE) — Nicaragua (Managua)",
        "Universidad Técnica de Comercio (UTC) — Nicaragua (Managua)",
        "Keiser University — Nicaragua (San Marcos, Carazo)",
        "Universidad Hispanoamericana — Nicaragua (Managua)",

        // COSTA RICA
        "Universidad de Costa Rica (UCR) — Costa Rica (San José)",
        "Instituto Tecnológico de Costa Rica (TEC) — Costa Rica (Cartago)",
        "Universidad Nacional de Costa Rica (UNA) — Costa Rica (Heredia)",
        "Universidad Estatal a Distancia (UNED) — Costa Rica (San José)",
        "Universidad Técnica Nacional (UTN) — Costa Rica (Alajuela)",
        "Universidad Latina de Costa Rica — Costa Rica (San José y otras sedes)",
        "Universidad Hispanoamericana — Costa Rica (San José)",
        "Universidad Autónoma de Centroamérica (UACA) — Costa Rica (San José)",
        "Universidad Internacional de las Américas (UIA) — Costa Rica (San José)",
        "Universidad Adventista de Centroamérica (UNADECA) — Costa Rica (Alajuela)",
        "Universidad Latinoamericana de Ciencia y Tecnología (ULACIT) — Costa Rica (San José)",
        "Universidad Veritas — Costa Rica (San José)",
        "Universidad de Iberoamérica (UNIBE) — Costa Rica (San José)",
        "Universidad La Salle — Costa Rica (San José)",
        "Universidad de las Ciencias y el Arte de Costa Rica (UACA) — Costa Rica (San José)",

        // PANAMÁ
        "Universidad de Panamá (UP) — Panamá (Ciudad de Panamá)",
        "Universidad Tecnológica de Panamá (UTP) — Panamá (Ciudad de Panamá)",
        "Universidad Autónoma de Chiriquí (UNACHI) — Panamá (David)",
        "Universidad Especializada de las Américas (UDELAS) — Panamá (Ciudad de Panamá)",
        "Universidad Marítima Internacional de Panamá (UMIP) — Panamá (Ciudad de Panamá)",
        "Universidad Latina de Panamá — Panamá (Ciudad de Panamá)",
        "Universidad Interamericana de Panamá (UIP) — Panamá (Ciudad de Panamá)",
        "Universidad del Istmo (UDI) — Panamá (Ciudad de Panamá)",
        "Universidad de las Américas (UDLA) — Panamá (Ciudad de Panamá)",
        "Universidad Metropolitana de Educación, Ciencia y Tecnología (UMECIT) — Panamá (Ciudad de Panamá)",
    ];

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function mount()
    {
        if (Auth::check()) {
            if (in_array(Auth::user()->role_id, [1, 2])) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('home-site');
            }
        }
    }

    public function render()
    {
        $roles = DB::table('roles')->select('name')->whereNotIn('id', [1, 2])->get();
        $universidades = $this->universidades;
        return view('livewire.admin.auth.register', compact('roles', 'universidades'))
            ->extends('layouts.loginAndRegister')->section('content');
    }

    public function register()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if (isset($this->fields['username']) && $this->fields['username'] !== null) {
            $this->fields['username'] = strtolower($this->fields['username']);
        }

        if (isset($this->fields['email']) && $this->fields['email'] !== null) {
            $this->fields['email'] = strtolower($this->fields['email']);
        }

        if (isset($this->fields['document_number']) && $this->fields['document_number'] !== null) {
            $this->fields['document_number'] = strtoupper($this->fields['document_number']);
        }

        $rules = [
            'fields.name' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.lastname' => 'required|string|min:3|max:50|regex:/^[\pL\s]+$/u',
            'fields.email' => 'required|email|regex:/^.+@.+\..+$/|unique:users,email',
            'fields.password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?]).+$/',
            'fields.password_confirmation' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?]).+$/',
            'fields.role_id' => 'required|string|exists:roles,name|not_in:Administrador,Directivo',
            'fields.document_type' => 'required|string|in:DUI,Carnet Estudiantil,Pasaporte,Carnet de extranjería',
            'fields.document_number' => 'required|string|min:4|max:20|unique:users,document_number',
            'fields.institution' => 'nullable|string|max:250|required_if:fields.name,Docente,Estudiante',
            'fields.phone' => 'required|string|min:8|max:20|unique:users,phone',
            'fields.username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9._]+$/u|unique:users,username',
        ];

        $messages = [
            'fields.name.required' => 'El nombre es obligatorio.',
            'fields.name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'fields.name.max' => 'El nombre no debe exceder los 50 caracteres.',
            'fields.name.regex' => 'El nombre solo debe contener letras y espacios.',

            'fields.lastname.required' => 'El apellido es obligatorio.',
            'fields.lastname.min' => 'El apellido debe tener al menos 3 caracteres.',
            'fields.lastname.max' => 'El apellido no debe exceder los 50 caracteres.',
            'fields.lastname.regex' => 'El apellido solo debe contener letras y espacios.',

            'fields.email.required' => 'El correo electrónico es obligatorio.',
            'fields.email.email' => 'El correo electrónico no es válido.',
            'fields.email.regex' => 'El formato del correo electrónico no es válido.',
            'fields.email.unique' => 'El correo electrónico ya está registrado.',

            'fields.password.required' => 'La contraseña es obligatoria.',
            'fields.password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'fields.password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.',

            'fields.password_confirmation.required' => 'La confirmación de la contraseña es obligatoria.',
            'fields.password_confirmation.min' => 'La confirmación de la contraseña debe tener al menos 6 caracteres.',
            'fields.password_confirmation.regex' => 'La confirmación de la contraseña debe contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.',

            'fields.role_id.required' => 'El rol es obligatorio.',
            'fields.role_id.string' => 'El rol seleccionado no es válido.',
            'fields.role_id.exists' => 'El rol seleccionado no existe.',
            'fields.role_id.not_in' => 'No se puede seleccionar este rol.',

            'fields.document_type.required' => 'El tipo de documento es obligatorio.',
            'fields.document_type.in' => 'El tipo de documento seleccionado no es válido.',

            'fields.document_number.required' => 'El número de documento es obligatorio.',
            'fields.document_number.min' => 'El número de documento debe tener al menos 4 caracteres.',
            'fields.document_number.max' => 'El número de documento no debe exceder los 20 caracteres.',
            'fields.document_number.unique' => 'El número de documento ya está registrado.',

            'fields.institution.max' => 'La institución no debe exceder los 250 caracteres.',
            'fields.institution.required_if' => 'La institución es obligatoria para el tipo de usuario seleccionado.',

            'fields.phone.min' => 'El teléfono debe tener al menos 8 caracteres.',
            'fields.phone.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'fields.phone.unique' => 'El teléfono ya está registrado.',
            'fields.phone.required' => 'El teléfono es obligatorio.',

            'fields.username.required' => 'El nombre de usuario es obligatorio.',
            'fields.username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'fields.username.max' => 'El nombre de usuario no debe exceder los 50 caracteres.',
            'fields.username.unique' => 'El nombre de usuario ya está registrado.',
            'fields.username.regex' => 'El nombre de usuario solo debe contener letras, números, puntos y guiones bajos.',
        ];

        $this->validate($rules, $messages);

        if ($this->fields['password'] !== $this->fields['password_confirmation']) {
            $this->addError('fields.password_confirmation', 'Las contraseñas no coinciden.');
            return;
        }

        if (in_array($this->fields['role_id'], ['Docente', 'Estudiante'])) {
            if (is_null($this->fields['institution']) || empty(trim($this->fields['institution']))) {
                $this->addError('fields.institution', 'La institución es obligatoria para el tipo de usuario seleccionado.');
                return;
            }
        } else {
            $this->fields['institution'] = null;
        }

        $role = DB::table('roles')->where('name', $this->fields['role_id'])->first();
        if (!$role) {
            $this->addError('fields.role_id', 'El rol seleccionado no es válido.');
            return;
        }

        $this->fields['role_id'] = $role->id;

        try {
            DB::beginTransaction();
            $userId = User::insertGetId([
                'name' => $this->fields['name'],
                'lastname' => $this->fields['lastname'],
                'email' => $this->fields['email'],
                'password' => bcrypt($this->fields['password']),
                'username' => $this->fields['username'],
                'role_id' => $this->fields['role_id'],
                'document_type' => $this->fields['document_type'],
                'document_number' => $this->fields['document_number'],
                'institution' => $this->fields['institution'],
                'phone' => $this->fields['phone'],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();

            Auth::loginUsingId($userId);

            LogsSistema::create([
                'action' => 'REGISTER',
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'description' => 'Registro de nuevo usuario en el sistema',
                'target_table' => 'users',
                'target_id' => $userId,
                'status' => 'success',
            ]);

            if (in_array(Auth::user()->role_id, [1, 2])) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('home-site');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            LogsSistema::create([
                'action' => 'REGISTER',
                'user_id' => null,
                'ip_address' => request()->ip(),
                'description' => 'Intento de registro fallido en el sistema - Excepción capturada',
                'target_table' => 'users',
                'target_id' => null,
                'status' => 'error',
            ]);
        }
    }

    #[On('setFields')]
    public function updateField($payload)
    {
        if (isset($payload['field']) && array_key_exists($payload['field'], $this->fields)) {
            $this->fields[$payload['field']] = $payload['value'];
        }
    }
}