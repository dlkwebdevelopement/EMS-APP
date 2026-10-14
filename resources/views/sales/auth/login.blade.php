<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Team Login - DLK Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0d3b1a 0%, #145a2c 50%, #1e7c42 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .sales-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .sales-header {
            background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);
            padding: 35px 30px 25px;
            text-align: center;
            color: #ffffff;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.18);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .btn-sales-submit {
            background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);
            color: #ffffff !important;
            font-weight: 600;
            padding: 12px;
            border-radius: 12px;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-sales-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13,59,26,0.3);
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <div class="sales-card">
        <div class="sales-header">
            <div class="brand-badge">
                <i class="fas fa-briefcase"></i> Sales & Lead Portal
            </div>
            <h3 class="fw-bold mb-1">Sales Team Login</h3>
            <p class="text-white-50 small mb-0">DLK Technologies Employee Management</p>
        </div>
        <div class="p-4 p-md-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('sales.login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Sales Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="sales@dlktech.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                        <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                    </div>
                    <span class="small text-muted"><i class="fas fa-user-shield me-1 text-success"></i> Accounts issued by Admin</span>
                </div>

                <button type="submit" class="btn btn-sales-submit w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i> Access Sales Dashboard
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('admin.login') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-shield-alt me-1"></i> Switch to Admin Login
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
