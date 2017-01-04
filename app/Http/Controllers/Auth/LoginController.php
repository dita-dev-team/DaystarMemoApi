<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required', 'password' => 'required',
        ]);

        if ($this->attemptLogin($request)) {
            //return $this->sendLoginResponse($request);
            $request->session()->regenerate();
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'error' => 'invalid credentials'
            ], 422);
        }
    }

    public function attemptLogin(Request $request)
    {
        // Use the 'web' guard to login
        return Auth::guard('web')->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }
}
