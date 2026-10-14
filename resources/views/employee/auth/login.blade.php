<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee & Team Leader Login - DLK Technologies</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gd: #0d3b1a;
            --gk: #124e24;
            --gm: #1e7c42;
            --gl: #7ed53a;
            --gs: #f0fdf4;
            --txt-d: #0f172a;
            --txt-m: #475569;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 50%, #124e24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(126, 213, 58, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .glow-1 { top: -150px; left: -150px; }
        .glow-2 { bottom: -150px; right: -150px; }

        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--gd), var(--gm));
            color: #ffffff;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 4px 14px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--gl);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .btn-employee-submit {
            background: linear-gradient(135deg, var(--gm), var(--gk));
            border: none;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.85rem 1.5rem;
            border-radius: 10px;
            letter-spacing: 0.2px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(30, 124, 66, 0.3);
        }

        .btn-employee-submit:hover {
            background: linear-gradient(135deg, var(--gk), var(--gd));
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(30, 124, 66, 0.4);
            color: #ffffff !important;
        }

        .form-control:focus {
            border-color: var(--gm);
            box-shadow: 0 0 0 3px rgba(30, 124, 66, 0.15);
        }
    </style>
</head>
<body>

    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-badge">
                <i class="fas fa-users"></i> Employee & TL Workspace
            </div>
            <h3 class="fw-bold mb-1">Staff Member Login</h3>
            <p class="text-white-50 small mb-0">DLK Technologies Employee Management Suite</p>
        </div>

        <div class="p-4 p-md-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('employee.login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Official Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control border-start-0" 
                               placeholder="employee@dlktech.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Account Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control border-start-0" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                        <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                    </div>
                    <span class="small text-muted"><i class="fas fa-shield-alt me-1 text-success"></i> HR Portal</span>
                </div>

                <button type="submit" class="btn btn-employee-submit w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i> Access Employee Dashboard
                </button>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <a href="{{ route('admin.login') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-user-shield me-1 text-success"></i> Admin Login
                    </a>
                    <a href="{{ route('sales.login') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-briefcase me-1 text-primary"></i> Sales Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
