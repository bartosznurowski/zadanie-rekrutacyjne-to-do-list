@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <h1 class="text-2xl font-bold mb-4">Nowe zadanie</h1>

    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block font-semibold">Nazwa *</label>
            <input type="text" name="name" id="name" class="w-full border p-2" required value="{{ old('name') }}">
            @error('name') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block font-semibold">Opis</label>
            <textarea name="description" id="description" class="w-full border p-2">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block font-semibold">Priorytet *</label>
            <select name="priority" class="w-full border p-2" required>
                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Niski</option>
                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Średni</option>
                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Wysoki</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold">Status *</label>
            <select name="status" class="w-full border p-2" required>
                <option value="to-do" {{ old('status') === 'to-do' ? 'selected' : '' }}>To do</option>
                <option value="in progress" {{ old('status') === 'in progress' ? 'selected' : '' }}>W trakcie</option>
                <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Zrobione</option>
            </select>
        </div>

        <div>
            <label for="due_date" class="block font-semibold">Termin *</label>
            <input type="datetime-local" name="due_date" id="due_date" class="w-full border p-2" required value="{{ old('due_date') ? \Carbon\Carbon::parse(old('due_date'))->format('Y-m-d\TH:i') : '' }}">

        </div>

        <div>
            <button type="submit" class="btn btn-primary mt-4">Zapisz</button>
        </div>
        <div class="mt-2">
            <a href="{{ route('tasks.index') }}" class="btn btn-danger">← Powrót do listy</a>
        </div>
    </form>
</div>
@endsection
