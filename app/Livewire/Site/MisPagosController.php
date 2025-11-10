<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pagos;
use Illuminate\Support\Facades\Auth;

class MisPagosController extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all'; // all, completed, pending, failed

    public function mount()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Pagos::with([
            'evento' => function($q) {
                $q->select(['id', 'title', 'start_time', 'end_time', 'location']);
            }
        ])
        ->where('user_id', auth()->id());

        // Filtrar por búsqueda (nombre del evento o ID de transacción)
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('transaction_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('evento', function($q) {
                      $q->where('title', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filtrar por estado
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Ordenar por fecha de pago descendente
        $query->orderBy('paid_at', 'desc');

        $pagos = $query->paginate(10);

        return view('livewire.site.mis-pagos', [
            'pagos' => $pagos,
        ])->extends('layouts.site')
          ->section('content');
    }
}
