<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ClienteVerificationMail;
use App\Mail\NuevaReservaPeluqueriaMail;
use App\Models\Cliente;
use App\Models\Peluqueria;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Tipocita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    private const DEFAULT_DURATION = 60;

    public function show(Request $request, Peluqueria $peluqueria)
    {
        $this->setTenantConnection($peluqueria);

        $cliente = $this->currentClient($peluqueria);
        $tipocitas = Tipocita::orderBy('nombre')->get();
        $estilistas = $this->availableStylists($peluqueria);
        $proximasReservas = collect();

        $captcha = $this->regenerateCaptcha($request, $peluqueria);

        if ($cliente) {
            $proximasReservas = Reserva::where('cliente_id', $cliente->id)
                ->orderBy('fecha')
                ->whereDate('fecha', '>=', Carbon::today())
                ->take(5)
                ->get();
        }

        return view('public.booking', [
            'peluqueria' => $peluqueria,
            'tipocitas' => $tipocitas,
            'cliente' => $cliente,
            'proximasReservas' => $proximasReservas,
            'captchaQuestion' => $captcha['question'],
            'defaultDuration' => self::DEFAULT_DURATION,
            'peluqueriaLogo' => $this->resolveLogoUrl($peluqueria),
            'estilistas' => $estilistas,
        ]);
    }

    public function register(Request $request, Peluqueria $peluqueria)
    {
        $this->setTenantConnection($peluqueria);

        $validator = Validator::make($request->all(), [
            'nombres' => ['required', 'string', 'max:200'],
            'apellidos' => ['nullable', 'string', 'max:200'],
            'correo' => ['required', 'email', 'max:200'],
            'whatsapp' => ['nullable', 'string', 'max:200'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha' => ['required', 'integer'],
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'captcha.required' => 'Resuelve el captcha para continuar.',
            'captcha.integer' => 'El captcha debe ser un número.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'register')->withInput();
        }

        $data = $validator->validated();
        $correo = strtolower($data['correo']);

        $captchaData = $request->session()->get($this->captchaKey($peluqueria));
        $captchaAnswer = $captchaData['answer'] ?? null;
        if (! $captchaAnswer || (int) $data['captcha'] !== (int) $captchaAnswer) {
            return back()
                ->withErrors(['captcha' => 'La respuesta del captcha es incorrecta.'], 'register')
                ->withInput($request->except('captcha'));
        }

        if (Cliente::where('correo', $correo)->exists()) {
            return back()
                ->withErrors(['correo' => 'Este correo ya está registrado.'], 'register')
                ->withInput();
        }

        $cliente = new Cliente([
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'] ?? null,
            'correo' => $correo,
            'whatsapp' => $data['whatsapp'] ?? null,
        ]);

        $cliente->password = Hash::make($data['password']);
        $cliente->verification_token = Str::random(64);
        $cliente->save();

        $verifyUrl = $this->verificationUrl($peluqueria, $cliente);
        Mail::to($cliente->correo)->send(new ClienteVerificationMail($peluqueria, $cliente, $verifyUrl));

        $request->session()->forget($this->sessionKey($peluqueria));
        $request->session()->put($this->pendingKey($peluqueria), $cliente->correo);
        $request->session()->forget($this->captchaKey($peluqueria));

        return redirect()
            ->route('public.booking.show', $peluqueria)
            ->with('status', 'Registro exitoso. Revisa tu correo para verificar tu cuenta.');
    }

    public function login(Request $request, Peluqueria $peluqueria)
    {
        $this->setTenantConnection($peluqueria);

        $validator = Validator::make($request->all(), [
            'correo' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'login')->withInput();
        }

        $data = $validator->validated();
        $correo = strtolower($data['correo']);

        $cliente = Cliente::where('correo', $correo)->first();

        if (! $cliente || empty($cliente->password) || ! Hash::check($data['password'], $cliente->password)) {
            return back()
                ->withErrors(['correo' => 'Credenciales inválidas.'], 'login')
                ->withInput($request->only('correo'));
        }

        if (! $cliente->email_verified_at) {
            return back()
                ->withErrors(['correo' => 'Debes verificar tu correo antes de agendar.'], 'login')
                ->withInput($request->only('correo'));
        }

        $request->session()->put($this->sessionKey($peluqueria), $cliente->id);
        $request->session()->forget($this->pendingKey($peluqueria));
        $request->session()->regenerate();

        return redirect()
            ->route('public.booking.show', $peluqueria)
            ->with('status', 'Bienvenido de nuevo. Ya puedes agendar tu cita.');
    }

    public function logout(Request $request, Peluqueria $peluqueria)
    {
        $request->session()->forget($this->sessionKey($peluqueria));
        $request->session()->forget($this->pendingKey($peluqueria));
        $request->session()->regenerateToken();

        return redirect()->route('public.booking.show', $peluqueria)
            ->with('status', 'Has cerrado sesión correctamente.');
    }

    public function schedule(Request $request, Peluqueria $peluqueria)
    {
        $this->setTenantConnection($peluqueria);

        $cliente = $this->currentClient($peluqueria);

        if (! $cliente) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->withErrors(['general' => 'Debes iniciar sesión para agendar.'], 'appointment');
        }

        if (! $cliente->email_verified_at) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->withErrors(['general' => 'Verifica tu correo para poder agendar.'], 'appointment');
        }

        if ($request->has('entrenador_id')) {
            $request->merge([
                'entrenador_id' => $this->normalizeStylistId($request->input('entrenador_id')),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'tipocita_id' => ['nullable', 'integer', 'exists:tipocita,id'],
            'nota_cliente' => ['nullable', 'string', 'max:1000'],
            'entrenador_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($peluqueria) {
                    if (! $this->stylistExists($peluqueria, (int) $value)) {
                        $fail('El estilista seleccionado no es válido.');
                    }
                },
            ],
        ], [
            'tipocita_id.exists' => 'El tipo de cita seleccionado no es válido.',
            'entrenador_id.required' => 'Selecciona el estilista que atenderá tu cita.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->withErrors($validator, 'appointment')
                ->withInput();
        }

        $data = $validator->validated();

        $inicio = Carbon::createFromFormat('Y-m-d H:i', $data['fecha'] . ' ' . $data['hora']);
        if ($inicio->isPast()) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->withErrors(['fecha' => 'No puedes agendar en una fecha pasada.'], 'appointment')
                ->withInput();
        }

        $duracion = self::DEFAULT_DURATION;
        $fin = (clone $inicio)->addMinutes($duracion);

        $conflicto = Reserva::where('estado', '<>', 'Cancelada')
            ->when(! empty($data['entrenador_id']), function ($query) use ($data) {
                $query->where(function ($sub) use ($data) {
                    $sub->whereNull('entrenador_id')
                        ->orWhere('entrenador_id', $data['entrenador_id']);
                });
            })
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha', [$inicio, $fin->copy()->subSecond()])
                    ->orWhere(function ($sub) use ($inicio, $fin) {
                        $sub->where('fecha', '<=', $inicio)
                            ->whereRaw('DATE_ADD(fecha, INTERVAL duracion MINUTE) > ?', [$inicio->format('Y-m-d H:i:s')]);
                    });
            })
            ->exists();

        if ($conflicto) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->withErrors(['fecha' => 'El horario seleccionado ya no está disponible.'], 'appointment')
                ->withInput();
        }

        $tipoCita = null;
        if (! empty($data['tipocita_id'])) {
            $tipoCita = Tipocita::find($data['tipocita_id']);
        }

        $reserva = Reserva::create([
            'fecha' => $inicio,
            'duracion' => $duracion,
            'cliente_id' => $cliente->id,
            'estado' => 'Pendiente',
            'tipo' => $tipoCita?->nombre ?? 'Reserva',
            'nota_cliente' => $data['nota_cliente'] ?? null,
            'entrenador_id' => $data['entrenador_id'],
        ]);

        $recipients = User::where('peluqueria_id', $peluqueria->id)
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (! empty($recipients)) {
            Mail::to($recipients)->send(new NuevaReservaPeluqueriaMail($peluqueria, $cliente, $reserva));
        }

        return redirect()
            ->route('public.booking.show', $peluqueria)
            ->with('status', 'Tu solicitud fue enviada. Te confirmaremos por correo.');
    }

    public function verify(Request $request, Peluqueria $peluqueria)
    {
        $this->setTenantConnection($peluqueria);

        $token = $request->query('token');
        $correo = strtolower((string) $request->query('email'));

        if (! $token || ! $correo) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        $cliente = Cliente::where('correo', $correo)->first();

        if (! $cliente) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        if ($cliente->email_verified_at) {
            $request->session()->put($this->sessionKey($peluqueria), $cliente->id);
            $request->session()->forget($this->pendingKey($peluqueria));
            $request->session()->regenerate();

            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->with('status', 'Tu correo ya estaba verificado. Ya puedes agendar tu cita.');
        }

        if (! $cliente->verification_token || ! hash_equals((string) $cliente->verification_token, (string) $token)) {
            return redirect()
                ->route('public.booking.show', $peluqueria)
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        $cliente->markEmailAsVerified();

        $request->session()->put($this->sessionKey($peluqueria), $cliente->id);
        $request->session()->forget($this->pendingKey($peluqueria));
        $request->session()->regenerate();

        return redirect()
            ->route('public.booking.show', $peluqueria)
            ->with('status', 'Correo verificado correctamente. Ya puedes agendar tu cita.');
    }

    public function availability(Request $request, Peluqueria $peluqueria)
    {
        $this->setTenantConnection($peluqueria);

        $date = $request->query('date');
        $rawStylistId = $request->query('entrenador_id');
        $stylistId = $this->normalizeStylistId($rawStylistId);
        if (! $date) {
            return response()->json(['error' => 'Debes indicar la fecha.'], 422);
        }

        if ($rawStylistId !== null && $rawStylistId !== '' && $stylistId === null) {
            return response()->json(['error' => 'El estilista seleccionado no es válido.'], 422);
        }

        if ($stylistId && ! $this->stylistExists($peluqueria, $stylistId)) {
            return response()->json(['error' => 'El estilista seleccionado no es válido.'], 422);
        }

        try {
            $inicioJornada = Carbon::parse($date . ' 08:00:00');
            $finJornada = Carbon::parse($date . ' 20:00:00');
            $intervalo = 15;

            $reservas = Reserva::whereDate('fecha', $date)
                ->where('estado', '<>', 'Cancelada')
                ->when($stylistId, function ($query) use ($stylistId) {
                    $query->where(function ($sub) use ($stylistId) {
                        $sub->whereNull('entrenador_id')
                            ->orWhere('entrenador_id', $stylistId);
                    });
                })
                ->get(['fecha', 'duracion', 'entrenador_id']);

            $ocupados = [];
            foreach ($reservas as $reserva) {
                $inicio = Carbon::parse($reserva->fecha);
                $fin = (clone $inicio)->addMinutes((int) ($reserva->duracion ?? 0));
                $ocupados[] = [$inicio, $fin];
            }

            $slots = [];
            for ($cursor = $inicioJornada->copy(); $cursor->lt($finJornada); $cursor->addMinutes($intervalo)) {
                $finSlot = $cursor->copy()->addMinutes($intervalo);
                $choca = false;
                foreach ($ocupados as [$ocInicio, $ocFin]) {
                    if ($cursor < $ocFin && $finSlot > $ocInicio) {
                        $choca = true;
                        break;
                    }
                }

                if (! $choca) {
                    $slots[] = $cursor->format('H:i');
                }
            }

            return response()->json([
                'slots' => $slots,
                'inicio' => $inicioJornada->format('H:i'),
                'fin' => $finJornada->format('H:i'),
            ]);
        } catch (\Throwable $exception) {
            return response()->json(['error' => 'No se pudo calcular la disponibilidad.'], 500);
        }
    }

    private function availableStylists(Peluqueria $peluqueria)
    {
        $stylists = collect();

        foreach ($this->connectionsFor($peluqueria) as $connection) {
            $connectionStylists = User::on($connection)
                ->where('peluqueria_id', $peluqueria->id)
                ->whereIn('role', [11, '11'])
                ->orderBy('nombre')
                ->orderBy('apellidos')
                ->get();

            if ($connectionStylists->isEmpty()) {
                $connectionStylists = User::on($connection)
                    ->where('peluqueria_id', $peluqueria->id)
                    ->orderBy('nombre')
                    ->orderBy('apellidos')
                    ->get();
            }

            if ($connectionStylists->isNotEmpty()) {
                $stylists = $stylists->merge($connectionStylists);
            }
        }

        return $stylists->unique('id')->values();
    }

    private function normalizeStylistId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            $value = reset($value);
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }

            if (str_contains($value, ':')) {
                $parts = array_values(array_filter(array_map('trim', explode(':', $value)), fn ($segment) => $segment !== ''));
                $value = end($parts) ?: reset($parts);
            }

            if (! is_numeric($value)) {
                preg_match_all('/\d+/', $value, $matches);
                if (! empty($matches[0])) {
                    $value = end($matches[0]);
                }
            }
        }

        if (! is_numeric($value)) {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }

    private function connectionsFor(Peluqueria $peluqueria): array
    {
        $connections = ['mysql'];

        if (! empty($peluqueria->db)) {
            $connections[] = 'tenant';
        }

        return array_unique($connections);
    }

    private function stylistExists(Peluqueria $peluqueria, int $stylistId): bool
    {
        foreach ($this->connectionsFor($peluqueria) as $connection) {
            $exists = User::on($connection)
                ->where('peluqueria_id', $peluqueria->id)
                ->where('id', $stylistId)
                ->exists();

            if ($exists) {
                return true;
            }
        }

        return false;
    }

    private function setTenantConnection(Peluqueria $peluqueria): void
    {
        config(['database.connections.tenant.database' => $peluqueria->db]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
    }

    private function currentClient(Peluqueria $peluqueria): ?Cliente
    {
        $id = session($this->sessionKey($peluqueria));
        if (! $id) {
            return null;
        }

        return Cliente::find($id);
    }

    private function verificationUrl(Peluqueria $peluqueria, Cliente $cliente): string
    {
        return route('public.booking.verify', [
            'peluqueria' => $peluqueria,
            'token' => $cliente->verification_token,
            'email' => $cliente->correo,
        ]);
    }

    private function sessionKey(Peluqueria $peluqueria): string
    {
        return 'public_cliente_' . $peluqueria->id;
    }

    private function pendingKey(Peluqueria $peluqueria): string
    {
        return 'public_cliente_pending_' . $peluqueria->id;
    }

    private function regenerateCaptcha(Request $request, Peluqueria $peluqueria): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        $captcha = [
            'question' => $a . ' + ' . $b,
            'answer' => $a + $b,
        ];

        $request->session()->put($this->captchaKey($peluqueria), $captcha);

        return $captcha;
    }

    private function captchaKey(Peluqueria $peluqueria): string
    {
        return 'public_captcha_' . $peluqueria->id;
    }

    private function resolveLogoUrl(Peluqueria $peluqueria): string
    {
        $candidates = [
            $peluqueria->logo_url ?? null,
            $peluqueria->logo ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (! $candidate) {
                continue;
            }

            if (filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }

            if (Str::startsWith($candidate, ['/'])) {
                return asset(ltrim($candidate, '/'));
            }

            if (Str::startsWith($candidate, ['storage/', 'images/'])) {
                return asset($candidate);
            }

            return asset('storage/' . ltrim($candidate, '/'));
        }

        return asset('images/logoligth.png');
    }
}
