<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Http\Requests\LoginAuthenticateRequest;

class LoginController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Login');
    }

    public function authenticate(LoginAuthenticateRequest $request): array
    {
        // always logout
        \Auth::logout();

        $email = $request->input('email');
        $password = $request->input('password');

        $account = User::query()
        ->where('email', $email)
        ->first();

        if (is_null($account)) {
            return [
                'success' => false,
                'message' => 'Incorrecte inloggegevens'
            ];
        }

        // check wether the password is correct, and return false if incorrect
        if (!\Hash::check($password, $account->getAttribute('password'))) {
            return [
                'success' => false,
                'message' => 'Incorrecte inloggegevens'
            ];
        }

        // login the user after all checks have passed
        \Auth::login($account);

        return [
            'success' => true,
            'message' => ''
        ];
    }
}
