<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin User - E-Paper Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .register-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            font-size: 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .role-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .role-info h6 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .role-info small {
            color: #6c757d;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link a:hover {
            color: #764ba2;
        }
        
        @media (max-width: 768px) {
            .register-card {
                padding: 2rem;
                margin: 10px;
            }
            
            .register-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1 class="register-title">
                    <i class="bi bi-person-plus-fill text-primary"></i><br>
                    Register Admin User
                </h1>
                <p class="register-subtitle">
                    Create a new administrator account for the E-Paper management system
                </p>
            </div>
            
            <!-- Laravel error handling -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.register') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Full Name"
                                   value="{{ old('name') }}" 
                                   required>
                            <label for="name">
                                <i class="bi bi-person me-2"></i>Full Name
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   placeholder="admin@example.com"
                                   value="{{ old('email') }}" 
                                   required>
                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>Email Address
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating">
                    <select class="form-select @error('role') is-invalid @enderror" 
                            id="role" 
                            name="role" 
                            required>
                        <option value="">Choose Role...</option>
                        @if(!App\Models\AdminUser::where('role', 'super_admin')->exists() || (Auth::guard('admin')->check() && Auth::guard('admin')->user()->isSuperAdmin()))
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>
                                Super Administrator
                            </option>
                        @endif
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            Administrator
                        </option>
                        <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>
                            Editor
                        </option>
                    </select>
                    <label for="role">
                        <i class="bi bi-shield-check me-2"></i>User Role
                    </label>
                </div>
                
                <div class="role-info">
                    <h6><i class="bi bi-info-circle"></i> Role Permissions:</h6>
                    <small>
                        <strong>Super Admin:</strong> Full system access, can manage all users<br>
                        <strong>Administrator:</strong> Can manage e-papers and view users<br>
                        <strong>Editor:</strong> Can create and edit e-papers only
                    </small>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Password"
                                   required>
                            <label for="password">
                                <i class="bi bi-lock me-2"></i>Password
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirm Password"
                                   required>
                            <label for="password_confirmation">
                                <i class="bi bi-lock-fill me-2"></i>Confirm Password
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>
                        Create Admin Account
                    </button>
                    
                    @if(Auth::guard('admin')->check())
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to User Management
                        </a>
                    @else
                        <div class="back-link">
                            <p class="mb-0">
                                Already have an account? <a href="{{ route('admin.login') }}">Sign In</a>
                            </p>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>