{{-- resources/views/ventas/index.blade.php --}}

@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])


@section('content')
<div class="container">

    {{-- ==============================================================
         1) SECCIÓN: Dropdown para elegir “Cliente” (Cliente)
         ——————————————————————————————————————————————————————————————
         Si el usuario selecciona un cliente, se envía GET a ventas.index?cliente_id=XX.
         Como no existe orden_id, el controlador creará la orden automáticamente y
         luego redirigirá a ventas.index?cliente_id=XX&orden_id=YY.
       ============================================================== --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="{{ route('ventas.index') }}">
                <div class="input-group">
                    <label class="input-group-text" for="cliente_id">
                        <i class="fa fa-user-graduate me-1"></i> Cliente
                    </label>
                    <select
                        name="cliente_id"
                        id="cliente_id"
                        class="form-select"
                        onchange="this.form.submit()"
                    >
                        <option value="" {{ $clienteSeleccionado ? '' : 'selected' }} disabled>
                            -- Seleccione un cliente --
                        </option>
                        @foreach($clientes as $cliente)
                            <option
                                value="{{ $cliente->id }}"
                                {{ $clienteSeleccionado && $clienteSeleccionado->id === $cliente->id ? 'selected' : '' }}
                            >
                                {{ $cliente->nombres }}
                                {{ $cliente->apellidos ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Como estamos enviando solo cliente_id en GET, 
                         el controlador se encarga de crear la orden y redirigir 
                         con ?cliente_id=XX&orden_id=YY --}}
                </div>
            </form>
        </div>
    </div>
	{{-- ==============================================================
             3) SECCIÓN: Datos de la orden de compra actual (muestra mínimo ID, cliente y fecha)
           ============================================================== --}}
        <div class="row mb-5">
  {{-- Columna mayor: tarjeta de la orden --}}
  <div class="col-md-8">
    <div class="card mb-4 shadow-sm" >
      <div class="card-header" style="background-color: #f8e5a1; color: #6c4e09;">
        <i class="fa fa-file-invoice me-2"></i>
        Orden de Compra #{{ $ordenSeleccionada->id }}
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <p class="mb-1"><strong>Cliente:</strong></p>
            <p>{{ optional($ordenSeleccionada->cliente)->nombres }}</p>
          </div>
          <div class="col-md-4">
            <p class="mb-1"><strong>Fecha/Hora:</strong></p>
            <p>{{ $ordenSeleccionada->fecha_hora->format('d/m/Y H:i') }}</p>
          </div>
          <div class="col-md-4">
            <p class="mb-1"><strong>Estado:</strong></p>
            <p>{{ $ordenSeleccionada->activa ? 'Activa' : 'Cerrada' }}</p>
          </div>
		  <div class="col-md-4">
            <p class="mb-1"><strong>Total:</strong></p>
             <span id="cardTotalFactura">${{ number_format($totalVentas,2,',','.') }}</span>
			
			 
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Columna menor: bloque de botones --}}
  <div class="col-md-4 d-grid gap-2">
   
        <button
                                        class="btn btn-dorado btn-pagar"
                                                                                style="height: 80px;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPagarFactura"
                                        data-cuenta="{{ $ordenSeleccionada->id }}"
                                                                                data-total="{{ $totalVentas }}"
                                    >
                                        <i class="fa fa-credit-card me-1"></i> Pagar
                                    </button>
      

    @if(auth()->user()->peluqueria && auth()->user()->peluqueria->pos == 1)
      <button id="btn-factura_post" class="btn btn-dorado" style="height: 80px;">
        <i class="fa fa-fw me-1" title="Finalizar factura pos"></i>
        Finalizar factura pos
      </button>
    @endif

    @if(auth()->user()->peluqueria && auth()->user()->peluqueria->cuentaCobro == 1)
  <a href="{{ route('orden_de_compras.show', $ordenSeleccionada->id) }}"
     class="btn btn-dorado"
         style="height: 80px;"
     id="btn-cuenta_cobro">
    <i class="fa fa-fw me-1" title="Cuenta de cobro"></i>
    Generar cuenta de cobro
  </a>
@endif


    @if(auth()->user()->peluqueria && auth()->user()->peluqueria->electronica == 1)
      <button id="btn-factura_electronica" class="btn btn-dorado" style="width: 80px;">
        <i class="pe-7s-news-paper me-1"></i>
        Generar factura electrónica
      </button>
    @endif
  </div>
</div>

    

    {{-- ==============================================================
         2) SECCIÓN: Si ya hay “ordenSeleccionada”, mostramos formulario 
            para “Agregar Venta” (seleccionar ítem).
       ============================================================== --}}
    @if($ordenSeleccionada && $clienteSeleccionado)
        <div class="row mb-12">
            <div class="col-md-8">
                <form method="POST" action="{{ route('ventas.storeByItem') }}">
                    @csrf
                    {{-- Enviamos hidden el cliente_id para que el redirect preserve ese parámetro --}}
                    <input type="hidden" name="cliente_id" value="{{ $clienteSeleccionado->id }}">
                    {{-- Enviamos hidden la orden para asociar la venta a esa orden --}}
                    <input type="hidden" name="orden_de_compra_id" value="{{ $ordenSeleccionada->id }}">

                    <div class="input-group">
                        <label class="input-group-text" for="item_id">
                            <i class="fa fa-box-open me-1"></i> Ítem
                        </label>
                        <select
                            name="item_id"
                            id="item_id"
                            class="form-select @error('item_id') is-invalid @enderror"
                            required
                        >
                            <option value="" selected disabled>
                                -- Elige un producto / servicio --
                            </option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nombre }}
                                    ( ${{ number_format($item->valor, 2, ',', '.') }} )
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-dorado" type="submit">
                            <i class="fa fa-plus me-1"></i> Agregar Venta
                        </button>
                    </div>
                    @error('item_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>

        
    @endif

    {{-- ==============================================================
         4) TABLA DE VENTAS (SIEMPRE VISIBLE)
       ============================================================== --}}
<div class="card mb-5 shadow-sm">
    <div class="card-header" style="background-color: #f8e5a1; color: #6c4e09;">
        <i class="fa fa-shopping-cart me-2"></i> Listado de Ventas
    </div>
    <div class="table-responsive">
  <table class="table table-striped table-hover align-middle mb-0 table-dorado">
    <thead class="table-light">
      <tr>
        <th>Producto (Ítem ID)</th>
        <th>Cantidad</th>
        <th>Descuento (%)</th>
        <th>Valor Unitario</th>
        <th>Valor Total</th>
		<th> comision </th>		
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    @forelse($ventas as $venta)
      <tr data-venta-id="{{ $venta->id }}">
        <td>{{ optional($venta->item)->nombre }}</td>

        {{-- Form por fila --}}
        <td>
          <form action="{{ route('ventas.update', $venta->id) }}" method="POST" class="d-flex align-items-center gap-2">
            @csrf
            @method('PUT')
            <input type="number"
                   name="cantidad"
                   value="{{ $venta->cantidad }}"
                   class="form-control form-control-sm js-cantidad"
                   style="width: 90px;" min="1" step="1" required>
        </td>

        <td>
          <input type="number"
                 name="descuento"
                 value="{{ $venta->descuento }}"
                 class="form-control form-control-sm js-descuento"
                 style="width: 90px;" min="0" max="100" step="0.01"
                 placeholder="0-100">
        </td>

        <td>
          <input type="number"
                 name="valor_unitario"
                 value="{{ $venta->valor_unitario }}"
                 step="0.01" min="0"
                 class="form-control form-control-sm js-valor-unitario"
                 style="width: 120px;" required>

          {{-- Muestra del unitario neto (solo visual) --}}
          <small class="text-muted d-block mt-1">
            Neto: <strong class="js-valor-unitario-neto"></strong>
          </small>
        </td>

        <td>
          <span class="fw-semibold js-valor-total"></span>
        </td>
		<td>
		 <input type="number" step="0.01" min="0" max="100"
           name="porcentajeComision"
           value="{{ old('porcentajeComision', $venta->porcentajeComision ?? '') }}"
           class="form-control" placeholder="Ej: 10">
		<br>
		<select name="usuario_id" class="form-select">
      <option value="">-- Sin asignar --</option>
      @foreach($usuarios as $u)
        <option value="{{ $u->id }}" {{ old('usuario_id', $venta->usuario_id ?? '') == $u->id ? 'selected' : '' }}>
          {{ $u->nombre }}
        </option>
      @endforeach
    </select>
		</td>

        <td class="d-flex">
          <button type="submit" class="btn btn-sm btn-dorado me-2">Guardar</button>
          </form>
		
          <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST"
                onsubmit="return confirm('¿Eliminar esta venta?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-dorado-outline">Eliminar</button>
          </form>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center">No hay ventas registradas.</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

    </div>
</div>
    {{-- ==============================================================
         5) TABLA DE PAGOS POR ORDEN (SI EXISTE ?orden_id=XX)
       ============================================================== --}}
    @if($cuentaSeleccionada)
        <div class="card mb-5 shadow-sm">
            <div class="card-header" style="background-color: #f8e5a1; color: #6c4e09;">
                <i class="fa fa-receipt me-2"></i> Pagos de la orden:
                <strong class="text-warning">{{ $cuentaSeleccionada }}</strong>
            </div>
            <div class="card-body p-0">
                @if($pagos->isEmpty())
                    <div class="alert alert-warning mb-0">
                        No se encontraron pagos asociados a la orden 
                        <strong>{{ $cuentaSeleccionada }}</strong>.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 table-dorado">
                            <thead class="table-light">
                                <tr>
                                   
                                    <th>Fecha y Hora</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagos as $pago)
                                    <tr>
                                        
                                        <td>{{ $pago->fecha_hora->format('d/m/Y H:i') }}</td>
                                        <td>
                                            $ {{ number_format($pago->valor, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            @switch($pago->estado)
                                                @case('pendiente')
                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                    @break
                                                @case('completado')
                                                    <span class="badge bg-success">Completado</span>
                                                    @break
                                                @case('anulado')
                                                    <span class="badge bg-danger">Anulado</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($pago->estado) }}
                                                    </span>
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div> {{-- /container --}}

{{-- ==============================================================
     6) MODAL DE PAGO DE FACTURA (idéntico a antes)
   ============================================================== --}}
<style>
    .btn-dorado {
        background-color: #f8e5a1;
        color: #6c4e09;
        border: none;
    }
    .btn-dorado:hover {
        background-color: #f6db81;
        color: #6c4e09;
    }
    .btn-dorado-outline {
        background-color: #fff;
        border: 2px solid #f8e5a1;
        color: #6c4e09;
    }
    .btn-dorado-outline:hover {
        background-color: #f8e5a1;
        color: #6c4e09;
    }
    .table-dorado thead {
        background-color: #f9eec7;
    }
    .table-dorado tbody tr:nth-child(even) {
        background-color: #fff8e1;
    }

    /* Estilos personalizados para el modal */
    #modalPagarFactura .modal-header {
        background-color: #f8e5a1;
        color: #6c4e09;
    }
    #modalPagarFactura .modal-title {
        font-weight: 600;
    }
    #modalPagarFactura .btn-close {
        /* sin filtro para fondo claro */
    }

    .btn-metodo {
        width: 100px;
        height: 90px;
        border: 2px solid #d4b54d;
        color: #d4b54d;
        font-weight: 500;
        border-radius: 8px;
        transition: background-color .2s, color .2s;
    }
    .btn-metodo i {
        font-size: 1.4rem;
    }
    .btn-metodo:hover {
        background-color: #d4b54d;
        color: #fff;
    }

    .content_payment_and_changed {
        margin-top: 20px;
    }
    .payment_text, .changed_text {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .payment_text p,
    .changed_text p {
        margin-bottom: 0;
    }
    .payment_text .border-top,
    .changed_text .border-top {
        border-top: 2px dashed #ccc;
    }
</style>

@include('ventas._modal-pago')

{{-- ==============================================================
     7) SCRIPTS PARA EL MODAL DE PAGO
   ============================================================== --}}
