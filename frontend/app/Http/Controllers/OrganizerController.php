<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrganizerController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:5000/api/event';

    public function index()
    {
        return view('organizer.index');
    }

    public function events(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $response = Http::get($this->apiBaseUrl, [
            'page' => $page,
            'limit' => $limit,
            'organizer_id' => session()->get('userId')
        ]);

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Failed to fetch events']);
        }

        $data = $response->json();

        return view('organizer.events.index', [
            'events' => $data['events'],
            'totalPages' => $data['totalPages'],
            'currentPage' => $data['currentPage'],
        ]);
    }

    public function createEvent()
    {
        return view('organizer.events.create');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
            'location' => 'required|string',
            'speaker' => 'nullable|string|max:255',
            'registration_fee' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
        }

        $postData = array_merge($validated, [
            'organizer_id' => session()->get('userId'),
            'poster_url' => $posterPath ? $posterPath : null,
        ]);

        $response = Http::post($this->apiBaseUrl, $postData);

        if ($response->successful()) {
            return redirect()->route('organizer.events.index')->with('success', 'Event created successfully.');
        }

        return back()->withErrors(['error' => 'Failed to create event'])->withInput();
    }

    public function showEvent($id)
    {
        $response = Http::get("{$this->apiBaseUrl}/{$id}");

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Event not found']);
        }

        $event = $response->json();

        return view('organizer.events.show', compact('event'));
    }

    public function editEvent($id)
    {
        $response = Http::get("{$this->apiBaseUrl}/{$id}");

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Event not found']);
        }

        $event = $response->json();

        return view('organizer.events.edit', compact('event'));
    }

    public function updateEvent(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'start_time' => 'sometimes|required|date_format:Y-m-d\TH:i',
            'end_time' => 'sometimes|required|date_format:Y-m-d\TH:i|after:start_time',
            'location' => 'sometimes|required|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $validated['poster_url'] = $posterPath;
        }

        $response = Http::put("{$this->apiBaseUrl}/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('organizer.events.index')->with('success', 'Event updated successfully.');
        }

        return back()->withErrors(['error' => 'Failed to update event'])->withInput();
    }

    public function destroyEvent($id)
    {
        $response = Http::delete("{$this->apiBaseUrl}/{$id}");

        if ($response->successful()) {
            return redirect()->route('organizer.events.index')->with('success', 'Event deleted successfully.');
        }

        return back()->withErrors(['error' => 'Failed to delete event']);
    }
}
