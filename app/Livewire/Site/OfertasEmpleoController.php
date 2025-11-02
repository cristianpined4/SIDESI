<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OfertasDeEmpleo;

class OfertasEmpleoController extends Component
{
    use WithPagination;

    public $currentSearchTerm = '';
    protected $queryString = [
        'currentSearchTerm' => ['except' => ''],
    ];

    public $paginate = 20;

    public function getOffers()
    {
        $query = OfertasDeEmpleo::query()->where('is_active', true);

        if (!empty($this->currentSearchTerm)) {
            $term = '%' . trim($this->currentSearchTerm) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('company_name', 'like', $term)
                  ->orWhere('location', 'like', $term);
            });
        }

        $query->orderBy('updated_at', 'desc');
        return $query->paginate($this->paginate);
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function render()
    {
        return view('livewire.site.ofertas_empleo', [
            'offers' => $this->getOffers()
        ])
            ->extends('layouts.site')
            ->section('content');
    }
}