@push('scripts')
<script>
   
document.addEventListener('DOMContentLoaded', function() {
    const modal                  = document.getElementById('modalPagarFactura');
    const ventaIdDisplay         = document.getElementById('ventaIdDisplay');
    const totalInvoiceDisplay    = document.getElementById('totalInvoiceDisplay');
    const fechaPagoDisplay       = document.getElementById('fechaPagoDisplay');
    const porPagarDisplay        = document.getElementById('porPagarDisplay');
    const cambioDisplay          = document.getElementById('cambioDisplay');
    const sectionPaymentAndChanged = document.querySelector('.content_payment_and_changed');
    const paymentText            = document.querySelector('.payment_text');
    const changedText            = document.querySelector('.changed_text');
    const containerOptions       = document.querySelector('.container_options');
    const changeMethodLink       = document.querySelector('.change_method');
    const cashContainer          = document.querySelector('.cash_container');
    const cardContainer          = document.querySelector('.card_container');
    const btnEfectivo            = document.querySelector('.btn-metodo-efectivo');
    const btnTarjeta             = document.querySelector('.btn-metodo-tarjeta');
    const btnOtro                = document.querySelector('.btn-metodo-otro');
    const btnTransferencia       = document.querySelector('.btn-metodo-transferencia');
    const inputMontoRecibido     = document.getElementById('inputMontoRecibido');
    const inputValorBanco        = document.getElementById('inputValorBanco');
    const selectBanco            = document.getElementById('selectBanco');

    // Formulario oculto
    const formPago               = document.getElementById('formPago');
    const pagoVentaIdInput       = document.getElementById('pago_venta_id');
    const pagoFechaHoraInput     = document.getElementById('pago_fecha_hora');
    const pagoValorInput         = document.getElementById('pago_valor');
    const pagoCuentaInput        = document.getElementById('pago_cuenta');
    const pagoEstadoInput        = document.getElementById('pago_estado');

    // Variable para guardar cuál botón abrió el modal
    let triggerButton = null;

    // Funciones para formatear/parsear COP
    function formatCOP(value) {
        if (!value) return '';
        let n = parseFloat(value.toString()
                   .replace(/[^0-9\.\,]/g, '')
                   .replace(/\,/, '.'));
        if (isNaN(n)) return '';
        return n.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
    }
    function parseCOP(formatted) {
        if (!formatted) return 0;
        let plain = formatted
                    .replace(/[^0-9\.\,]/g, '')
                    .replace(/\./g,'')
                    .replace(/\,/,'.');
        let n = parseFloat(plain);
        return isNaN(n) ? 0 : n;
    }

    // Cuando se abra el modal, capturamos el botón que lo disparó
    modal.addEventListener('show.bs.modal', function(event) {
        triggerButton = event.relatedTarget;

        // Obtenemos datos del botón disparador
        const ventaId   = triggerButton.getAttribute('data-venta-id');
        const total     = parseFloat(triggerButton.getAttribute('data-total'));
        const cuenta    = triggerButton.getAttribute('data-cuenta');

        // Mostrar ID y total
        ventaIdDisplay.textContent = cuenta;
        totalInvoiceDisplay.textContent = total
            .toLocaleString('es-CO',{minimumFractionDigits:2, maximumFractionDigits:2});

        // Inicializar por pagar = total, cambio = 0
        porPagarDisplay.textContent = total
            .toLocaleString('es-CO',{minimumFractionDigits:2, maximumFractionDigits:2});
        cambioDisplay.textContent  = '0,00';

        // Ocultar secciones internas
        sectionPaymentAndChanged.style.display = 'none';
        changedText.style.display    = 'none';
        paymentText.style.display    = 'flex';
        containerOptions.style.display = 'block';
        changeMethodLink.style.display = 'none';
        cashContainer.style.display    = 'none';
        cardContainer.style.display    = 'none';
        inputMontoRecibido.value       = '';
        inputValorBanco.value          = '';
        selectBanco.selectedIndex      = 0;

        // Fecha actual en formato “YYYY-MM-DD HH:mm:ss”
        const now = new Date();
        const dd  = String(now.getDate()).padStart(2, '0');
        const mm  = String(now.getMonth() + 1).padStart(2, '0');
        const yyyy= now.getFullYear();
        const hh  = String(now.getHours()).padStart(2, '0');
        const mi  = String(now.getMinutes()).padStart(2, '0');
        const ss  = String(now.getSeconds()).padStart(2, '0');
        const fechaHoraIso = `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
        fechaPagoDisplay.value = `${dd}/${mm}/${yyyy} ${hh}:${mi}`;

        // Guardamos la cuenta en un atributo de totalInvoiceDisplay para facilitar el acceso
        totalInvoiceDisplay.setAttribute('data-cuenta', cuenta);
    });

    // Al hacer clic en “Efectivo”
    btnEfectivo.addEventListener('click', function() {
        containerOptions.style.display     = 'none';
        changeMethodLink.style.display     = 'block';
        cashContainer.style.display        = 'block';
        cardContainer.style.display        = 'none';
        sectionPaymentAndChanged.style.display = 'block';
        paymentText.style.display          = 'flex';
        changedText.style.display          = 'none';
    });
    // Al hacer clic en “Tarjeta”
    btnTarjeta.addEventListener('click', function() {
        containerOptions.style.display     = 'none';
        changeMethodLink.style.display     = 'block';
        cashContainer.style.display        = 'none';
        cardContainer.style.display        = 'block';
        sectionPaymentAndChanged.style.display = 'block';
        paymentText.style.display          = 'flex';
        changedText.style.display          = 'none';
    });
    // Al hacer clic en “Otro”
    btnOtro.addEventListener('click', function() {
        containerOptions.style.display     = 'none';
        changeMethodLink.style.display     = 'block';
        cashContainer.style.display        = 'none';
        cardContainer.style.display        = 'block';
        sectionPaymentAndChanged.style.display = 'block';
        paymentText.style.display          = 'flex';
        changedText.style.display          = 'none';
    });
    // Al hacer clic en “Transferencia”
    btnTransferencia.addEventListener('click', function() {
        containerOptions.style.display     = 'none';
        changeMethodLink.style.display     = 'block';
        cashContainer.style.display        = 'none';
        cardContainer.style.display        = 'block';
        sectionPaymentAndChanged.style.display = 'block';
        paymentText.style.display          = 'flex';
        changedText.style.display          = 'none';
    });

    // Calcular “Por Pagar” o “Cambio” cuando cambie el monto en efectivo
    inputMontoRecibido.addEventListener('keyup', function() {
        let recibido = parseCOP(this.value);
        let total    = parseCOP(totalInvoiceDisplay.textContent);
        if (recibido >= total) {
            let cambio = recibido - total;
            cambioDisplay.textContent  = cambio
                .toLocaleString('es-CO',{minimumFractionDigits:2, maximumFractionDigits:2});
            changedText.style.display  = 'flex';
            paymentText.style.display  = 'none';
        } else {
            let falta = total - recibido;
            porPagarDisplay.textContent = falta
                .toLocaleString('es-CO',{minimumFractionDigits:2, maximumFractionDigits:2});
            changedText.style.display  = 'none';
            paymentText.style.display  = 'flex';
        }
    });

    // Formatear inputs de moneda al perder foco
    document.querySelectorAll('.currency-input').forEach(input => {
        input.addEventListener('blur', function() {
            this.value = formatCOP(this.value);
        });
        input.addEventListener('focus', function() {
            let num = parseCOP(this.value);
            this.value = num ? num.toFixed(2).replace('.', ',') : '';
        });
    });

    // “Cambiar Método”
    changeMethodLink.addEventListener('click', function(e) {
        e.preventDefault();
        containerOptions.style.display     = 'block';
        changeMethodLink.style.display     = 'none';
        sectionPaymentAndChanged.style.display = 'none';
        cashContainer.style.display        = 'none';
        cardContainer.style.display        = 'none';
        inputMontoRecibido.value           = '';
        inputValorBanco.value              = '';
        selectBanco.selectedIndex          = 0;
        porPagarDisplay.textContent        = totalInvoiceDisplay.textContent;
        changedText.style.display          = 'none';
        paymentText.style.display          = 'flex';
    });

    // Aquí se guarda el pago en la base de datos
    document.querySelector('.btn-confirmar-pago').addEventListener('click', function() {
        // 1) Obtener ID de venta y monto pagado
        const ventaId = ventaIdDisplay.textContent;
        let montoPagado = 0;
        let metodo = '';

        if (cashContainer.style.display === 'block') {
            metodo = 'EFECTIVO';
            montoPagado = parseCOP(inputMontoRecibido.value);
        } else if (cardContainer.style.display === 'block' && selectBanco.value) {
            metodo = 'BANCO';
            montoPagado = parseCOP(inputValorBanco.value);
        } else {
            metodo = 'OTRO';
            montoPagado = parseCOP(inputValorBanco.value);
        }

        // 2) Rellenar campos ocultos del formulario
        // Venta ID
        pagoVentaIdInput.value = ventaId;

        // Fecha/Hora actual en formato ISO
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm   = String(now.getMonth() + 1).padStart(2, '0');
        const dd   = String(now.getDate()).padStart(2, '0');
        const hh   = String(now.getHours()).padStart(2, '0');
        const mi   = String(now.getMinutes()).padStart(2, '0');
        const ss   = String(now.getSeconds()).padStart(2, '0');
        const fechaHoraIso = `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
        pagoFechaHoraInput.value = fechaHoraIso;

        // Valor pagado
        pagoValorInput.value = montoPagado;

        // Cuenta (orden_id) viene de triggerButton.getAttribute('data-cuenta')
        const cuenta = triggerButton.getAttribute('data-cuenta');
        pagoCuentaInput.value = cuenta;

        // Estado (por ejemplo “completado”)
        pagoEstadoInput.value = 'completado';

        // 3) Enviar formulario oculto para crear el pago
        formPago.submit();
    });
});
</script>

