@extends('layouts.app')

@section('title', 'User Management - Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        
        <div class="col-lg-2 col-md-3 sidebar-container">
            <div class="sidebar">
                <div class="sidebar-header">
                    <h5><i class="bi bi-gear-fill"></i> Super Admin</h5>
                </div>
                
                <nav class="sidebar-nav">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <!-- User Management (Super Admin Only) -->
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people-fill"></i> User Management
                            </a>
                        </li>
                        
                        <!-- E-Paper Management -->
                        <li class="nav-item">
                            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#epaperMenu" role="button">
                                <i class="bi bi-journal-text"></i> E-Paper Management
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="epaperMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.index') }}">
                                            <i class="bi bi-list-ul"></i> All E-Papers
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.create') }}">
                                            <i class="bi bi-upload"></i> Upload New
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <hr class="sidebar-divider">
                        
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-10 col-md-9 main-content">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="epaper-title">USER MANAGEMENT</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                                <li class="breadcrumb-item active">User Management</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Add New Admin
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $users->where('role', 'super_admin')->count() }}</h4>
                                    <p class="mb-0">Super Admins</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $users->where('role', 'admin')->count() }}</h4>
                                    <p class="mb-0">Administrators</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="bi bi-person-gear"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $users->where('role', 'editor')->count() }}</h4>
                                    <p class="mb-0">Editors</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $users->where('is_active', false)->count() }}</h4>
                                    <p class="mb-0">Inactive Users</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="bi bi-person-x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people"></i> Admin Users
                    </h5>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2">
                                                        @if($user->avatar)
                                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle" width="32" height="32">
                                                        @else
                                                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #667eea; color: white; font-size: 14px;">
                                                                {{ substr($user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $user->name }}</div>
                                                        @if($user->id === Auth::guard('admin')->id())
                                                            <small class="text-muted">(You)</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @switch($user->role)
                                                    @case('super_admin')
                                                        <span class="badge bg-primary">
                                                            <i class="bi bi-shield-check"></i> Super Admin
                                                        </span>
                                                        @break
                                                    @case('admin')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-person-gear"></i> Administrator
                                                        </span>
                                                        @break
                                                    @case('editor')
                                                        <span class="badge bg-info">
                                                            <i class="bi bi-pencil-square"></i> Editor
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->last_login_at)
                                                    <small>
                                                        {{ $user->last_login_at->diffForHumans() }}<br>
                                                        <span class="text-muted">{{ $user->last_login_ip }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $user->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    @if($user->id !== Auth::guard('admin')->id())
                                                        <form method="POST" 
                                                              action="{{ route('admin.users.toggle-status', $user->id) }}" 
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                                    title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                                                    onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}-circle"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <button type="button" 
                                                                class="btn btn-outline-danger" 
                                                                onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                                                title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h4>No Admin Users Found</h4>
                            <p>Start by creating the first admin user.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create First Admin
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong><span id="userName"></span></strong>?</p>
                <p class="text-muted">This action cannot be undone. All associated data will be permanently removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteUserForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection

@push('styles')
<style>
/* Include your existing sidebar styles from the original dashboard */
.sidebar-container {
    padding: 0;
}

.sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    position: sticky;
    top: 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-header h5 {
    color: white;
    margin: 0;
    font-weight: 600;
}

.sidebar-nav {
    padding: 1rem 0;
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 0.75rem 1rem;
    border-radius: 0;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.sidebar .nav-link:hover {
    color: white;
    background-color: rgba(255,255,255,0.1);
    border-left-color: #fff;
}

.sidebar .nav-link.active {
    color: white;
    background-color: rgba(255,255,255,0.2);
    border-left-color: #fff;
}

.sidebar .nav-link i {
    margin-right: 0.5rem;
    width: 20px;
    text-align: center;
}

.sidebar-divider {
    margin: 1rem 0;
    border-color: rgba(255,255,255,0.1);
}

.main-content {
    padding: 2rem;
    background-color: #f8f9fa;
    min-height: 100vh;
}

.main-header {
    background: white;
    margin: -2rem -2rem 2rem -2rem;
    padding: 2rem;
    border-bottom: 1px solid #dee2e6;
}

.epaper-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1rem;
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #495057;
}
</style>
@endpush

@push('scripts')
<script>
    function deleteUser(userId, userName) {
        document.getElementById('userName').textContent = userName;
        document.getElementById('deleteUserForm').action = `/admin/users/${userId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        modal.show();
    }
</script>
@endpush