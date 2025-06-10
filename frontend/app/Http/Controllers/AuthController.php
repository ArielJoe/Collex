<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login_()
    {
        return view('auth.login');
    }

    public function register_()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $response = Http::post('http://localhost:5000/api/auth/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            Session::put('userId', $data['user']['id'] ?? null);
            Session::put('email', $data['user']['email'] ?? null);
            Session::put('full_name', $data['user']['full_name'] ?? null);
            Session::put('phone_number', $data['user']['phone_number'] ?? null);
            Session::put('role', $data['user']['role'] ?? null);

            $successMessage = $data['message'] ?? 'Login successful';
            return redirect('/')->with('success', $successMessage);
        }

        $errorMessage = $response->json('message');
        return back()->withErrors(['error' => $errorMessage])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^\d{10,12}$/',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $response = Http::post('http://localhost:5000/api/auth/register', [
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'member',
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $successMessage = $data['message'];
            return redirect('/login')->with('success', $successMessage);
        }

        $errorMessage = $response->json('message');
        return back()->withErrors(['error' => $errorMessage])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
