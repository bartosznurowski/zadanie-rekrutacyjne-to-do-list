@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <h1 class="text-2xl font-bold mb-4">Twoje zadania</h1>

    <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-4">+ Nowe zadanie</a>

    <form method="GET" action="{{ route('tasks.index') }}" class="mb-4 flex flex-wrap items-end space-x-4">
        <div class="flex flex-col w-40">
            <label class="mb-1 font-medium text-gray-700">Priorytet:</label>
            <select name="priority" class="border rounded px-2 py-1">
                <option value="">-- wszystkie --</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
            </select>
        </div>

        <div class="flex flex-col w-40">
            <label class="mb-1 font-medium text-gray-700">Status:</label>
            <select name="status" class="border rounded px-2 py-1">
                <option value="">-- wszystkie --</option>
                <option value="to-do" {{ request('status') == 'to-do' ? 'selected' : '' }}>To-Do</option>
                <option value="in progress" {{ request('status') == 'in progress' ? 'selected' : '' }}>In Progress</option>
                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
            </select>
        </div>

        <div class="flex flex-col w-40">
            <label class="mb-1 font-medium text-gray-700">Termin od:</label>
            <input type="date" name="due_date_from" value="{{ request('due_date_from') }}" class="border rounded px-2 py-1">
        </div>

        <div class="flex flex-col w-40">
            <label class="mb-1 font-medium text-gray-700">Termin do:</label>
            <input type="date" name="due_date_to" value="{{ request('due_date_to') }}" class="border rounded px-2 py-1">
        </div>

        <div class="flex flex-row justify-end space-x-2">
            <button type="submit" class="btn btn-primary">Filtruj</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-danger">Wyczyść</a>
        </div>
    </form>


    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($tasks->isEmpty())
        <p>Brak zadań do wykonania. Utwórz nowe.</p>
    @else
        <table class="w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2">Nazwa</th>
                    <th class="p-2">Priorytet</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Termin</th>
                    <th class="p-2">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr class="border-t">
                    <td class="p-2">{{ $task->name }}</td>
                    <td class="p-2">{{ ucfirst($task->priority) }}</td>
                    <td class="p-2">{{ ucfirst($task->status) }}</td>
                    <td class="p-2">{{ $task->due_date }}</td>
                    <td class="p-2">
                    <div class="row">
                        <div class="col-4 flex gap-2">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-primary">Pokaż</a>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary">Edytuj</a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Na pewno usunąć?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Usuń</button>
                            </form>
                        </div>

                        
                            <div class="col-4">
                                <button class="btn btn-link generate-link-btn" data-task-id="{{ $task->id }}">Udostępnij link do zadania</button>
                            </div>
                            <div class="col-4">
                                @if(auth()->user()->google_token)
                                    <form action="{{ route('tasks.addToGoogleCalendar', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Dodaj do Google Calendar</button>
                                    </form>
                                @else
                                    <a href="{{ route('google.redirect') }}" class="btn btn-secondary">Połącz z Google Calendar</a>
                                @endif
                            </div>
                            <p class="mt-1 text-green-600 public-link" id="public-link-{{ $task->id }}" style="display:none;"></p>
                        
                    </div> 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
document.querySelectorAll('.generate-link-btn').forEach(button => {
    button.addEventListener('click', function () {
        const taskId = this.dataset.taskId;
        const tokenElem = document.getElementById('public-link-' + taskId);

        fetch(`/tasks/${taskId}/generate-public-link`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            tokenElem.style.display = 'block';
            tokenElem.textContent = `Udostępniony link: ${data.public_link}`;
        })
        .catch(() => {
            tokenElem.style.display = 'block';
            tokenElem.textContent = 'Błąd podczas generowania linku.';
        });
    });
});
</script>
@endsection
