{{-- resources/views/pagos/create.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])

@section('content')
<div class="container">
    <h1>
        <i class="fa fa-plus-circle me-1"></i> Registrar Nuevo Pago
    </h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @isset($saldoPendiente)
        <div class="alert alert-info">
            <i class="fa fa-info-circle me-1"></i>
            Saldo pendiente de la orden: <strong>COP {{ number_format($saldoPendiente, 0, ',', '.') }}</strong>
        </div>
    @endisset

    <form action="{{ route('pagos.store') }}" method="POST" id="pago-form">
        @csrf
        <input type="hidden" name="redirect_to_order" value="1">

        @include('pagos._form', [
            'saldoPendiente' => $saldoPendiente ?? null,
            'defaultDate' => $defaultDate ?? null,
            'useStandaloneDefaults' => true,
        ])
    </form>
</div>
@endsection
