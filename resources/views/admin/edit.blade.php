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
            <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
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
                            <label for="edition" class="form-label">Edition <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('edition') is-invalid @enderror" 
                                   id="edition" name="edition" value="{{ old('edition', $epaper->edition) }}" required>
                            @error('edition')
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

                <!-- Current Pages Management Section -->
                @if($epaper->pages->count() > 0)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Current Pages ({{ $epaper->pages->count() }})</label>
                            <div>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="addPageAfter()">
                                    <i class="fas fa-plus"></i> Insert Page
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPageAtEnd()">
                                    <i class="fas fa-plus-circle"></i> Add to End
                                </button>
                            </div>
                        </div>
                        
                        <div id="currentPagesContainer">
                            @foreach($epaper->pages->sortBy('page_number') as $index => $page)
                                <div class="existing-page-item mb-3" data-page-id="{{ $page->id }}" data-original-order="{{ $index }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <label class="form-label small">Page {{ $index + 1 }}:</label>
                                            <div class="page-thumbnail">
                                                <img src="{{ asset('storage/' . $page->thumbnail_path) }}" 
                                                     alt="Page {{ $page->page_number }}"
                                                     style="width: 100%; height: 60px; object-fit: cover; border-radius: 4px;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="page-info">
                                                <small class="text-muted d-block">Current page</small>
                                                <small class="text-info">Original: Page {{ $page->page_number }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="file" class="form-control form-control-sm page-replacement" 
                                                   name="page_replacements[{{ $page->id }}]" 
                                                   accept="image/*" 
                                                   onchange="previewReplacement(this, {{ $page->id }})"
                                                   style="display: none;">
                                            <button type="button" class="btn btn-outline-warning btn-sm" 
                                                    onclick="this.previousElementSibling.click()">
                                                <i class="fas fa-edit"></i> Replace Image
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="movePageUp(this)" title="Move Up">
                                                    <i class="fas fa-chevron-up"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="movePageDown(this)" title="Move Down">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="deletePage(this)" title="Delete Page">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Pages Section -->
                <div class="mb-4" id="newPagesSection" style="{{ $epaper->pages->count() > 0 ? 'display: none;' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label mb-0">
                            @if($epaper->pages->count() > 0)
                                Replace All Pages
                            @else
                                Page Images *
                            @endif
                        </label>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addNewPageUpload()">
                                <i class="fas fa-plus"></i> Add Page
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addMultipleNewPages()">
                                <i class="fas fa-images"></i> Add Multiple
                            </button>
                            @if($epaper->pages->count() > 0)
                                <button type="button" class="btn btn-outline-info btn-sm ms-2" onclick="toggleNewPagesSection()">
                                    <i class="fas fa-times"></i> Cancel Replace
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div id="newPagesContainer">
                        @if($epaper->pages->count() == 0)
                            <!-- Initial upload field for new e-papers -->
                            <div class="new-page-item mb-3" data-index="0">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label small">Page 1:</label>
                                        <input type="file" class="form-control form-control-sm" 
                                               name="new_page_images[]" accept="image/*" 
                                               onchange="previewNewPageImage(this)" required>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="image-preview-container" style="height: 80px; display: none;">
                                            <img class="preview-img" src="" alt="Preview" 
                                                 style="height: 100%; width: auto; object-fit: cover; border-radius: 4px;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="moveNewPageUp(this)" title="Move Up" disabled>
                                                <i class="fas fa-chevron-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="moveNewPageDown(this)" title="Move Down">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="removeNewPageUpload(this)" title="Remove" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="form-text">
                        @if($epaper->pages->count() > 0)
                            <i class="fas fa-warning text-warning"></i> 
                            <strong>Warning:</strong> Adding new images will replace ALL existing pages.
                        @else
                            <i class="fas fa-info-circle"></i> Upload images in order. Supported formats: WEBP, JPG, JPEG, PNG
                        @endif
                    </div>
                </div>

                @if($epaper->pages->count() > 0)
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>Options:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Manage existing pages:</strong> Reorder, replace individual images, or delete pages</li>
                                <li><strong>Replace all pages:</strong> 
                                    <button type="button" class="btn btn-link p-0" onclick="showNewPagesSection()">
                                        Click here to upload completely new set of pages
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Hidden file input for multiple selection -->
                <input type="file" id="multipleNewPageInput" multiple accept="image/*" 
                       style="display: none;" onchange="handleMultipleNewPages(this)">

                <!-- Hidden inputs for tracking changes -->
                <input type="hidden" name="page_order" id="page_order" value="">
                <input type="hidden" name="deleted_pages" id="deleted_pages" value="">
                <input type="hidden" name="replace_all_pages" id="replace_all_pages" value="0">

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
    .existing-page-item, .new-page-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .existing-page-item:hover, .new-page-item:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .existing-page-item.deleted {
        background: #fee;
        border-color: #f5c6cb;
        opacity: 0.6;
    }
    
    .existing-page-item.deleted::after {
        content: ' (WILL BE DELETED)';
        color: #dc3545;
        font-weight: bold;
        font-size: 12px;
    }
    
    .page-thumbnail {
        border: 2px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .page-info {
        padding: 5px 0;
    }
    
    .image-preview-container {
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px dashed #dee2e6;
        border-radius: 4px;
        background: white;
    }
    
    .preview-img {
        max-width: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .slide-out {
        animation: slideOut 0.2s ease-out forwards;
    }
    
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(-20px); }
    }

    #newPagesSection {
        border: 2px dashed #007bff;
        border-radius: 8px;
        padding: 20px;
        background: rgba(0, 123, 255, 0.02);
    }
