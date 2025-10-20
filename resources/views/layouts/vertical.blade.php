@php
    $peluqueria   = optional(auth()->user())->peluqueria;
    $menuColor    = $peluqueria->menu_color ?? null;
    $topbarColor  = $peluqueria->topbar_color ?? null;

    $computeTextColor = function (?string $color): string {
        if (! $color) {
            return '#ffffff';
        }

        $hex = ltrim($color, '#');
        if (strlen($hex) !== 6) {
            return '#ffffff';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $luminance = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return $luminance > 128 ? '#000000' : '#ffffff';
    };

    $adjustColor = function (?string $color, float $factor, bool $lighten = true): ?string {
        if (! $color) {
            return null;
        }

        $hex = ltrim($color, '#');
        if (strlen($hex) !== 6) {
            return null;
        }

        $components = [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];

        foreach ($components as &$component) {
            if ($lighten) {
                $component = (int) round($component + (255 - $component) * $factor);
            } else {
                $component = (int) round($component * (1 - $factor));
            }

            $component = max(0, min(255, $component));
        }
        unset($component);

        return sprintf('#%02x%02x%02x', $components[0], $components[1], $components[2]);
    };

    $menuTextColor    = $computeTextColor($menuColor);
    $topbarTextColor  = $computeTextColor($topbarColor);
    $menuHoverColor   = $adjustColor($menuColor, 0.12, true) ?? $menuColor;
    $topbarInputColor = $adjustColor($topbarColor, 0.25, true) ?? '#ffffff';
    $topbarBadgeBg    = $adjustColor($topbarColor, 0.2, false) ?? $topbarTextColor;
    $topbarBadgeText  = $computeTextColor($topbarBadgeBg);
@endphp

<!DOCTYPE html>
<html lang="en" @yield('html-attribute')
    @if($menuColor) data-menu-color="{{ $menuColor }}" data-sidebar-color="{{ $menuTextColor === '#ffffff' ? 'dark' : 'light' }}" @endif
    @if($topbarColor) data-topbar-custom="{{ $topbarColor }}" data-topbar-color="{{ $topbarTextColor === '#ffffff' ? 'dark' : 'light' }}" @endif
>

<head>
    @include('layouts.partials/title-meta')

    @include('layouts.partials/head-css')

    @if($menuColor || $topbarColor)
        <style>
            @if($menuColor)
                .app-sidebar,
                .app-sidebar .logo-box {
                    background-color: {{ $menuColor }} !important;
                }

                .app-sidebar .menu-title,
                .app-sidebar .nav-link,
                .app-sidebar .nav-link .nav-icon,
                .app-sidebar .nav-link .nav-text,
                .app-sidebar .nav-link iconify-icon,
                .app-sidebar .nav-link i {
                    color: {{ $menuTextColor }} !important;
                }

                .app-sidebar .nav-link:hover,
                .app-sidebar .nav-link:focus,
                .app-sidebar .nav-link.active {
                    background-color: {{ $menuHoverColor ?? $menuColor }} !important;
                    color: {{ $menuTextColor }} !important;
                }

                .app-sidebar .nav-link:hover .nav-icon,
                .app-sidebar .nav-link:focus .nav-icon,
                .app-sidebar .nav-link.active .nav-icon,
                .app-sidebar .nav-link:hover iconify-icon,
                .app-sidebar .nav-link:focus iconify-icon,
                .app-sidebar .nav-link.active iconify-icon {
                    color: {{ $menuTextColor }} !important;
                }

                .app-sidebar .nav-link:hover .nav-text,
                .app-sidebar .nav-link:focus .nav-text,
                .app-sidebar .nav-link.active .nav-text {
                    color: {{ $menuTextColor }} !important;
                }

                .app-sidebar .navbar-nav,
                .app-sidebar {
                    border-color: transparent !important;
                }
            @endif

            @if($topbarColor)
                .app-topbar {
                    background-color: {{ $topbarColor }} !important;
                }

                .app-topbar .topbar-button,
                .app-topbar .topbar-button iconify-icon,
                .app-topbar .topbar-item > a,
                .app-topbar .topbar-item > button,
                .app-topbar .topbar-item a iconify-icon,
                .app-topbar .topbar-item button iconify-icon {
                    color: {{ $topbarTextColor }} !important;
                }

                .app-topbar .app-search .form-control {
                    background-color: {{ $topbarInputColor }} !important;
                    color: {{ $topbarTextColor }} !important;
                    border-color: transparent !important;
                }

                .app-topbar .app-search .form-control::placeholder {
                    color: {{ $computeTextColor($topbarInputColor) }} !important;
                }

                .app-topbar .topbar-badge {
                    background-color: {{ $topbarBadgeBg }} !important;
                    color: {{ $topbarBadgeText }} !important;
                }
            @endif
        </style>
    @endif

        <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
/>
  <!-- Select2 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet"
  />




</head>

<body>

    <div class="app-wrapper">

        @include('layouts.partials/sidebar')

        @include('layouts.partials/topbar')

        <div class="page-content">

            <div class="container-fluid">

                @yield('content')

            </div>

            @include('layouts.partials/footer')
        </div>

    </div>

    @include('layouts.partials/vendor-scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>
  // Si no tienes ninguna configuración personalizada,
  // inicialízalas como objetos vacíos.
  window.defaultConfig = window.defaultConfig || {};
  window.config        = window.config        || {};
</script>
@vite('resources/js/app.js')
@stack('scripts')

</body>

</html>
