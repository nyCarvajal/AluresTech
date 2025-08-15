{{-- resources/views/ventas/_modal-pago.blade.php --}}

<div class="modal fade" id="modalPagarFactura" tabindex="-1" aria-labelledby="modalPagarFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagarFacturaLabel">
                    <i class="fa fa-file-invoice-dollar me-2"></i> Pagar Factura
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                {{-- Encabezado: Factura #ID / Total / Fecha de Pago --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <p class="mb-1 text-muted small text-uppercase">
                            Factura #<span id="ventaIdDisplay">0</span>
                        </p>
                        <p class="h2">
                            $ <span id="totalInvoiceDisplay">0,00</span>
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="mb-1 text-muted small">Fecha de Pago</p>
                        <input 
                            type="text" 
                            class="form-control-plaintext fw-bold fs-5" 
                            id="fechaPagoDisplay" 
                            readonly
                        >
                    </div>
                    <div class="text-end">
                        <a href="#" class="text-primary small fst-italic change_method" style="display: none;">
                            <i class="fa fa-exchange-alt me-1"></i> Cambiar Método
                        </a>
                    </div>
                </div>

                {{-- “Por Pagar” / “Cambio” --}}
                <div class="content_payment_and_changed" style="display: none;">
                    <div class="payment_text mb-2">
                        <p class="text-uppercase text-muted mb-0">Por Pagar</p>
                        <div class="border-top w-50 mx-2"></div>
                        <p class="h4 text-danger mb-0">
                            $ <span id="porPagarDisplay">0,00</span>
                        </p>
                    </div>
                    <div class="changed_text mb-2" style="display: none;">
                        <p class="text-uppercase text-muted mb-0">Cambio</p>
                        <div class="border-top w-50 mx-2"></div>
                        <p class="h4 text-danger mb-0">
                            $ <span id="cambioDisplay">0,00</span>
                        </p>
                    </div>
                </div>

                {{-- Métodos de Pago --}}
                <div class="container_options text-center mb-4">
                    <h6 class="text-uppercase fw-semibold">Método de Pago</h6>
                    <div class="d-flex justify-content-center flex-wrap gap-3 mt-3">
                        <button type="button" class="btn-metodo btn-metodo-efectivo" title="Efectivo">
                            <i class="fa fa-money-bill-wave mb-2"></i>
                            <div>Efectivo</div>
                        </button>
                        <button type="button" class="btn-metodo btn-metodo-tarjeta" title="Tarjeta">
                            <i class="fa fa-credit-card mb-2"></i>
                            <div>Tarjeta</div>
                        </button>
                        <button type="button" class="btn-metodo btn-metodo-otro" title="Otro">
                            <i class="fa fa-piggy-bank mb-2"></i>
                            <div>Otro</div>
                        </button>
                        <button type="button" class="btn-metodo btn-metodo-transferencia" title="Transferencia">
                            <i class="fa fa-university mb-2"></i>
                            <div>Transferencia</div>
                        </button>
                    </div>
                </div>

                {{-- Sección Efectivo --}}
                <div class="cash_container mb-4" style="display: none;">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="inputMontoRecibido" class="form-label">
                                <i class="fa fa-hand-holding-dollar me-1"></i> Monto Recibido
                            </label>
                            <input 
                                type="text" 
                                id="inputMontoRecibido" 
                                class="form-control currency-input" 
                                placeholder="0,00"
                            >
                        </div>
                    </div>
                </div>

                {{-- Sección Tarjeta / Banco / Otro --}}
                <div class="card_container mb-4" style="display: none;">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="inputValorBanco" class="form-label">
                                <i class="fa fa-dollar-sign me-1"></i> Valor
                            </label>
                            <input 
                                type="text" 
                                id="inputValorBanco" 
                                class="form-control currency-input" 
                                placeholder="0,00"
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="selectBanco" class="form-label">
                                <i class="fa fa-university me-1"></i> Banco
                            </label>
                            <select id="selectBanco" class="form-select">
                                <option value="" disabled selected>Selecciona un banco</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Formulario oculto de pago --}}
                <form id="formPago" method="POST" action="{{ route('pagos.store') }}">
                    @csrf
                    <input type="hidden" name="venta_id"   id="pago_venta_id">
                    <input type="hidden" name="fecha_hora" id="pago_fecha_hora">
                    <input type="hidden" name="valor"      id="pago_valor">
                    <input type="hidden" name="cuenta"     id="pago_cuenta">
                    <input type="hidden" name="estado"     id="pago_estado" value="pendiente">
                </form>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button 
                    type="button" 
                    class="btn btn-outline-secondary" 
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>
                <button 
                    type="button" 
                    class="btn btn-primary btn-confirmar-pago"
                >
                    <i class="fa fa-check-circle me-1"></i> Confirmar Pago
                </button>
            </div>

        </div>
    </div>
</div>
