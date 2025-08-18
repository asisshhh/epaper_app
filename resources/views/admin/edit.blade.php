@extends('layouts.app')

@section('title', 'Edit E-Paper - Admin Dashboard')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="epaper-title">EDIT E-PAPER</h1>
                <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-pencil-square"></i> Edit E-Paper Details
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.update', $epaper) }}" method="POST" enctype="multipart/form-data" id="editForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $epaper->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="publication_date" class="form-label">Publication Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('publication_date') is-invalid @enderror" 
                                   id="publication_date" name="publication_date" 
                                   value="{{ old('publication_date', $epaper->publication_date->format('Y-m-d')) }}" required>
                            @error('publication_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city', $epaper->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $epaper->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $epaper->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="pdf_file" class="form-label">PDF File</label>
                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" 
                           id="pdf_file" name="pdf_file" accept=".pdf" onchange="validatePdf(this)">
                    @error('pdf_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    @if($epaper->pdf_path)
                        <div class="form-text">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> 
                            Current PDF: <strong>{{ basename($epaper->pdf_path) }}</strong>
                            <br><small class="text-muted">Upload a new file to replace the current one.</small>
                        </div>
                    @else
                        <div class="form-text text-warning">
                            <i class="bi bi-exclamation-triangle"></i> No PDF currently uploaded.
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="page_images" class="form-label">Page Images</label>
                    <input type="file" class="form-control @error('page_images') is-invalid @enderror @error('page_images.*') is-invalid @enderror" 
                           id="page_images" name="page_images[]" accept="image/jpeg,image/png,image/jpg" multiple>

                    @error('page_images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('page_images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    <div class="form-text">
                        <small class="text-muted">
                            Current pages: <strong>{{ $epaper->total_pages }}</strong>
                            <br>Upload new images to replace all existing page images. Leave empty to keep current pages.
                            <br>Accepted formats: JPEG, PNG, JPG. Max file size: 100MB per image.
                        </small>
                    </div>

                    <!-- Current Pages Preview -->
                    @if($epaper->pages->count() > 0)
                        <div class="mt-3" id="currentPagesSection">
                            <h6>Current Pages Preview:</h6>
                            <div class="row sortable-container" id="currentPagesContainer">
                                @foreach($epaper->pages as $page)
                                    <div class="col-md-2 col-sm-3 col-4 mb-3 draggable-item" 
                                         draggable="true" 
                                         data-page-id="{{ $page->id }}"
                                         data-type="existing">
                                        <div class="card h-100 position-relative">
                                            <div class="drag-handle">
                                                <i class="bi bi-grip-vertical"></i>
                                            </div>
                                            <img src="{{ asset('storage/' . $page->thumbnail_path) }}" 
                                                 class="card-img-top" alt="Page {{ $page->page_number }}"
                                                 style="height: 100px; object-fit: cover;">
                                            <div class="page-number">Page {{ $page->page_number }}</div>
                                            <button type="button" class="btn btn-danger btn-sm delete-page-btn" 
                                                    data-page-id="{{ $page->id }}"
                                                    title="Remove this page">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> You can drag pages to reorder them. Click the trash icon to remove a page.
                                </small>
                            </div>
                        </div>
                    @endif

                    <!-- New Images Preview -->
                    <div id="newImagesPreview" class="mt-3" style="display: none;">
                        <h6>New Images Preview:</h6>
                        <div id="newImagesContainer" class="row sortable-container"></div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> You can drag images to reorder them. These will replace all existing pages.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for page reordering and deletion -->
                <input type="hidden" name="page_order" id="page_order" value="">
                <input type="hidden" name="deleted_pages" id="deleted_pages" value="">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update E-Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sortable-container {
        min-height: 120px;
    }
    
    .draggable-item {
        cursor: move;
        transition: all 0.2s ease;
        user-select: none;
        position: relative;
    }
    
    .draggable-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .draggable-item.dragging {
        opacity: 0.6;
        transform: rotate(5deg);
        z-index: 1000;
    }
    
    .drag-over {
        border: 2px dashed #007bff;
        background-color: rgba(0,123,255,0.05);
    }
    
    .drag-handle {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 10;
        cursor: grab;
    }
    
    .draggable-item:hover .drag-handle {
        opacity: 1;
    }
    
    .page-number {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.8);
        color: white;
        text-align: center;
        padding: 4px;
        font-size: 11px;
        font-weight: bold;
    }
    
    .delete-page-btn {
        position: absolute;
        top: 5px;
        left: 5px;
        width: 25px;
        height: 25px;
        padding: 0;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 10;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .draggable-item:hover .delete-page-btn {
        opacity: 1;
    }
    
    .deleted-page {
        opacity: 0.3;
        filter: grayscale(100%);
        pointer-events: none;
    }
    
    .deleted-page::after {
        content: 'DELETED';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(220, 53, 69, 0.9);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 12px;
        z-index: 15;
    }

    .sortable-item {
        margin-bottom: 15px;
    }

    .new-image-item {
        position: relative;
        cursor: move;
    }

    .new-image-item .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editForm');
    const fileInput = document.getElementById('page_images'); // Fixed ID
    const previewDiv = document.getElementById('newImagesPreview');
    const previewContainer = document.getElementById('newImagesContainer');
    const pageOrderInput = document.getElementById('page_order');
    const deletedPagesInput = document.getElementById('deleted_pages');
    
    let filesArray = [];
    let deletedPages = [];

    // Handle file input change
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            filesArray = Array.from(this.files);
            previewNewImages();
        });
    }

    // Preview new images function
    function previewNewImages() {
        if (!previewContainer) return;
        
        previewContainer.innerHTML = '';
        if (filesArray.length === 0) {
            previewDiv.style.display = 'none';
            return;
        }

        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-2 col-sm-3 col-4 mb-3 sortable-item new-image-item';
                colDiv.draggable = true;
                colDiv.dataset.index = index;

                colDiv.innerHTML = `
                    <div class="card h-100 position-relative">
                        <div class="drag-handle">
                            <i class="bi bi-grip-vertical"></i>
                        </div>
                        <img src="${e.target.result}" class="card-img-top" 
                             style="height: 100px; object-fit: cover;"
                             alt="New Page ${index + 1}">
                        <div class="page-number">New Page ${index + 1}</div>
                        <button type="button" class="btn btn-danger btn-sm remove-btn" 
                                title="Remove this image">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;

                // Add remove functionality
                const removeBtn = colDiv.querySelector('.remove-btn');
                removeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    filesArray.splice(index, 1);
                    updateFileInput();
                    previewNewImages();
                });

                previewContainer.appendChild(colDiv);
            };
            reader.readAsDataURL(file);
        });

        previewDiv.style.display = 'block';
        initNewImagesDragAndDrop();
    }

    // Update file input with reordered files
    function updateFileInput() {
        const dt = new DataTransfer();
        filesArray.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
    }

    // Initialize drag and drop for new images
    function initNewImagesDragAndDrop() {
        const items = previewContainer.querySelectorAll('.sortable-item');
        
        items.forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('drop', handleDrop);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragenter', handleDragEnter);
            item.addEventListener('dragleave', handleDragLeave);
        });
    }

    // Initialize drag and drop for existing pages
    function initExistingPagesDragAndDrop() {
        const container = document.getElementById('currentPagesContainer');
        if (!container) return;

        const items = container.querySelectorAll('.draggable-item');
        
        items.forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('drop', handleDrop);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragenter', handleDragEnter);
            item.addEventListener('dragleave', handleDragLeave);
        });

        // Add delete functionality
        const deleteButtons = container.querySelectorAll('.delete-page-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                deleteExistingPage(this);
            });
        });
    }

    // Drag and drop event handlers
    let dragSrcEl = null;

    function handleDragStart(e) {
        dragSrcEl = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDragEnter(e) {
        this.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        this.classList.remove('drag-over');
    }

    function handleDrop(e) {
        e.stopPropagation();
        e.preventDefault();

        if (dragSrcEl !== this) {
            // Determine if we're dealing with new images or existing pages
            const isNewImage = dragSrcEl.classList.contains('new-image-item');
            const isExistingPage = dragSrcEl.dataset.pageId;

            if (isNewImage && this.classList.contains('new-image-item')) {
                // Reorder new images
                const srcIndex = parseInt(dragSrcEl.dataset.index);
                const targetIndex = parseInt(this.dataset.index);
                
                // Swap files in array
                const temp = filesArray[srcIndex];
                filesArray[srcIndex] = filesArray[targetIndex];
                filesArray[targetIndex] = temp;
                
                updateFileInput();
                previewNewImages();
            } else if (isExistingPage && this.dataset.pageId) {
                // Reorder existing pages - move DOM element
                const parent = dragSrcEl.parentNode;
                
                if (dragSrcEl !== this) {
                    const allItems = Array.from(parent.children);
                    const srcIndex = allItems.indexOf(dragSrcEl);
                    const targetIndex = allItems.indexOf(this);
                    
                    if (srcIndex < targetIndex) {
                        parent.insertBefore(dragSrcEl, this.nextSibling);
                    } else {
                        parent.insertBefore(dragSrcEl, this);
                    }
                    
                    // Update page numbers visually
                    updateExistingPageNumbers();
                    updatePageOrder();
                }
            }
        }

        this.classList.remove('drag-over');
        return false;
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        // Clean up drag-over classes
        const items = document.querySelectorAll('.drag-over');
        items.forEach(item => item.classList.remove('drag-over'));
    }

    // Delete existing page
    function deleteExistingPage(button) {
        const pageItem = button.closest('.draggable-item');
        const pageId = button.dataset.pageId;
        
        if (pageItem && pageId) {
            // Add to deleted pages array
            if (!deletedPages.includes(pageId)) {
                deletedPages.push(pageId);
            }
            
            // Mark as deleted visually
            pageItem.classList.add('deleted-page');
            pageItem.draggable = false;
            
            // Update hidden input
            deletedPagesInput.value = deletedPages.join(',');
            
            // Update page numbers after deletion
            updateExistingPageNumbers();
            
            // Update page order (exclude deleted pages)
            updatePageOrder();
        }
    }

    // Update existing page numbers visually
    function updateExistingPageNumbers() {
        const container = document.getElementById('currentPagesContainer');
        if (!container) return;
        
        const pages = container.querySelectorAll('.draggable-item:not(.deleted-page)');
        pages.forEach((page, index) => {
            const pageNumberElement = page.querySelector('.page-number');
            if (pageNumberElement) {
                pageNumberElement.textContent = `Page ${index + 1}`;
            }
        });
    }

    // Update page order
    function updatePageOrder() {
        const container = document.getElementById('currentPagesContainer');
        if (!container || !pageOrderInput) return;
        
        const pages = container.querySelectorAll('.draggable-item:not(.deleted-page)');
        const order = Array.from(pages).map(item => item.dataset.pageId).filter(id => id);
        pageOrderInput.value = order.join(',');
        
        console.log('Updated page order:', order); // Debug log
    }

    // PDF validation function (if needed)
    window.validatePdf = function(input) {
        const file = input.files[0];
        if (file && file.type !== 'application/pdf') {
            alert('Please select a valid PDF file.');
            input.value = '';
            return false;
        }
        return true;
    };

    // Initialize on page load
    initExistingPagesDragAndDrop();
    
    // Set initial page order on load
    updatePageOrder();
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        // Update page order one final time before submission
        updatePageOrder();
        console.log('Final page order on submit:', pageOrderInput.value); // Debug log
        console.log('Deleted pages on submit:', deletedPagesInput.value); // Debug log
    });
});
</script>
@endpush