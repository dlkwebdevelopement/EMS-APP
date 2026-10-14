<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - DLK Technologies Portal Gateway</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gd: #0d3b1a;
            --gk: #124e24;
            --gm: #1e7c42;
            --gl: #7ed53a;
            --gb: #5ab020;
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
            background: linear-gradient(135deg, #092611 0%, #0d3b1a 50%, #175e32 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle ambient glow circles */
        .ambient-glow-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(126, 213, 58, 0.15) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            pointer-events: none;
        }
        .ambient-glow-2 {
            position: absolute;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30, 124, 66, 0.25) 0%, transparent 70%);
            bottom: -150px;
            right: -150px;
            pointer-events: none;
        }

        .gateway-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            max-width: 980px;
            width: 100%;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        .gateway-banner {
            background: linear-gradient(135deg, var(--gd), var(--gm));
            color: #ffffff;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .gateway-banner::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(126, 213, 58, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .portal-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--gl);
            margin-bottom: 1.5rem;
        }

        .role-switcher-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 1.75rem;
        }

        .role-switch-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--txt-m);
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.2s ease;
            background: #ffffff;
            border: 1px solid #edf2f7;
        }

        .role-switch-btn:hover {
            color: var(--gm);
            border-color: var(--gm);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .role-switch-btn i {
            font-size: 1.1rem;
            margin-bottom: 4px;
            color: var(--gm);
        }

        .btn-submit {
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

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--gk), var(--gd));
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(30, 124, 66, 0.4);
            color: #ffffff !important;
        }

        .form-control:focus {
            border-color: var(--gm);
            box-shadow: 0 0 0 3px rgba(30, 124, 66, 0.15);
        }

        @media (max-width: 768px) {
            .gateway-banner {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="gateway-card">
        <div class="row g-0">

            {{-- Left Banner Column --}}
            <div class="col-lg-5 gateway-banner">
                <div>
                    <div class="portal-pill">
                        <i class="fas fa-shield-alt"></i> Unified Enterprise Gateway
                    </div>
                    <h2 class="brand-title mb-2">DLK TECHNOLOGIES</h2>
                    <p class="text-white-50 small mb-4" style="line-height: 1.6;">
                        Centralized Employee, Team Leader & Sales Management Suite. Access your authorized workspace seamlessly.
                    </p>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3 text-white-50 small">
                            <span class="p-2 rounded-circle bg-white bg-opacity-10 text-white">
                                <i class="fas fa-user-shield"></i>
                            </span>
                            <div>
                                <strong class="text-white d-block">Admin & Operations</strong>
                                <span>Complete workforce control & reports</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-white-50 small">
                            <span class="p-2 rounded-circle bg-white bg-opacity-10 text-white">
                                <i class="fas fa-hand-holding-usd"></i>
                            </span>
                            <div>
                                <strong class="text-white d-block">Sales CRM & Pipeline</strong>
                                <span>Lead capture, TL assignment & deals</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-white-50 small">
                            <span class="p-2 rounded-circle bg-white bg-opacity-10 text-white">
                                <i class="fas fa-users-cog"></i>
                            </span>
                            <div>
                                <strong class="text-white d-block">TL & Worker Execution</strong>
                                <span>Milestones, deadlines & deliverable review</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-top border-white border-opacity-10 small text-white-50">
                    &copy; {{ date('Y') }} DLK Technologies. All rights reserved.
                </div>
            </div>

            {{-- Right Form Column --}}
            <div class="col-lg-7 p-4 p-md-5 bg-white">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Sign In to Your Workspace</h3>
                    <p class="text-muted small">Enter your credentials below to automatically access your dashboard.</p>
                </div>

                {{-- Direct Portal Switcher Cards --}}
                <div class="role-switcher-card">
                    <a href="{{ route('admin.login') }}" class="role-switch-btn">
                        <i class="fas fa-shield-alt text-success"></i>
                        <span>Admin Login</span>
                    </a>
                    <a href="{{ route('sales.login') }}" class="role-switch-btn">
                        <i class="fas fa-briefcase text-primary"></i>
                        <span>Sales Portal</span>
                    </a>
                    <a href="{{ route('employee.login') }}" class="role-switch-btn">
                        <i class="fas fa-user text-warning"></i>
                        <span>Employee Hub</span>
                    </a>
                </div>

                {{-- Status Alerts --}}
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

                {{-- Unified Login Form --}}
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control border-start-0" 
                                   placeholder="name@dlktech.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-dark mb-0">Password</label>
                        </div>
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
                            <label class="form-check-label small text-muted" for="rememberMe">Remember my session</label>
                        </div>
                        <span class="small text-muted"><i class="fas fa-lock me-1 text-success"></i> Secure SSO</span>
                    </div>

                    <button type="submit" class="btn btn-submit w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Authenticate & Continue
                    </button>

                    <div class="text-center pt-3 border-top">
                        <span class="text-muted small">
                            <i class="fas fa-info-circle me-1 text-success"></i>
                            Sales & Employee accounts are managed by the System Administrator.
                        </span>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
