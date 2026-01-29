@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">Preview Promote</h4>
      <small class="text-muted">
        Dari Semester <b>{{ $from }}</b> → ke Semester <b>{{ $to }}</b>
        @if($schoolId) | Sekolah ID: <b>{{ $schoolId }}</b>@endif
      </small>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-muted">Total peserta (aktif)</div>
        <div class="h4 mb-0">{{ $total }}</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-muted">Akan naik kelas (ada mapping)</div>
        <div class="h4 mb-0">{{ $willChange }}</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-muted">Tanpa mapping (kelas terakhir / tetap)</div>
        <div class="h4 mb-0">{{ $noMapping }}</div>
      </div></div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.semester_promote.run') }}">
        @csrf
        <input type="hidden" name="from_semester_id" value="{{ $from }}">
        <input type="hidden" name="to_semester_id" value="{{ $to }}">
        <input type="hidden" name="sekolah_id" value="{{ $schoolId }}">

        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" value="1" id="mark_graduates" name="mark_graduates">
          <label class="form-check-label" for="mark_graduates">
            Tandai <b>lulus</b> untuk yang tidak punya mapping (kelas terakhir)
          </label>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-success" type="submit"
            onclick="return confirm('Yakin jalankan promote? Ini akan membuat/overwrite snapshot semester tujuan untuk peserta terkait.');">
            Jalankan Promote
          </button>
          <a class="btn btn-outline-secondary" href="{{ route('admin.semester_promote.index') }}">Kembali</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="bg-light">
          <tr>
            <th style="width:120px;">User ID</th>
            <th style="width:120px;">Sekolah</th>
            <th style="width:120px;">Jenjang</th>
            <th>Kelas Lama</th>
            <th>Kelas Baru</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->user_id }}</td>
              <td>{{ $r->sekolah_id }}</td>
              <td>{{ $r->jenjang_id }}</td>
              <td>{{ $r->kelas_lama }} ({{ $r->kelas_lama_id }})</td>
              <td>{{ $r->kelas_baru }} ({{ $r->kelas_baru_id }})</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">Tidak ada data preview.</td></tr>
          @endforelse
        </tbody>
      </table>
      <small class="text-muted">Preview menampilkan maksimal 200 baris.</small>
    </div>
  </div>
</div>
@endsection
