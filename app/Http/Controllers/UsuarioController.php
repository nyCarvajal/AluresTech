<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\RoleLabelResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($req, $next) {
            return $next($req);
        });
    }

    public function createTrainer()
    {
        $labels = $this->stylistLabels();

        return view('users.create_trainer', [
            'trainerLabelSingular' => $labels['singular'],
            'trainerLabelPlural' => $labels['plural'],
        ]);
    }

    public function storeTrainer(Request $request)
    {
        $labels = $this->stylistLabels();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'tipo_identificacion' => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'password' => 'required|string|confirmed|min:8',
            'color' => 'nullable|string|max:7',
        ]);

        User::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'tipo_identificacion' => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion' => $data['direccion'],
            'whatsapp' => $data['whatsapp'],
            'password' => Hash::make($data['password']),
            'peluqueria_id' => Auth::user()->peluqueria_id,
            'role' => 11,
            'color' => $data['color'] ?? null,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', $labels['singular'] . ' creado correctamente.');
    }

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'tipo_identificacion' => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'password' => 'required|string|confirmed|min:8',
            'color' => 'nullable|string|max:7',
        ]);

        User::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'tipo_identificacion' => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion' => $data['direccion'],
            'whatsapp' => $data['whatsapp'],
            'password' => Hash::make($data['password']),
            'peluqueria_id' => Auth::user()->peluqueria_id,
            'role' => 18,
            'color' => $data['color'] ?? null,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Administrador creado correctamente.');
    }

    public function createAdmin()
    {
        return view('users.create_admin');
    }

    public function index()
    {
        $peluqueriaId = Auth::user()->peluqueria_id;

        $users = User::with('peluqueria')
            ->where('peluqueria_id', $peluqueriaId)
            ->orderBy('nombre')
            ->paginate(15);

        $labels = $this->stylistLabels();

        return view('users.index', [
            'users' => $users,
            'trainerLabelSingular' => $labels['singular'],
            'trainerLabelPlural' => $labels['plural'],
        ]);
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $user->id,
            'tipo_identificacion' => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'password' => 'nullable|string|confirmed|min:8',
            'color' => 'nullable|string|max:7',
        ]);

        $updateData = [
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'tipo_identificacion' => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion' => $data['direccion'],
            'whatsapp' => $data['whatsapp'],
            'color' => $data['color'] ?? null,
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    protected function stylistLabels(): array
    {
        return RoleLabelResolver::forStylist();
    }
}
