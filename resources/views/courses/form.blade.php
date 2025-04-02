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
