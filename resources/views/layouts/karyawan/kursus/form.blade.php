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

<div class="mb-3">
    <label>Program</label>
    <select name="program_id" class="form-control">
    <option value="">Pilih Program</option>
    @foreach ($programs as $program)
            <option value="{{ $program->id }}" {{ (isset($course) && $course->program_id == $program->id) ? 'selected' : '' }}>
                {{ $program->nama_program }}
            </option>
        @endforeach
    </select>
</div>


<div class="mb-3">
    <label>Jenjang</label>
    <select name="jenjang_id" class="form-control">
        @foreach ($jenjangs as $jenjang)
            <option value="{{ $jenjang->id }}" {{ (isset($course) && $course->jenjang_id == $jenjang->id) ? 'selected' : '' }}>
                {{ $jenjang->nama_jenjang }}
            </option>
        @endforeach
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

<div class="mb-3">
    <label>Level</label>
    <select name="level" class="form-control" required>
        <option value="Beginner" {{ (isset($course) && $course->level == 'Beginner') ? 'selected' : '' }}>Beginner</option>
        <option value="Intermediate" {{ (isset($course) && $course->level == 'Intermediate') ? 'selected' : '' }}>Intermediate</option>
        <option value="Advanced" {{ (isset($course) && $course->level == 'Advanced') ? 'selected' : '' }}>Advanced</option>
    </select>
</div>

<div class="mb-3">
    <label>Waktu Mulai</label>
    <input type="date" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', isset($course->waktu_mulai) ? $course->waktu_mulai->format('Y-m-d') : '') }}" required>
</div>

<div class="mb-3">
    <label>Waktu Akhir</label>
    <input type="date" name="waktu_akhir" class="form-control" value="{{ old('waktu_akhir', isset($course->waktu_akhir) ? $course->waktu_akhir->format('Y-m-d') : '') }}" required>
</div>

<div class="mb-3">
    <label>Jumlah Peserta</label>
    <input type="number" name="jumlah_peserta" class="form-control" value="{{ old('jumlah_peserta', $course->jumlah_peserta ?? 0) }}" required min="0">
</div>

<div class="mb-3">
    <label>Harga</label>
    <input type="number" name="harga" class="form-control" value="{{ old('harga', $course->harga ?? '') }}" step="0.01">
</div>
