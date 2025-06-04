@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4"> {{-- Sesuaikan padding horizontal untuk mobile --}}

    {{--- Header Section ---}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient">
                <div class="card-body text-white py-4 px-4 px-md-5"> {{-- Sesuaikan padding horizontal --}}
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between text-center text-md-start"> {{-- Atur flex-direction untuk mobile --}}
                        <div class="d-flex flex-column flex-md-row align-items-center mb-3 mb-md-0"> {{-- Atur flex-direction dan margin untuk mobile --}}
                            <div class="me-md-3 mb-3 mb-md-0"> {{-- Margin untuk icon --}}
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 text-primary mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;"> {{-- Pusat icon di mobile --}}
                                    <i class="bi bi-pencil-square fs-2"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-white fs-4 fs-md-3">Buat Catatan Pembelajaran</h3> {{-- Ukuran font responsif --}}
                                <p class="mb-0 fs-5 fs-md-5 opacity-90">Pertemuan ke-{{ $meeting->pertemuan }}</p> {{-- Ukuran font responsif --}}
                                <small class="opacity-75">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d F Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="text-center text-md-end"> {{-- Atur text-align responsif --}}
                            <div class="bg-dark bg-opacity-20 rounded p-3"> {{-- Sesuaikan padding --}}
                                <small class="d-block mb-1 text-primary fw-semibold">Progress Pengisian</small> {{-- Tambah fw-semibold --}}
                                <div class="progress mx-auto" style="height: 8px; width: 120px;"> {{-- Tinggikan progress bar sedikit --}}
                                    <div class="progress-bar bg-white" id="formProgress" style="width: 0%"></div>
                                </div>
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
                <div class="card-body py-3 px-4 px-md-5"> {{-- Sesuaikan padding --}}
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bookmark-fill text-primary me-3 fs-4"></i>
                        <div>
                            <h5 class="mb-0 fw-semibold fs-6 fs-md-5">{{ $meeting->judul }}</h5> {{-- Ukuran font responsif --}}
                            <small class="text-muted d-block mt-1">Topik Pertemuan</small> {{-- Tambah d-block agar di baris baru di mobile jika perlu --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{--- Form Section ---}}
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

                <div class="row g-4"> {{-- Gunakan g-4 untuk jarak antar kartu --}}
                    @foreach($fields as $name => $field)
                    <div class="col-lg-6 col-md-12 col-12"> {{-- Kolom akan mengambil setengah lebar di lg, penuh di md dan sm --}}
                        <div class="card border-0 shadow-sm form-card h-100"> {{-- h-100 agar semua kartu sama tinggi --}}
                            <div class="card-header border-0 bg-{{ $field['color'] }} text-white py-3 px-4"> {{-- Sesuaikan padding header --}}
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
                                        oninput="updateProgress(); updateCharCount('{{ $name }}');" {{-- Panggil updateCharCount --}}
                                        style="background-color: #f8f9fa; border-radius: 8px;"
                                    >{{ old($name, $note->$name ?? '') }}</textarea> {{-- Tambah old() dan nilai yang sudah ada jika mengedit --}}
                                    <div class="d-flex justify-content-between align-items-center mt-2 small text-muted"> {{-- Ubah font-size ke small --}}
                                        <span>
                                            Karakter: <span id="char_count_{{ $name }}">0</span>
                                        </span>
                                        <span>
                                            Kata: <span id="word_count_{{ $name }}">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{--- Action Buttons ---}}
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card border-0 bg-light shadow-sm"> {{-- Tambah shadow-sm --}}
                            <div class="card-body py-4 px-4 px-md-5"> {{-- Sesuaikan padding --}}
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center"> {{-- Atur flex-direction untuk mobile --}}
                                    <div class="mb-3 mb-md-0 text-center text-md-start"> {{-- Atur margin dan text-align --}}
                                        <h6 class="mb-1 fw-semibold">Status Pengisian Catatan</h6>
                                        <small class="text-muted">
                                            <span id="filled_count">0</span> dari {{ count($fields) }} bagian terisi
                                        </small>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto"> {{-- Atur flex-direction dan lebar untuk tombol --}}
                                        <button type="button" class="btn btn-outline-secondary py-2" onclick="clearAll()">
                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                            Reset Formulir
                                        </button>
                                        <button type="button" class="btn btn-outline-primary py-2" onclick="saveAsDraft(event)"> {{-- Pastikan event diteruskan --}}
                                            <i class="bi bi-cloud-arrow-up me-1"></i>
                                            Simpan Draft
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-lg py-2">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Kirim Catatan
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
        border: 2px solid var(--bs-primary); /* Menggunakan variabel Bootstrap */
        transform: scale(1.01); /* Sedikit diperkecil animasinya agar tidak terlalu besar */
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }

    .icon-wrapper {
        width: 38px; /* Sedikit diperbesar */
        height: 38px; /* Sedikit diperbesar */
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.25); /* Lebih solid */
        border-radius: 8px;
    }

    .form-control {
        transition: all 0.3s ease;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .form-control:focus {
        background-color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), .25) !important; /* Gunakan primary-rgb untuk shadow */
        transform: scale(1.005); /* Animasi fokus lebih halus */
    }

    /* .form-check-input:checked {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
    } */ /* Ini tidak relevan karena Anda tidak menggunakan checkbox toggle */

    .resize-vertical {
        resize: vertical;
        min-height: 80px;
        max-height: 250px; /* Batas tinggi maksimal textarea */
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
        background-color: rgba(255,255,255,0.3); /* Warna latar progress bar */
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

    /* Media Queries for enhanced responsiveness */
    @media (max-width: 767.98px) { /* Untuk perangkat di bawah 768px (MD breakpoint) */
        .px-md-4, .px-md-5 {
            padding-left: 1.5rem !important; /* Mengatur padding untuk mobile */
            padding-right: 1.5rem !important;
        }

        .fs-4 { font-size: 1.25rem !important; } /* Mengatur ukuran font header */
        .fs-5 { font-size: 1.15rem !important; }

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

        .d-flex.flex-sm-row {
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
    }
</style>

{{--- JavaScript ---}}
<script>
let totalFields = {{ count($fields) }};
let filledFields = 0;

document.addEventListener('DOMContentLoaded', function() {
    loadDraft();
    updateProgress();

    // Animasi kartu saat dimuat
    const cards = document.querySelectorAll('.form-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
            // Inisialisasi char/word count setelah animasi selesai dan nilai dimuat
            updateCharCount(card.querySelector('textarea').id);
        }, index * 100);
    });
});

