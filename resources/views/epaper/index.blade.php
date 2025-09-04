@extends('layouts.app')

@section('title', 'Around Odisha E-Paper - ' . date('d-M-Y', strtotime($date)))

@section('content')
<div class="container my-4">

    <div class="row">
        <div class="col-12">
            <h1 class="epaper-title">AROUND ODISHA E-PAPER</h1>
        </div>
    </div>

    <div class="page-controls">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-6 mb-2 ">
                <label for="dateSelect" class="form-label">Select Date:</label>
                <input type="date" class="form-control date-picker" id="dateSelect" 
                       value="{{ $date }}" onchange="changeDate()">
            </div>
            
            <div class="col-lg-3 col-md-6 mb-2">
                <label for="pageSelect" class="form-label">Select Page:</label>
                <select class="form-select page-selector" id="pageSelect" onchange="changePage()">
                    @if($epaper && $epaper->pages->count() > 0)
                        @foreach($epaper->pages as $pageItem)
                            <option value="{{ $pageItem->page_number }}" {{ $pageItem->page_number == $page ? 'selected' : '' }}>
                                Page {{ $pageItem->page_number }}
                            </option>
                        @endforeach
                    @else
                        <option value="1">Page 1</option>
                    @endif
                </select>
            </div>
            <div class="col-lg-3 col-md-6 mb-2">
            <div class="btn-group" role="group" aria-label="Zoom controls">
                        
                        <button class="btn btn-outline-secondary btn-sm me-1" onclick="zoomOut()" title="Zoom Out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <span id="zoomLevel" class="mx-2">100%</span>
                        <button class="btn btn-outline-secondary btn-sm me-2" onclick="zoomIn()" title="Zoom In">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetZoom()" title="Reset Zoom">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </button>
                    </div>
        </div>

           {{-- <div class="col-lg-3 col-md-6 mb-2">
                <label class="form-label">View Options:</label>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm active" id="gridView" onclick="toggleView('grid')">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="listView" onclick="toggleView('list')">
                        <i class="fas fa-list"></i> List
                    </button>
                </div>
            </div> --}}
            
            <div class="col-lg-3 col-md-6 mb-2">
                @if($epaper && $epaper->pdf_path)
                    <label class="form-label">Download:</label>
                    <div class="dropdown">
                        <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i> Full PDF
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('epaper.download', ['epaper_id' => $epaper->id]) }}">
                                    <i class="fas fa-file-pdf"></i> Download PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="printPage()">
                                    <i class="fas fa-print"></i> Print Current Page
                                </a>
                            </li>
                            <li>
                                @if($pdfUrl)
    
        <a href="{{ $pdfUrl }}" target="_blank" class="dropdown-item">
            <i class="fas fa-file-pdf"></i> View Full PDF
        </a>


    <iframe src="{{ $pdfUrl }}" width="100%" height="400px"></iframe>
@else
    <p>No PDF available.</p>
