<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Google\Client;                
use Google\Service\Calendar;   
use Carbon\Carbon; 
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class TaskController extends Controller
{

    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id());

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        $tasks = $query->latest()->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to-do,in progress,done',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $validated['user_id'] = Auth::id();

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Zadanie utworzone.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorizeTask($task);

        $task->load('histories.user');

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorizeTask($task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to-do,in progress,done',
            'due_date' => 'required|date|after_or_equal:' . now()->startOfDay()->format('Y-m-d'),
        ]);
        
        $fieldsToTrack = ['name', 'description', 'priority', 'status'];

        foreach ($fieldsToTrack as $field) {
            if ($task->$field != $validated[$field]) {
                \App\Models\TaskHistory::create([
                    'task_id' => $task->id,
                    'user_id' => \Auth::id(),
                    'field' => $field,
                    'old_value' => $task->$field,
                    'new_value' => $validated[$field],
                ]);
            }
        }

        $newDueDate = \Carbon\Carbon::parse($validated['due_date'])->format('Y-m-d H:i:s');

        if ($task->due_date->format('Y-m-d H:i:s') !== $newDueDate) {
            \App\Models\TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => \Auth::id(),
                'field' => 'due_date',
                'old_value' => $task->due_date->format('Y-m-d H:i:s'),
                'new_value' => $newDueDate,
            ]);
        }
        
        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Zadanie zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Zadanie usunięte.');
    }

    private function authorizeTask(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
    }

    public function generatePublicLink(Task $task)
    {
        $this->authorizeTask($task);

        $token = $task->generatePublicToken(24);

        $url = route('tasks.public.show', ['token' => $token]);

        return response()->json(['public_link' => $url]);
    }

    public function showPublic($token)
    {
        $task = Task::where('public_token', $token)->firstOrFail();

        if (!$task->isPublicTokenValid()) {
            abort(403, 'Link wygasł lub jest nieprawidłowy.');
        }

        return view('tasks.show_public', compact('task'));
    }

    public function addToGoogleCalendar(Task $task)
    {
        $user = auth()->user();

        if (!$user->google_token) {
            return redirect()->route('google.redirect')->with('error', 'Połącz konto Google, aby dodać zadanie do kalendarza.');
        }

        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessToken([
            'access_token' => $user->google_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in' => $user->google_token_expires_at ? $user->google_token_expires_at->diffInSeconds(now()) : 3600,
            'created' => now()->subSeconds($user->google_token_expires_at ? $user->google_token_expires_at->diffInSeconds(now()) : 3600)->timestamp,
        ]);

        if ($client->isAccessTokenExpired()) {
            $refreshToken = $user->google_refresh_token;
            $client->fetchAccessTokenWithRefreshToken($refreshToken);
            $newToken = $client->getAccessToken();

            $user->update([
                'google_token' => $newToken['access_token'],
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
            ]);
        }

        $service = new Calendar($client);

        $startDate = Carbon::parse($task->due_date)->toRfc3339String();
        $endDate = Carbon::parse($task->due_date)->addHour()->toRfc3339String();

        $event = new Event([
            'summary' => $task->name,
            'description' => $task->description ?? '',
            'start' => new EventDateTime(['dateTime' => $startDate, 'timeZone' => config('app.timezone')]),
            'end' => new EventDateTime(['dateTime' => $endDate, 'timeZone' => config('app.timezone')]),
        ]);

        $calendarId = 'primary';
        $service->events->insert($calendarId, $event);

        return redirect()->route('tasks.show', $task)->with('success', 'Zadanie dodane do Twojego Google Calendar!');
    }


}
