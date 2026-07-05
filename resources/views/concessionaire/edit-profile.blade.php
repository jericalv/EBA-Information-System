@extends('concessionaire.layout')

@section('title', 'Edit Profile')

@section('extra-css')
<style>
    .edit-container {
        max-width: 680px;
        margin: 0 auto;
    }
    .edit-card {
        padding: 26px 28px;
    }
    .edit-card h2 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
        margin-bottom: 20px;
    }
    .edit-card .alert {
        margin-bottom: 18px;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
        margin-bottom: 7px;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
    }
    .form-group textarea {
        min-height: 120px;
        resize: vertical;
        line-height: 1.5;
    }
    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }
    .error-msg {
        color: var(--danger);
        font-size: 12.5px;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
    <div class="edit-container">
        <div class="panel edit-card">
            <h2>Edit Profile</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('concessionaire.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="business_name">Public Business Name</label>
                    <input type="text" id="business_name" name="business_name" class="control" value="{{ old('business_name', $user->business_name) }}" maxlength="255" placeholder="e.g., Campus Canteen">
                    @error('business_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Business Name</label>
                    <input type="text" id="name" name="name" class="control" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="control" value="{{ old('location', $user->location) }}" placeholder="e.g., Near the main gate">
                    @error('location')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">About / Description</label>
                    <textarea id="description" name="description" class="control" placeholder="Tell students about your business...">{{ old('description', $user->description) }}</textarea>
                    @error('description')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('concessionaire.dashboard') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
