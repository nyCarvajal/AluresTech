<!DOCTYPE html>
<html lang="en" @yield('html-attribute')>

<head>
    @include('layouts.partials/title-meta')

    @include('layouts.partials/head-css')
	
	
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