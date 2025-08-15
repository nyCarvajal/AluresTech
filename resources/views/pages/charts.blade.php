@extends('layouts.vertical', ['subtitle' => 'Charts'])

@section('content')

@include('layouts.partials/page-title', ['title' => 'Darkone', 'subtitle' => 'Chart'])





@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">

      <div class="card-header text-center">
        <h4 class="mb-0">Informes Administrativos</h4>
      </div>

      <div class="card-body">
        <div class="row g-4">

          {{-- === Informe de Comisiones === --}}
          <div class="col-md-6">
            <a href=""
               class="btn btn-outline-primary w-100 py-4 d-flex flex-column align-items-center justify-content-center">
              <i class="bx bx-credit-card-alt text-primary mb-2"></i> <i class='bxr  '  ></i> 
              <span class="fw-semibold">Informe de Comisiones</span>
            </a>
          </div>

          {{-- === Informe de Gastos === --}}
          <div class="col-md-6">
            <a href="{{ route('salidas.index') }}"
               class="btn btn-outline-danger w-100 py-4 d-flex flex-column align-items-center justify-content-center">
              <i class="bx  bx-dollar-circle text-danger"></i>
              <span class="fw-semibold">Informe de Gastos</span>
            </a>
          </div>

          {{-- === Informe de Pagos === --}}
          <div class="col-md-6">
            <a href="{{ route('pagos.index') }}"
               class="btn btn-outline-success w-100 py-4 d-flex flex-column align-items-center justify-content-center">
              <i class="bx  bx-wallet text-success mb-2"></i>
              <span class="fw-semibold">Informe de Pagos</span>
            </a>
          </div>

          {{-- === Informe de Clases === --}}
          <div class="col-md-6">
            <a href=""
               class="btn btn-outline-warning w-100 py-4 d-flex flex-column align-items-center justify-content-center">
              <i class="bx bx-tennis-ball text-warning mb-2"></i>
              <span class="fw-semibold">Informe de Clases</span>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection














<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Line with Data Labels</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-line-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Gradient Line Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-line-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Stacked Area</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-area" class="apex-charts" ED4DC">
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic Column Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-column-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->


<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Column Chart with Datalabels</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-column-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Mixed Chart - Line & Area</h4>
            </div>

            <div class="card-body">

                <div dir="ltr">
                    <div id="apex-mixed-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic Bar Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-bar-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Bar with Negative Values</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-bar-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Line, Column & Area Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-mixed-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Scatter (XY) Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-scatter-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Scatter Chart - Datetime</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-scatter-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Simple Candlestick Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-candlestick-1" class="apex-charts" abc9c">
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Combo Candlestick Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div class="pt-3">
                        <div id="apex-candlestick-2" class="apex-charts" 7b84b">
                        </div>
                        <div id="apex-candlestick-3" class="apex-charts" 7cde6">
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Simple Pie Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-pie-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Gradient Donut Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-pie-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Patterned Donut Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-pie-3" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic RadialBar Chart</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-radialbar-1" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Multiple RadialBars</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-radialbar-2" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Stroked Circular Guage</h4>
            </div>

            <div class="card-body">
                <div dir="ltr">
                    <div id="apex-radialbar-3" class="apex-charts"></div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Sparkline Charts</h4>
            </div>
            <div class="card-body">
                <div dir="ltr">
                    <div class="row pt-3">
                        <div class="col-md-4">
                            <div id="spark1" class="apex-charts mb-sm-0 mb-4" 658dd">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="spark2" class="apex-charts mb-sm-0 mb-4" CE6EC">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="spark3" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->

@endsection

@section('scripts')
<script src="https://apexcharts.com/samples/assets/irregular-data-series.js"></script>
<script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
@vite(['resources/js/pages/chart.js'])
@endsection