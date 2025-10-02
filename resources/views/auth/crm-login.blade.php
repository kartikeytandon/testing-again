@extends('layouts.auth', ['title' => 'CRM Login'])

@section('content')
    <div class="col-xl-12">
        <div class="card auth-card">
            <div class="card-body p-0">
                <div class="row align-items-center g-0">
                    <div class="col-lg-6 d-none d-lg-inline-block border-end">
                        <div class="auth-page-sidebar">
                            <img src="/images/sign-in.svg" alt="auth" class="img-fluid" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-4">
                            <div class="mx-auto mb-4 text-center auth-logo">
                                <a href="{{ route('any', 'home') }}" class="logo-dark text-decoration-none">
                                    <span class="fs-4 fw-semibold">RD &amp; Company</span>
                                </a>
                                <a href="{{ route('any', 'home') }}" class="logo-light text-decoration-none">
                                    <span class="fs-4 fw-semibold text-light">RD &amp; Company</span>
                                </a>
                            </div>

                            <div class="text-center mb-4">
                                <h4 class="fw-semibold">Welcome Back!</h4>
                                <p class="text-muted">Sign in to continue</p>
                            </div>

                            <form id="crmLoginForm" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Enter your email" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid email.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="position-relative auth-pass-inputgroup">
                                        <input type="password" class="form-control pe-5" id="password" 
                                               name="password" placeholder="Enter password" required>
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" 
                                                type="button" id="password-addon">
                                            <i class="ri-eye-fill align-middle"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            Please provide a password.
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                    <label class="form-check-label" for="auth-remember-check">
                                        Remember me
                                    </label>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-primary w-100" type="submit" id="loginBtn">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" id="loginSpinner"></span>
                                        Sign In
                                    </button>
                                </div>

                                
                            </form>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
@endsection

@section('script')
    <script src="{{ asset('build/assets/app-DhVc4B2T.js') }}"></script>
    <script>
        // Inline CRM Login JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Basic login functionality
            const form = document.getElementById('crmLoginForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    
                    if (!email || !password) {
                        alert('Please fill in all fields');
                        return;
                    }
                    
                    try {
                        const response = await fetch('/api/auth/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email, password })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            localStorage.setItem('auth_token', data.data.token);
                            localStorage.setItem('user_data', JSON.stringify(data.data.user));
                            const role = String((data.data.user?.role) || '').toLowerCase();
                            alert('Login successful! Redirecting...');
                            if (role === 'admin') {
                                window.location.href = '/admin';
                            } else {
                                window.location.href = '/crm';
                            }
                        } else {
                            alert(data.message || 'Login failed');
                        }
                    } catch (error) {
                        console.error('Login error:', error);
                        alert('Login failed. Please try again.');
                    }
                });
            }
        });
    </script>
@endsection
