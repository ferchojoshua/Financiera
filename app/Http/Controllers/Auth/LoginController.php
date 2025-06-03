<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

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

    use AuthenticatesUsers;

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
    }
    
    /**
     * Override the username method to use login field
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }
    
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Intentar autenticar con diferentes campos
        if ($this->attemptMultiFieldLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    
    /**
     * Attempt to log the user in using multiple fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptMultiFieldLogin(Request $request)
    {
        $login = $request->input($this->username());
        $password = $request->input('password');
        
        // Try with email
        if (Auth::attempt(['email' => $login, 'password' => $password], $request->filled('remember'))) {
            return true;
        }
        
        // Try with username if the column exists
        if (Schema::hasColumn('users', 'username')) {
            if (Auth::attempt(['username' => $login, 'password' => $password], $request->filled('remember'))) {
                return true;
            }
        }
        
        // Try with nit
        if (Auth::attempt(['nit' => $login, 'password' => $password], $request->filled('remember'))) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Sobreescribir el método para validar con múltiples campos
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Obtener las credenciales para la solicitud de autenticación
     */
    protected function credentials(Request $request)
    {
        $login = $request->input($this->username());
        
        // Determinar qué campo usar para autenticación
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nit';
        
        // Si existe el campo username y no es un email, intentamos identificar si parece un username
        if (Schema::hasColumn('users', 'username') && !filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Si no parece un NIT (número de identificación tributaria), asumimos que es un username
            if (!is_numeric($login)) {
                $field = 'username';
            }
        }
        
        return [
            $field => $login,
            'password' => $request->input('password'),
        ];
    }
}
