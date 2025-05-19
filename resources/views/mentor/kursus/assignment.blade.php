@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Daftar Assignment untuk Kursus: {{ $course->nama_kelas }}</h3>
    @include('mentor.kursus.partials.nav-tabs', ['activeTab' => 'assignment'])
    
    @foreach($course->meetings as $meeting)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <strong>Pertemuan: {{ $meeting->judul }}</strong>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-{{ $meeting->id }}">
                    Buat Tugas
                </button>
            </div>

            <!-- Modal Form Tugas -->
            <div class="modal fade" id="modal-{{ $meeting->id }}" tabindex="-1" aria-labelledby="modalLabel-{{ $meeting->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('kursus.mentor.assignment.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">

                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel-{{ $meeting->id }}">Buat Tugas untuk: {{ $meeting->judul }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-2">
                                    <label>Judul Tugas</label>
                                    <input type="text" name="judul" class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control"></textarea>
                                </div>

                                <div class="mb-2">
                                    <label>Lampiran (opsional)</label>
                                    <input type="file" name="file_attachment" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label>Deadline</label>
                                    <input type="datetime-local" name="deadline" class="form-control">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Simpan Tugas</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Modal -->

            @if($meeting->assignments->count())
                <ul class="list-group list-group-flush">
                    @foreach($meeting->assignments as $assignment)
                        <li class="list-group-item">
                            <strong>{{ $assignment->judul }}</strong> <br>
                            {{ $assignment->deskripsi }}
                            @if($assignment->file_attachment)
                                <br><a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank">📎 Download Lampiran</a>
                            @endif
                            <br><small>Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y H:i') }}</small>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="card-body text-muted">Belum ada tugas untuk pertemuan ini.</div>
            @endif
        </div>
    @endforeach
</div>
@endsection
