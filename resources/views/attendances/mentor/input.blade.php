@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Absensi Siswa - {{ $course->nama_kelas }}</h4>
    
    <form action="{{ route('attendances.storeInput', $course->id) }}" method="POST">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="hidden" name="absences[{{ $loop->index }}][user_id]" value="{{ $student->id }}">
                            <input type="radio" class="btn-check" name="absences[{{ $loop->index }}][status]" id="hadir{{ $loop->index }}" value="Hadir" checked>
                            <label class="btn btn-outline-success" for="hadir{{ $loop->index }}">H</label>

                            <input type="radio" class="btn-check" name="absences[{{ $loop->index }}][status]" id="izin{{ $loop->index }}" value="Izin">
                            <label class="btn btn-outline-info" for="izin{{ $loop->index }}">I</label>

                            <input type="radio" class="btn-check" name="absences[{{ $loop->index }}][status]" id="alpha{{ $loop->index }}" value="Tidak Hadir">
                            <label class="btn btn-outline-danger" for="alpha{{ $loop->index }}">A</label>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Simpan Absensi</button>
        </div>
    </form>
</div>
@endsection
