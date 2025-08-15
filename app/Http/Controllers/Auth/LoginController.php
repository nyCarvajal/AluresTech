<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;              // ← importa esto
use Illuminate\Support\Facades\Auth;  

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

   

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
	 public function showLoginForm()
    {
        return view('auth.signin');
    }
	 public function login(Request $request)
    {
        // 1) Valida los datos del formulario
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2) Intenta autenticar con email+password
        if (Auth::attempt($credentials)) {
            // Regenera la sesión para protegerte contra fixation attacks
            $request->session()->regenerate();

            // Redirige a la ruta anterior o al dashboard
            return redirect()->intended(route('dashboard'));
        }

        // 3) Si falla, vuelve con error
        return back()
            ->withErrors(['email' => 'Las credenciales no coinciden.'])
            ->onlyInput('email');
    }
	
	public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/showLoginForm');
}

}
