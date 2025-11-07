<!DOCTYPE html>
<html lang="es">
<head>
@php
    $trainerLabelSingular = $trainerLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $trainerLabelPlural = $trainerLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda tu cita - {{ $peluqueria->nombre }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fb;
            color: #1f2933;
        }
        .hero {
            background: linear-gradient(135deg, {{ $peluqueria->color ?? '#6c5ce7' }} 0%, #1f2933 100%);
            color: #fff;
            border-radius: 24px;
            padding: 2.5rem;
        }
        .hero-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .hero-logo {
            width: 96px;
            height: 96px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
        }
        .hero-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .card-shadow {
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
            border: none;
            border-radius: 18px;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-primary {
            background-color: {{ $peluqueria->color ?? '#6c5ce7' }};
            border-color: {{ $peluqueria->color ?? '#6c5ce7' }};
        }
        .btn-primary:hover {
            filter: brightness(0.92);
        }
        .nav-pills .nav-link.active {
            background-color: rgba(15, 23, 42, 0.08);
            color: #1f2933;
        }
        .timeline-item {
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.6rem;
            top: 0.2rem;
            width: 10px;
            height: 10px;
            background: {{ $peluqueria->color ?? '#6c5ce7' }};
            border-radius: 50%;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 1rem;
            top: 1.2rem;
            bottom: -1.2rem;
            width: 2px;
            background: rgba(15, 23, 42, 0.1);
        }
        .timeline-item:last-child::after {
            display: none;
        }
        @media (max-width: 575.98px) {
            .hero {
                padding: 2rem;
            }
            .hero-logo {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="hero mb-5">
        <div class="hero-header">
            <div class="hero-logo">
                <img src="{{ $peluqueriaLogo }}" alt="Logo de {{ $peluqueria->nombre }}">
            </div>
            <div>
                <h1 class="fw-bold mb-3">Reserva tu próximo servicio en {{ $peluqueria->nombre }}</h1>
                <p class="lead mb-0">Completa tus datos y solicita tu cita en pocos pasos. Nosotros nos encargamos del resto.</p>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show card-shadow" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show card-shadow" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @php
        $appointmentErrors = $errors->appointment ?? null;
        $defaultStylistId = old('entrenador_id');
        if (is_string($defaultStylistId) && str_contains($defaultStylistId, ':')) {
            $parts = array_values(array_filter(explode(':', $defaultStylistId), fn ($segment) => $segment !== ''));
            $defaultStylistId = end($parts) ?: reset($parts);
        }
        if (! $defaultStylistId && isset($estilistas) && $estilistas->count() === 1) {
            $defaultStylistId = optional($estilistas->first())->id;
        }
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card card-shadow">
                <div class="card-body p-4">
                    <h2 class="h4 fw-bold mb-4">Agenda tu cita</h2>
                    <p class="text-muted mb-4">Déjanos tus datos básicos y elige el horario que mejor te funcione. Te contactaremos para confirmar tu reserva.</p>
                    <form method="POST" action="{{ route('public.booking.appointment', $peluqueria) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-first-name">Nombre</label>
                                <input type="text" class="form-control @if($appointmentErrors?->has('nombres')) is-invalid @endif" id="appointment-first-name" name="nombres" value="{{ old('nombres', $cliente->nombres ?? '') }}" required>
                                @if($appointmentErrors?->has('nombres'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('nombres') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-last-name">Apellidos</label>
                                <input type="text" class="form-control @if($appointmentErrors?->has('apellidos')) is-invalid @endif" id="appointment-last-name" name="apellidos" value="{{ old('apellidos', $cliente->apellidos ?? '') }}">
                                @if($appointmentErrors?->has('apellidos'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('apellidos') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-whatsapp">WhatsApp</label>
                                <input type="text" class="form-control @if($appointmentErrors?->has('whatsapp')) is-invalid @endif" id="appointment-whatsapp" name="whatsapp" value="{{ old('whatsapp', $cliente->whatsapp ?? '') }}" required>
                                @if($appointmentErrors?->has('whatsapp'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('whatsapp') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-stylist">{{ $trainerLabelSingular }}</label>
                                <select class="form-select @if($appointmentErrors?->has('entrenador_id')) is-invalid @endif" id="appointment-stylist" name="entrenador_id" @if(($estilistas ?? collect())->isEmpty()) disabled @endif required>
                                    <option value="">Selecciona a tu {{ \Illuminate\Support\Str::lower($trainerLabelSingular) }}</option>
                                    @foreach($estilistas ?? [] as $estilista)
                                        @php
                                            $estilistaId = $estilista->id;
                                            if (is_string($estilistaId) && str_contains($estilistaId, ':')) {
                                                $idParts = array_values(array_filter(explode(':', $estilistaId), fn ($segment) => $segment !== ''));
                                                $estilistaId = end($idParts) ?: reset($idParts);
                                            }
                                        @endphp
                                        <option value="{{ $estilistaId }}" @selected((string) $defaultStylistId === (string) $estilistaId)>{{ trim($estilista->nombre . ' ' . ($estilista->apellidos ?? '')) }}</option>
                                    @endforeach
                                </select>
                                @if(($estilistas ?? collect())->isEmpty())
                                    <div class="form-text text-danger">No hay {{ \Illuminate\Support\Str::lower($trainerLabelPlural) }} disponibles por ahora.</div>
                                @endif
                                @if($appointmentErrors?->has('entrenador_id'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('entrenador_id') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-date">Fecha</label>
                                <input type="date" class="form-control @if($appointmentErrors?->has('fecha')) is-invalid @endif" id="appointment-date" name="fecha" value="{{ old('fecha') ?? now()->format('Y-m-d') }}" required>
                                @if($appointmentErrors?->has('fecha'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('fecha') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-time">Hora</label>
                                <select class="form-select @if($appointmentErrors?->has('hora')) is-invalid @endif" id="appointment-time" name="hora" required>
                                    <option value="">Selecciona un horario</option>
                                </select>
                                @if($appointmentErrors?->has('hora'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('hora') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Duración</label>
                                <p class="form-control-plaintext fw-semibold text-muted mb-0">{{ $defaultDuration }} minutos</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-tipocita">Servicio</label>
                                <select class="form-select @if($appointmentErrors?->has('tipocita_id')) is-invalid @endif" id="appointment-tipocita" name="tipocita_id">
                                    <option value="">Selecciona una opción</option>
                                    @foreach($tipocitas as $tipo)
                                        <option value="{{ $tipo->id }}" @selected(old('tipocita_id') == $tipo->id)>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @if($appointmentErrors?->has('tipocita_id'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('tipocita_id') }}</div>
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="appointment-note">Notas adicionales</label>
                                <textarea class="form-control @if($appointmentErrors?->has('nota_cliente')) is-invalid @endif" id="appointment-note" name="nota_cliente" rows="3" placeholder="Cuéntanos detalles que debamos saber">{{ old('nota_cliente') }}</textarea>
                                @if($appointmentErrors?->has('nota_cliente'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('nota_cliente') }}</div>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 w-100">Solicitar cita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($cliente && $proximasReservas->isNotEmpty())
        <div class="card card-shadow mt-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-4">Tus próximas solicitudes</h2>
                @foreach($proximasReservas as $reserva)
                    <div class="timeline-item">
                        <h3 class="h6 mb-1">{{ $reserva->tipo ?? 'Reserva' }} · {{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y H:i') }}</h3>
                        <p class="mb-1 text-muted">Estado: <strong>{{ $reserva->estado }}</strong> · Duración: {{ $reserva->duracion }} minutos</p>
                        @if($reserva->nota_cliente)
                            <p class="mb-0 small">Nota: {{ $reserva->nota_cliente }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateInput = document.getElementById('appointment-date');
        const timeSelect = document.getElementById('appointment-time');
        const stylistSelect = document.getElementById('appointment-stylist');
        const storedTime = @json(old('hora'));
        const availabilityUrl = @json(route('public.booking.availability', $peluqueria));
        const stylistLabelSingular = @json($trainerLabelSingular);
        const stylistLabelPlural = @json($trainerLabelPlural);

        const setTimePlaceholder = (message) => {
            if (!timeSelect) {
                return;
            }
            timeSelect.innerHTML = `<option value="">${message}</option>`;
            timeSelect.disabled = true;
        };

        const fetchSlots = () => {
            if (!dateInput || !timeSelect) {
                return;
            }

            const dateValue = dateInput.value;
            const stylistValue = stylistSelect ? stylistSelect.value : '';

            if (!dateValue) {
                setTimePlaceholder('Selecciona una fecha');
                return;
            }

            if (stylistSelect && !stylistValue) {
                setTimePlaceholder(`Selecciona a tu ${stylistLabelSingular}`);
                return;
            }

            timeSelect.innerHTML = '<option value="">Cargando horarios...</option>';
            timeSelect.disabled = true;

            const params = new URLSearchParams({ date: dateValue });
            if (stylistValue) {
                const normalizedStylist = stylistValue.includes(':')
                    ? stylistValue.split(':').filter(Boolean).pop()
                    : stylistValue;
                if (normalizedStylist) {
                    params.append('entrenador_id', normalizedStylist);
                }
            }

            fetch(`${availabilityUrl}?${params.toString()}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('No se pudo obtener disponibilidad');
                    }
                    return response.json();
                })
                .then(data => {
                    timeSelect.innerHTML = '<option value="">Selecciona un horario</option>';
                    if (Array.isArray(data.slots) && data.slots.length) {
                        timeSelect.disabled = false;
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = slot;
                            if (storedTime && storedTime === slot) {
                                option.selected = true;
                            }
                            timeSelect.appendChild(option);
                        });
                    } else {
                        setTimePlaceholder('Sin horarios disponibles');
                    }
                })
                .catch(() => {
                    setTimePlaceholder('No se pudo cargar la disponibilidad');
                });
        };

        if (dateInput) {
            dateInput.addEventListener('change', fetchSlots);
        }
        if (stylistSelect) {
            stylistSelect.addEventListener('change', fetchSlots);
        }
        fetchSlots();
    });
</script>
</body>
</html>