<script>
(function () {
  const money = (n) => {
    // Formato COP; ajusta a tu moneda si usas otra
    if (isNaN(n)) n = 0;
    return n.toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 2 });
  };

  function recalcularFila(tr) {
    const qtyInput  = tr.querySelector('.js-cantidad');
    const dscInput  = tr.querySelector('.js-descuento');
    const unitInput = tr.querySelector('.js-valor-unitario');
    const netoEl    = tr.querySelector('.js-valor-unitario-neto');
    const totalEl   = tr.querySelector('.js-valor-total');

    const cantidad  = parseFloat(qtyInput?.value)  || 0;
    const descuento = parseFloat(dscInput?.value)  || 0;
    const unitBase  = parseFloat(unitInput?.value) || 0;

    // Limita descuento 0-100 visualmente (por si el user teclea >100)
    const d = Math.min(Math.max(descuento, 0), 100);

    const unitNeto = +(unitBase * (1 - d/100)).toFixed(2);
    const total    = +(unitNeto * cantidad).toFixed(2);

    if (netoEl)  netoEl.textContent  = money(unitNeto);
    if (totalEl) totalEl.textContent = money(total);

    // Si QUIERES que el input "valor_unitario" muestre el neto (y no el base),
    // descomenta la línea siguiente. OJO: perderías el valor base en el form.
    // unitInput.value = unitNeto;
  }

  // Inicializa y añade listeners por cada fila
  document.querySelectorAll('tr[data-venta-id]').forEach(tr => {
    recalcularFila(tr);

    ['input', 'change'].forEach(evt => {
      tr.querySelectorAll('.js-cantidad, .js-descuento, .js-valor-unitario')
        .forEach(inp => inp.addEventListener(evt, () => recalcularFila(tr)));
    });
  });
})();
</script>


@endpush

@endsection
