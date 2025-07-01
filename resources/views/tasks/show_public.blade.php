@extends('welcome')

@section('content')
<div class="text-center">
    <h1 class="text-2xl font-bold mb-4">Zadanie: {{ $task->name }}</h1>

    <p><strong>Opis:</strong> {{ $task->description ?? 'Brak opisu' }}</p>
    <p><strong>Priorytet:</strong> {{ ucfirst($task->priority) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
    <p><strong>Termin wykonania:</strong> {{ $task->due_date }}</p>

    @if($task->public_token_expires_at)
        <p class="mt-4 text-sm text-gray-600">
            Link ważny do: {{ $task->public_token_expires_at->format('Y-m-d H:i') }}
        </p>
    @endif
</div>
@endsection