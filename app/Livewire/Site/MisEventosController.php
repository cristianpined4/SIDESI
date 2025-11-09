<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InscripcionesEvento;
use App\Models\Eventos;
use App\Models\SessionesEvento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MisEventosController extends Component
{
    use WithPagination;

    public $records_event;
    public $records_sesiones;
    public $records_sesion;
    public $is_registered_evento = false;
    public $is_registered_sesion = false;
    public $is_organizer = false;
    public $is_ponente = false;
    public $pendiente = false;
    public $rechazado = false;
    public $records_ponente;
    public $search = '';
    public $tab = 'proximos';

    public function mount()
    {
        $this->records_event = null;
        $this->records_sesiones = null;
        $this->records_sesion = null;
        $this->records_ponente = null;
        $this->is_registered_evento = false;
        $this->is_registered_sesion = false;
        $this->is_organizer = false;
        $this->is_ponente = false;
        $this->pendiente = false;
        $this->rechazado = false;
    }

    public function verDetalles($eventoId)
    {
        try {
            // Load the event with its relationships
            $this->records_event = Eventos::with([
                'sesiones' => function($query) {
                    $query->orderBy('start_time', 'asc');
                },
                'imagenes' => function($query) {
                    $query->where('is_main', true);
                },
                'inscripciones' => function($query) {
                    $query->where('user_id', auth()->id());
                }
            ])->findOrFail($eventoId);

            // Ensure we have the sesiones relationship loaded
            $this->records_sesiones = $this->records_event->sesiones;

            // Compute user state for event
            $insc = $this->records_event->inscripciones->first();
            $this->is_registered_evento = (bool) $insc;
            $this->pendiente = $insc?->status === 'pendiente';
            $this->rechazado = $insc?->status === 'rechazado';
            $this->is_organizer = ($this->records_event->organizer_id ?? null) === auth()->id();
            
            // Dispatch the modal open event (JS listens to 'abrir-modal')
            $this->dispatch('abrir-modal', ['modal' => 'event-modal']);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error loading event details: ' . $e->getMessage());
            $this->addError('error', 'No se pudo cargar la información del evento.');
        }
    }

    public function verDetallesSesion($sesionId)
    {
        try {
            // Cargar la sesión sin depender de relaciones no definidas
            $this->records_sesion = SessionesEvento::findOrFail($sesionId);

            // Ponente como en EventosController
            $this->records_ponente = $this->records_sesion->ponente_id
                ? User::find($this->records_sesion->ponente_id)
                : null;

            // Compute user state for session/event
            $this->is_ponente = optional($this->records_ponente)->id === auth()->id();
            $this->is_registered_evento = InscripcionesEvento::where('evento_id', $this->records_sesion->evento_id)
                ->where('user_id', auth()->id())
                ->exists();
            $this->pendiente = false;
            $this->rechazado = false;
            
            // Cerrar el modal de evento (si está abierto) y abrir el de sesión
            $this->dispatch('abrir-modal', ['modal' => 'sesion-modal']);

        } catch (\Exception $e) {
            \Log::error('Error loading session details: ' . $e->getMessage());
            $this->addError('error', 'No se pudo cargar la información de la sesión.');
        }
    }

    public function cerrarModal(string $modal, bool $clear = true)
    {
        try {
            if ($clear) {
                if ($modal === 'event-modal') {
                    $this->records_event = null;
                    $this->records_sesiones = null;
                } elseif ($modal === 'sesion-modal') {
                    $this->records_sesion = null;
                }
            }
            $this->dispatch('cerrar-modal', ['modal' => $modal]);
            
        } catch (\Throwable $th) {
            \Log::error('Error closing modal: ' . $th->getMessage());
        }
    }

    public function render()
    {
        $query = InscripcionesEvento::with([
            'evento' => function($q) {
                $q->select([
                    'id', 'title', 'description', 'start_time', 'end_time',
                    'location', 'mode', 'is_active',
                    'created_at', 'updated_at'
                ])->with(['imagenes' => function($q) {
                    $q->where('is_main', true);
                }]);
            },
            'evento.sesiones' => function($q) {
                $q->select([
                    'id', 'evento_id', 'title', 'description', 'start_time',
                    'end_time', 'location', 'mode', 'created_at', 'updated_at'
                ])->with(['imagenes' => function($q) {
                    $q->where('is_main', true);
                }]);
            }
        ])
        ->where('user_id', auth()->id())
        ->whereHas('evento', function($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
              ->where('is_active', true);
            
            if ($this->tab === 'proximos') {
                $q->where('end_time', '>=', now());
            } else {
                $q->where('end_time', '<', now());
            }
        })
        ->orderBy('created_at', 'desc');

        $eventos = $query->paginate(9);

        return view('livewire.site.mis-eventos', [
            'eventos' => $eventos,
        ])->extends('layouts.site')
          ->section('content');
    }
}
