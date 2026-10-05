<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Wedding Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm mx-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800" style="font-family: 'Playfair Display', serif;">Wedding Admin</h1>
            <p class="text-gray-500 mt-2">Sign in to manage your wedding</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-green-700 focus:ring-2 focus:ring-green-700/20 outline-none transition">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-green-700 focus:ring-2 focus:ring-green-700/20 outline-none transition">
            </div>
            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 rounded-full text-sm uppercase tracking-wider transition-all">
                Sign In
            </button>
        </form>

        <p class="text-center text-gray-400 text-xs mt-6">Wedding Hub Admin</p>
    </div>
</body>
</html>
