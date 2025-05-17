<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $response = Http::post('http://localhost:5000/api/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            Session::put('email', $data['user']['email'] ?? null);
            Session::put('role', $data['user']['role'] ?? null);

            $successMessage = $data['message'] ?? 'Login successful';
            return back()->with('success', $successMessage);
        }

        $errorMessage = $response->json('message');
        return back()->withErrors(['error' => $errorMessage])->withInput();
    }
}
