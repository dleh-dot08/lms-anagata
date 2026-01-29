@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">Promote Semester (Naik Kelas Massal)</h4>
      <small class="text-muted">
        Semester aktif: <b>{{ $activeSemesterId ?? '-' }}</b>
        @if($activeCount !== null)
          | Snapshot peserta aktif (semester aktif): <b>{{ $activeCount }}</b>
        @endif
      </small>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- =======================
      FORM PREVIEW
  ======================== --}}
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.semester_promote.preview') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Semester Asal</label>
            <select name="from_semester_id" class="form-select" required>
              @foreach($semesters as $s)
                @php $cnt = $snapshotCounts[$s->id] ?? 0; @endphp
                <option value="{{ $s->id }}">
                  {{ $s->name ?? ('Semester ID: '.$s->id) }}
                  (ID: {{ $s->id }})
                  — Snapshot: {{ $cnt }}
                  {{ !empty($s->is_active) ? '— aktif' : '' }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">Tips: pilih semester asal yang snapshot-nya sudah penuh.</small>
          </div>

          <div class="col-md-4">
            <label class="form-label">Semester Tujuan</label>
            <select name="to_semester_id" class="form-select" required>
              @foreach($semesters as $s)
                @php $cnt = $snapshotCounts[$s->id] ?? 0; @endphp
                <option value="{{ $s->id }}">
                  {{ $s->name ?? ('Semester ID: '.$s->id) }}
                  (ID: {{ $s->id }})
                  — Snapshot: {{ $cnt }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">Semester tujuan akan dibuat/diupdate snapshot-nya.</small>
          </div>

          <div class="col-md-4">
            <label class="form-label">Sekolah (opsional)</label>
            <select name="sekolah_id" class="form-select">
              <option value="">Semua sekolah</option>
              @foreach($schools as $sc)
                <option value="{{ $sc->id }}">{{ $sc->nama_sekolah }} (ID: {{ $sc->id }})</option>
              @endforeach
            </select>
            <small class="text-muted">Bisa jalankan per sekolah dulu untuk aman.</small>
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Preview</button>
          <a class="btn btn-outline-secondary" href="{{ route('admin.semester_promote.index') }}">Reset</a>
        </div>

        <div class="mt-3">
          <small class="text-muted">
            Preview menampilkan maksimal 200 baris + ringkasan naik kelas dari <code>kelas_promotion_map</code>.
          </small>
        </div>
      </form>
    </div>
  </div>

  {{-- =======================
      HASIL PREVIEW
  ======================== --}}
  @if(!empty($preview))
    <hr class="my-4">

    <div class="d-flex align-items-center justify-content-between mb-2">
      <div>
        <h5 class="mb-0">Hasil Preview</h5>
        <small class="text-muted">
          Dari Semester <b>{{ $preview['from'] }}</b> → ke Semester <b>{{ $preview['to'] }}</b>
          @if(!empty($preview['schoolId']))
            | Sekolah ID: <b>{{ $preview['schoolId'] }}</b>
          @endif
        </small>
      </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="card"><div class="card-body">
          <div class="text-muted">Total peserta (aktif) di semester asal</div>
          <div class="h4 mb-0">{{ $preview['total'] }}</div>
        </div></div>
      </div>

      <div class="col-md-3">
        <div class="card"><div class="card-body">
          <div class="text-muted">Akan naik kelas (ada mapping)</div>
          <div class="h4 mb-0">{{ $preview['willChange'] }}</div>
        </div></div>
      </div>

      <div class="col-md-3">
        <div class="card"><div class="card-body">
          <div class="text-muted">Tanpa mapping (kelas terakhir / tetap)</div>
          <div class="h4 mb-0">{{ $preview['noMapping'] }}</div>
        </div></div>
      </div>

      <div class="col-md-3">
        <div class="card"><div class="card-body">
          <div class="text-muted">Coverage semester tujuan</div>
          <div class="h4 mb-0">
            {{ $preview['toCount'] ?? 0 }} / {{ $preview['fromCount'] ?? $preview['total'] ?? 0 }}
          </div>
          <small class="text-muted">{{ $preview['coveragePct'] ?? 0 }}%</small>
        </div></div>
      </div>
    </div>

    {{-- =======================
        FORM RUN (membungkus table checkbox)
    ======================== --}}
    <div class="card mb-3">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.semester_promote.run') }}">
          @csrf
          <input type="hidden" name="from_semester_id" value="{{ $preview['from'] }}">
          <input type="hidden" name="to_semester_id" value="{{ $preview['to'] }}">
          <input type="hidden" name="sekolah_id" value="{{ $preview['schoolId'] ?? '' }}">

          <div class="d-flex flex-wrap gap-4 align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="mark_graduates" name="mark_graduates">
              <label class="form-check-label" for="mark_graduates">
                Tandai <b>lulus</b> untuk yang tidak punya mapping (kelas terakhir)
              </label>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="update_users_kelas" name="update_users_kelas" checked>
              <label class="form-check-label" for="update_users_kelas">
                Update juga <b>users.kelas_id</b> (agar sistem kursus langsung menyesuaikan)
              </label>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="bg-light">
                <tr>
                  <th style="width:60px;">
                    <input type="checkbox" id="check_all">
                  </th>
                  <th>Peserta</th>
                  <th>Sekolah</th>
                  <th>Jenjang</th>
                  <th>Kelas Lama</th>
                  <th>Kelas Baru</th>
                </tr>
              </thead>
              <tbody>
                @forelse($preview['rows'] as $r)
                  <tr>
                    <td>
                      {{-- Default: TIDAK dicentang agar tidak promote semua tanpa sadar --}}
                      <input type="checkbox" name="user_ids[]" value="{{ $r->user_id }}">
                    </td>

                    <td>
                      <div class="fw-semibold">{{ $r->user_name ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->user_id }}</small>
                    </td>

                    <td>
                      <div class="fw-semibold">{{ $r->sekolah_nama ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->sekolah_id }}</small>
                    </td>

                    <td>
                      <div class="fw-semibold">{{ $r->jenjang_nama ?? '-' }}</div>
                      <small class="text-muted">ID: {{ $r->jenjang_id }}</small>
                    </td>

                    <td>
                      {{ $r->kelas_lama ?? '-' }}
                      <small class="text-muted d-block">ID: {{ $r->kelas_lama_id ?? '-' }}</small>
                    </td>

                    <td>
                      {{ $r->kelas_baru ?? '-' }}
                      <small class="text-muted d-block">ID: {{ $r->kelas_baru_id ?? '-' }}</small>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted">Preview kosong.</td></tr>
                @endforelse
              </tbody>
            </table>

            <small class="text-muted">Preview menampilkan maksimal 200 baris.</small>
          </div>

          <div class="mt-3 d-flex flex-wrap gap-2">
            <button class="btn btn-success"
              onclick="return confirm('Jalankan promote HANYA untuk peserta yang dicentang?');">
              Jalankan Promote (yang dicentang)
            </button>

            {{-- Tombol aktivasi semester tujuan (opsional) --}}
            <form method="POST" action="{{ route('admin.semester_promote.activate') }}" class="d-inline">
              @csrf
              <input type="hidden" name="semester_id" value="{{ $preview['to'] }}">
              <input type="hidden" name="from_semester_id" value="{{ $preview['from'] }}">
              <input type="hidden" name="sekolah_id" value="{{ $preview['schoolId'] ?? '' }}">
              <button class="btn btn-warning"
                onclick="return confirm('Aktifkan semester tujuan? Pastikan snapshot semester tujuan sudah hampir lengkap.');">
                Aktifkan Semester {{ $preview['to'] }}
              </button>
            </form>

            <a class="btn btn-outline-secondary" href="{{ route('admin.semester_promote.index') }}">Batal</a>
          </div>

          <small class="text-muted d-block mt-2">
            Jika tidak ada yang dicentang, sistem akan menolak agar tidak mempromote semuanya tanpa sengaja.
          </small>
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
