<?php

namespace App\Livewire\Admin;

use App\Exports\TotalesExport;
use App\Models\Eventos;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LogsSistema;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class DashboardController extends Component
{
    use WithPagination, WithFileUploads;

    public $record_id;
    public $fields = [];   // inputs normales
    public $file;          // archivo temporal
    public $search = '';
    public $paginate = 10;
    public bool $loading = false;
    public $periodo = 'diario'; // diario, mes, año
    public $actividadGrafico = [];
    public $totales = [];

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

        $this->actividadGrafico = $this->obtenerActividad();
    }

    public function cambiarPeriodo($periodo)
    {
        $this->periodo = $periodo;
        $this->actividadGrafico = $this->obtenerActividad();
        $this->dispatch("render-grafico", $this->actividadGrafico);
    }

    private function obtenerActividad()
    {
        $query = LogsSistema::query();

        $mesesString = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        // Filtro según el periodo seleccionado
        if ($this->periodo === 'diario') {
            // 📅 Últimos 30 días
            $inicio = now()->subDays(29)->startOfDay();
            $fin = now()->endOfDay();

            $query->whereBetween('created_at', [$inicio, $fin]);

            // Detectar motor de base de datos
            $driver = DB::getDriverName();
            $v = $driver === 'pgsql'
                ? "TO_CHAR(created_at, 'DD/MM')"
                : "DATE_FORMAT(created_at, '%d/%m')";

            // 🔢 Obtener conteo agrupado por día
            $logs = $query->selectRaw("$v as dia, COUNT(*) as total")
                ->groupBy('dia')
                ->orderByRaw("MIN(created_at)") // mantiene el orden cronológico
                ->pluck('total', 'dia');

            // 📆 Reformatear a DD/MesAbrev (ej: 01/Nov)
            $formateados = collect();
            foreach ($logs as $clave => $valor) {
                [$dia, $mesNum] = explode('/', $clave);
                $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);

                // Nombres abreviados de los meses
                $mesesString = [
                    1 => 'Ene',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Abr',
                    5 => 'May',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Ago',
                    9 => 'Sep',
                    10 => 'Oct',
                    11 => 'Nov',
                    12 => 'Dic',
                ];

                $mesAbrev = $mesesString[(int) $mesNum] ?? $mesNum;
                $formateados["$dia/$mesAbrev"] = $valor;
            }

            // 🧭 Reordenar según fecha real
            $ordenados = $formateados->sortBy(function ($_, $key) {
                [$dia, $mesAbrev] = explode('/', $key);
                $meses = [
                    'Ene' => 1,
                    'Feb' => 2,
                    'Mar' => 3,
                    'Abr' => 4,
                    'May' => 5,
                    'Jun' => 6,
                    'Jul' => 7,
                    'Ago' => 8,
                    'Sep' => 9,
                    'Oct' => 10,
                    'Nov' => 11,
                    'Dic' => 12,
                ];
                return sprintf('%04d-%02d-%02d', now()->year, $meses[$mesAbrev], $dia);
            });

            return [
                'meses' => $ordenados->keys()->values(),
                'valores' => $ordenados->values(),
                'etiqueta' => 'Día',
            ];
        } elseif ($this->periodo === 'mes') {
            // 🕒 Últimos 12 meses desde el mes actual hacia atrás
            $inicio = now()->subMonths(11)->startOfMonth();
            $fin = now()->endOfMonth();

            $query->whereBetween('created_at', [$inicio, $fin]);

            // 🧮 Agrupar por año y mes (para evitar confusiones entre años)
            $driver = DB::getDriverName();
            $mesExpr = $driver === 'pgsql'
                ? "TO_CHAR(created_at, 'YYYY-MM')"
                : "DATE_FORMAT(created_at, '%Y-%m')";

            $logs = $query->selectRaw("$mesExpr as periodo, COUNT(*) as total")
                ->groupBy('periodo')
                ->orderBy('periodo')
                ->pluck('total', 'periodo');

            // 📆 Generar lista de los últimos 12 meses (ordenados)
            $meses = collect(range(0, 11))->map(function ($i) {
                return now()->subMonths(11 - $i);
            });

            // 🏷️ Formatear etiquetas y valores
            $etiquetas = $meses->map(fn($m) => ucfirst($m->locale('es')->translatedFormat('M Y')));
            $valores = $meses->map(function ($m) use ($logs) {
                $key = $m->format('Y-m');
                return $logs[$key] ?? 0;
            });

            return [
                'meses' => $etiquetas, // Ej: ["Nov 2024", "Dic 2024", ..., "Oct 2025"]
                'valores' => $valores,
                'etiqueta' => 'Mes',
            ];
        } elseif ($this->periodo === 'año') {
            // 🕒 Últimos 7 años (incluido el actual)
            $inicio = now()->subYears(6)->startOfYear();
            $fin = now()->endOfYear();

            $query->whereBetween('created_at', [$inicio, $fin]);

            // 🔧 Compatibilidad con MySQL y PostgreSQL
            $driver = DB::getDriverName();
            $yearExpr = $driver === 'pgsql'
                ? "EXTRACT(YEAR FROM created_at)"
                : "YEAR(created_at)";

            // 📊 Agrupar por año
            $logs = $query->selectRaw("$yearExpr as anio, COUNT(*) as total")
                ->groupBy('anio')
                ->orderBy('anio')
                ->pluck('total', 'anio');

            // 🗓️ Generar los últimos 7 años (ordenados)
            $anios = collect(range(0, 6))->map(fn($i) => now()->subYears(6 - $i)->year);

            // 🧮 Mapear valores (si no existe, asignar 0)
            $valores = $anios->map(fn($anio) => $logs[$anio] ?? 0);

            return [
                'meses' => $anios,   // etiquetas: [2019, 2020, 2021, 2022, 2023, 2024, 2025]
                'valores' => $valores,
                'etiqueta' => 'Año',
            ];
        }

        return [
            'meses' => collect(),
            'valores' => collect(),
            'etiqueta' => '',
        ];
    }

    public function refresh()
    {
        $this->loading = true;
        $this->resetPage();
        $this->loading = false;
    }

    private function getLast10Logs()
    {
        $logs = LogsSistema::orderBy('created_at', 'desc')->limit(10)->get();

        $icons = [
            'users' => 'fa-users',
            'eventos' => 'fa-calendar',
            'sessiones_eventos' => 'fa-calendar',
            'documentos' => 'fa-file',
            'contenidos' => 'fa-newspaper',
            '' => 'fa-question',
            'null' => 'fa-question',
            null => 'fa-question',
            'default' => 'fa-question',
            'undefined' => 'fa-question',
            'configuraciones' => 'fa-cogs',
            'roles' => 'fa-users-cog',
            'pagos' => 'fa-coins',
            'redes_sociales' => 'fa-share-alt',
            'Dashboard' => 'fa-tachometer-alt',
            'ofertas_de_empleos' => 'fa-briefcase',
        ];

        $colors = [
            'users' => [
                'text' => '#2563EB', // blue-600
                'bg' => '#DBEAFE', // blue-100
            ],
            'eventos' => [
                'text' => '#059669', // emerald-600
                'bg' => '#D1FAE5', // emerald-100
            ],
            'sessiones_eventos' => [
                'text' => '#0D9488', // teal-600
                'bg' => '#CCFBF1', // teal-100
            ],
            'documentos' => [
                'text' => '#4F46E5', // indigo-600
                'bg' => '#E0E7FF', // indigo-100
            ],
            'contenidos' => [
                'text' => '#0891B2', // cyan-600
                'bg' => '#CFFAFE', // cyan-100
            ],
            'configuraciones' => [
                'text' => '#D97706', // amber-600
                'bg' => '#FEF3C7', // amber-100
            ],
            'roles' => [
                'text' => '#7C3AED', // violet-600
                'bg' => '#EDE9FE', // violet-100
            ],
            'pagos' => [
                'text' => '#E11D48', // rose-600
                'bg' => '#FFE4E6', // rose-100
            ],
            'redes_sociales' => [
                'text' => '#0EA5E9', // sky-600
                'bg' => '#E0F2FE', // sky-100
            ],
            'Dashboard' => [
                'text' => '#9333EA', // purple-600
                'bg' => '#F3E8FF', // purple-100
            ],
            'ofertas_de_empleos' => [
                'text' => '#3D14B8FF',
                'bg' => '#E0D7FF',
            ],

            // Casos vacíos o desconocidos
            '' => [
                'text' => '#6B7280', // gray-500
                'bg' => '#F3F4F6', // gray-100
            ],
            'null' => [
                'text' => '#6B7280',
                'bg' => '#F3F4F6',
            ],
            null => [
                'text' => '#6B7280',
                'bg' => '#F3F4F6',
            ],
            'default' => [
                'text' => '#6B7280',
                'bg' => '#F3F4F6',
            ],
            'undefined' => [
                'text' => '#6B7280',
                'bg' => '#F3F4F6',
            ]
        ];


        $logs = $logs->map(function ($log) use ($icons, $colors) {
            [$text, $bg] = isset($colors[$log->target_table]) ? [$colors[$log->target_table]['text'], $colors[$log->target_table]['bg']] : ['#6B7280', '#F3F4F6'];

            return [
                'titulo' => $log->action . ' - ' . $log->description,
                'user' => $log->display_name,
                'tabla' => $log->target_table == 'users' ? 'Usuarios' : $log->target_table,
                'tiempo' => $log->created_at->locale('es')->diffForHumans(),
                'icon' => isset($icons[$log->target_table]) ? $icons[$log->target_table] : 'fa-question',
                'id' => $log->target_id,
                'status' => $log->status,
                'text-color' => $text,
                'bg-color' => $bg
            ];
        });
        return $logs;
    }

    private function randomTailwindColor(): array
    {
        $colors = [
            'users' => ['text' => '#2563EB', 'bg' => '#DBEAFE'], // blue
            'eventos' => ['text' => '#059669', 'bg' => '#D1FAE5'], // emerald
            'sessiones_eventos' => ['text' => '#0D9488', 'bg' => '#CCFBF1'], // teal
            'documentos' => ['text' => '#4F46E5', 'bg' => '#E0E7FF'], // indigo
            'contenidos' => ['text' => '#0891B2', 'bg' => '#CFFAFE'], // cyan
            'configuraciones' => ['text' => '#D97706', 'bg' => '#FEF3C7'], // amber
            'roles' => ['text' => '#7C3AED', 'bg' => '#EDE9FE'], // violet
            'pagos' => ['text' => '#E11D48', 'bg' => '#FFE4E6'], // rose
            'redes_sociales' => ['text' => '#0EA5E9', 'bg' => '#E0F2FE'], // sky
            'Dashboard' => ['text' => '#9333EA', 'bg' => '#F3E8FF'], // purple
            'ofertas_de_empleos' => ['text' => '#3D14B8FF', 'bg' => '#E0D7FF'],
        ];

        // Elegir uno al azar
        return $colors[array_rand($colors)];
    }

    private function getLast10Eventos()
    {
        $eventos = Eventos::where('is_active', true)
            ->where('start_time', '>=', Carbon::now()) // Solo eventos futuros
            ->orderBy('start_time', 'asc')            // Ordenar de más cercano a más lejano
            ->limit(10)
            ->get();

        if ($eventos->count() > 0) {
            return $eventos->map(function ($evento) {
                $colors = $this->randomTailwindColor();
                $hora = Carbon::parse($evento->start_time)->format('h:i A');
                $fecha = Carbon::parse($evento->start_time)->format('d M');

                return [
                    'titulo' => $evento->title,
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'personas' => $evento->max_participants,
                    'text-color' => $colors['text'],
                    'bg-color' => $colors['bg'],
                ];
            });
        }
        return [];
    }

    public function render()
    {
        $current_user = Auth::user();

        $this->totales = [
            [
                'name' => 'Eventos',
                'slug' => 'eventos',
                'icon' => 'fa-calendar',
                'count' => DB::table('eventos')->count(),
                'count_active' => DB::table('eventos')->where('is_active', true)->count(),
                'count_inactive' => DB::table('eventos')->where('is_active', false)->count(),
                'color' => 'blue',
                'border' => '#3b82f6',
                'last_updated_max' => DB::table('eventos')->max('updated_at'),
                'have_items_active' => DB::table('eventos')->where('is_active', true)->count() > 0,
            ],
            [
                'name' => 'Usuarios',
                'slug' => 'usuarios',
                'icon' => 'fa-users',
                'count' => DB::table('users')->count(),
                'count_active' => DB::table('users')->where('is_active', true)->count(),
                'count_inactive' => DB::table('users')->where('is_active', false)->count(),
                'color' => 'green',
                'border' => '#10b981',
                'last_updated_max' => DB::table('users')->max('updated_at'),
                'have_items_active' => DB::table('users')->where('is_active', true)->count() > 0,
            ],
            [
                'name' => 'Noticias',
                'slug' => 'noticias',
                'icon' => 'fa-newspaper',
                'count' => DB::table('contenidos')->count(),
                'count_active' => DB::table('contenidos')->where('status', 'published')->count(),
                'count_inactive' => DB::table('contenidos')->whereNot('status', 'published')->count(),
                'color' => 'yellow',
                'border' => '#f59e0b',
                'last_updated_max' => DB::table('contenidos')->max('updated_at'),
                'have_items_active' => DB::table('contenidos')->where('status', 'published')->count() > 0,
            ],
            [
                'name' => 'Documentos',
                'slug' => 'documentos',
                'icon' => 'fa-file-alt',
                'count' => DB::table('documentos')->count(),
                'count_active' => DB::table('documentos')->where('visibility', 'publico')->count(),
                'count_inactive' => DB::table('documentos')->whereNot('visibility', 'publico')->count(),
                'color' => 'red',
                'border' => '#ef4444',
                'last_updated_max' => DB::table('documentos')->max('updated_at'),
                'have_items_active' => DB::table('documentos')->where('visibility', 'publico')->count() > 0,
            ],
            [
                'name' => 'Ofertas de Empleo',
                'slug' => 'ofertas',
                'icon' => 'fa-briefcase',
                'count' => DB::table('ofertas_de_empleos')->count(),
                'count_active' => DB::table('ofertas_de_empleos')->where('is_active', true)->count(),
                'count_inactive' => DB::table('ofertas_de_empleos')->where('is_active', false)->count(),
                'color' => 'purple',
                'border' => '#8b5cf6',
                'last_updated_max' => DB::table('ofertas_de_empleos')->max('updated_at'),
                'have_items_active' => DB::table('ofertas_de_empleos')->where('is_active', true)->count() > 0,
            ],
        ];

        $totales = $this->totales;

        $this->actividadGrafico = $this->obtenerActividad();

        $last10Logs = $this->getLast10Logs();

        $last10Eventos = $this->getLast10Eventos();

        return view('livewire.admin.dashboard', compact('current_user', 'totales', 'last10Logs', 'last10Eventos'))
            ->extends('layouts.admin')
            ->section('content');
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

    public function resumenExportarExcel()
    {
        try {
            $filename = "totales_sistema_" . date('Y-m-d_h-i_A') . ".xlsx";
            LogsSistema::create([
                'action' => "resumenExportarExcel Dashboard",
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => "Generó reporte Excel de totales del sistema",
                'target_table' => 'Dashboard',
                'target_id' => null,
                'status' => 'success',
            ]);
            return Excel::download(new TotalesExport(), $filename);
        } catch (\Exception $e) {
            // Manejar el error, registrar en logs, etc.
            LogsSistema::create([
                'action' => "resumenExportarExcel Dashboard",
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => "Error al generar reporte Excel de totales del sistema: " . $e->getMessage(),
                'target_table' => 'Dashboard',
                'target_id' => null,
                'status' => 'error',
            ]);

            // Opcional: lanzar una excepción o retornar un mensaje de error
            abort(500, 'Error al generar el reporte Excel.');
        }
    }

    public function resumenExportarPDF()
    {
        try {
            $totales = $this->totales;

            $fecha = now()->format('d-m-Y h:i A');

            $html = view('exports.totales_mpdf', compact('totales', 'fecha'))->render();

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 20,
                'margin_bottom' => 15,
            ]);

            $mpdf->SetTitle('Reporte de Totales del Sistema');
            $mpdf->WriteHTML($html);

            LogsSistema::create([
                'action' => "resumenExportarPDF Dashboard",
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => "Generó reporte PDF de totales del sistema",
                'target_table' => 'Dashboard',
                'target_id' => null,
                'status' => 'success',
            ]);

            return response()->streamDownload(function () use ($mpdf) {
                echo $mpdf->Output('', 'S');
            }, "totales_sistema_" . date('Y-m-d_h-i_A') . ".pdf");

        } catch (\Exception $e) {
            // Manejar el error, registrar en logs, etc.
            LogsSistema::create([
                'action' => "resumenExportarPDF Dashboard",
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'description' => "Error al generar reporte PDF de totales del sistema: " . $e->getMessage(),
                'target_table' => 'Dashboard',
                'target_id' => null,
                'status' => 'error',
            ]);

            // Opcional: lanzar una excepción o retornar un mensaje de error
            abort(500, 'Error al generar el reporte PDF.');
        }
    }

    #[On('pdfResult')]
    public function pdfResult($data)
    {
        LogsSistema::create([
            'action' => "reporteExportarPDF Dashboard",
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'description' => $data['message'],
            'target_table' => 'Dashboard',
            'target_id' => null,
            'status' => boolval($data['success']) ? 'success' : 'error',
        ]);
    }
}