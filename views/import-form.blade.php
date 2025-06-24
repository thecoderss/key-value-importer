@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Import for: {{ ucfirst($target) }}</h3>

    <form action="{{ route('import.handle', $target) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="import_file" required>
        <button type="submit">Upload & Save</button>
    </form>
</div>
@endsection
