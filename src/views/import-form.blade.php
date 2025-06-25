@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Import for: {{ ucfirst($target) }}</h3>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="color: green; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="color: red; margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="color: red; margin-bottom: 1rem;">
            <ul style="margin: 0; padding: 0; list-style: none;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload form --}}
    <form action="{{ route('import.handle', $target) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="import_file" required>
        <button type="submit">Upload & Save</button>
    </form>
</div>
@endsection
