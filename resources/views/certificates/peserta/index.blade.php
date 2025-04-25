@extends('layouts.peserta.template') {{-- ganti sesuai layout peserta yang kamu gunakan --}}

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Daftar Sertifikat Saya</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($certificates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($certificates as $certificate)
                <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $certificate->course ? $certificate->course->nama_kelas : ($certificate->activity->nama_kegiatan ?? '-') }}
                        </h2>

                        <p class="text-sm text-gray-600 mb-1"><strong>Kode Sertifikat:</strong> {{ $certificate->kode_sertifikat }}</p>
                        <p class="text-sm text-gray-600 mb-1"><strong>Tanggal Terbit:</strong> {{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d M Y') }}</p>
                        <p class="text-sm text-gray-600 mb-2"><strong>Status:</strong> 
                            <span class="{{ $certificate->status == 'Diterbitkan' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $certificate->status }}
                            </span>
                        </p>

                        <a href="{{ asset('storage/sertifikat/' . $certificate->file_sertifikat) }}" 
                           class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200 text-sm"
                           target="_blank">
                            Lihat Sertifikat
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-10 text-gray-500">
            <p>Belum ada sertifikat yang diterbitkan untuk Anda.</p>
        </div>
    @endif
</div>
@endsection
