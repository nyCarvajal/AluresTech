<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function __construct()
    {
        // Solo usuarios autenticados y con rol admin (solo ejemplo)
        $this->middleware('auth');
        $this->middleware(function($req, $next){
          //  if (auth()->user()->role != 18) {
          
//		  abort(403);
        //    }
            return $next($req);
        });
    }

    /** 1️⃣ Crear entrenador */
    public function createTrainer()
    {

        return view('users.create_trainer');
    }

   public function storeTrainer(Request $request)
    {
        $data = $request->validate([
            'nombre'                 => 'required|string|max:255',
            'apellidos'             => 'required|string|max:255',
            'email'                 => 'required|email|unique:usuarios,email',
           // 'nivel'                 => 'required|string|max:100',
            'tipo_identificacion'   => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion'             => 'required|string|max:255',
            'whatsapp'              => 'required|string|max:30',
            'password'              => 'required|string|confirmed|min:8',
        ]);

        User::create([
            'nombre'                => $data['nombre'],
            'apellidos'             => $data['apellidos'],
            'email'                 => $data['email'],
         //   'nivel'                 => $data['nivel'],
            'tipo_identificacion'   => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion'             => $data['direccion'],
            'whatsapp'              => $data['whatsapp'],
            'password'              => Hash::make($data['password']),
            'club_id'               => Auth::user()->club_id,
            'role'                  => 11,  // entranador
        ]);

        return redirect()
       ->route('users.index')
       ->with('success', 'Entrenador creado correctamente.');
    }

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'nombre'                  => 'required|string|max:255',
            'apellidos'             => 'required|string|max:255',
            'email'                 => 'required|email|unique:usuarios,email',
         //   'nivel'                 => 'required|string|max:100',
            'tipo_identificacion'   => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion'             => 'required|string|max:255',
            'whatsapp'              => 'required|string|max:30',
            'password'              => 'required|string|confirmed|min:8',
        ]);

        User::create([
            'nombre'                  => $data['nombre'],
            'apellidos'             => $data['apellidos'],
            'email'                 => $data['email'],
       //     'nivel'                 => $data['nivel'],
            'tipo_identificacion'   => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion'             => $data['direccion'],
            'whatsapp'              => $data['whatsapp'],
            'password'              => Hash::make($data['password']),
            'club_id'               => Auth::user()->club_id,
            'role'                  => 18,  // administrador
        ]);

        return redirect()
       ->route('users.index')
       ->with('success', 'Administrador creado correctamente.');
    }

    /** 2️⃣ Crear administrador */
    public function createAdmin()
    {
        return view('users.create_admin');
    }

   
	
	public function index()
{
	$clubId=Auth::user()->club_id;
	
    $users = User::with('club') ->where('club_id', $clubId)->orderBy('nombre')->paginate(15);
    return view('users.index', compact('users'));
}

}
