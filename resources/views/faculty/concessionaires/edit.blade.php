@extends('faculty.layout')

@section('title', 'Edit Concessionaire')

@section('content')
    <h1 class="page-title">Edit Concessionaire</h1>
    <p class="page-subtitle">Update approved concessionaire business details.</p>

    <div style="margin-bottom:14px;">
        <a href="{{ route('staff.concessionaires.index') }}" class="btn btn-outline">Back to Concessionaires</a>
    </div>

    <div class="card" style="max-width:760px;">
        <div class="card-header">
            <strong>{{ $concessionaire->name }}</strong>
            <div style="font-size:13px;color:#64748b;">{{ $concessionaire->email }}</div>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="{{ route('staff.concessionaires.update', $concessionaire->id) }}">
                @csrf
                @method('PATCH')

                <div style="display:grid;gap:8px;margin-bottom:12px;">
                    <label style="font-size:13px;font-weight:700;color:#334155;">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $concessionaire->business_name) }}" maxlength="255" required style="width:100%;padding:10px 12px;border:1px solid var(--line-strong);border-radius:6px;">
                </div>

                <div style="display:grid;gap:8px;margin-bottom:12px;">
                    <label style="font-size:13px;font-weight:700;color:#334155;">Description</label>
                    <textarea name="description" maxlength="2000" style="width:100%;padding:10px 12px;border:1px solid var(--line-strong);border-radius:6px;min-height:110px;">{{ old('description', $concessionaire->description) }}</textarea>
                </div>

                <div style="display:grid;gap:8px;margin-bottom:16px;">
                    <label style="font-size:13px;font-weight:700;color:#334155;">Location</label>
                    <input type="text" name="location" value="{{ old('location', $concessionaire->location) }}" maxlength="255" style="width:100%;padding:10px 12px;border:1px solid var(--line-strong);border-radius:6px;">
                </div>

                <button type="submit" class="btn btn-green">Save Changes</button>
            </form>
        </div>
    </div>
@endsection
