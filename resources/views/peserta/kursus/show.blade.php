@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow mb-4 mt-4 border-0">
        <div class="card-header bg-primary py-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 text-white font-weight-bold">{{ $course->nama_kelas }}</h4>
            </div>
        </div>
        <div class="card-body">
            {{-- Cek status aktif --}}
            @if($course->status === 'Aktif' && now()->between($course->waktu_mulai, $course->waktu_akhir))
                @include('peserta.kursus.partials.nav-tabs', ['activeTab' => 'meetings'])
                <div class="redirect-notice alert alert-info d-none">
                    <i class="fas fa-info-circle me-2"></i>Mengalihkan ke halaman yang sesuai...
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <h5>Kursus Tidak Aktif</h5>
                    <p>Kursus ini sudah tidak aktif atau telah berakhir. Silakan hubungi admin jika kamu memerlukan akses ulang.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Redirect to meetings page since this is the default active tab
    document.addEventListener('DOMContentLoaded', function() {
        const redirectNotice = document.querySelector('.redirect-notice');
        if (redirectNotice) {
            redirectNotice.classList.remove('d-none');
            window.location.href = '{{ route("peserta.kursus.meetings", $course->id) }}';
        }
    });
</script>
@endpush
@endsection
