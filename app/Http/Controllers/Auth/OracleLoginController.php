<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\OracleLoginRequest;
use App\Models\User;
use App\Services\OracleAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OracleLoginController extends Controller
{
    protected OracleAuthService $oracleAuthService;

    public function __construct(OracleAuthService $oracleAuthService)
    {
        $this->oracleAuthService = $oracleAuthService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.oracle-login');
    }

    /**
     * Handle an incoming authentication request using Oracle.
     */
    public function store(OracleLoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Hashear la contraseña con MD5 como en el script original
        $hashedPassword = md5($validated['password']);

        // Intentar autenticar contra Oracle
        $userData = $this->oracleAuthService->authenticateUser(
            $validated['estamento'],
            $validated['provincia'],
            $validated['clase'],
            $validated['tomo'],
            $validated['folio'],
            $hashedPassword
        );

        if (!$userData) {
            return back()->withErrors([
                'credentials' => 'Credenciales incorrectas o usuario no encontrado.'
            ])->withInput($request->except('password'));
        }

        // Generar identificador único para el usuario
        $userIdentifier = $this->oracleAuthService->generateUserIdentifier(
            $validated['provincia'],
            $validated['clase'],
            $validated['tomo'],
            $validated['folio']
        );

        // Buscar o crear usuario en la base de datos local
        $user = User::firstOrCreate(
            ['oracle_id' => $userIdentifier],
            [
                'name' => $userData['NOMBRES'] . ' ' . $userData['APELLIDOS'],
                'email' => $userIdentifier . '@up.edu.pa', // Email temporal
                'password' => Hash::make($validated['password']), // Guardar contraseña local
                'oracle_estamento' => $validated['estamento'],
                'oracle_data' => $userData, // Guardar datos completos de Oracle
            ]
        );

        // Actualizar datos del usuario si han cambiado
        $user->update([
            'name' => $userData['NOMBRES'] . ' ' . $userData['APELLIDOS'],
            'oracle_data' => $userData,
        ]);

        // Loguear al usuario
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        Log::info('Usuario autenticado exitosamente via Oracle', [
            'user_id' => $user->id,
            'oracle_id' => $userIdentifier,
            'estamento' => $validated['estamento']
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
