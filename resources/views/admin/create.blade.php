@extends('layouts.app')

@section('title', 'Upload New E-Paper')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="epaper-title">UPLOAD NEW E-PAPER</h1>
                <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
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
            <h5 class="card-title mb-0"><i class="fas fa-upload"></i> E-Paper Upload Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">E-Paper Title *</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   value="{{ old('title') }}" required
                                   placeholder="e.g., Bhubaneswar Edition">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="publication_date" class="form-label">Publication Date *</label>
                            <input type="date" class="form-control" id="publication_date"
                                   name="publication_date" value="{{ old('publication_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="edition" class="form-label">Edition *</label>
                            <select class="form-select" id="edition" name="edition" required>
                                <option value="">Select Edition</option>
                                @foreach(['Bhubaneswar'] as $editionName)
                                    <option value="{{ $editionName }}" {{ old('edition') == $editionName ? 'selected' : '' }}>
                                        {{ $editionName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="pdf_file" class="form-label">Full PDF (Optional)</label>
                    <input type="file" class="form-control" id="pdf_file" name="pdf_file"
                           accept=".pdf" onchange="validatePdf(this)">
                    <div class="form-text">Upload the complete e-paper as PDF (Max: 100MB)</div>
                </div>

                <!-- Single Image Upload Section -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label mb-0">Page Images *</label>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addImageUpload()">
                                <i class="fas fa-plus"></i> Add Page Image
                            </button>
                            {{-- <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="addMultipleImages()">
                                <i class="fas fa-images"></i> Add Multiple
                            </button> --}}
                        </div>
                    </div>
                    
                    <div id="imageUploadsContainer">
                        <!-- Initial upload field -->
                        <div class="image-upload-item mb-3" data-index="0">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label small">Page 1:</label>
                                    <input type="file" class="form-control form-control-sm" 
                                           name="page_images[]" accept="image/*" 
                                           onchange="previewSingleImage(this)" required>
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
                                                onclick="moveUp(this)" title="Move Up" disabled>
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="moveDown(this)" title="Move Down">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="removeImageUpload(this)" title="Remove" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> Upload images in order (Page 1, Page 2, etc.). 
                        Supported formats: WEBP, JPG, JPEG, PNG
                    </div>
                </div>

                <!-- Hidden file input for multiple selection -->
                <input type="file" id="multipleFileInput" multiple accept="image/*" 
                       style="display: none;" onchange="handleMultipleFiles(this)">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <i class="fas fa-upload"></i> Upload E-Paper
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Progress Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload"></i> Uploading E-Paper</h5>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Uploading...</span>
                </div>
                <p>Please wait while we upload and process your e-paper...</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .image-upload-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .image-upload-item:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
</style>
@endpush

@push('scripts')
<script>
    let imageCount = 1;

    function validatePdf(input) {
        const file = input.files[0];
        if (file && file.size / 1024 / 1024 > 100) {
            alert('PDF file size should not exceed 100MB');
            input.value = '';
            return false;
        }
        return true;
    }

    function addImageUpload() {
        const container = document.getElementById('imageUploadsContainer');
        const newIndex = imageCount++;
        
        const newItem = document.createElement('div');
        newItem.className = 'image-upload-item mb-3 fade-in';
        newItem.dataset.index = newIndex;
        newItem.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label small">Page ${newIndex + 1}:</label>
                    <input type="file" class="form-control form-control-sm" 
                           name="page_images[]" accept="image/*" 
                           onchange="previewSingleImage(this)">
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
                                onclick="moveUp(this)" title="Move Up">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success" 
                                onclick="moveDown(this)" title="Move Down">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="removeImageUpload(this)" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(newItem);
        updateControls();
    }

    function addMultipleImages() {
        document.getElementById('multipleFileInput').click();
    }

    function handleMultipleFiles(input) {
        const files = Array.from(input.files);
        files.forEach(file => {
            addImageUpload();
            const items = document.querySelectorAll('.image-upload-item');
            const lastItem = items[items.length - 1];
            const fileInput = lastItem.querySelector('input[type="file"]');
            
            // Create a new FileList with just this file
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            
            // Trigger preview
            previewSingleImage(fileInput);
        });
        
        // Clear the hidden input
        input.value = '';
    }

    function previewSingleImage(input) {
        const item = input.closest('.image-upload-item');
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

    function removeImageUpload(button) {
        const item = button.closest('.image-upload-item');
        const items = document.querySelectorAll('.image-upload-item');
        
        // Don't allow removing if it's the only item
        if (items.length <= 1) {
            alert('At least one page image is required');
            return;
        }
        
        item.classList.add('slide-out');
        setTimeout(() => {
            item.remove();
            updatePageNumbers();
            updateControls();
        }, 200);
    }

    function moveUp(button) {
        const item = button.closest('.image-upload-item');
        const prev = item.previousElementSibling;
        if (prev) {
            item.parentNode.insertBefore(item, prev);
            updatePageNumbers();
            updateControls();
        }
    }

    function moveDown(button) {
        const item = button.closest('.image-upload-item');
        const next = item.nextElementSibling;
        if (next) {
            item.parentNode.insertBefore(next, item);
            updatePageNumbers();
            updateControls();
        }
    }

    function updatePageNumbers() {
        const items = document.querySelectorAll('.image-upload-item');
        items.forEach((item, index) => {
            const label = item.querySelector('label');
            label.textContent = `Page ${index + 1}:`;
        });
    }

    function updateControls() {
        const items = document.querySelectorAll('.image-upload-item');
        
        items.forEach((item, index) => {
            const upBtn = item.querySelector('.btn-outline-success:first-child');
            const downBtn = item.querySelector('.btn-outline-success:last-child');
            const removeBtn = item.querySelector('.btn-outline-danger');
            
            // Update move buttons
            upBtn.disabled = index === 0;
            downBtn.disabled = index === items.length - 1;
            
            // Update remove button (disable if only one item)
            removeBtn.disabled = items.length <= 1;
        });
    }

    function resetForm() {
        document.getElementById('uploadForm').reset();
        
        // Reset to single upload item
        const container = document.getElementById('imageUploadsContainer');
        container.innerHTML = `
            <div class="image-upload-item mb-3" data-index="0">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small">Page 1:</label>
                        <input type="file" class="form-control form-control-sm" 
                               name="page_images[]" accept="image/*" 
                               onchange="previewSingleImage(this)" required>
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
                                    onclick="moveUp(this)" title="Move Up" disabled>
                                <i class="fas fa-chevron-up"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success" 
                                    onclick="moveDown(this)" title="Move Down">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="removeImageUpload(this)" title="Remove" disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        imageCount = 1;
    }

    document.getElementById('uploadForm').addEventListener('submit', function (e) {
        // Validate that at least one image is uploaded
        const fileInputs = document.querySelectorAll('input[name="page_images[]"]');
        let hasImages = false;
        
        fileInputs.forEach(input => {
            if (input.files && input.files.length > 0) {
                hasImages = true;
            }
        });
        
        if (!hasImages) {
            e.preventDefault();
            alert('Please upload at least one page image');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
        document.getElementById('submitBtn').disabled = true;
    });

    // Auto-generate title functionality
    document.getElementById('edition').addEventListener('change', generateTitle);
    document.getElementById('publication_date').addEventListener('change', generateTitle);

    function generateTitle() {
        const titleField = document.getElementById('title');
        if (titleField.value.trim() !== '') return; 

        const edition = document.getElementById('edition').value;
        const date = document.getElementById('publication_date').value;

        if (edition && date) {
            const formattedDate = new Date(date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            titleField.value = `${edition} Edition`;
        }
    }

    // Initialize controls on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateControls();
    });
</script>
@endpush