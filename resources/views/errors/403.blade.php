<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-rose-500/10 text-rose-400 text-5xl font-black border border-rose-500/20">
            403
        </div>
        <div class="space-y-2">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Access Forbidden</h1>
            <p class="text-slate-400 text-sm">You do not have permission to access this area.</p>
        </div>
        <div class="pt-4 flex justify-center">
            <a href="{{ route('admin.login') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-sm border border-slate-700 transition-all">
                Return to Login
            </a>
        </div>
    </div>
</body>
</html>
