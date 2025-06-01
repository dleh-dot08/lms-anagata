@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="bi bi-pencil-square fs-2 text-dark"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold">Edit Catatan Pembelajaran</h3>
                                <p class="mb-0 fs-5 opacity-90">Pertemuan ke-{{ $meeting->pertemuan }}</p>
                                <small class="opacity-75">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('l, d F Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="bg-dark bg-opacity-20 rounded p-2">
                                <small class="d-block mb-1">Progress</small>
                                <div class="progress" style="height: 6px; width: 120px;">
                                    <div class="progress-bar bg-white" id="formProgress" style="width: 0%"></div>
                                </div>
                                <small class="d-block mt-1" id="lastModified">
                                    <i class="bi bi-clock me-1"></i>
                                    Terakhir diubah: {{ $note->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting Info Card -->
    @if($meeting->judul)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bookmark-fill text-warning me-3 fs-4"></i>
                        <div>
                            <h5 class="mb-0 fw-semibold">{{ $meeting->judul }}</h5>
                            <small class="text-muted">Topik Pertemuan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Change History Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(90deg, #e3f2fd 0%, #f3e5f5 100%);">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-info me-2 fs-5"></i>
                    <div>
                        <strong>Mode Edit Aktif</strong><br>
                        <small>Perubahan akan disimpan otomatis sebagai draft. Klik "Update Catatan" untuk menyimpan secara permanen.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <form action="{{ route('mentor.notes.update', $meeting->id) }}" method="POST" id="editNoteForm">
                @csrf
                @method('PUT')
                
                @php
                    $fields = [
                        'materi' => [
                            'label' => 'Materi yang Disampaikan',
                            'icon' => 'bi-book',
                            'color' => 'primary',
                            'placeholder' => 'Jelaskan materi apa saja yang telah disampaikan pada pertemuan ini...',
                            'rows' => 3
                        ],
                        'project' => [
                            'label' => 'Project yang Dikerjakan',
                            'icon' => 'bi-folder2-open',
                            'color' => 'success',
                            'placeholder' => 'Deskripsikan project atau tugas praktik yang dikerjakan siswa...',
                            'rows' => 3
                        ],
                        'sikap_siswa' => [
                            'label' => 'Sikap Siswa',
                            'icon' => 'bi-people',
                            'color' => 'info',
                            'placeholder' => 'Bagaimana sikap dan antusiasme siswa selama pembelajaran...',
                            'rows' => 2
                        ],
                        'hambatan' => [
                            'label' => 'Hambatan',
                            'icon' => 'bi-exclamation-triangle',
                            'color' => 'warning',
                            'placeholder' => 'Kendala atau hambatan yang dihadapi selama pembelajaran...',
                            'rows' => 2
                        ],
                        'solusi' => [
                            'label' => 'Solusi',
                            'icon' => 'bi-lightbulb',
                            'color' => 'success',
                            'placeholder' => 'Solusi yang diterapkan untuk mengatasi hambatan...',
                            'rows' => 2
                        ],
                        'masukan' => [
                            'label' => 'Masukan',
                            'icon' => 'bi-chat-left-text',
                            'color' => 'secondary',
                            'placeholder' => 'Saran atau masukan untuk pembelajaran selanjutnya...',
                            'rows' => 2
                        ],
                        'lain_lain' => [
                            'label' => 'Lain-lain',
                            'icon' => 'bi-three-dots',
                            'color' => 'dark',
                            'placeholder' => 'Catatan tambahan atau hal lain yang perlu dicatat...',
                            'rows' => 2
                        ]
                    ];
                @endphp

                <div class="row g-4">
                    @foreach($fields as $name => $field)
                    <div class="col-lg-6 col-12">
                        <div class="card border-0 shadow-sm form-card h-100 @if(!empty(old($name, $note->$name))) active @endif">
                            <div class="card-header border-0 bg-{{ $field['color'] }} text-white py-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper me-3">
                                        <i class="bi {{ $field['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold">{{ $field['label'] }}</h6>
                                    </div>
                                    {{-- <div class="d-flex align-items-center">
                                        @if(!empty(old($name, $note->$name)))
                                            <span class="badge bg-white text-{{ $field['color'] }} me-2">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </span>
                                        @endif
                                        <div class="form-check form-switch">
                                            <input class="form-check-input bg-white" type="checkbox" 
                                                   id="toggle_{{ $name }}" 
                                                   @if(!empty(old($name, $note->$name))) checked @endif
                                                   onchange="toggleField('{{ $name }}')">
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="form-group">
                                    <textarea 
                                        name="{{ $name }}" 
                                        id="{{ $name }}" 
                                        class="form-control border-0 shadow-none resize-vertical" 
                                        rows="{{ $field['rows'] }}"
                                        placeholder="{{ $field['placeholder'] }}"
                                        oninput="updateProgress(); updateCharCount('{{ $name }}'); trackChanges('{{ $name }}')"
                                        style="background-color: #f8f9fa; border-radius: 8px;"
                                    >{{ old($name, $note->$name) }}</textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <span id="char_count_{{ $name }}">0</span> karakter
                                        </small>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted" id="word_count_{{ $name }}">
                                                0 kata
                                            </small>
                                            <span id="change_indicator_{{ $name }}" class="badge bg-transparent" style="display: none;">
                                                <i class="bi bi-pencil text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Comparison Section -->
                <div class="row mt-5" id="changesSection" style="display: none;">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-warning text-dark py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <h6 class="mb-0 fw-semibold">Perubahan Terdeteksi</h6>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted mb-3">Berikut adalah ringkasan perubahan yang akan disimpan:</p>
                                <div id="changesList"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold">Status Pengisian</h6>
                                        <small class="text-muted">
                                            <span id="filled_count">0</span> dari {{ count($fields) }} bagian terisi
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('mentor.notes.show', $meeting->id) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-1"></i>
                                            Batal
                                        </a>
                                        <button type="button" class="btn btn-outline-primary" onclick="saveAsDraft()">
                                            <i class="bi bi-cloud-arrow-up me-1"></i>
                                            Simpan Draft
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="resetToOriginal()">
                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                            Reset
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg" id="updateBtn">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Update Catatan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .form-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .form-card.active {
        border: 2px solid #28a745;
        transform: scale(1.02);
    }
    
    .form-card.changed {
        border: 2px solid #ffc107;
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.3) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .icon-wrapper {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
    }
    
    .form-control {
        transition: all 0.3s ease;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .form-control:focus {
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1) !important;
        transform: scale(1.01);
    }
    
    .form-check-input:checked {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
    }
    
    .resize-vertical {
        resize: vertical;
        min-height: 80px;
        max-height: 200px;
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(45deg, #218838, #17a2b8);
    }
    
    .change-highlight {
        background: linear-gradient(90deg, transparent 0%, #fff3cd 50%, transparent 100%);
        animation: highlight 2s ease-in-out;
    }
    
    @keyframes highlight {
        0%, 100% { background: transparent; }
        50% { background: linear-gradient(90deg, transparent 0%, #fff3cd 50%, transparent 100%); }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .pulse {
        animation: pulse 0.6s ease-in-out;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>

<!-- JavaScript -->
<script>
    let totalFields = {{ count($fields) }};
    let filledFields = 0;
    let originalData = {};
    let hasChanges = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Store original data
        storeOriginalData();
        
        // Initialize progress and char counts
        updateProgress();
        updateAllCharCounts();
        
        // Load draft if exists
        loadDraft();
        
        // Animate cards on load
        const cards = document.querySelectorAll('.form-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });

    function storeOriginalData() {
        const fields = @json(array_keys($fields));
        fields.forEach(field => {
            const textarea = document.getElementById(field);
            originalData[field] = textarea.value;
        });
    }

    function updateProgress() {
        filledFields = 0;
        const textareas = document.querySelectorAll('textarea');
        
        textareas.forEach(textarea => {
            if (textarea.value.trim() !== '') {
                filledFields++;
            }
        });
        
        const percentage = (filledFields / totalFields) * 100;
        document.getElementById('formProgress').style.width = percentage + '%';
        document.getElementById('filled_count').textContent = filledFields;
    }

    function updateCharCount(fieldName) {
        const textarea = document.getElementById(fieldName);
        const text = textarea.value;
        const charCount = text.length;
        const wordCount = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
        
        document.getElementById('char_count_' + fieldName).textContent = charCount;
        document.getElementById('word_count_' + fieldName).textContent = wordCount;
        
        // Highlight active card
        const card = textarea.closest('.form-card');
        if (text.trim() !== '') {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    }

    function updateAllCharCounts() {
        const fields = @json(array_keys($fields));
        fields.forEach(field => {
            updateCharCount(field);
        });
    }

    function trackChanges(fieldName) {
        const textarea = document.getElementById(fieldName);
        const currentValue = textarea.value;
        const originalValue = originalData[fieldName] || '';
        const hasChanged = currentValue !== originalValue;
        
        const card = textarea.closest('.form-card');
        const indicator = document.getElementById('change_indicator_' + fieldName);
        
        if (hasChanged) {
            card.classList.add('changed');
            indicator.style.display = 'inline-block';
        } else {
            card.classList.remove('changed');
            indicator.style.display = 'none';
        }
        
        // Check if any field has changes
        checkOverallChanges();
    }

    function checkOverallChanges() {
        const fields = @json(array_keys($fields));
        hasChanges = false;
        
        fields.forEach(field => {
            const textarea = document.getElementById(field);
            const currentValue = textarea.value;
            const originalValue = originalData[field] || '';
            
            if (currentValue !== originalValue) {
                hasChanges = true;
            }
        });
        
        const changesSection = document.getElementById('changesSection');
        const updateBtn = document.getElementById('updateBtn');
        
        if (hasChanges) {
            changesSection.style.display = 'block';
            updateBtn.classList.add('pulse');
            updateChangesList();
        } else {
            changesSection.style.display = 'none';
            updateBtn.classList.remove('pulse');
        }
    }

    function updateChangesList() {
        const fields = @json($fields);
        const changesList = document.getElementById('changesList');
        let changesHtml = '';
        
        Object.keys(fields).forEach(field => {
            const textarea = document.getElementById(field);
            const currentValue = textarea.value;
            const originalValue = originalData[field] || '';
            
            if (currentValue !== originalValue) {
                changesHtml += `
                    <div class="mb-2 p-2 border-start border-warning border-3 bg-light">
                        <strong class="text-${fields[field].color}">
                            <i class="bi ${fields[field].icon} me-1"></i>
                            ${fields[field].label}
                        </strong>
                        <div class="mt-1">
                            <small class="text-muted">Panjang: ${originalValue.length} → ${currentValue.length} karakter</small>
                        </div>
                    </div>
                `;
            }
        });
        
        changesList.innerHTML = changesHtml || '<p class="text-muted">Tidak ada perubahan.</p>';
    }

    function toggleField(fieldName) {
        const textarea = document.getElementById(fieldName);
        const toggle = document.getElementById('toggle_' + fieldName);
        
        if (toggle.checked) {
            textarea.focus();
        } else {
            textarea.value = '';
            updateCharCount(fieldName);
            updateProgress();
            trackChanges(fieldName);
        }
    }

    function resetToOriginal() {
        if (confirm('Apakah Anda yakin ingin mengembalikan semua perubahan ke data asli?')) {
            const fields = @json(array_keys($fields));
            fields.forEach(field => {
                const textarea = document.getElementById(field);
                textarea.value = originalData[field] || '';
                updateCharCount(field);
                trackChanges(field);
                
                const toggle = document.getElementById('toggle_' + field);
                toggle.checked = textarea.value.trim() !== '';
            });
            
            updateProgress();
            localStorage.removeItem('editNoteDraft_{{ $meeting->id }}');
        }
    }

    function saveAsDraft() {
        // Jangan auto-save jika sedang submit
        if (isSubmitting) return;
        
        const formData = new FormData(document.getElementById('editNoteForm'));
        const draftData = {};
        
        for (let [key, value] of formData.entries()) {
            if (key !== '_token' && key !== '_method') {
                draftData[key] = value;
            }
        }
        
        localStorage.setItem('editNoteDraft_{{ $meeting->id }}', JSON.stringify(draftData));
        
        // Show success feedback hanya jika dipanggil manual (bukan auto-save)
        if (event && event.target) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check me-1"></i>Tersimpan!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        }
    }

    function loadDraft() {
        const draftData = localStorage.getItem('editNoteDraft_{{ $meeting->id }}');
        if (draftData) {
            const data = JSON.parse(draftData);
            
            Object.keys(data).forEach(key => {
                const textarea = document.getElementById(key);
                if (textarea && data[key]) {
                    textarea.value = data[key];
                    updateCharCount(key);
                    trackChanges(key);
                    document.getElementById('toggle_' + key).checked = true;
                }
            });
            
            updateProgress();
        }
    }

    let isSubmitting = false;

    // Form validation
    document.getElementById('editNoteForm').addEventListener('submit', function(e) {
        if (!hasChanges) {
            e.preventDefault();
            alert('Tidak ada perubahan yang perlu disimpan!');
            return;
        }
        
        // Set flag bahwa form sedang disubmit
        isSubmitting = true;
        
        // Clear draft on successful submit
        localStorage.removeItem('editNoteDraft_{{ $meeting->id }}');
        
        // Update button state
        const updateBtn = document.getElementById('updateBtn');
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
    });

    // Auto-save draft every 30 seconds
    setInterval(() => {
        if (hasChanges && !isSubmitting) {
            saveAsDraft();
        }
    }, 30000);

    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        // Jangan tampilkan warning jika sedang submit form
        if (hasChanges && !isSubmitting) {
            e.preventDefault();
            e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        }
    });
</script>

@endsection