@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <h1 class="text-2xl font-bold mb-4">Edytuj zadanie</h1>

    <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold">Nazwa *</label>
            <input type="text" name="name" class="w-full border p-2" value="{{ old('name', $task->name) }}" required>
        </div>

        <div>
            <label class="block font-semibold">Opis</label>
            <textarea name="description" class="w-full border p-2">{{ old('description', $task->description) }}</textarea>
        </div>

        <div>
            <label class="block font-semibold">Priorytet *</label>
            <select name="priority" class="w-full border p-2">
                @foreach(['low', 'medium', 'high'] as $level)
                    <option value="{{ $level }}" @if($task->priority === $level) selected @endif>{{ ucfirst($level) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-semibold">Status *</label>
            <select name="status" class="w-full border p-2">
                @foreach(['to-do', 'in progress', 'done'] as $status)
                    <option value="{{ $status }}" @if($task->status === $status) selected @endif>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-semibold">Termin *</label>
            <input type="datetime-local" name="due_date" class="w-full border p-2" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary mt-4">Aktualizuj</button>
        </div>
        <div class="mt-2">
            <a href="{{ route('tasks.index') }}" class="btn btn-danger">← Powrót do listy</a>
        </div>
    </form>
    @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-4 mb-4 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>
@endsection
