@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 text-primary">
                                    <i class="bi bi-pencil-square fs-2"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-white">Buat Catatan Pembelajaran</h3>
                                <p class="mb-0 fs-5 opacity-90">Pertemuan ke-{{ $meeting->pertemuan }}</p>
                                <small class="opacity-75">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d F Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="bg-white bg-opacity-20 rounded p-2">
                                <small class="d-block mb-1 text-primary">Progress</small>
                                <div class="progress" style="height: 6px; width: 120px;">
                                    <div class="progress-bar bg-white" id="formProgress" style="width: 0%"></div>
                                </div>
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
                        <i class="bi bi-bookmark-fill text-primary me-3 fs-4"></i>
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

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <form action="{{ route('mentor.notes.store', $meeting->id) }}" method="POST" id="noteForm">
                @csrf
                
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
                        <div class="card border-0 shadow-sm form-card h-100">
                            <div class="card-header border-0 bg-{{ $field['color'] }} text-white py-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper me-3">
                                        <i class="bi {{ $field['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold text-white">{{ $field['label'] }}</h6>
                                    </div>
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
                                        oninput="updateProgress(); updateCharCount('{{ $name }}')"
                                        style="background-color: #f8f9fa; border-radius: 8px;"
                                    ></textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <span id="char_count_{{ $name }}">0</span> karakter
                                        </small>
                                        <small class="text-muted" id="word_count_{{ $name }}">
                                            0 kata
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearAll()">
                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                            Reset
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="saveAsDraft()">
                                            <i class="bi bi-cloud-arrow-up me-1"></i>
                                            Simpan Draft
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Simpan Catatan
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
        border: 2px solid #007bff;
        transform: scale(1.02);
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
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1) !important;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.5s ease;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s ease-in-out;
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

document.addEventListener('DOMContentLoaded', function() {
    loadDraft();
    updateProgress();

    // Animasi kartu
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

    const card = textarea.closest('.form-card');
    if (text.trim() !== '') {
        card.classList.add('active');
    } else {
        card.classList.remove('active');
    }

    updateProgress();
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
    }
}

function clearAll() {
    if (confirm('Apakah Anda yakin ingin menghapus semua isian?')) {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.value = '';
            updateCharCount(textarea.id);
        });
        const toggles = document.querySelectorAll('.form-check-input');
        toggles.forEach(toggle => {
            toggle.checked = false;
        });
        updateProgress();
        localStorage.removeItem('noteDraft_{{ $meeting->id }}');
    }
}

function saveAsDraft(event) {
    event = event || window.event;
    const formData = new FormData(document.getElementById('noteForm'));
    const draftData = {};
    for (let [key, value] of formData.entries()) {
        if (key !== '_token') draftData[key] = value;
    }
    localStorage.setItem('noteDraft_{{ $meeting->id }}', JSON.stringify(draftData));

    const btn = event.target.closest('button');
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

function loadDraft() {
    const draftData = localStorage.getItem('noteDraft_{{ $meeting->id }}');
    if (draftData) {
        const data = JSON.parse(draftData);
        Object.keys(data).forEach(key => {
            const textarea = document.getElementById(key);
            const toggle = document.getElementById('toggle_' + key);
            if (textarea && data[key]) {
                textarea.value = data[key];
                if (toggle) toggle.checked = true;
                updateCharCount(key);
            }
        });
        updateProgress();
    }
}

// Validasi submit
document.getElementById('noteForm').addEventListener('submit', function(e) {
    const textareas = document.querySelectorAll('textarea');
    let allFieldsFilled = true;
    textareas.forEach(textarea => {
        if (textarea.value.trim() === '') {
            allFieldsFilled = false;
            // Optional: Add a visual cue to the empty field
            textarea.style.border = '1px solid red';
            textarea.closest('.form-card').classList.add('shake');
        }
    });

    if (!allFieldsFilled) {
        e.preventDefault();
        // Show a more user-friendly modal or toast notification
        // For example, using Bootstrap modal if available
        // Or a simple custom modal
        const warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
        warningModal.show();

        // Remove shake animation after it plays
        textareas.forEach(textarea => {
            if (textarea.closest('.form-card').classList.contains('shake')) {
                setTimeout(() => {
                    textarea.closest('.form-card').classList.remove('shake');
                    textarea.style.border = ''; // Reset border
                }, 500);
            }
        });
    }
});
</script>

<!-- Warning Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningModalLabel">Peringatan!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Mohon isi semua bagian catatan sebelum menyimpan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Oke</button>
            </div>
        </div>
    </div>
</div>

@endsection