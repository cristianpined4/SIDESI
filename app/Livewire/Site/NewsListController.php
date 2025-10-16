<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contenidos;

class NewsListController extends Component
{
    use WithPagination;

    public $currentCategory = 'all';
    public $currentSearchTerm = '';
    public $paginate = 20;

    public function getFilteredNews()
    {
        $query = Contenidos::with(['imagenes', 'contenidoCuerpo'])
            ->where('status', 'published');

        // Filtro directo: currentCategory es 'Evento', 'Convocatoria', etc.
        if ($this->currentCategory !== 'all') {
            $query->where('content_type', $this->currentCategory);
        }

        if (!empty($this->currentSearchTerm)) {
            $term = '%' . trim($this->currentSearchTerm) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        return $query->paginate($this->paginate);
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function render()
    {
        return view('livewire.site.news-list-controller', [
            'filteredNews' => $this->getFilteredNews()
        ])
            ->extends('layouts.site')
            ->section('content');
    }
}