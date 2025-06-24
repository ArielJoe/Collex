<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QRScannerController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('QR Scan Request', $request->all());

        try {
            // Validate request
            $validated = $request->validate([
                'qr_code' => 'required|string'
            ]);

            // Your QR processing logic here
            // Example: find attendance record
            // $attendance = Attendance::where('qr_code', $validated['qr_code'])->first();

            return response()->json([
                'success' => true,
                'event_name' => 'Test Event', // Replace with actual data
                'message' => 'Attendance confirmed'
            ]);
        } catch (\Exception $e) {
            Log::error('QR Scan Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
