<?php

namespace App\Http\Controllers\Auth;

use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo='/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = [
            'name' => ['required', 'string', 'max:16', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'allowed_email_domain'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agreement' => ['required'],
        ];
        $messages = [];
        if(config('function.password.strong')) {
            $validator['password'][] = 'regex:/^(?![A-Za-z0-9]+$)(?![a-z0-9\\W]+$)(?![A-Za-z\\W]+$)(?![A-Z0-9\\W]+$)[a-zA-Z0-9\\W]{8,}$/';
            $messages['password.regex'] = __('validation.password.strong');
        }
        return Validator::make($data, $validator, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Eloquent\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => "/static/img/avatar/default.png",
            'contest_account' => null,
            'professional_rate' => 1500
        ]);
    }

    public function showRegistrationForm()
    {
        return view("auth.register", [
            'page_title'=>"Register",
            'site_title'=>config("app.name"),
            'navigation' => "Account"
        ]);
    }
}
