<div class="mb-3">
  <label>Nombre</label>
  <input type="text" name="nombre" value="{{ old('nombre', $peluqueria->nombre ?? '') }}" class="form-control" required>
</div>

<div class="form-check form-check-inline">
  <input type="checkbox" name="cuentaCobro" value="1" id="cuentaCobro" class="form-check-input" {{ (old('cuentaCobro', $peluqueria->cuentaCobro ?? false) ? 'checked' : '') }}>
  <label for="cuentaCobro">Cuenta Cobro</label>
</div>

<div class="mb-3">
  <label>Términos</label>
  <textarea name="terminos" class="form-control" required>{{ old('terminos', $peluqueria->terminos ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label>Mensaje Nueva Reserva</label>
  <textarea name="msj_reserva_confirmada" class="form-control">{{ old('msj_reserva_confirmada', $peluqueria->msj_reserva_confirmada ?? '') }}</textarea>
</div>
<div class="mb-3">
  <label>Mensaje Bienvenida</label>
  <textarea name="msj_bienvenida" class="form-control">{{ old('msj_bienvenida', $peluqueria->msj_bienvenida ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label>NIT</label>
  <input type="text" name="nit" value="{{ old('nit', $peluqueria->nit ?? '') }}" class="form-control">
</div>
<div class="mb-3">
  <label>Dirección</label>
  <input type="text" name="direccion" value="{{ old('direccion', $peluqueria->direccion ?? '') }}" class="form-control">
</div>

