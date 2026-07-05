@extends('concessionaire.layout')

@section('title', 'Edit Profile')

@section('extra-css')
<style>
    .edit-container {
        max-width: 700px;
        margin: 32px auto;
        padding: 0 24px;
    }
    .edit-card {
        background: #fff;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .edit-card h2 {
        font-size: 24px;
        font-weight: 800;
        color: var(--green);
        margin-bottom: 24px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 8px;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 15px;
        font-family: inherit;
        background: #f8fafc;
        transition: all 0.2s;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--green);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(10,92,47,0.1);
    }
    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }
    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }
    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }
    .btn-primary {
        background: var(--green);
        color: #fff;
    }
    .btn-primary:hover {
        background: var(--green-light);
    }
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .btn-secondary:hover {
        background: #e2e8f0;
    }
    .error-msg {
        color: #dc2626;
        font-size: 13px;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
    <div class="edit-container">
        <div class="edit-card">
            <h2>Edit Profile</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('concessionaire.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="business_name">Public Business Name</label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $user->business_name) }}" maxlength="255" placeholder="e.g., Campus Canteen">
                    @error('business_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Business Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $user->location) }}" placeholder="e.g., Near the main gate">
                    @error('location')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">About / Description</label>
                    <textarea id="description" name="description" placeholder="Tell students about your business...">{{ old('description', $user->description) }}</textarea>
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
