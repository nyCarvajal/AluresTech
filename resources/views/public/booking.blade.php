<!DOCTYPE html>
<html lang="es">
<head>
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
                <p class="lead mb-0">Crea tu cuenta, verifica tu correo e ingresa cuando quieras para agendar la cita que necesitas.</p>
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
        $pendingVerification = session('public_cliente_pending_' . $peluqueria->id);
        $appointmentErrors = $errors->appointment ?? null;
    @endphp

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card card-shadow h-100">
                <div class="card-body p-4">
                    <ul class="nav nav-pills nav-fill mb-4" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="register-tab" data-bs-toggle="pill" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="true">Registrarme</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="false">Iniciar sesión</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="authTabsContent">
                        <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
                            <form method="POST" action="{{ route('public.booking.register', $peluqueria) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="register-nombres">Nombre</label>
                                    <input type="text" class="form-control @error('nombres', 'register') is-invalid @enderror" id="register-nombres" name="nombres" value="{{ old('nombres') }}" required>
                                    @error('nombres', 'register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="register-apellidos">Apellidos</label>
                                    <input type="text" class="form-control @error('apellidos', 'register') is-invalid @enderror" id="register-apellidos" name="apellidos" value="{{ old('apellidos') }}">
                                    @error('apellidos', 'register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="register-correo">Correo electrónico</label>
                                    <input type="email" class="form-control @error('correo', 'register') is-invalid @enderror" id="register-correo" name="correo" value="{{ old('correo') }}" required>
                                    @error('correo', 'register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="register-whatsapp">WhatsApp</label>
                                    <input type="text" class="form-control @error('whatsapp', 'register') is-invalid @enderror" id="register-whatsapp" name="whatsapp" value="{{ old('whatsapp') }}">
                                    @error('whatsapp', 'register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="register-password">Contraseña</label>
                                    <input type="password" class="form-control @error('password', 'register') is-invalid @enderror" id="register-password" name="password" required>
                                    @error('password', 'register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="register-password-confirmation">Confirmar contraseña</label>
                                    <input type="password" class="form-control" id="register-password-confirmation" name="password_confirmation" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="register-captcha">Verificación anti-spam</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $captchaQuestion }}</span>
                                        <input type="number" class="form-control @error('captcha', 'register') is-invalid @enderror" id="register-captcha" name="captcha" inputmode="numeric" pattern="[0-9]*" placeholder="Resultado" required>
                                    </div>
                                    @error('captcha', 'register')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Escribe el resultado para demostrar que no eres un robot.</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="login" role="tabpanel" aria-labelledby="login-tab">
                            <form method="POST" action="{{ route('public.booking.login', $peluqueria) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="login-correo">Correo electrónico</label>
                                    <input type="email" class="form-control @error('correo', 'login') is-invalid @enderror" id="login-correo" name="correo" value="{{ old('correo') }}" required>
                                    @error('correo', 'login')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="login-password">Contraseña</label>
                                    <input type="password" class="form-control @error('password', 'login') is-invalid @enderror" id="login-password" name="password" required>
                                    @error('password', 'login')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                            </form>
                        </div>
                    </div>
                    @if ($pendingVerification)
                        <div class="alert alert-warning mt-4" role="alert">
                            Hemos enviado un enlace de verificación a <strong>{{ $pendingVerification }}</strong>. Revisa tu bandeja de entrada o correo no deseado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-shadow h-100">
                <div class="card-body p-4">
                    <h2 class="h4 fw-bold mb-4">Agenda tu cita</h2>
                    @if ($cliente)
                        <div class="alert alert-info" role="alert">
                            Hola {{ $cliente->nombres }}. @if(! $cliente->email_verified_at) Tu correo aún no está verificado. Revisa tu correo para confirmar tu cuenta. @else ¡Ya puedes solicitar tu cita! @endif
                        </div>
                        <form method="POST" action="{{ route('public.booking.appointment', $peluqueria) }}" class="mb-4">
                            @csrf
                            <div class="row g-3">
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
                            <button type="submit" class="btn btn-primary mt-4 w-100" @if(! $cliente->email_verified_at) disabled @endif>Solicitar cita</button>
                        </form>
                        <form method="POST" action="{{ route('public.booking.logout', $peluqueria) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">Cerrar sesión</button>
                        </form>
                    @else
                        <p class="text-muted">Regístrate o inicia sesión para agendar una cita. Si ya hiciste tu registro recuerda verificar el enlace que enviamos a tu correo.</p>
                    @endif
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
        const storedTime = @json(old('hora'));

        const fetchSlots = () => {
            if (!dateInput || !timeSelect || !dateInput.value) {
                return;
            }

            timeSelect.innerHTML = '<option value="">Cargando horarios...</option>';

            fetch('{{ route('public.booking.availability', $peluqueria) }}?date=' + dateInput.value)
                .then(response => response.json())
                .then(data => {
                    timeSelect.innerHTML = '<option value="">Selecciona un horario</option>';
                    if (data.slots && data.slots.length) {
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
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Sin horarios disponibles';
                        timeSelect.appendChild(option);
                    }
                })
                .catch(() => {
                    timeSelect.innerHTML = '<option value="">No se pudo cargar la disponibilidad</option>';
                });
        };

        if (dateInput) {
            dateInput.addEventListener('change', fetchSlots);
            fetchSlots();
        }
    });
</script>
</body>
</html>