</style>
@endpush

@push('scripts')
<script>
    let newPageCount = 0;
    let deletedPages = [];

    function validatePdf(input) {
        const file = input.files[0];
        if (file && file.size / 1024 / 1024 > 100) {
            alert('PDF file size should not exceed 100MB');
            input.value = '';
            return false;
        }
        return true;
    }

    // New Pages Management
    function showNewPagesSection() {
        document.getElementById('newPagesSection').style.display = 'block';
        document.getElementById('replace_all_pages').value = '1';
        
        // Add initial upload if container is empty
        const container = document.getElementById('newPagesContainer');
        if (container.children.length === 0) {
            addNewPageUpload();
        }
    }

    function toggleNewPagesSection() {
        const section = document.getElementById('newPagesSection');
        if (section.style.display === 'none') {
            showNewPagesSection();
        } else {
            section.style.display = 'none';
            document.getElementById('replace_all_pages').value = '0';
            // Clear all new page uploads
            document.getElementById('newPagesContainer').innerHTML = '';
            newPageCount = 0;
        }
    }

    function addNewPageUpload() {
        const container = document.getElementById('newPagesContainer');
        const newIndex = newPageCount++;
        
        const newItem = document.createElement('div');
        newItem.className = 'new-page-item mb-3 fade-in';
        newItem.dataset.index = newIndex;
        newItem.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label small">Page ${newIndex + 1}:</label>
                    <input type="file" class="form-control form-control-sm" 
                           name="new_page_images[]" accept="image/*" 
                           onchange="previewNewPageImage(this)">
                </div>
                <div class="col-md-6">
                    <div class="image-preview-container" style="height: 80px; display: none;">
                        <img class="preview-img" src="" alt="Preview" 
                             style="height: 100%; width: auto; object-fit: cover; border-radius: 4px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-success" 
                                onclick="moveNewPageUp(this)" title="Move Up">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success" 
                                onclick="moveNewPageDown(this)" title="Move Down">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="removeNewPageUpload(this)" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(newItem);
        updateNewPageControls();
    }

    function addMultipleNewPages() {
        document.getElementById('multipleNewPageInput').click();
    }

    function handleMultipleNewPages(input) {
        const files = Array.from(input.files);
        files.forEach(file => {
            addNewPageUpload();
            const items = document.querySelectorAll('.new-page-item');
            const lastItem = items[items.length - 1];
            const fileInput = lastItem.querySelector('input[type="file"]');
            
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            
            previewNewPageImage(fileInput);
        });
        
        input.value = '';
    }

    function previewNewPageImage(input) {
        const item = input.closest('.new-page-item');
        const previewContainer = item.querySelector('.image-preview-container');
        const previewImg = item.querySelector('.preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.style.display = 'none';
        }
    }

    function removeNewPageUpload(button) {
        const item = button.closest('.new-page-item');
        const items = document.querySelectorAll('.new-page-item');
        
        if (items.length <= 1 && document.getElementById('replace_all_pages').value === '1') {
            alert('At least one page image is required when replacing all pages');
            return;
        }
        
        item.classList.add('slide-out');
        setTimeout(() => {
            item.remove();
            updateNewPageNumbers();
            updateNewPageControls();
        }, 200);
    }

    function moveNewPageUp(button) {
        const item = button.closest('.new-page-item');
        const prev = item.previousElementSibling;
        if (prev) {
            item.parentNode.insertBefore(item, prev);
            updateNewPageNumbers();
            updateNewPageControls();
        }
    }

    function moveNewPageDown(button) {
        const item = button.closest('.new-page-item');
        const next = item.nextElementSibling;
        if (next) {
            item.parentNode.insertBefore(next, item);
            updateNewPageNumbers();
            updateNewPageControls();
        }
    }

    function updateNewPageNumbers() {
        const items = document.querySelectorAll('.new-page-item');
        items.forEach((item, index) => {
            const label = item.querySelector('label');
            label.textContent = `Page ${index + 1}:`;
        });
    }

    function updateNewPageControls() {
        const items = document.querySelectorAll('.new-page-item');
        
        items.forEach((item, index) => {
            const upBtn = item.querySelector('.btn-outline-success:first-child');
            const downBtn = item.querySelector('.btn-outline-success:last-child');
            const removeBtn = item.querySelector('.btn-outline-danger');
            
            upBtn.disabled = index === 0;
            downBtn.disabled = index === items.length - 1;
            removeBtn.disabled = items.length <= 1 && document.getElementById('replace_all_pages').value === '1';
        });
    }

    // Existing Pages Management
    function previewReplacement(input, pageId) {
        const item = input.closest('.existing-page-item');
        const thumbnail = item.querySelector('.page-thumbnail img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                thumbnail.src = e.target.result;
                thumbnail.style.border = '2px solid #28a745';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deletePage(button) {
        const item = button.closest('.existing-page-item');
        const pageId = item.dataset.pageId;
        
        if (confirm('Are you sure you want to delete this page?')) {
            if (!deletedPages.includes(pageId)) {
                deletedPages.push(pageId);
            }
            
            item.classList.add('deleted');
            button.disabled = true;
            
            updateDeletedPagesInput();
            updateExistingPageNumbers();
        }
    }

    function movePageUp(button) {
        const item = button.closest('.existing-page-item');
        const prev = item.previousElementSibling;
        if (prev && !prev.classList.contains('deleted') && !item.classList.contains('deleted')) {
            item.parentNode.insertBefore(item, prev);
            updateExistingPageNumbers();
            updateExistingPageOrder();
        }
    }

    function movePageDown(button) {
        const item = button.closest('.existing-page-item');
        const next = item.nextElementSibling;
        if (next && !next.classList.contains('deleted') && !item.classList.contains('deleted')) {
            item.parentNode.insertBefore(next, item);
            updateExistingPageNumbers();
            updateExistingPageOrder();
        }
    }

    function updateExistingPageNumbers() {
        const items = document.querySelectorAll('.existing-page-item:not(.deleted)');
        items.forEach((item, index) => {
            const label = item.querySelector('label');
            label.textContent = `Page ${index + 1}:`;
        });
    }

    function updateExistingPageOrder() {
        const items = document.querySelectorAll('.existing-page-item:not(.deleted)');
        const order = Array.from(items).map(item => item.dataset.pageId);
        document.getElementById('page_order').value = order.join(',');
    }

    function updateDeletedPagesInput() {
        document.getElementById('deleted_pages').value = deletedPages.join(',');
    }

    // Insert page functionality
    function addPageAfter() {
        // This would need to be implemented based on your specific requirements
        alert('Insert page functionality - would open a dialog to select where to insert and upload new page');
    }

    function addPageAtEnd() {
        addNewPageUpload();
        showNewPagesSection();
    }

    // Form submission
    document.getElementById('editForm').addEventListener('submit', function(e) {
        updateExistingPageOrder();
        updateDeletedPagesInput();
        
        // Validation
        const hasExistingPages = document.querySelectorAll('.existing-page-item:not(.deleted)').length > 0;
        const hasNewPages = document.querySelectorAll('.new-page-item input[type="file"]').length > 0;
        const replaceAllPages = document.getElementById('replace_all_pages').value === '1';
        
        if (!hasExistingPages && !hasNewPages) {
            e.preventDefault();
            alert('E-paper must have at least one page.');
            return;
        }
        
        if (replaceAllPages) {
            // Check if all new page inputs have files
            const newPageInputs = document.querySelectorAll('.new-page-item input[type="file"]');
            let allHaveFiles = true;
            newPageInputs.forEach(input => {
                if (!input.files || input.files.length === 0) {
                    allHaveFiles = false;
                }
            });
            
            if (!allHaveFiles) {
                e.preventDefault();
                alert('Please upload images for all new pages or cancel the "Replace All Pages" option.');
                return;
            }
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateExistingPageOrder();
        
        // Set up initial new page count if there are existing new page items
        newPageCount = document.querySelectorAll('.new-page-item').length;
        updateNewPageControls();
    });
</script>
@endpush