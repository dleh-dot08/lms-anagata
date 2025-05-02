@extends('layouts.peserta.template')

@section('content')
<div class="container py-5 px-md-4">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-lg-10 col-md-10 col-sm-12">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h3 class="fw-bold mb-0 text-center">Gabung Kursus</h3>
                </div>
                <div class="card-body p-4">
                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('courses.joinPeserta.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="kode_unik" class="form-label fw-semibold">Kode Kursus</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="kode_unik" 
                                    id="kode_unik" 
                                    class="form-control @error('kode_unik') is-invalid @enderror" 
                                    value="{{ old('kode_unik') }}" 
                                    placeholder="Masukkan kode kursus (contoh: CDM-ABCDE)"
                                    required
                                    autocomplete="off"
                                >
                            </div>
                            @error('kode_unik')
                            <div class="invalid-feedback d-block mt-1">
                                {{ $message }}
                            </div>
                            @enderror
                            <small class="text-muted mt-2 d-block">
                                Kode kursus biasanya dalam format CDM-XXXXX dan diberikan oleh instruktur Anda.
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Gabung Kursus
                            </button>
                            <a href="{{ route('courses.indexpeserta') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4 border-0 shadow-sm rounded-3 bg-light">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill text-primary me-2"></i>Butuh Bantuan?</h6>
                    <p class="mb-0 small">Jika Anda tidak memiliki kode kursus atau mengalami kesulitan, silakan hubungi Mentor Anda atau tim kami.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection