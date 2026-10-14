<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Team Registration - DLK Technologies</title>
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
            padding: 25px;
        }
        .sales-card {
            width: 100%;
            max-width: 480px;
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
                <i class="fas fa-user-plus"></i> Join Sales Force
            </div>
            <h3 class="fw-bold mb-1">Sales Team Register</h3>
            <p class="text-white-50 small mb-0">Create your account to manage leads & assign to TLs</p>
        </div>
        <div class="p-4 p-md-5">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('sales.register.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="name" class="form-control border-start-0" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Official Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="john.sales@dlktech.com" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Mobile Phone <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                        <input type="text" name="phone" class="form-control border-start-0" placeholder="+91 9876543210" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Min 6 chars" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                            <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Repeat password" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-sales-submit w-100 mb-3">
                    <i class="fas fa-check-circle me-2"></i> Register Sales Account
                </button>

                <div class="text-center mt-3">
                    <span class="text-muted small">Already have an account? </span>
                    <a href="{{ route('sales.login') }}" class="text-success fw-bold small text-decoration-none">
                        Login Here
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
