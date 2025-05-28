<div class="mb-3">
    <label>Nama Kelas</label>
    <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas', $course->nama_kelas ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Mentor</label>
    <select name="mentor_id" class="form-control">
        @foreach ($mentors as $mentor)
            <option value="{{ $mentor->id }}" {{ (isset($course) && $course->mentor_id == $mentor->id) ? 'selected' : '' }}>
                {{ $mentor->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Kategori</label>
    <select name="kategori_id" class="form-control">
        @foreach ($kategoris as $kategori)
            <option value="{{ $kategori->id }}" {{ (isset($course) && $course->kategori_id == $kategori->id) ? 'selected' : '' }}>
                {{ $kategori->nama_kategori }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group mb-3">
    <label for="jenjang_id">Jenjang</label>
    <select name="jenjang_id" id="jenjang_id" class="form-control" required>
        <option value="">Pilih Jenjang</option>
        @foreach($jenjangs as $jenjang)
            <option value="{{ $jenjang->id }}" {{ isset($course) && $course->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                {{ $jenjang->nama_jenjang }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group mb-3">
    <label for="kelas_id">Kelas</label>
    <select name="kelas_id" id="kelas_id" class="form-control" {{ !isset($course) || !$course->jenjang_id ? 'disabled' : '' }}>
        <option value="">Pilih Kelas</option>
        @if(isset($course) && $course->jenjang_id)
            @foreach($kelas->where('id_jenjang', $course->jenjang_id) as $k)
                <option value="{{ $k->id }}" {{ $course->kelas_id == $k->id ? 'selected' : '' }}>
                    {{ $k->nama }}
                </option>
            @endforeach
        @endif
    </select>
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $course->deskripsi ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="Aktif" {{ (isset($course) && $course->status == 'Aktif') ? 'selected' : '' }}>Aktif</option>
        <option value="Nonaktif" {{ (isset($course) && $course->status == 'Nonaktif') ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>

@push('scripts')
<script>
document.getElementById('jenjang_id').addEventListener('change', function() {
    const jenjangId = this.value;
    const kelasSelect = document.getElementById('kelas_id');
    
    // Reset and disable kelas select if no jenjang selected
    if (!jenjangId) {
        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
        kelasSelect.disabled = true;
        return;
    }

    // Enable kelas select
    kelasSelect.disabled = false;

    // Fetch kelas options based on selected jenjang
    fetch(`/api/jenjang/${jenjangId}/kelas`)
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Pilih Kelas</option>';
            data.forEach(kelas => {
                options += `<option value="${kelas.id}">${kelas.nama}</option>`;
            });
            kelasSelect.innerHTML = options;
        });
});
</script>
@endpush
