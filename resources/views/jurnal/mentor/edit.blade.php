@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4"> {{-- Padding horizontal responsif --}}

    {{--- Header Section ---}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white py-4 px-4 px-md-5"> {{-- Padding horizontal responsif --}}
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between text-center text-md-start"> {{-- Layout responsif: column di mobile, row di desktop --}}
                        <div class="d-flex flex-column flex-md-row align-items-center mb-3 mb-md-0"> {{-- Bagian kiri header: icon dan teks --}}
                            <div class="me-md-3 mb-3 mb-md-0"> {{-- Margin untuk icon --}}
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 mx-auto d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;"> {{-- Memastikan icon di tengah dan ukuran tetap --}}
                                    <i class="bi bi-pencil-square fs-2 text-dark"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold fs-4 fs-md-3">Edit Catatan Pembelajaran</h3> {{-- Ukuran font responsif --}}
                                <p class="mb-0 fs-5 fs-md-5 opacity-90">Pertemuan ke-{{ $meeting->pertemuan }}</p> {{-- Ukuran font responsif --}}
                                <small class="opacity-75 d-block mt-1"> {{-- D-block agar selalu di baris baru di mobile --}}
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('l, d F Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="text-center text-md-end"> {{-- Bagian kanan header: progress --}}
                            <div class="bg-dark bg-opacity-20 rounded p-3 mx-auto" style="max-width: 180px;"> {{-- Padding dan max-width responsif --}}
                                <small class="d-block mb-1 fw-semibold">Progress Pengisian</small>
                                <div class="progress mx-auto" style="height: 8px; width: 120px; background-color: rgba(255,255,255,0.3);"> {{-- Tinggikan progress bar, tambahkan background --}}
                                    <div class="progress-bar bg-white" id="formProgress" style="width: 0%"></div>
                                </div>
                                <small class="d-block mt-2 opacity-75" id="lastModified"> {{-- Margin-top sedikit lebih besar --}}
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

    {{--- Meeting Info Card ---}}
    @if($meeting->judul)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light shadow-sm"> {{-- Tambah shadow-sm --}}
                <div class="card-body py-3 px-4 px-md-5"> {{-- Padding responsif --}}
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bookmark-fill text-warning me-3 fs-4"></i>
                        <div>
                            <h5 class="mb-0 fw-semibold fs-6 fs-md-5">{{ $meeting->judul }}</h5> {{-- Ukuran font responsif --}}
                            <small class="text-muted d-block mt-1">Topik Pertemuan</small> {{-- D-block agar selalu di baris baru di mobile --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{--- Change History Alert ---}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm py-3 px-4 px-md-5" style="background: linear-gradient(90deg, #e3f2fd 0%, #f3e5f5 100%);"> {{-- Padding responsif --}}
                <div class="d-flex flex-column flex-sm-row align-items-center"> {{-- Layout responsif: column di mobile, row di desktop --}}
                    <i class="bi bi-info-circle-fill text-info me-sm-2 mb-2 mb-sm-0 fs-5"></i> {{-- Margin dan font-size responsif --}}
                    <div class="text-center text-sm-start"> {{-- Text-align responsif --}}
                        <strong>Mode Edit Aktif</strong><br>
                        <small>Perubahan akan disimpan otomatis sebagai draft. Klik "Update Catatan" untuk menyimpan secara permanen.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--- Form Section ---}}
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

                <div class="row g-4"> {{-- Gutter antar kartu --}}
                    @foreach($fields as $name => $field)
                    <div class="col-lg-6 col-md-12 col-12"> {{-- Kolom akan mengambil 50% di lg, 100% di md dan sm --}}
                        <div class="card border-0 shadow-sm form-card h-100 @if(!empty(old($name, $note->$name))) active @endif">
                            <div class="card-header border-0 bg-{{ $field['color'] }} text-white py-3 px-4"> {{-- Padding responsif --}}
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper me-3">
                                        <i class="bi {{ $field['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold">{{ $field['label'] }}</h6>
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
                                        oninput="updateProgress(); updateCharCount('{{ $name }}'); trackChanges('{{ $name }}')"
                                        style="background-color: #f8f9fa; border-radius: 8px;"
                                    >{{ old($name, $note->$name) }}</textarea>
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-2 small text-muted"> {{-- Layout responsif untuk counter --}}
                                        <span>
                                            Karakter: <span id="char_count_{{ $name }}">0</span>
                                        </span>
                                        <div class="d-flex align-items-center gap-2 mt-1 mt-sm-0"> {{-- Margin responsif --}}
                                            <span>
                                                Kata: <span id="word_count_{{ $name }}">0</span>
                                            </span>
                                            <span id="change_indicator_{{ $name }}" class="badge bg-transparent" style="display: none;">
                                                <i class="bi bi-pencil-fill text-warning"></i> {{-- Ganti icon pensil --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{--- Comparison Section ---}}
                <div class="row mt-5" id="changesSection" style="display: none;">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-warning text-dark py-3 px-4"> {{-- Padding responsif --}}
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

                {{--- Action Buttons ---}}
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card border-0 bg-light shadow-sm"> {{-- Tambah shadow-sm --}}
                            <div class="card-body py-4 px-4 px-md-5"> {{-- Padding responsif --}}
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center"> {{-- Layout responsif untuk info dan tombol --}}
                                    <div class="mb-3 mb-md-0 text-center text-md-start"> {{-- Margin dan text-align responsif --}}
                                        <h6 class="mb-1 fw-semibold">Status Pengisian</h6>
                                        <small class="text-muted">
                                            <span id="filled_count">0</span> dari {{ count($fields) }} bagian terisi
                                        </small>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto"> {{-- Layout responsif untuk tombol --}}
                                        <a href="{{ route('mentor.notes.show', $meeting->id) }}" class="btn btn-outline-secondary py-2"> {{-- Padding responsif --}}
                                            <i class="bi bi-arrow-left me-1"></i>
                                            Batal
                                        </a>
                                        <button type="button" class="btn btn-outline-primary py-2" onclick="saveAsDraft(event)"> {{-- Padding responsif --}}
                                            <i class="bi bi-cloud-arrow-up me-1"></i>
                                            Simpan Draft
                                        </button>
                                        <button type="button" class="btn btn-outline-danger py-2" onclick="resetToOriginal()"> {{-- Padding responsif --}}
                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                            Reset
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg py-2" id="updateBtn"> {{-- Padding responsif --}}
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

{{--- Custom Styles ---}}
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
        border: 2px solid #28a745; /* Success color */
        transform: scale(1.01);
    }
    
    .form-card.changed {
        border: 2px solid #ffc107; /* Warning color */
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.3) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .icon-wrapper {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.25);
        border-radius: 8px;
    }
    
    .form-control {
        transition: all 0.3s ease;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .form-control:focus {
        background-color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important; /* Success color for focus shadow */
        transform: scale(1.005);
    }
    
    .resize-vertical {
        resize: vertical;
        min-height: 80px;
        max-height: 250px; /* Batas tinggi maksimal textarea */
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
        50% { transform: scale(1.03); } /* Sedikit mengurangi scale agar tidak terlalu besar */
    }
    
    .pulse {
        animation: pulse 0.6s ease-in-out infinite alternate; /* Tambah infinite alternate */
    }
    
    /* Media Queries for enhanced responsiveness */
    @media (max-width: 767.98px) { /* Untuk perangkat di bawah 768px (MD breakpoint) */
        .px-md-4, .px-md-5 {
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }

        .fs-4 { font-size: 1.25rem !important; }
        .fs-5 { font-size: 1.15rem !important; }
        .fs-6 { font-size: 1rem !important; }

        .d-flex.flex-column.flex-md-row {
            flex-direction: column !important;
        }

        .align-items-md-center {
            align-items: center !important;
        }

        .text-md-start, .text-md-end {
            text-align: center !important;
        }

        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .d-flex.flex-column.flex-sm-row {
            flex-direction: column !important; /* Tombol aksi menumpuk di mobile */
            width: 100% !important; /* Tombol aksi mengambil lebar penuh */
        }

        .btn {
            width: 100%; /* Pastikan tombol mengambil lebar penuh di mobile */
        }

        .mx-auto { /* Memastikan elemen di tengah di mobile */
            margin-left: auto !important;
            margin-right: auto !important;
        }
        
        .header-progress { /* Kelas tambahan untuk progress di header mobile */
            text-align: center;
            margin-top: 1rem; /* Jarak dari teks di atasnya */
        }
    }
</style>

{{--- JavaScript ---}}
<script>
    let totalFields = {{ count($fields) }};
    let filledFields = 0;
    let originalData = {};
    let hasChanges = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Store original data
        storeOriginalData();
        
        // Initialize progress and char counts for current data
        updateProgress();
        updateAllCharCounts();
        
        // Load draft if exists (override initial data)
        loadDraft();
        
        // Re-calculate progress and char counts after loading draft
        updateProgress();
        updateAllCharCounts();
        
        // Check for initial changes after loading draft
        checkOverallChanges();

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
            originalData[field] = textarea ? textarea.value : ''; // Pastikan textarea ada
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
        if (!textarea) return; // Exit if textarea doesn't exist
        
        const text = textarea.value;
        const charCount = text.length;
        const wordCount = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
        
        document.getElementById('char_count_' + fieldName).textContent = charCount;
        document.getElementById('word_count_' + fieldName).textContent = wordCount;
        
        // Highlight active card
        const card = textarea.closest('.form-card');
        if (card) { // Pastikan card ditemukan
            if (text.trim() !== '') {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
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
        if (!textarea) return;
        
        const currentValue = textarea.value;
        const originalValue = originalData[fieldName] || '';
        const hasChangedField = currentValue !== originalValue;
        
        const card = textarea.closest('.form-card');
        const indicator = document.getElementById('change_indicator_' + fieldName);
        
        if (card) {
            if (hasChangedField) {
                card.classList.add('changed');
                if (indicator) indicator.style.display = 'inline-block';
            } else {
                card.classList.remove('changed');
                if (indicator) indicator.style.display = 'none';
            }
        }
        
        // Check if any field has changes
        checkOverallChanges();
    }

    function checkOverallChanges() {
        const fields = @json(array_keys($fields));
        hasChanges = false;
        
        fields.forEach(field => {
            const textarea = document.getElementById(field);
            if (!textarea) return;
            
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
        let hasActualChanges = false;
        
        Object.keys(fields).forEach(field => {
            const textarea = document.getElementById(field);
            if (!textarea) return;
            
            const currentValue = textarea.value;
            const originalValue = originalData[field] || '';
            
            if (currentValue !== originalValue) {
                hasActualChanges = true;
                changesHtml += `
                    <div class="mb-2 p-2 border-start border-warning border-3 bg-light rounded">
                        <strong class="text-${fields[field].color}">
                            <i class="bi ${fields[field].icon} me-1"></i>
                            ${fields[field].label}
                        </strong>
                        <div class="mt-1 small text-muted">
                            ${originalValue.length > 0 ? 'Diubah' : 'Ditambahkan'}: ${originalValue.length} → ${currentValue.length} karakter
                        </div>
                    </div>
                `;
            }
        });
        
        changesList.innerHTML = hasActualChanges ? changesHtml : '<p class="text-muted mb-0">Tidak ada perubahan yang terdeteksi pada konten catatan Anda.</p>';
    }

    function resetToOriginal() {
        if (confirm('Apakah Anda yakin ingin mengembalikan semua perubahan ke data asli? Draft juga akan dihapus.')) {
            const fields = @json(array_keys($fields));
            fields.forEach(field => {
                const textarea = document.getElementById(field);
                if (textarea) {
                    textarea.value = originalData[field] || '';
                    updateCharCount(field);
                    trackChanges(field); // Ini akan menghapus kelas 'changed'
                }
            });
            
            updateProgress();
            localStorage.removeItem('editNoteDraft_{{ $meeting->id }}');
            checkOverallChanges(); // Pastikan section perubahan tersembunyi
        }
    }

    function saveAsDraft(event) {
        // Jangan auto-save jika sedang submit
        if (isSubmitting) return;
        
        // Cek apakah ada perubahan sebelum menyimpan draft
        checkOverallChanges(); // Ini akan memperbarui `hasChanges`
        if (!hasChanges) {
            // Jika tidak ada perubahan, tidak perlu menyimpan draft dan berikan feedback
            if (event && event.target) {
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                const originalClasses = btn.className;
                btn.innerHTML = '<i class="bi bi-info-circle me-1"></i> Tidak Ada Perubahan!';
                btn.classList.remove('btn-outline-primary', 'btn-success');
                btn.classList.add('btn-info');
                btn.disabled = true;

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.className = originalClasses;
                    btn.disabled = false;
                }, 2000);
            }
            return;
        }

        const formData = new FormData(document.getElementById('editNoteForm'));
        const draftData = {};
        
        for (let [key, value] of formData.entries()) {
            if (key !== '_token' && key !== '_method') { // Hindari token dan method dari draft
                draftData[key] = value;
            }
        }
        
        localStorage.setItem('editNoteDraft_{{ $meeting->id }}', JSON.stringify(draftData));
        
        // Show success feedback hanya jika dipanggil manual (bukan auto-save)
        if (event && event.target) {
            const btn = event.target.closest('button'); // Menggunakan closest('button') lebih robust
            const originalText = btn.innerHTML;
            const originalClasses = btn.className;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Tersimpan!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            btn.disabled = true; // Disable tombol sementara

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.className = originalClasses;
                btn.disabled = false; // Enable tombol kembali
            }, 2000);
        }
        // Update "Terakhir diubah" pada header
        document.getElementById('lastModified').innerHTML = '<i class="bi bi-clock me-1"></i> Terakhir diubah: Baru saja';
    }

    function loadDraft() {
        const draftData = localStorage.getItem('editNoteDraft_{{ $meeting->id }}');
        if (draftData) {
            const data = JSON.parse(draftData);
            
            Object.keys(data).forEach(key => {
                const textarea = document.getElementById(key);
                if (textarea && data[key]) { // Pastikan textarea ada dan ada data draft
                    textarea.value = data[key];
                    // trackChanges(key); // Tidak perlu di sini, akan dipanggil setelah semua draft dimuat
                }
            });
            // Setelah semua draft dimuat, panggil trackChanges untuk setiap field
            const fields = @json(array_keys($fields));
            fields.forEach(field => {
                trackChanges(field);
            });
        }
    }

    let isSubmitting = false;

    // Form validation
    document.getElementById('editNoteForm').addEventListener('submit', function(e) {
        // Cek apakah ada perubahan sama sekali sebelum submit
        checkOverallChanges(); // Pastikan `hasChanges` terbaru
        if (!hasChanges) {
            e.preventDefault();
            alert('Tidak ada perubahan yang terdeteksi untuk disimpan.');
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