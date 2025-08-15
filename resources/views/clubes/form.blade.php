<div class="mb-3">
  <label>Nombre</label>
  <input type="text" name="nombre" value="{{ old('nombre', $club->nombre ?? '') }}" class="form-control" required>
</div>

<div class="form-check form-check-inline">
  <input type="checkbox" name="cuentaCobro" value="1" id="cuentaCobro" class="form-check-input" {{ (old('cuentaCobro', $club->cuentaCobro ?? false) ? 'checked' : '') }}>
  <label for="cuentaCobro">Cuenta Cobro</label>
</div>

<div class="mb-3">
  <label>Términos</label>
  <textarea name="terminos" class="form-control" required>{{ old('terminos', $club->terminos ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label>Mensaje Recordatorio</label>
  <textarea name="msj_reserva_confirmada" class="form-control">{{ old('msj_reserva_confirmada', $club->msj_reserva_confirmada ?? '') }}</textarea>
</div>
<div class="mb-3">
  <label>Mensaje Bienvenida</label>
  <textarea name="msj_bienvenida" class="form-control">{{ old('msj_bienvenida', $club->msj_bienvenida ?? '') }}</textarea>
</div>
<div class="mb-3">
  <label>Mensaje paquete finalizado</label>
  <textarea name="msj_finalizado" class="form-control">{{ old('msj_finalizado', $club->msj_finalizado ?? '') }}</textarea>
</div>
<div class="mb-3">
  <label>NIT</label>
  <input type="text" name="nit" value="{{ old('nit', $club->nit ?? '') }}" class="form-control">
</div>
<div class="mb-3">
  <label>Dirección</label>
  <input type="text" name="direccion" value="{{ old('direccion', $club->direccion ?? '') }}" class="form-control">
</div>

