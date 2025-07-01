@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-4">Szczegóły zadania</h1>

    <div class="mb-4">
        <strong>Nazwa:</strong> {{ $task->name }}
    </div>
    <div class="mb-4">
        <strong>Opis:</strong> {{ $task->description ?? 'Brak opisu' }}
    </div>
    <div class="mb-4">
        <strong>Priorytet:</strong> {{ ucfirst($task->priority) }}
    </div>
    <div class="mb-4">
        <strong>Status:</strong> {{ ucfirst($task->status) }}
    </div>
    <div class="mb-4">
        <strong>Termin:</strong> {{ $task->due_date }}
    </div>

    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary">Edytuj</a>
    <a href="{{ route('tasks.index') }}" class="btn btn-danger">← Powrót do listy</a>

    <hr class="mt-4">

    <h2 class="text-xl font-semibold mb-4 mt-4">Historia zmian</h2>

    @if($task->histories->isEmpty())
        <p class="text-gray-600">Brak historii zmian dla tego zadania.</p>
    @else
        <ul class="space-y-4">
            @foreach($task->histories as $history)
                <li class="border p-4 rounded shadow mt-4">
                    <div class="text-sm text-gray-600 mb-1">
                        <strong>{{ $history->user->name ?? 'Nieznany użytkownik' }}</strong>
                        zmodyfikował <strong>{{ $history->field }}</strong> {{ $history->old_value }} -> <strong>{{ $history->new_value }}</strong> {{ $history->created_at->diffForHumans() }}
                    </div>
                    <pre class="bg-gray-100 p-2 rounded text-sm whitespace-pre-wrap">{{ $history->changes }}</pre>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
