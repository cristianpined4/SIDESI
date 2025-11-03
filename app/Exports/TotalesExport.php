<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalesExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return [
            [
                'Eventos',
                DB::table('eventos')->count() ?? 0,
                DB::table('eventos')->where('is_active', true)->count() ?? 0,
                DB::table('eventos')->where('is_active', false)->count() ?? 0,
                DB::table('eventos')->where('is_active', true)->count() > 0 ? 'Activo' : 'Inactivo',
                optional(DB::table('eventos')->max('updated_at'))
                ? Carbon::parse(DB::table('eventos')->max('updated_at'))->format('d-m-Y h:i A')
                : '—',
            ],
            [
                'Usuarios',
                DB::table('users')->count() ?? 0,
                DB::table('users')->where('is_active', true)->count() ?? 0,
                DB::table('users')->where('is_active', false)->count() ?? 0,
                DB::table('users')->where('is_active', true)->count() > 0 ? 'Activo' : 'Inactivo',
                optional(DB::table('users')->max('updated_at'))
                ? Carbon::parse(DB::table('users')->max('updated_at'))->format('d-m-Y h:i A')
                : '—',
            ],
            [
                'Noticias',
                DB::table('contenidos')->count() ?? 0,
                DB::table('contenidos')->where('status', 'published')->count() ?? 0,
                DB::table('contenidos')->where('status', '!=', 'published')->count() ?? 0,
                DB::table('contenidos')->where('status', 'published')->count() > 0 ? 'Activo' : 'Inactivo',
                optional(DB::table('contenidos')->max('updated_at'))
                ? Carbon::parse(DB::table('contenidos')->max('updated_at'))->format('d-m-Y h:i A')
                : '—',
            ],
            [
                'Documentos',
                DB::table('documentos')->count() ?? 0,
                DB::table('documentos')->where('visibility', 'publico')->count() ?? 0,
                DB::table('documentos')->where('visibility', '!=', 'publico')->count() ?? 0,
                DB::table('documentos')->where('visibility', 'publico')->count() > 0 ? 'Activo' : 'Inactivo',
                optional(DB::table('documentos')->max('updated_at'))
                ? Carbon::parse(DB::table('documentos')->max('updated_at'))->format('d-m-Y h:i A')
                : '—',
            ],
            [
                'Ofertas de Empleo',
                DB::table('ofertas_de_empleos')->count(),
                DB::table('ofertas_de_empleos')->where('is_active', true)->count(),
                DB::table('ofertas_de_empleos')->where('is_active', false)->count(),
                DB::table('ofertas_de_empleos')->where('is_active', true)->count() > 0 ? 'Activo' : 'Inactivo',
                optional(DB::table('ofertas_de_empleos')->max('updated_at'))
                ? Carbon::parse(DB::table('ofertas_de_empleos')->max('updated_at'))->format('d-m-Y h:i A')
                : '—',
            ],
        ];
    }

    public function headings(): array
    {
        return ['Nombre Módulo', 'Cantidad Total', 'Cantidad Activos', 'Cantidad Inactivos', 'Estado', 'Última Actualización'];
    }
}