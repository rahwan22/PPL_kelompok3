@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/profil.css') }}">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
    
       
            <div class="card shadow-xl border-0 rounded-4 overflow-hidden"> 
                
          
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h4 class="mb-0 text-center font-weight-bold">
                        <i class="bi bi-shield-lock-fill me-2"></i> Pengaturan Keamanan
                    </h4>
                </div>
                
                <div class="card-body p-4 p-md-4"> {{-- Padding sedikit dikurangi --}}

 
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> 
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <p class="text-muted mb-4 text-center">
                        Masukkan sandi lama Anda untuk memverifikasi, kemudian masukkan sandi baru yang kuat.
                    </p>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3"> 

                            {{-- Sandi Lama --}}
                            <div class="col-12">
                                <label for="current_password" class="form-label fw-semibold">
                                    <i class="bi bi-key-fill me-1 text-primary"></i> Sandi Lama
                                </label>
                                <input type="password" 
                                       class="form-control rounded-pill @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Masukkan sandi lama Anda" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-3 text-muted"> {{-- Garis pemisah --}}

                     
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-1 text-success"></i> Sandi Baru
                                </label>
                       
                                <input type="password" 
                                       class="form-control rounded-pill @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Sandi baru (Min. 8 karakter)" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                  
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1 text-success"></i> Konfirmasi Sandi Baru
                                </label>
                         
                                <input type="password" 
                                       class="form-control rounded-pill" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Ulangi sandi baru" 
                                       required>
                            </div>
                        </div> 
                        
      
                        <div class="d-grid mt-4"> 
                            <button type="submit" class="btn btn-primary rounded-pill btn-modern-submit"> 
                                <i class="bi bi-save me-2"></i> Simpan Sandi Baru
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>



@endsection