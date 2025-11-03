<?php

namespace App\Livewire\Site;

use App\Models\Certificados;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Eventos;
use App\Models\LogsSistema;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class VerCertificadosController extends Component
{
    use WithPagination, WithFileUploads;

    public $certificate;

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function mount($code)
    {
        $this->certificate = Certificados::where('codigo_qr', $code)->first();

        if (!$this->certificate) {
            abort(404, 'Certificado no encontrado');
        }

        // Crear el código QR
        $result = (new Builder(
            writer: new PngWriter(),
            data: $this->certificate->url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 150,
            margin: 5,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        ))->build();

        // Convertir a Base64 para usar en PDF o Blade
        $codigoQr = $result->getDataUri();

        $participante = User::select('name', 'lastname')->find($this->certificate->user_id);
        $evento = Eventos::select('title', 'end_time')->find($this->certificate->evento_id);

        $data = [
            'recipient_name' => $participante->name . ' ' . $participante->lastname,
            'event_name' => $evento->title,
            'date' => Carbon::parse($evento->end_time)->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
            'url' => $this->certificate->url,
            'qr_code' => $codigoQr,
            'code' => $this->certificate->codigo_qr,
            'is_valid' => boolval($this->certificate->is_valid),
        ];

        $this->certificate = (object) $data;
    }

    public function render()
    {
        return view('livewire.site.ver-certificados')
            ->extends('layouts.site')
            ->section('content');
    }

    #[On('pdfResult')]
    public function pdfResult($data)
    {
        LogsSistema::create([
            'action' => "PDF Generation Diploma",
            'user_id' => null,
            'ip_address' => request()->ip(),
            'description' => $data['message'],
            'target_table' => 'Dashboard',
            'target_id' => null,
            'status' => boolval($data['success']) ? 'success' : 'error',
        ]);
    }
}