@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <!-- Bootstrap CSS -->
    <link 
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
      rel="stylesheet"
    >
	
    @stack('styles')
</head>
<body>
    {{-- Incluye aquí tu navbar lateral o superior del admin template --}}
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Bootstrap JS (Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @stack('scripts')


    @section('scripts')
  

  @vite('resources/js/app.js')
  
@endsection
</body>
</html>

