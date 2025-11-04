@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $user = auth()->user();
    $modoDemo = session('modo_demo', false) || ($user && method_exists($user, 'hasRole') && $user->hasRole('demo'));

    $resolveLink = function (array $entry) {
        $candidates = Arr::wrap($entry['route'] ?? null);

        foreach ($candidates as $candidate) {
            if (!$candidate) {
                continue;
            }

            if (Route::has($candidate)) {
                return route($candidate);
            }

            if (is_string($candidate) && Str::startsWith($candidate, ['http://', 'https://', '/'])) {
                return Str::startsWith($candidate, ['http://', 'https://']) ? $candidate : url($candidate);
            }
        }

        if (!empty($entry['url'])) {
            return Str::startsWith($entry['url'], ['http://', 'https://'])
                ? $entry['url']
                : url($entry['url']);
        }

        return '#';
    };

    $canView = function (array $entry) use ($user) {
        $ability = $entry['can'] ?? null;

        if (empty($ability)) {
            return true;
        }

        return $user?->can($ability) ?? false;
    };

    $menuDemo = [
        ['group' => 'Menú SIMPLE (Demo / Dueño rápido)'],
        [
            'label' => 'Resumen de Hoy',
            'icon' => 'lucide:home',
            'route' => ['dashboard.index', 'dashboard'],
        ],
        [
            'label' => 'Agenda & Huecos',
            'icon' => 'lucide:calendar',
            'route' => ['agenda.hoy', 'reservas.calendar'],
            'badge' => 'Huecos libres',
        ],
        [
            'label' => 'Caja de Hoy',
            'icon' => 'lucide:wallet',
            'route' => ['caja.hoy', 'cajas.index'],
            'can' => 'ver_caja',
        ],
        [
            'label' => 'Clientes (WhatsApp)',
            'icon' => 'lucide:users',
            'route' => ['clientes.index'],
        ],
        [
            'label' => 'Reportes Rápidos',
            'icon' => 'lucide:bar-chart-3',
            'route' => ['reportes.rapidos', 'pages.charts'],
        ],
        [
            'label' => '⚙️ Ver más opciones…',
            'icon' => 'lucide:chevron-right',
            'route' => ['menu.completo', 'config.index'],
            'url' => '/config/opciones-avanzadas',
        ],
    ];

    $menuCompleto = [
        ['group' => 'Menú COMPLETO (No demo)'],
        ['group' => 'Operación diaria'],
        [
            'label' => 'Resumen de Hoy',
            'icon' => 'lucide:home',
            'route' => ['dashboard.index', 'dashboard'],
        ],
        [
            'label' => 'Agenda & Huecos',
            'icon' => 'lucide:calendar',
            'route' => ['agenda.hoy', 'reservas.calendar'],
            'badge' => 'Huecos libres',
            'children' => [
                ['label' => 'Día', 'route' => ['agenda.hoy', 'reservas.calendar']],
                ['label' => 'Semana', 'route' => ['agenda.semana']],
                ['label' => 'Mes', 'route' => ['agenda.mes']],
            ],
        ],
        [
            'label' => 'Caja / Ventas',
            'icon' => 'lucide:wallet',
            'route' => ['caja.hoy', 'cajas.index'],
            'can' => 'ver_caja',
            'children' => [
                ['label' => 'Movimientos', 'route' => ['caja.movimientos', 'cajas.index']],
                ['label' => 'Apertura', 'route' => ['caja.apertura', 'cajas.create']],
                ['label' => 'Cierre', 'route' => ['caja.cierre', 'cajas.index']],
            ],
        ],
        [
            'label' => 'Clientes (WhatsApp)',
            'icon' => 'lucide:users',
            'route' => ['clientes.index'],
            'children' => [
                ['label' => 'Segmentos', 'route' => ['clientes.segmentos', 'clientes.index']],
                ['label' => 'Cumpleaños', 'route' => ['clientes.cumpleanios', 'clientes.index']],
            ],
        ],
        [
            'label' => 'Servicios & Precios',
            'icon' => 'lucide:scissors',
            'route' => ['servicios.index', 'items.index'],
        ],
        ['group' => 'Administración'],
        [
            'label' => 'Arqueo de Caja',
            'icon' => 'lucide:clipboard-list',
            'route' => ['caja.arqueo', 'cajas.index'],
            'can' => 'ver_caja',
        ],
        [
            'label' => 'Inventario',
            'icon' => 'lucide:boxes',
            'route' => ['inventario.index', 'items.index'],
        ],
        [
            'label' => 'Reportes',
            'icon' => 'lucide:bar-chart',
            'route' => ['reportes.index', 'pages.charts'],
            'children' => [
                ['label' => 'Ventas diarias', 'route' => ['reportes.ventas-diarias', 'pages.charts']],
                ['label' => 'Top barberos', 'route' => ['reportes.por-barbero', 'pages.charts']],
                ['label' => 'Asistencia', 'route' => ['reportes.asistencia', 'pages.charts']],
            ],
        ],
        ['group' => 'Ajustes'],
        [
            'label' => 'Usuarios & Roles',
            'icon' => 'lucide:shield',
            'route' => ['usuarios.index', 'users.index'],
            'can' => 'administrar_usuarios',
        ],
        [
            'label' => 'Personalizar Peluquería',
            'icon' => 'lucide:paintbrush',
            'route' => ['personalizar.index', 'peluquerias.edit'],
        ],
        [
            'label' => 'Configuración',
            'icon' => 'lucide:settings',
            'route' => ['config.index', 'tipocitas.index'],
        ],
    ];

    $items = $modoDemo ? $menuDemo : $menuCompleto;
