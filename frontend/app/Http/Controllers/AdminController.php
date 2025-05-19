<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:5000/api/user';

    // Dashboard with user stats
    public function dashboard()
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/stats");
            $stats = $response->json();
            
            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch dashboard data');
        }
    }

    // List users
    public function index(Request $request)
    {
        $role = $request->query('role');
        $page = $request->query('page', 1);
        
        try {
            $response = Http::get($this->apiBaseUrl, [
                'role' => $role,
                'page' => $page,
                'limit' => 10
            ]);
            
            $data = $response->json();
            
            return view('admin.user.index', [
                'users' => $data['users'],
                'totalPages' => $data['totalPages'],
                'currentPage' => $data['currentPage'],
                'roleFilter' => $role
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch users');
        }
    }

    // Show user details
    public function show($id)
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/{$id}");
            $user = $response->json();
            
            return view('admin.user.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found');
        }
    }

    // Show create user form
    public function create()
    {
        return view('admin.user.create');
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'phone_number' => 'required|regex:/^\d{10,12}$/',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required|in:member,finance,organizer'
        ]);

        try {
            $response = Http::post($this->apiBaseUrl, $request->all());
            
            if ($response->successful()) {
                return redirect()->route('admin.user.index')->with('success', 'User created successfully');
            }
            
            return back()->with('error', $response->json()['message'] ?? 'Failed to create user');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create user');
        }
    }

    // Show edit user form
    public function edit($id)
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/{$id}");
            $user = $response->json();
            
            return view('admin.user.edit', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found');
        }
    }

    // Update user
    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required',
            'phone_number' => 'required|regex:/^\d{10,12}$/',
            'email' => 'required|email',
            'role' => 'required|in:member,finance,organizer'
        ]);

        try {
            $response = Http::put("{$this->apiBaseUrl}/{$id}", $request->all());
            
            if ($response->successful()) {
                return redirect()->route('admin.user.index')->with('success', 'User updated successfully');
            }
            
            return back()->with('error', $response->json()['message'] ?? 'Failed to update user');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update user');
        }
    }

    // Delete user
    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->apiBaseUrl}/{$id}");
            
            if ($response->successful()) {
                return redirect()->route('admin.user.index')->with('success', 'User deleted successfully');
            }
            
            return back()->with('error', $response->json()['message'] ?? 'Failed to delete user');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user');
        }
    }
}
