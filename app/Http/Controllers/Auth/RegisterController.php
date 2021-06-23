<?php

declare(strict_types=1);
namespace App\Http\Controllers\Auth;

use App\Events\Registered;
use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

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

    protected $errors = [];

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

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
        return Validator::make($data, [
            'first_name'     => 'string|max:255',
            'last_name'      => 'string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:6|confirmed',
            'team_name'      => 'required_if:team_type,create',
            'invite_code'    => 'required_if:team_type,join'
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        if (!$user) {
            return redirect()->back()->withErrors($this->errors);
        }
        event(new Registered($user));

        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name'     => $data['first_name'] ?? '',
                'last_name'      => $data['last_name'] ?? '',
                'email'          => $data['email'],
                'password'       => $data['password'],
            ]);

            if (request()->team_type === 'create') {
                $user->ownedTeams()->create(['name' => request()->team_name]);
            }

            if (request()->team_type === 'join' && !empty(request()->invite_code)) {
                $user->claim(request()->invite_code);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $this->errors[] = ['error' => $e->getMessage()];

            return false;
        }
        DB::commit();

        return $user->refresh();
    }

    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }
}
