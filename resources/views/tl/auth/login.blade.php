<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Leader Hub Login — DLK Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0d3b1a 0%, #175429 55%, #1e7c42 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .tl-card {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.28);
            overflow: hidden;
        }
        .tl-header {
            background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);
            padding: 35px 30px 25px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }
        .tl-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.18);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            text-transform: uppercase;
            color: #fef08a;
        }
        .btn-tl-submit {
            background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);
            color: #ffffff !important;
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-tl-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13,59,26,0.3);
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<div class="tl-card">
    <div class="tl-header">
        <div class="tl-badge">
            <i class="fas fa-crown"></i> Team Leader Management Hub
        </div>
        <h4 class="fw-bold mb-1">Team Leader Sign In</h4>
        <p class="small text-white-50 mb-0">Authorize to supervise workers, deadlines & project milestones</p>
    </div>

    <div class="p-4 p-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 px-3 small mb-3">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2 px-3 small mb-3">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('tl.login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Team Leader Corporate Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="tl@dlktechnologies.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                </div>
                <a href="{{ route('login') }}" class="text-success text-decoration-none fw-bold">Unified Portal</a>
            </div>

            <button type="submit" class="btn btn-tl-submit w-100 shadow-sm mb-3">
                <i class="fas fa-sign-in-alt me-1"></i> Sign In to TL Hub
            </button>

            <div class="text-center small text-muted pt-2 border-top">
                <span>Need other portals? </span>
                <a href="{{ route('sales.login') }}" class="text-primary text-decoration-none fw-semibold">Sales Login</a>
                &bull;
                <a href="{{ route('admin.login') }}" class="text-success text-decoration-none fw-semibold">Admin Login</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
