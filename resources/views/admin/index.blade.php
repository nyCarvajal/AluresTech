@extends('layouts.vertical', ['subtitle' => 'Inicio'])

@section('content')

@include('layouts.partials/page-title', ['title' => 'SmashsTech', 'subtitle' => 'Inicio'])

<!doctype html>
<html lang="es">
<head>
  <link href="{{ asset('vendor/darkone/css/app.css') }}" rel="stylesheet">
</head>
<body>
  @yield('content')
  <script src="{{ asset('vendor/darkone/js/app.js') }}"></script>
</body>
</html>


<div class="row">
    <!-- Card 1 -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-7">
                        <p class="text-muted mb-0 text-truncate">Ventas</p>
                        <h5 class="text-dark mt-2 mb-0">${{number_format($totalPagosMes, 0, ',', '.')}}</h5>
                    </div>

                    <div class="col-5">
                        <div class="ms-auto avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:cart-large-linear"
                                class="fs-32 avatar-title text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart01"></div>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                       <p class="text-muted mb-0 text-truncate">Clientes</p>

                        <h3 class="text-dark mt-2 mb-0">{{$totalClientes}}</h3>

                    </div>

                    <div class="col-6">
                        <div class="ms-auto avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:users-group-two-rounded-broken"
                                class="fs-32 avatar-title text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart02"></div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="text-muted mb-0 text-truncate">Reservas</p>
                        <h3 class="text-dark mt-2 mb-0">{{$totalReservas}}</h3>
                    </div>

                    <div class="col-6">
                        <div class="ms-auto avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:calendar-outline"
                                class="fs-32 avatar-title text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart03"></div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="text-muted mb-0 text-truncate">Clases</p>
                        <h3 class="text-dark mt-2 mb-0">{{$totalClases}}</h3>
                    </div>

                    <div class="col-6">
                        <div class="ms-auto avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:dumbbell-large-minimalistic-line-duotone"
                                class="fs-32 avatar-title text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart04"></div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h4 class="card-title mb-0">Nuevos Clientes</h4>

                <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-light">

                    Ver Todos
                </a>
            </div>
            <!-- end card-header-->

            <div class="card-body pb-1">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-centered">
                        <thead>
                            <th class="py-1">ID</th>
                            <th class="py-1">Nombres</th>
                            <th class="py-1">Whatsapp</th>
                            <th class="py-1">Nivel</th>
                        </thead>
                        <tbody>
						
                            @forelse($clientes as $cliente)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><a href="{{ route('clientes.show', $cliente) }}" >{{ $cliente->nombres }} {{ $cliente->apellidos }} </a></td>
                                <td>  @php
                  // Limpiar el número para wa.me
                  $clean = preg_replace('/\D+/', '', $cliente->whatsapp);
                @endphp<a href="https://wa.me/{{ $clean }}" target="_blank">
                  {{ $cliente->whatsapp }}
                </a></td>
                                <td>
                                    <span class="badge bg-success">{{ optional($cliente->nivel)->nivel ?? '—' }}</span>
                                </td>
                                
                            </tr>
							 @empty
                      <tr><td colspan="4" class="text-center">Aún no hay clientes</td></tr>
                  @endforelse
                            
                               
                           
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card-->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    Transacciones Recientes
                </h4>

                <a href="{{ route('orden_de_compras.index') }}" class="btn btn-sm btn-light">
                    Ver Todas
                </a>
            </div>
            <!-- end card-header-->

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-centered">
                        <thead>
                            <th class="py-1">ID</th>
                            <th class="py-1">Fecha</th>
                            <th class="py-1">Cliente</th>
                            <th class="py-1">Monto</th>
                           
                        </thead>
                        <tbody>
						 @forelse($cuentas as $cuenta)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
						  <td><a href="{{ route('orden_de_compras.show', $cuenta) }}" >{{ $cuenta->fecha_hora->format('d/m/Y H:i') }}</a></td>
                          <td>{{ $cuenta->cliente->nombres }} {{ $cuenta->cliente->apellidos }}</td>
                          <td>${{ number_format($cuenta->monto, 0, ',', '.') }}</td>
                          
                      </tr>
                  @empty
                      <tr><td colspan="4" class="text-center">Sin cuentas</td></tr>
                  @endforelse
						
						
						
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card-->
    </div>
    <!-- end col -->
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <h4 class=" mb-0 flex-grow-1 mb-0">Revenue</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-light">ALL</button>
                    <button type="button" class="btn btn-sm btn-outline-light">1M</button>
                    <button type="button" class="btn btn-sm btn-outline-light">6M</button>
                    <button type="button" class="btn btn-sm btn-outline-light active">1Y</button>
                </div>
            </div>

            <div class="card-body pt-0">
                <div dir="ltr">
                    <div id="dash-performance-chart" class="apex-charts"></div>
                </div>
            </div>

        </div> <!-- end card-->
    </div> <!-- end col -->
	

    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <h4 class="card-title flex-grow-1 mb-0">Sales By Category</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-light">ALL</button>
                    <button type="button" class="btn btn-sm btn-outline-light">1M</button>
                    <button type="button" class="btn btn-sm btn-outline-light">6M</button>
                    <button type="button" class="btn btn-sm btn-outline-light active">1Y</button>
                </div>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="conversions" class="apex-charts"></div>
                </div>
                <div class="table-responsive mb-n1 mt-2">
                    <table class="table table-nowrap table-borderless table-sm table-centered mb-0">
                        <thead class="bg-light bg-opacity-50 thead-sm">
                            <tr>
                                <th class="py-1">
                                    Category
                                </th>
                                <th class="py-1">Orders</th>
                                <th class="py-1">Perc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Grocery</td>
                                <td>187,232</td>
                                <td>
                                    48.63%
                                    <span class="badge badge-soft-success float-end">2.5% Up</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Electonics</td>
                                <td>126,874</td>
                                <td>
                                    36.08%
                                    <span class="badge badge-soft-success float-end">8.5% Up</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td>90,127</td>
                                <td>
                                    23.41%
                                    <span class="badge badge-soft-danger float-end">10.98% Down</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive-->
            </div>

        </div> <!-- end card-->
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div
                class="d-flex card-header justify-content-between align-items-center border-bottom border-dashed">
                <h4 class="card-title mb-0">Sessions by Country</h4>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        View Data
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Download</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Export</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Import</a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div id="world-map-markers" class="mt-3" style="height: 309px">
                </div>
            </div> <!-- end card-body-->


        </div> <!-- end card-->
    </div> <!-- end col-->

</div> <!-- End row -->

<!-- end row -->
@endsection

@section('scripts')
@vite(['resources/js/pages/dashboard.js'])
@endsection