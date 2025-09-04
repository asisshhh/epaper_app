@extends('layouts.app')

@section('title', 'E-Paper Archive - ' . $edition)

@section('content')
<div class="container my-4">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="epaper-title">E-PAPER ARCHIVE</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('epaper.index') }}">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Archive</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="page-controls">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="cityFilter" class="form-label">
                    <i class="fas fa-map-marker-alt"></i> Edition:
                </label>
                <select class="form-select" id="cityFilter" onchange="filterArchive()">
                    @foreach($cities as $editionName)
                        <option value="{{ $editionName }}" {{ $edition == $editionName ? 'selected' : '' }}>
                            {{ $editionName }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="monthFilter" class="form-label">
                    <i class="fas fa-calendar-alt"></i> Month:
                </label>
                <input type="month" class="form-control" id="monthFilter" 
                       value="{{ $month }}" onchange="filterArchive()">
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="yearFilter" class="form-label">
                    <i class="fas fa-calendar"></i> Year:
                </label>
                <select class="form-select" id="yearFilter" onchange="filterByYear()">
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ substr($month, 0, 4) == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <a href="{{ route('epaper.index') }}" class="btn btn-primary">
                        <i class="fas fa-newspaper"></i> Latest Edition
                    </a>
                    <button class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fas fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="row mt-3">
            <div class="col-lg-6 col-md-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" 
                           placeholder="Search by title or date..." 
                           onkeyup="searchArchive()"
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-6 col-md-4">
                <div class="d-flex justify-content-end align-items-center">
                    <span class="text-muted me-3">
                        <i class="fas fa-info-circle"></i> 
                        {{ $epapers->total() }} editions found
                    </span>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm active" id="gridViewBtn" onclick="toggleArchiveView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="listViewBtn" onclick="toggleArchiveView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Content -->
    @if($epapers->count() > 0)
        <!-- Grid View -->
        <div id="gridView" class="archive-content">
            <div class="row" id="archiveResults">
                @foreach($epapers as $epaper)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4 archive-item" 
                         data-title="{{ strtolower($epaper->title) }}" 
                         data-date="{{ $epaper->publication_date->format('Y-m-d') }}">
                        <div class="card h-100 archive-card">
                            <!-- Card Image -->
                            <div class="card-img-container">
                                @if($epaper->pages->first())
                                    <img src="{{ $epaper->pages->first()->thumbnail_url }}" 
                                         class="card-img-top archive-thumbnail" 
                                         alt="{{ $epaper->title }}"
                                         loading="lazy"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDIwMCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZjhmOWZhIi8+Cjx0ZXh0IHg9IjEwMCIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM2Yzc1N2QiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pgo8L3N2Zz4K'">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-newspaper fa-3x text-muted"></i>
                                        <p class="text-muted mt-2">No Preview</p>
                                    </div>
                                @endif
                                
                                <!-- Overlay with page count -->
                                <div class="card-overlay">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-file-alt"></i> {{ $epaper->total_pages }} Pages
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body">
                                <h6 class="card-title text-truncate" title="{{ $epaper->title }}">
                                    {{ $epaper->title }}
                                </h6>
                                <div class="card-meta">
                                    <p class="card-text mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $epaper->formatted_date }}<br>
                                            <i class="fas fa-map-marker-alt"></i> {{ $epaper->edition }}<br>
                                            <i class="fas fa-clock"></i> {{ $epaper->created_at->diffForHumans() }}
                                        </small>
                                    </p>
                                </div>
                                
                                <!-- Quick Stats -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> Available
                                    </small>
                                    @if($epaper->pdf_path)
                                        <small class="text-info">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </small>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer bg-transparent">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('epaper.index', ['date' => $epaper->publication_date->format('Y-m-d'), 'edition' => $epaper->edition]) }}" 
                                       class="btn btn-primary btn-sm flex-fill" title="Read E-Paper">
                                        <i class="fas fa-eye"></i> Read
                                    </a>
                                    @if($epaper->pdf_path)
                                        <a href="{{ route('epaper.download', ['epaper_id' => $epaper->id]) }}" 
                                           class="btn btn-danger btn-sm" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="shareEpaper('{{ $epaper->id }}', '{{ $epaper->title }}', '{{ $epaper->publication_date->format('Y-m-d') }}', '{{ $epaper->edition }}')"
                                            title="Share">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- List View (Hidden by default) -->
        <div id="listView" class="archive-content" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Edition</th>
                            <th>Pages</th>
                            <th>PDF</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($epapers as $epaper)
                            <tr class="archive-item" 
                                data-title="{{ strtolower($epaper->title) }}" 
                                data-date="{{ $epaper->publication_date->format('Y-m-d') }}">
                                <td>
                                    @if($epaper->pages->first())
                                        <img src="{{ $epaper->pages->first()->thumbnail_url }}" 
                                             alt="Preview" class="table-thumbnail">
                                    @else
                                        <div class="table-no-image">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $epaper->title }}</strong>
                                </td>
                                <td>
                                    {{ $epaper->formatted_date }}
                                    <br><small class="text-muted">{{ $epaper->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $epaper->edition }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $epaper->total_pages }}</span>
                                </td>
                                <td>
                                    @if($epaper->pdf_path)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Available
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-times"></i> N/A
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('epaper.index', ['date' => $epaper->publication_date->format('Y-m-d'), 'edition' => $epaper->edition]) }}" 
                                           class="btn btn-outline-primary" title="Read">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($epaper->pdf_path)
                                            <a href="{{ route('epaper.download', ['epaper_id' => $epaper->id]) }}" 
                                               class="btn btn-outline-success" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-outline-secondary" 
                                                onclick="shareEpaper('{{ $epaper->id }}', '{{ $epaper->title }}', '{{ $epaper->publication_date->format('Y-m-d') }}', '{{ $epaper->edition }}')"
                                                title="Share">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Showing {{ $epapers->firstItem() ?? 0 }} to {{ $epapers->lastItem() ?? 0 }} 
                            of {{ $epapers->total() }} results
                        </small>
                    </div>
                    <div class="pagination-links">
                        {{ $epapers->appends(request()->query())->onEachSide(2)->links() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Archives Found -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-search fa-4x mb-3 text-info"></i>
                    <h3>No E-Papers Found</h3>
                    <p class="lead">No e-papers found for the selected criteria.</p>
                    <div class="mt-4">
                        <button class="btn btn-primary btn-lg me-2" onclick="resetFilters()">
                            <i class="fas fa-refresh"></i> Reset Filters
                        </button>
                        <a href="{{ route('epaper.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home"></i> Go to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Share E-Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h6 id="shareTitle" class="mb-3"></h6>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button class="btn btn-facebook text-white" onclick="shareOnFacebook(shareUrl)">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                        <button class="btn btn-twitter text-white" onclick="shareOnTwitter(shareUrl, shareTitle)">
                            <i class="fab fa-twitter"></i> Twitter
                        </button>
                        <button class="btn btn-whatsapp text-white" onclick="shareOnWhatsApp(shareUrl, shareTitle)">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </button>
                        <button class="btn btn-email text-white" onclick="shareByEmail(shareUrl, shareTitle)">
                            <i class="fas fa-envelope"></i> Email
                        </button>
                    </div>
                    <hr>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .archive-card {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }
    
    .archive-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-color: #dc3545;
    }
    
    .card-img-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .archive-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .archive-card:hover .archive-thumbnail {
        transform: scale(1.05);
    }
    
    .card-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .no-image-placeholder {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    
    .table-thumbnail {
        width: 60px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .table-no-image {
        width: 60px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 4px;
        color: #6c757d;
    }
    
    .pagination-info {
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .card-img-container {
            height: 150px;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let shareUrl = '';
    let shareTitle = '';
    let currentView = 'grid';

    function filterArchive() {
        const edition = document.getElementById('cityFilter').value;
        const month = document.getElementById('monthFilter').value;
        
        const url = new URL(window.location);
        url.searchParams.set('edition', edition);
        url.searchParams.set('month', month);
        
        window.location.href = url.toString();
    }

    function filterByYear() {
        const year = document.getElementById('yearFilter').value;
        const currentMonth = document.getElementById('monthFilter').value;
        const newMonth = year + (currentMonth.length > 4 ? currentMonth.substr(4) : '-01');
        
        document.getElementById('monthFilter').value = newMonth;
        filterArchive();
    }

    function resetFilters() {
        const url = new URL(window.location);
        url.search = '';
        window.location.href = url.toString();
    }

    function searchArchive() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.archive-item');
        
        items.forEach(item => {
            const title = item.dataset.title;
            const date = item.dataset.date;
            const isVisible = title.includes(searchTerm) || date.includes(searchTerm);
            
            if (currentView === 'grid') {
                item.style.display = isVisible ? 'block' : 'none';
            } else {
                item.style.display = isVisible ? 'table-row' : 'none';
            }
        });
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        searchArchive();
    }

    function toggleArchiveView(view) {
        currentView = view;
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        
        if (view === 'grid') {
            gridView.style.display = 'block';
            listView.style.display = 'none';
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        } else {
            gridView.style.display = 'none';
            listView.style.display = 'block';
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        }
    }

    function shareEpaper(id, title, date, edition) {
        shareTitle = title;
        shareUrl = `{{ url('/epaper') }}?date=${date}&edition=${edition}`;
        
        document.getElementById('shareTitle').textContent = title;
        document.getElementById('shareLink').value = shareUrl;
        
        const modal = new bootstrap.Modal(document.getElementById('shareModal'));
        modal.show();
    }

    function copyToClipboard() {
        const linkInput = document.getElementById('shareLink');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(linkInput.value);
        
        // Show feedback
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }

    // Social sharing functions
    function shareOnFacebook(url) {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
    }

    function shareOnTwitter(url, title) {
        const text = `Check out: ${title}`;
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank', 'width=600,height=400');
    }

    function shareOnWhatsApp(url, title) {
        const text = `Check out: ${title} - ${url}`;
        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
    }

    function shareByEmail(url, title) {
        const subject = encodeURIComponent(title);
        const body = encodeURIComponent(`Check out this e-paper: ${url}`);
        window.location.href = `mailto:?subject=${subject}&body=${body}`;
    }

    // Auto-search on input
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput.value) {
            searchArchive();
        }
    });
</script>
@endpush