@endphp

<div class="app-sidebar">
     <div class="logo-box">
          <a href="{{ Route::has('dashboard.index') ? route('dashboard.index') : (Route::has('dashboard') ? route('dashboard') : url('/')) }}" class="logo-dark">
               <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
               <img src="/images/logodarkal.png" class="logo-lg" alt="logo dark">
          </a>

          <a href="{{ Route::has('dashboard.index') ? route('dashboard.index') : (Route::has('dashboard') ? route('dashboard') : url('/')) }}" class="logo-light">
               <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
               <img src="/images/logo-light.png" class="logo-lg" alt="logo light">
          </a>
     </div>

     <div class="scrollbar" data-simplebar>
          <div class="px-3 pt-3 pb-2">
               @if($modoDemo)
                    <p class="text-uppercase text-muted fw-semibold small mb-1">Menú SIMPLE (Demo / Dueño rápido)</p>
                    <p class="text-muted small mb-0">Pensado para cerrar ventas: plata → agenda → clientes → reporte.</p>
               @else
                    <p class="text-uppercase text-muted fw-semibold small mb-1">Menú COMPLETO (No demo)</p>
                    <p class="text-muted small mb-0">Operación diaria, administración y ajustes al alcance.</p>
               @endif
          </div>

          <ul class="navbar-nav" id="navbar-nav">
               @foreach($items as $index => $item)
                    @if(isset($item['group']))
                         <li class="menu-title">{{ $item['group'] }}</li>
                         @continue
                    @endif

                    @if(!$canView($item))
                         @continue
                    @endif

                    @php
                        $hasChildren = !empty($item['children']);
                        $icon = $item['icon'] ?? 'lucide:circle';
                        $url = $resolveLink($item);
                    @endphp

                    <li class="nav-item">
                         <a class="nav-link d-flex align-items-center gap-2" href="{{ $url }}">
                              <span class="nav-icon d-inline-flex align-items-center justify-content-center">
                                   <iconify-icon icon="{{ $icon }}" class="fs-5"></iconify-icon>
                              </span>
                              <span class="nav-text flex-grow-1">{{ $item['label'] }}</span>
                              @if(!empty($item['badge']))
                                   <span class="badge bg-light text-primary ms-auto">{{ $item['badge'] }}</span>
                              @endif
                         </a>

                         @if($hasChildren)
                              <ul class="nav sub-navbar-nav ms-4 mt-1">
                                   @foreach($item['children'] as $child)
                                        @if($canView($child))
                                             @php $childUrl = $resolveLink($child); @endphp
                                             <li class="sub-nav-item">
                                                  <a class="sub-nav-link d-flex justify-content-between align-items-center" href="{{ $childUrl }}">
                                                       <span>{{ $child['label'] }}</span>
                                                       @if(!empty($child['badge']))
                                                            <span class="badge bg-light text-muted ms-2">{{ $child['badge'] }}</span>
                                                       @endif
                                                  </a>
                                             </li>
                                        @endif
                                   @endforeach
                              </ul>
                         @endif
                    </li>
               @endforeach
          </ul>
     </div>
</div>
