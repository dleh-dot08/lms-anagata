@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">Bootstrap Snapshot Peserta</h4>
      <small class="text-muted">
        Mengecek peserta baru yang belum punya record di <code>student_semesters</code> untuk semester tertentu,
        lalu menambahkan snapshot agar siap dipromote bareng-bareng.
      </small>
    </div>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

  <div class="card mb-3">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.semester_snapshot.check') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label">Semester yang Dicek</label>
            <select name="semester_id" class="form-select" required>
              @foreach($semesters as $s)
                <option value="{{ $s->id }}" {{ ($activeSemesterId == $s->id) ? 'selected' : '' }}>
                  {{ $s->name ?? ('Semester ID: '.$s->id) }} (ID: {{ $s->id }}) {{ !empty($s->is_active) ? '— aktif' : '' }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">Default: semester aktif.</small>
          </div>

          <div class="col-md-5">
            <label class="form-label">Sekolah (opsional)</label>
            <select name="sekolah_id" class="form-select">
              <option value="">Semua sekolah</option>
              @foreach($schools as $sc)
                <option value="{{ $sc->id }}">{{ $sc->nama_sekolah }} (ID: {{ $sc->id }})</option>
              @endforeach
            </select>
            <small class="text-muted">Bisa cek per sekolah dulu.</small>
          </div>

          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">Cek</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  @if(!empty($check))
    <div class="d-flex align-items-center justify-content-between mb-2">
      <div>
        <h5 class="mb-0">Hasil Cek</h5>
        <small class="text-muted">
          Semester: <b>{{ $check['semester_id'] }}</b>
          @if(!empty($check['sekolah_id'])) | Sekolah ID: <b>{{ $check['sekolah_id'] }}</b>@endif
        </small>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <div class="card"><div class="card-body">
          <div class="text-muted">Peserta belum punya snapshot</div>
          <div class="h4 mb-0">{{ $check['total_missing'] ?? 0 }}</div>
        </div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.semester_snapshot.apply') }}">
          @csrf
          <input type="hidden" name="semester_id" value="{{ $check['semester_id'] }}">
          <input type="hidden" name="sekolah_id" value="{{ $check['sekolah_id'] ?? '' }}">

          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-success" type="submit" name="mode" value="selected"
              onclick="return confirm('Tambahkan snapshot untuk peserta yang dicentang?');">
              Tambahkan Snapshot (yang dicentang)
            </button>

            <button class="btn btn-outline-success" type="submit" name="mode" value="all"
              onclick="return confirm('Tambahkan snapshot untuk SEMUA hasil cek?');">
              Tambahkan Snapshot (semua hasil cek)
            </button>

            <small class="text-muted d-block ms-2">
              Yang sudah punya snapshot semester ini tidak akan dimasukkan lagi.
            </small>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="bg-light">
                <tr>
                  <th style="width:60px;"><input type="checkbox" id="check_all"></th>
                  <th>Peserta</th>
                  <th>Sekolah</th>
                  <th>Jenjang</th>
                  <th>Kelas (dari Users)</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                @forelse($check['rows'] as $r)
                  <tr>
                    <td><input type="checkbox" name="user_ids[]" value="{{ $r->user_id }}"></td>
                    <td>
                      <div class="fw-semibold">{{ $r->user_name ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->user_id }}</small>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $r->sekolah_nama ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->sekolah_id ?? '-' }}</small>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $r->jenjang_nama ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->jenjang_id ?? '-' }}</small>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $r->kelas_nama ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->kelas_id ?? '-' }}</small>
                    </td>
                    <td>
                      <small class="text-muted">{{ $r->created_at ?? '-' }}</small>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted">
                      Tidak ada peserta yang missing snapshot (sudah aman).
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <small class="text-muted">Hasil cek ditampilkan maksimal 500 baris.</small>
        </form>
      </div>
    </div>
  @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const checkAll = document.getElementById('check_all');
  if (!checkAll) return;
  checkAll.addEventListener('change', function () {
    document.querySelectorAll('input[name="user_ids[]"]').forEach(cb => cb.checked = checkAll.checked);
  });
});
</script>
@endsection
