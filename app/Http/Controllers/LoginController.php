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
                'message' => 'Invalid email or password.'
            ];
        }

        // check wether the password is correct, and return false if incorrect
        if (!\Hash::check($password, $account->getAttribute('password'))) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }

        // login the user after all checks have passed
        \Auth::login($account);

        return [
            'success' => true,
            'message' => ''
        ];
    }

    // public function redirectToCorrectDashboard(): \Illuminate\Http\RedirectResponse
    public function redirectToCorrectDashboard()
    {
        // check if the user is logged in, else redirect to index
        if (!\Auth::check()) {
            return \Redirect(route('login'));
        }

        $account = \Auth::user();

        // check if the user is an admin
        $roles = $account->getRelationValue('roles');
        if (in_array('Admin', $roles->pluck('name')->toArray())) {
            return \Redirect(route('admin'));
        }

        return \Redirect(route('crew'));
    }
}