// Memperbarui progress bar dan status pengisian
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

// Memperbarui hitungan karakter dan kata
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

    updateProgress(); // Pastikan progress juga diupdate saat karakter berubah
}

// Mengosongkan semua field
function clearAll() {
    if (confirm('Apakah Anda yakin ingin menghapus semua isian dan draft?')) {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.value = '';
            // Hapus kelas 'active' dan reset border
            textarea.closest('.form-card').classList.remove('active', 'shake');
            textarea.style.border = '';
            updateCharCount(textarea.id); // Update count to 0
        });
        updateProgress();
        localStorage.removeItem('noteDraft_{{ $meeting->id }}');
        // Notifikasi reset berhasil (opsional)
        // alert('Formulir berhasil direset!');
    }
}

// Menyimpan draft ke Local Storage
function saveAsDraft(event) {
    // Check if event is passed (e.g., from button click) or if it's called internally
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault(); // Mencegah form disubmit jika dipanggil dari tombol
    }
    const formData = new FormData(document.getElementById('noteForm'));
    const draftData = {};
    for (let [key, value] of formData.entries()) {
        if (key !== '_token') draftData[key] = value;
    }
    localStorage.setItem('noteDraft_{{ $meeting->id }}', JSON.stringify(draftData));

    // Animasi feedback pada tombol
    const btn = event ? event.target.closest('button') : document.querySelector('button[onclick="saveAsDraft(event)"]');
    if (btn) {
        const originalText = btn.innerHTML;
        const originalClasses = btn.className;
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Draft Tersimpan!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        btn.disabled = true; // Disable tombol sementara

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.className = originalClasses;
            btn.disabled = false; // Enable tombol kembali
        }, 2000);
    }
}

// Memuat draft dari Local Storage
function loadDraft() {
    const draftData = localStorage.getItem('noteDraft_{{ $meeting->id }}');
    if (draftData) {
        const data = JSON.parse(draftData);
        Object.keys(data).forEach(key => {
            const textarea = document.getElementById(key);
            if (textarea && data[key]) {
                textarea.value = data[key];
                updateCharCount(key); // Update count for loaded data
            }
        });
        updateProgress();
    }
}

// Validasi saat submit
document.getElementById('noteForm').addEventListener('submit', function(e) {
    const textareas = document.querySelectorAll('textarea');
    let allFieldsFilled = true;
    let firstEmptyField = null;

    textareas.forEach(textarea => {
        // Hapus border merah dan shake dari validasi sebelumnya
        textarea.style.border = '';
        textarea.closest('.form-card').classList.remove('shake');

        if (textarea.value.trim() === '') {
            allFieldsFilled = false;
            // Tambahkan border merah dan animasi shake
            textarea.style.border = '1px solid var(--bs-danger)';
            textarea.closest('.form-card').classList.add('shake');
            if (!firstEmptyField) {
                firstEmptyField = textarea; // Tandai field kosong pertama
            }
        }
    });

    if (!allFieldsFilled) {
        e.preventDefault(); // Mencegah submit
        const warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
        warningModal.show();

        // Fokus ke field kosong pertama jika ada
        if (firstEmptyField) {
            firstEmptyField.focus();
        }

        // Hapus animasi shake setelah selesai
        textareas.forEach(textarea => {
            setTimeout(() => {
                textarea.closest('.form-card').classList.remove('shake');
                // Border merah tetap ada sampai diisi
            }, 500); // Durasi animasi shake
        });
    } else {
        // Jika semua field terisi, hapus draft dari local storage setelah submit berhasil
        localStorage.removeItem('noteDraft_{{ $meeting->id }}');
    }
});
</script>

{{--- Warning Modal ---}}
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> {{-- Tengah modal --}}
        <div class="modal-content">
            <div class="modal-header bg-danger text-white"> {{-- Header modal merah --}}
                <h5 class="modal-title" id="warningModalLabel"><i class="bi bi-exclamation-circle me-2"></i>Peringatan!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button> {{-- Tombol close putih --}}
            </div>
            <div class="modal-body text-center py-4"> {{-- Padding dan text-center --}}
                <p class="fs-5 mb-0">Mohon isi semua bagian catatan sebelum menyimpan.</p>
            </div>
            <div class="modal-footer justify-content-center"> {{-- Tengah tombol footer --}}
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">Oke</button>
            </div>
        </div>
    </div>
</div>

@endsection