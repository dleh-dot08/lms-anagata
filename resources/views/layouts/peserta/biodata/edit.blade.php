@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Edit Biodata</h3>
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Perhatian!</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form action="{{ route('biodata.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Informasi Akun -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Akun</h5>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No WhatsApp</label>
                    <input type="text" name="no_telepon" class="form-control" 
                        value="{{ $user->no_telepon }}">
                    <small class="text-muted">Gunakan Nomor Whatsapp Aktif</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sekolah</label>
                    <input type="text" class="form-control" value="{{ $user->sekolah->nama_sekolah ?? '-' }}" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat_tempat_tinggal" class="form-control">{{ $user?->alamat_tempat_tinggal }}</textarea>
                </div>
            </div>

            <!-- Informasi Biodata -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" {{ $user?->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $user?->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ $biodata?->tempat_lahir }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ $biodata?->tanggal_lahir }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang_id" id="jenjang_id" class="form-select">
                        <option value="">-- Pilih Jenjang --</option>
                        @foreach($jenjangs as $jenjang)
                            <option value="{{ $jenjang->id }}" {{ $user?->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                                {{ $jenjang->nama_jenjang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="kelas_id">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-control" disabled>
                        <option value="">Pilih Kelas</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Media Sosial (Instagram/Facebook/LinkedIn)</label>
                    <input type="text" name="media_sosial" class="form-control" value="{{ $user?->media_sosial }}">
                </div>
            </div>

            <!-- Upload Berkas -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Upload Berkas</h5>
                <div class="mb-3">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" class="form-control">
                    <small class="text-muted">Type File JPEG/PNG ukuran max. 2MB </small>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-center">
                <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
                <a href="{{ route('biodata.index') }}" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Mentor Select2 initialization
        $('#mentor_id').select2({
            placeholder: 'Cari Mentor',
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('courses.searchMentor') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        // Jenjang change event handler
        $('#jenjang_id').on('change', function() {
            const jenjangId = $(this).val();
            const kelasSelect = $('#kelas_id');
            
            // Reset and disable kelas select if no jenjang selected
            if (!jenjangId) {
                kelasSelect.html('<option value="">Pilih Kelas</option>');
                kelasSelect.prop('disabled', true);
                return;
            }

            // Enable kelas select
            kelasSelect.prop('disabled', false);

            // Fetch kelas options based on selected jenjang
            fetch(`/api/jenjang/${jenjangId}/kelas`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Pilih Kelas</option>';
                    data.forEach(kelas => {
                        options += `<option value="${kelas.id}">${kelas.nama}</option>`;
                    });
                    kelasSelect.html(options);
                });
        });
    });
</script>
@endpush

@endsection
