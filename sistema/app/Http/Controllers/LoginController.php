<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Valida las credenciales e inicia la sesión del usuario.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.index'));
        }

        return back()
            ->withErrors(['email' => 'El correo o la contraseña no son correctos.'])
            ->onlyInput('email');
    }

    /**
     * Cierra la sesión e invalida sus datos.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('ok', 'Cerraste sesión correctamente.');
    }
}
