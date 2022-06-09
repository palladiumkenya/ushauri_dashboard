<?php
namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Session;
use Auth;
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
   // protected $redirectTo = '/Reports/dashboard';

   protected function redirectTo() {
    if (Auth::user()->first_access == 'Yes') {
        return '/user/passwordreset';
    }
    else if (Auth::user()->access_level == 'Facility') {
        Session::flash('statuscode', 'Login Success!, You will be redirected to your Home page in a few.');
        return '/admin/dashboard';
    } else if (Auth::user()->access_level == 'Partner') {
        Session::flash('statuscode', 'success');
        return '/admin/dashboard';
    } else if (Auth::user()->access_level == 'Admin') {
        Session::flash('statuscode', 'success');
        return '/admin/dashboard';
    }else {
        return '/';
    }
}
/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

/**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */

     public function login(Request $request)
    {


        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the status making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Customization: Validate if status status is active (1)
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // Customization: Validate if status status is active (1)
        $email = $request->get($this->username());
        // Customization: It's assumed that email field should be an unique field
        $user = User::where($this->username(), $email)->first();

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        if (!($user)) {
            return $this->sendFailedLoginResponse($request, 'User does not exist or Wrong Password');
        }

        // Customization: If status status is inactive (0) return failed_status error.
        if ($user->status == 'Disabled') {
            return $this->sendFailedLoginResponse($request, 'Your account is not active. Kindly contact system admin.');
        }
        if ($user->status == 'Deleted') {
            return $this->sendFailedLoginResponse($request, 'User not Found.');
        }


        return $this->sendFailedLoginResponse($request);
    }

    protected function credentials(Request $request)
    {
        if(is_numeric($request->get('email'))){
            return ['phone_no'=>$request->get('email'),'password'=>$request->get('password')];
        }
        return $request->only($this->username(), 'password');
    }

    protected function sendFailedLoginResponse(Request $request, $trans = 'auth.failed')
    {
        $errors = [$this->username() => trans($trans)];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
}