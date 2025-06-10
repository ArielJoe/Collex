<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index()
    {
        $response = Http::get('http://127.0.0.1:5000/api/payment/all', [
            'page' => 1,
            'limit' => 10,
        ]);

        if ($response->failed()) {
            return view('finance.index')->with('error', 'Failed to fetch payments.');
        }

        $payments = $response->json()['data'] ?? [];
        $totalPages = $response->json()['totalPages'] ?? 1;
        $currentPage = $response->json()['currentPage'] ?? 1;

        return view('finance.index', compact('payments', 'totalPages', 'currentPage'));
    }

    public function approvePayment(Request $request, $id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->patch("http://127.0.0.1:5000/api/payment/update-payment-status/{$id}", [
            'status' => 'confirmed',
            'confirmed_by_user_id' => session()->get('userId'),
        ]);

        if ($response->successful()) {
            return redirect()->route('finance.index')->with('success', 'Payment approved successfully.');
        }

        return redirect()->route('finance.index')->with('error', 'Failed to approve payment: ' . ($response->json()['message'] ?? 'Unknown error'));
    }

    public function rejectPayment(Request $request, $id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->patch("http://127.0.0.1:5000/api/payment/update-payment-status/{$id}", [
            'status' => 'rejected',
            'confirmed_by_user_id' => session()->get('userId'),
        ]);

        if ($response->successful()) {
            return redirect()->route('finance.index')->with('success', 'Payment rejected successfully.');
        }

        return redirect()->route('finance.index')->with('error', 'Failed to reject payment: ' . ($response->json()['message'] ?? 'Unknown error'));
    }
}