@endif
                            </li>

                        </ul>
                    </div>
                @else
                    <label class="form-label">Download:</label>
                    <button class="btn btn-secondary" disabled>
                        <i class="fas fa-download"></i> No PDF Available
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Pagination -->
       <div class="row mt-3">
    
              
            
                         
            </div>
        </div>
    </div>

    @if($epaper && $epaper->pages->count() > 0)
    
        <div class="row" id="epaperContent">
            <!-- Sidebar Thumbnails -->
            <div class="col-lg-2 col-md-3" id="thumbnailSidebar">
                <div class="sidebar-header mb-3">
                    <h6 class="text-muted"><i class="fas fa-images"></i> Pages</h6>
                </div>
                <div class="sidebar-thumbnails">
                    @foreach($epaper->pages as $pageItem)
                        <div class="page-thumbnail {{ $pageItem->page_number == $page ? 'active' : '' }}" 
                             onclick="selectPage({{ $pageItem->page_number }})" 
                             data-page="{{ $pageItem->page_number }}"
                             title="Go to Page {{ $pageItem->page_number }}">
                            <img src="{{ $pageItem->thumbnail_url }}" 
                                 alt="Page {{ $pageItem->page_number }}" 
                                 class="img-fluid"
                                 loading="lazy">
                            <div class="thumbnail-label">PAGE-{{ $pageItem->page_number }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Main Page View -->
            <div class="col-lg-8 col-md-6" id="mainContentArea">
                <div class="main-page-view" id="mainPageView">
                    <nav aria-label="Epaper page navigation">
    <div class="d-flex justify-content-between align-items-center w-100">

        {{-- Left buttons --}}
        <ul class="pagination mb-0">
            <li class="page-item {{ !$epaper || $page <= 0 ? 'disabled' : '' }}">
                <button class="page-link" onclick="firstPage()" tabindex="-1">
                    <i class="fas fa-angle-double-left"></i> First
                </button>
            </li>
            <li class="page-item {{ !$epaper || $page <= 0 ? 'disabled' : '' }}">
                <button class="page-link" onclick="prevPage()" tabindex="-1">
                    <i class="fas fa-angle-left"></i> Prev
                </button>
            </li>
        </ul>

        {{-- Page info --}}
        @if($epaper && $currentPage = $epaper->pages->where('page_number', $page)->first())
            <div class="page-info text-center">
                <h6 class="mb-0">
                    {{ $epaper->title }} - {{ $epaper->formatted_date }}
                    <span class="badge bg-primary ms-2">Page {{ $currentPage->page_number }}</span>
                </h6>
            </div>

        {{-- Right buttons --}}
        <ul class="pagination mb-0">
            <li class="page-item {{ !$epaper || $page >= ($epaper->total_pages ?? 1) ? 'disabled' : '' }}">
                <button class="page-link" onclick="nextPage()">
                    Next <i class="fas fa-angle-right"></i>
                </button>
            </li>
            <li class="page-item {{ !$epaper || $page >= ($epaper->total_pages ?? 1) ? 'disabled' : '' }}">
                <button class="page-link" onclick="lastPage()">
                    Last <i class="fas fa-angle-double-right"></i>
                </button>
            </li>
        </ul>

    </div>
</nav>

                        
                        <div class="image-container" id="imageContainer">
                            <img id="mainPageImage" 
                                 src="{{ $currentPage->image_url }}" 
                                 alt="Page {{ $currentPage->page_number }}" 
                                 class="img-fluid main-page-image"
                                 style="cursor: zoom-in;">
                        </div>
                        
                    
                        <div id="loadingIndicator" class="text-center" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading page...</p>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <h5>Page not available</h5>
                            <p>The requested page is not available in this edition.</p>
                        </div>
                    @endif

                    <div class="col-12">
                      <nav aria-label="Epaper page navigation">
                      <ul class="pagination d-flex justify-content-end">
                    <li class="page-item {{ !$epaper || $page <= 0 ? 'disabled' : '' }}">
                    <button class="page-link" onclick="prevPage()" tabindex="-1">
                        <i class="fas fa-angle-left"></i> Prev
                    </button>
                </li>
                <li class="page-item {{ !$epaper || $page >= ($epaper->total_pages ?? 1) ? 'disabled' : '' }}">
                    <button class="page-link" onclick="nextPage()">
                        Next <i class="fas fa-angle-right"></i>
                    </button>
                </li>
            </ul>
                      </nav>
                    </div>
                    <!-- Social Share Buttons -->
                    <div class="social-share">
                        <h6 class="text-center mb-3">Share this page:</h6>
                        <div class="text-center">
                            <button class="btn btn-facebook text-white" onclick="shareOnFacebook()" title="Share on Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-twitter text-white" onclick="shareOnTwitter()" title="Share on Twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-linkedin text-white" onclick="shareOnLinkedIn()" title="Share on LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </button>
                            <button class="btn btn-whatsapp text-white" onclick="shareOnWhatsApp()" title="Share on WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-print text-white" onclick="printPage()" title="Print">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn btn-email text-white" onclick="shareByEmail()" title="Email">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3" id="adsSidebar">
        <div class="sidebar-header mb-3">
            <h6 class="text-muted"><i class="fas fa-ad"></i> Ads</h6>
        </div>
        <div class="sidebar-thumbnails">
            <img src="{{ asset('logo/image.png') }}" class="img-fluid mb-3" alt="Ad 1">
            <img src="{{ asset('logo/image.png') }}" class="img-fluid mb-3" alt="Ad 2">
            {{-- More ads here --}}
        </div>
    </div>
        </div>
        
            
        
        <!-- List View -->
        {{-- <div class="row" id="listViewContent" style="display: none;">
            <div class="col-12">
                <div class="list-view-container">
                    <h5 class="mb-3">{{ $epaper->title }} - {{ $epaper->formatted_date }}</h5>
                    <div class="row">
                        @foreach($epaper->pages as $pageItem)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card page-card" onclick="selectPage({{ $pageItem->page_number }})">
                                    <img src="{{ $pageItem->thumbnail_url }}" 
                                         class="card-img-top" 
                                         alt="Page {{ $pageItem->page_number }}"
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Page {{ $pageItem->page_number }}</h6>
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div> --}}
    @else
        <!-- No E-Paper Available -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x mb-3 text-warning"></i>
                    <h3>No E-Paper Available</h3>
                    <p class="lead">E-Paper for {{ date('d-M-Y', strtotime($date)) }} in {{ $edition }} is not available.</p>
                    <div class="mt-4">
                        <a href="{{ route('epaper.index') }}" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home"></i> Go to Latest Edition
                        </a>
                        <a href="{{ route('epaper.archive') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-archive"></i> Browse Archive
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Fullscreen for Image Viewing -->
<div class="modal fade" id="fullscreenModal" tabindex="-1" aria-labelledby="fullscreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullscreenModalLabel">Page {{ $page }} - Full View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="fullscreenImage" src="" alt="Full page view" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadCurrentPage()">
                    <i class="fas fa-download"></i> Download Page
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentEpaper = @json($epaper);
    let currentPage = {{ $page }};
    let currentCity = '{{ $edition }}';
    let currentZoom = 100;
    let currentView = 'grid';


    document.addEventListener('DOMContentLoaded', function() {
        // Add click event to main image for fullscreen
        const mainImage = document.getElementById('mainPageImage');
        if (mainImage) {
            mainImage.addEventListener('click', function() {
                openFullscreen(this.src);
            });
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    function changeDate() {
        const date = document.getElementById('dateSelect').value;
        showLoading();
        window.location.href = `{{ route('epaper.index') }}?date=${date}&edition=${currentCity}`;
    }

    function changePage() {
        const page = document.getElementById('pageSelect').value;
        selectPage(page);
    }

    function selectPage(pageNumber) {
        if (!currentEpaper) return;
        
        showLoading();
        
        // Update URL
        const url = new URL(window.location);
        //url.searchParams.set('page', pageNumber);
        window.history.pushState({}, '', url);
        
        // Update current page
        currentPage = pageNumber;
        
        // Update page selector
        document.getElementById('pageSelect').value = pageNumber;
        
        // Update thumbnails
        document.querySelectorAll('.page-thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
            if (thumb.dataset.page == pageNumber) {
                thumb.classList.add('active');
            }
        });
        
        // Load page image via AJAX
        fetch(`{{ route('epaper.getPage') }}?epaper_id=${currentEpaper.id}&page_number=${pageNumber}`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success && data.page) {
                    const mainImage = document.getElementById('mainPageImage');
                    const pageInfo = document.querySelector('.page-info .badge');
                    
                    if (mainImage) {
                        mainImage.src = data.page.image_url;
                        mainImage.alt = `Page ${data.page.page_number}`;
                    }
                    
                    if (pageInfo) {
                        pageInfo.textContent = `Page ${data.page.page_number}`;
                    }
                    
                    // Update fullscreen modal title
                    document.getElementById('fullscreenModalLabel').textContent = `Page ${data.page.page_number} - Full View`;
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error loading page:', error);
                showAlert('Error loading page. Please try again.', 'danger');
            });
    }

    function firstPage() {
        if (currentEpaper && currentPage > 1) {
            selectPage(1);
        }
    }

    function prevPage() {
        if (currentEpaper && currentPage > 1) {
            selectPage(currentPage - 1);
        }
    }

    function nextPage() {
        if (currentEpaper && currentPage < currentEpaper.total_pages) {
            selectPage(currentPage + 1);
        }
    }

    function lastPage() {
        if (currentEpaper && currentPage < currentEpaper.total_pages) {
            selectPage(currentEpaper.total_pages);
        }
    }

    // Zoom functions
    function zoomIn() {
        if (currentZoom < 200) {
            currentZoom += 25;
            applyZoom();
        }
    }

    function zoomOut() {
        if (currentZoom > 50) {
            currentZoom -= 25;
            applyZoom();
        }
    }

    function resetZoom() {
        currentZoom = 100;
        applyZoom();
    }

    function applyZoom() {
        const mainImage = document.getElementById('mainPageImage');
        if (mainImage) {
            mainImage.style.transform = `scale(${currentZoom / 100})`;
            mainImage.style.cursor = currentZoom >= 100 ? 'zoom-out' : 'zoom-in';
        }
        document.getElementById('zoomLevel').textContent = currentZoom + '%';
    }

    // View toggle functions
    function toggleView(view) {
        currentView = view;
        const gridBtn = document.getElementById('gridView');
        const listBtn = document.getElementById('listView');
        const gridContent = document.getElementById('epaperContent');
        const listContent = document.getElementById('listViewContent');
        
        if (view === 'grid') {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            gridContent.style.display = 'flex';
            listContent.style.display = 'none';
        } else {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            gridContent.style.display = 'none';
            listContent.style.display = 'block';
        }
    }

    function printPage() {
        const mainImage = document.getElementById('mainPageImage');
        if (mainImage) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Page ${currentPage}</title>
                        <style>
                            body { 
                                margin: 0; 
                                text-align: center; 
                                font-family: Arial, sans-serif;
                            }
                            img { 
                                max-width: 100%; 
                                height: auto; 
                                border: 1px solid #ccc;
                            }
                            .header {
                                padding: 20px;
                                background: #f8f9fa;
                                margin-bottom: 20px;
                            }
                            @media print {
                                .header { background: white; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>${currentEpaper ? currentEpaper.title : 'E-Paper'}</h2>
                            <p>Page ${currentPage} - ${currentEpaper ? currentEpaper.formatted_date : ''}</p>
                        </div>
                        <img src="${mainImage.src}" alt="Page ${currentPage}">
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        }
    }

    function openFullscreen(imageSrc) {
        document.getElementById('fullscreenImage').src = imageSrc;
        const modal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
        modal.show();
    }

    function downloadCurrentPage() {
        const mainImage = document.getElementById('mainPageImage');
        if (mainImage) {
            const link = document.createElement('a');
            link.href = mainImage.src;
            link.download = `page_${currentPage}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    function showLoading() {
        const loading = document.getElementById('loadingIndicator');
        const mainImage = document.getElementById('mainPageImage');
        
        if (loading && mainImage) {
            loading.style.display = 'block';
            mainImage.style.opacity = '0.5';
        }
    }

    function hideLoading() {
        const loading = document.getElementById('loadingIndicator');
        const mainImage = document.getElementById('mainPageImage');
        
        if (loading && mainImage) {
            loading.style.display = 'none';
            mainImage.style.opacity = '1';
        }
    }

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Social sharing functions
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(`${currentEpaper ? currentEpaper.title : 'Around Odisha E-Paper'} - ${currentEpaper ? currentEpaper.formatted_date : ''}`);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&t=${title}`, '_blank', 'width=600,height=400');
    }

    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(`Reading ${currentEpaper ? currentEpaper.title : 'Around Odisha E-Paper'} - ${currentEpaper ? currentEpaper.formatted_date : ''}`);
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
    }

    function shareOnLinkedIn() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
    }

    function shareOnWhatsApp() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(`Check out ${currentEpaper ? currentEpaper.title : 'Around Odisha E-Paper'}: ${url}`);
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function shareByEmail() {
        const subject = encodeURIComponent(`${currentEpaper ? currentEpaper.title : 'Around Odisha E-Paper'} - ${currentEpaper ? currentEpaper.formatted_date : ''}`);
        const body = encodeURIComponent(`Check out this e-paper: ${window.location.href}`);
        window.location.href = `mailto:?subject=${subject}&body=${body}`;
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
        
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                prevPage();
                break;
            case 'ArrowRight':
                e.preventDefault();
                nextPage();
                break;
            case 'Home':
                e.preventDefault();
                firstPage();
                break;
            case 'End':
                e.preventDefault();
                lastPage();
                break;
            case '+':
            case '=':
                e.preventDefault();
                zoomIn();
                break;
            case '-':
                e.preventDefault();
                zoomOut();
                break;
            case '0':
                e.preventDefault();
                resetZoom();
                break;
            case 'p':
            case 'P':
                if (e.ctrlKey) {
                    e.preventDefault();
                    printPage();
                }
                break;
        }
    });
</script>

@endpush