{{-- DEV ONLY - DELETE THIS FILE BEFORE DEPLOYING --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Test Accounts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f5f5;
            color: #222;
        }
        .container {
            max-width: 980px;
            margin: 24px auto;
            padding: 0 16px 40px;
        }
        .banner {
            background: #b91c1c;
            color: #fff;
            font-weight: 700;
            padding: 16px;
            border-radius: 8px;
            font-size: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .card h2 {
            margin: 0 0 12px;
            font-size: 20px;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .field {
            margin-bottom: 12px;
        }
        .field label {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: 700;
        }
        .field input,
        .field select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn {
            border: 0;
            background: #0f766e;
            color: #fff;
            padding: 10px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
        }
        .btn:hover {
            background: #115e59;
        }
        .error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }
        .success {
            background: #dcfce7;
            border: 1px solid #16a34a;
            color: #14532d;
            border-radius: 6px;
            padding: 12px;
        }
        .note {
            background: #fff7ed;
            border: 1px solid #fb923c;
            color: #9a3412;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            background: #fff;
        }
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }
        th {
            background: #f3f4f6;
        }
        .muted {
            color: #555;
            font-size: 13px;
            margin: 0;
        }
        @media (max-width: 700px) {
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="banner">⚠️ DEV TOOL - DELETE BEFORE DEPLOYING TO CLIENT</div>

        <div class="card">
            <h2>Create Test Account</h2>

            @if ($errors->any())
                <div class="error">
                    <strong>Validation errors:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('createdAccount'))
                <div class="success">
                    <strong>Account created successfully.</strong>
                    <div>Name: {{ session('createdAccount.name') }}</div>
                    <div>Email: {{ session('createdAccount.email') }}</div>
                    <div>Role: {{ session('createdAccount.role') }}</div>
                    <div>Password: {{ session('createdAccount.password') }}</div>
                </div>
            @endif

            @if (session('devNote'))
                <div class="note">{{ session('devNote') }}</div>
            @endif

            <form method="POST" action="{{ route('dev.test-accounts.store') }}" style="margin-top:12px;">
                @csrf

                <div class="row">
                    <div class="field">
                        <label for="name">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                    </div>

                    <div class="field">
                        <label for="email">Email Address</label>
                        <input id="email" name="email" type="text" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="text" value="{{ old('password') }}" required>
                    </div>

                    <div class="field">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
                            <option value="concessionaire" {{ old('role') === 'concessionaire' ? 'selected' : '' }}>concessionaire</option>
                            <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>cashier</option>
                        </select>
                    </div>
                </div>

                <button class="btn" type="submit">Create Test Account</button>
            </form>
        </div>

        <div class="card">
            <h2>Existing Users</h2>
            <p class="muted">Showing all users for reference.</p>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ optional($user->created_at)->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
