@extends('layouts.app')

@section('title', 'Admin Dashboard - E-Paper Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3 sidebar-container">
            <div class="sidebar">
                <div class="sidebar-header">
                    <div class="admin-info">
                        <div class="admin-avatar mb-2">
                            @if(Auth::guard('admin')->user()->avatar)
                                <img src="{{ asset('storage/' . Auth::guard('admin')->user()->avatar) }}" 
                                     alt="{{ Auth::guard('admin')->user()->name }}" 
                                     class="rounded-circle" 
                                     width="40" height="40">
                            @else
                                <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); color: white; font-size: 16px;">
                                    {{ substr(Auth::guard('admin')->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h6 class="text-white mb-0">{{ Auth::guard('admin')->user()->name }}</h6>
                        <small class="text-light opacity-75">
                            {{ ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role)) }}
                        </small>
                    </div>
                </div>
                
                <nav class="sidebar-nav">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <!-- User Management (Super Admin Only) -->
                        @if(Auth::guard('admin')->user()->isSuperAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people-fill"></i> User Management
                                </a>
                            </li>
                        @endif
                        
                        <!-- E-Paper Management -->
                        @if(Auth::guard('admin')->user()->canManageEpapers())
                            <li class="nav-item">
                                <a class="nav-link collapsed" data-bs-toggle="collapse" href="#epaperMenu" role="button">
                                    <i class="bi bi-journal-text"></i> E-Paper Management
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </a>
                                <div class="collapse show" id="epaperMenu">
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
                        @endif
                        
                        <!-- Archive & Public Views -->
                        <li class="nav-item">
                            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#publicMenu" role="button">
                                <i class="bi bi-globe"></i> Public Views
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="publicMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('epaper.index') }}" target="_blank">
                                            <i class="bi bi-house"></i> E-Paper Home
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('epaper.archive') }}" target="_blank">
                                            <i class="bi bi-archive"></i> Archive
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- System Tools -->
                        @if(Auth::guard('admin')->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link collapsed" data-bs-toggle="collapse" href="#toolsMenu" role="button">
                                    <i class="bi bi-tools"></i> System Tools
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </a>
                                <div class="collapse" id="toolsMenu">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a class="nav-link" href="/test-image" target="_blank">
                                                <i class="bi bi-image"></i> Test Image Processing
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" onclick="alert('Clear cache functionality coming soon!')">
                                                <i class="bi bi-arrow-clockwise"></i> Clear Cache
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        
                        <hr class="sidebar-divider">
                        
                        <!-- Quick Actions -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('epaper.index') }}" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> View Public Site
                            </a>
                        </li>
                        
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
                        <h1 class="epaper-title">E-PAPER ADMIN DASHBOARD</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                    @if(Auth::guard('admin')->user()->canManageEpapers())
                        <a href="{{ route('admin.create') }}" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Upload New E-Paper
                        </a>
                    @endif
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

            <!-- Dashboard Statistics -->
            <div class="row mb-4">
                @if(Auth::guard('admin')->user()->canManageEpapers())
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $totalEpapers ?? 0 }}</h4>
                                        <p class="mb-0">Total E-Papers</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-journal-text"></i>
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
                                        <h4 class="mb-0">{{ $activeEpapers ?? 0 }}</h4>
                                        <p class="mb-0">Active E-Papers</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-check-circle"></i>
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
                                        <h4 class="mb-0">{{ $totalPages ?? 0 }}</h4>
                                        <p class="mb-0">Total Pages</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-files"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(Auth::guard('admin')->user()->isSuperAdmin())
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $totalAdmins ?? 0 }}</h4>
                                        <p class="mb-0">Admin Users</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Activity -->
            @if(Auth::guard('admin')->user()->canManageEpapers() && isset($epapers) && $epapers->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-journal-text"></i> Recent E-Papers
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Edition</th>
                                        <th>Pages</th>
                                        <th>PDF</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($epapers->take(10) as $epaper)
                                        <tr>
                                            <td>{{ $epaper->id }}</td>
                                            <td>{{ $epaper->title }}</td>
                                            <td>{{ $epaper->formatted_date }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $epaper->edition }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $epaper->total_pages }} pages</span>
                                            </td>
                                            <td>
                                                @if($epaper->pdf_path)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-lg"></i> Available
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-x-lg"></i> Not Available
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($epaper->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('epaper.index', ['date' => $epaper->publication_date->format('Y-m-d'), 'edition' => $epaper->edition]) }}" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(Auth::guard('admin')->user()->canManageEpapers())
                                                        <a href="{{ route('admin.edit', $epaper->id) }}" 
                                                           class="btn btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    @endif
                                                    @if($epaper->pdf_path)
                                                        <a href="{{ route('epaper.download', ['epaper_id' => $epaper->id]) }}" 
                                                           class="btn btn-outline-success" title="Download PDF">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::guard('admin')->user()->isAdmin())
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteEpaper({{ $epaper->id }})" title="Delete">
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
                        
                        @if($epapers->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-right"></i> View All E-Papers
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif(Auth::guard('admin')->user()->canManageEpapers())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-journal-plus display-1 text-muted mb-3"></i>
                        <h4>No E-Papers Uploaded</h4>
                        <p class="text-muted mb-4">Start by uploading your first e-paper edition.</p>
                        <a href="{{ route('admin.create') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle"></i> Upload First E-Paper
                        </a>
                    </div>
                </div>
            @endif

            <!-- Welcome Message for New Users -->
            @if(!Auth::guard('admin')->user()->last_login_at || Auth::guard('admin')->user()->last_login_at->isToday())
                <div class="card mt-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle-fill text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title">Welcome to E-Paper Admin Dashboard!</h5>
                                <p class="card-text mb-2">
                                    You're logged in as <strong>{{ ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role)) }}</strong>.
                                </p>
                                <div class="mt-3">
                                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                                        <span class="badge bg-primary me-2">✓ Manage Admin Users</span>
                                        <span class="badge bg-success me-2">✓ Full E-Paper Access</span>
                                        <span class="badge bg-info me-2">✓ System Administration</span>
                                    @elseif(Auth::guard('admin')->user()->role === 'admin')
                                        <span class="badge bg-success me-2">✓ E-Paper Management</span>
                                        <span class="badge bg-info me-2">✓ Delete Operations</span>
                                        <span class="badge bg-warning me-2">✓ System Tools</span>
                                    @else
                                        <span class="badge bg-info me-2">✓ Create E-Papers</span>
                                        <span class="badge bg-secondary me-2">✓ Edit E-Papers</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this e-paper? This action cannot be undone.</p>
                <p class="text-muted">This will also delete all associated page images and PDF files.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete
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
    text-align: center;
}

.admin-info h6 {
    font-weight: 600;
    margin-bottom: 0;
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

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
}

.stat-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: 250px;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .main-header {
        margin: -1rem -1rem 1rem -1rem;
        padding: 1rem;
    }
    
    .col-lg-10.col-md-9 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Sidebar toggle button for mobile */
.sidebar-toggle {
    display: none;
}

@media (max-width: 768px) {
    .sidebar-toggle {
        display: inline-block;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: #667eea;
        color: white;
        border: none;
        padding: 0.5rem;
        border-radius: 0.25rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    function deleteEpaper(epaperId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/epaper/${epaperId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        // Create mobile toggle button
        const toggleButton = document.createElement('button');
        toggleButton.className = 'btn sidebar-toggle';
        toggleButton.innerHTML = '<i class="bi bi-list"></i>';
        document.body.appendChild(toggleButton);

        const sidebar = document.querySelector('.sidebar');
        
        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !toggleButton.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    });
</script>
@endpush