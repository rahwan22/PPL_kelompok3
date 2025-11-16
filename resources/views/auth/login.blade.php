<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login Sistem Sekolah' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
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

        /* ====== FORM INPUT UTAMA ====== */
        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            /* Tambahkan padding-left untuk memberi ruang pada ikon */
            padding: 10px 10px 10px 40px; 
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 5px rgba(67, 97, 238, 0.3);
        }

        /* ====== ICON CSS ====== */
        
        /* Wrapper untuk menahan input dan icon */
        .input-icon-group {
            position: relative;
            margin-bottom: 1rem; /* Gantikan mb-3 Bootstrap */
        }
        
        /* Style untuk Icon */
        .input-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888; /* Warna abu-abu default */
            font-size: 1rem;
            z-index: 10;
            transition: color 0.3s;
        }
        
        /* Ubah warna icon ketika input fokus */
        .form-control:focus + .input-icon {
            color: #4361ee;
        }
        
        /* ====== ALERT & ANIMASI (tetap sama) ====== */
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

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
        <h4 class="text-center mb-4 fw-bold text-success"><i class="fas fa-school me-2"></i>Login Sistem Sekolah</h4>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            
            <label class="form-label">Email / NIP</label>
            <div class="input-icon-group">
                <input type="text" name="email" class="form-control" required value=" " id="email-nip">
                <i class="fas fa-user input-icon" for="email-nip"></i>
            </div>
            
            <label class="form-label mt-2">Password</label>
            <div class="input-icon-group">
                <input type="password" name="password" class="form-control" required id="password">
                <i class="fas fa-lock input-icon" for="password"></i>
            </div>
            
            <button class="btn btn-login w-100 mt-3">Login</button>
        </form>

    </div>
</body>

</html>