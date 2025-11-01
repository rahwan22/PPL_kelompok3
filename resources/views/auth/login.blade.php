<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Login Sistem Sekolah' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body { 
          background-color: #000000ff; 
          height: 100vh; 
          display:flex; 
          justify-content:center; 
          align-items:center; 
      }
      .card { 
          padding:2rem; 
          border-radius:1rem; 
          box-shadow:0 0 20px rgba(0,0,0,0.1); 
          width:360px; 
      }
      .btn-login { background:black; color:white; }
  </style>
</head>
<body>
  <div class="card">
      <h4 class="text-center mb-4 fw-bold text-success">Login Sistem Sekolah</h4>

      @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if(session('success'))
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
      <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                lOGIN SEKARANG!
                
                    
            </p>
        </div>
  </div>
</body>
</html>
