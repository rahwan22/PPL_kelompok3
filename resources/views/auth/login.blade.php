<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login Sistem Sekolah' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ====== RESET & BODY ====== */
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #005fbd);
            color: #333;
            overflow: hidden;
        }

        /* ====== CARD ====== */
        .card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 360px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .card h4 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: #005fbd;
        }

        /* ====== BUTTON LOGIN ====== */
        .btn-login {
            background: linear-gradient(90deg, #4361ee);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #4cc9f0, #7209b7);
            transform: translateY(-2px);
        }

        /* ====== FORM INPUT ====== */
        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 5px rgba(67, 97, 238, 0.3);
        }

        /* ====== ALERT STYLE ====== */
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* ====== ANIMASI ====== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 480px) {
            .card {
                width: 90%;
                padding: 1.5rem;
            }
        }
    </style>

</head>

<body>
    <div class="card">
        <h4 class="text-center mb-4 fw-bold text-success">Login Sistem Sekolah</h4>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-login w-100">Login</button>
        </form>

    </div>
</body>

</html>
