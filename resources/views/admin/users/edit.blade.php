<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - AquWatch Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <main class="max-w-xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow border border-slate-200 p-6">
            <h1 class="text-2xl font-bold text-slate-900 mb-5">Edit User</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold mb-1">Name</label>
                    <input name="name" value="{{ old('name', $managedUser->name) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $managedUser->email) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Role</label>
                    <select name="role" required class="w-full rounded-xl border border-slate-300 px-3 py-2">
                        <option value="user" {{ old('role', $managedUser->role ?: 'user') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $managedUser->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-slate-600">Back</a>
                    <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
