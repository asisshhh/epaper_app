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
                                   placeholder="e.g., Odisha Edition">
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
                            <label for="city" class="form-label">City *</label>
                            <select class="form-select" id="city" name="city" required>
                                <option value="">Select City</option>
                                @foreach(['Odisha', 'Ranchi', 'Delhi', 'Mumbai', 'Kolkata'] as $cityName)
                                    <option value="{{ $cityName }}" {{ old('city') == $cityName ? 'selected' : '' }}>
                                        {{ $cityName }}
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

                <div class="mb-3">
                    <label for="page_images" class="form-label">Page Images *</label>
                    <input type="file" class="form-control" id="page_images" name="page_images[]"
                           accept="image/*" multiple required onchange="previewImages(this)">
                    <div class="form-text">Upload individual page images (JPEG, PNG). Select multiple files in order.</div>
                </div>

                <!-- Image Preview -->
                <div id="imagePreview" class="mb-3" style="display: none;">
                    <label class="form-label">Selected Images Preview:</label>
                    <div id="previewContainer" class="row sortable-container"></div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> You can drag images to reorder them. Images will be processed in the order shown.
                        </small>
                    </div>
                </div>

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
    .sortable-container {
        min-height: 120px;
    }
    
    .draggable-item {
        cursor: move;
        transition: all 0.2s ease;
        user-select: none;
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
    .placeholder {
    transition: all 0.2s ease;
}

</style>
@endpush

@push('scripts')
<script>
    let filesArray = [];
    let draggedElement = null;
    let placeholder;

    function validatePdf(input) {
        const file = input.files[0];
        if (file && file.size / 1024 / 1024 > 100) {
            alert('PDF file size should not exceed 100MB');
            input.value = '';
            return false;
        }
        return true;
    }

    function previewImages(input) {
        const previewDiv = document.getElementById('imagePreview');
        const previewContainer = document.getElementById('previewContainer');
        previewContainer.innerHTML = '';

        if (input.files && input.files.length > 0) {
            filesArray = Array.from(input.files);
            previewDiv.style.display = 'block';
            renderImagePreviews();
        } else {
            previewDiv.style.display = 'none';
            filesArray = [];
        }
    }

    function renderImagePreviews() {
        const previewContainer = document.getElementById('previewContainer');
        previewContainer.innerHTML = '';

        filesArray.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 col-sm-3 col-4 mb-3 draggable-item';
                    col.draggable = true;
                    col.dataset.index = index;
                    col.innerHTML = `
                        <div class="card h-100 position-relative">
                            <div class="drag-handle">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;" />
                            <div class="page-number">Page ${index + 1}</div>
                        </div>
                    `;
                    
                    // Add drag event listeners
                    col.addEventListener('dragstart', handleDragStart);
                    col.addEventListener('dragover', handleDragOver);
                    col.addEventListener('drop', handleDrop);
                    col.addEventListener('dragend', handleDragEnd);
                    
                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function handleDragStart(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);

        // Create placeholder
        placeholder = document.createElement('div');
        placeholder.className = 'placeholder col-md-2 col-sm-3 col-4 mb-3';
        placeholder.style.height = this.offsetHeight + 'px';
        placeholder.style.border = '2px dashed #007bff';
        placeholder.style.background = 'rgba(0,123,255,0.05)';
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        const container = document.getElementById('previewContainer');
        if (this !== draggedElement && !this.classList.contains('placeholder')) {
            const rect = this.getBoundingClientRect();
            const offset = e.clientY - rect.top;
            if (offset > rect.height / 2) {
                container.insertBefore(placeholder, this.nextSibling);
            } else {
                container.insertBefore(placeholder, this);
            }
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();

        if (placeholder && placeholder.parentNode) {
            placeholder.parentNode.insertBefore(draggedElement, placeholder);
            placeholder.remove();
        }

        // Update filesArray order
        const items = document.querySelectorAll('#previewContainer .draggable-item');
        filesArray = Array.from(items).map(item => filesArray[parseInt(item.dataset.index)]);

        // Update numbering and file input
        updatePageNumbers();
        updateFileInput();
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        if (placeholder && placeholder.parentNode) {
            placeholder.remove();
        }
        draggedElement = null;
    }

    function updatePageNumbers() {
        const items = document.querySelectorAll('#previewContainer .draggable-item');
        items.forEach((item, index) => {
            item.dataset.index = index;
            const pageNum = item.querySelector('.page-number');
            if (pageNum) {
                pageNum.textContent = `Page ${index + 1}`;
            }
        });
    }

    function updateFileInput() {
        const fileInput = document.getElementById('page_images');
        const dt = new DataTransfer();
        
        filesArray.forEach(file => {
            dt.items.add(file);
        });
        
        fileInput.files = dt.files;
    }

    function resetForm() {
        document.getElementById('uploadForm').reset();
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('previewContainer').innerHTML = '';
        filesArray = [];
    }

    document.getElementById('uploadForm').addEventListener('submit', function (e) {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
        document.getElementById('submitBtn').disabled = true;
    });

    document.getElementById('city').addEventListener('change', generateTitle);
    document.getElementById('publication_date').addEventListener('change', generateTitle);

    function generateTitle() {
        const titleField = document.getElementById('title');
        if (titleField.value.trim() !== '') return; 

        const city = document.getElementById('city').value;
        const date = document.getElementById('publication_date').value;

        if (city && date) {
            const formattedDate = new Date(date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            titleField.value = `${city} Edition  `;
        }
    }
</script>
@endpush