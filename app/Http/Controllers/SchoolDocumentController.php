<?php

namespace App\Http\Controllers;

use App\Models\SchoolDocument;
use App\Models\Sekolah;
// use App\Models\User; // Tidak perlu lagi jika tidak ada import otomatis
// use App\Models\Enrollment; // Tidak perlu lagi
// use App\Models\Course; // Tidak perlu lagi di sini, hanya untuk dropdown

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use PhpOffice\PhpSpreadsheet\Spreadsheet; // Tetap perlu untuk membuat template
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; // Tetap perlu
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Tetap perlu


class SchoolDocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen yang sudah diunggah untuk sekolah ini.
     */
    public function index()
    {
        $sekolah = Auth::user()->sekolah;

        if (!$sekolah) {
            return redirect()->back()->with('error', 'Anda tidak terhubung dengan sekolah manapun.');
        }

        $documents = $sekolah->documents;

        // Tambahkan 'daftar_peserta' ke daftar jenis dokumen yang diharapkan
        $documentTypes = [
            'ktp_kepsek' => 'KTP Kepala Sekolah',
            'ktp_tu' => 'KTP Tata Usaha',
            'sertifikat_akreditasi' => 'Sertifikat Akreditasi',
            'npwp' => 'NPWP Sekolah',
            'logo' => 'Logo Sekolah',
            'daftar_peserta' => 'Daftar Peserta Kursus (Excel)', // Jenis dokumen baru
        ];

        // Variabel $courses tidak diperlukan lagi di sini karena tidak ada dropdown kursus untuk upload peserta
        // $courses = Course::where('sekolah_id', $sekolah->id)
        //                  ->where('status', 'Aktif')
        //                  ->orderBy('nama_kelas')
        //                  ->get();

        return view('layouts.sekolah.document.index', compact('sekolah', 'documents', 'documentTypes')); // Hapus 'courses' dari compact
    }

    /**
     * Mengunggah atau memperbarui dokumen.
     */
    public function store(Request $request)
    {
        $sekolah = Auth::user()->sekolah;
        if (!$sekolah) {
            return redirect()->back()->with('error', 'Anda tidak terhubung dengan sekolah manapun.');
        }

        // Validasi input: tambahkan 'daftar_peserta' dan ubah mimes untuk Excel
        $validatedData = $request->validate([
            'document_type' => ['required', 'string',
                Rule::in(['ktp_kepsek', 'ktp_tu', 'sertifikat_akreditasi', 'npwp', 'logo', 'daftar_peserta']) // Tambahkan ini
            ],
            'document_file' => 'required|file|max:5000|mimes:pdf,jpeg,png,jpg,xlsx,xls', // Tambahkan xlsx, xls
        ]);

        $documentType = $validatedData['document_type'];
        $file = $request->file('document_file');
        $fileName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // Tentukan folder penyimpanan: public/school_documents/{school_id}/
        $folderPath = 'school_documents/' . $sekolah->id;
        // Simpan file dengan nama yang jelas, misalnya 'daftar_peserta_timestamp.xlsx'
        // Jika document_type adalah 'daftar_peserta', buat nama file yang unik atau standar
        if ($documentType === 'daftar_peserta') {
            $storageFileName = 'daftar_peserta_' . time() . '.' . $file->extension();
        } else {
            $storageFileName = $documentType . '.' . $file->extension();
        }
        $filePath = Storage::disk('public')->putFileAs($folderPath, $file, $storageFileName);

        // Cek apakah dokumen dengan jenis ini sudah ada untuk sekolah ini
        // Untuk daftar_peserta, kita mungkin ingin mengizinkan multiple uploads
        // Jadi, kita hanya cek jika BUKAN daftar_peserta
        if ($documentType !== 'daftar_peserta') {
            $existingDocument = SchoolDocument::where('sekolah_id', $sekolah->id)
                                          ->where('document_type', $documentType)
                                          ->first();
        } else {
            $existingDocument = null; // Selalu anggap sebagai file baru untuk daftar peserta
        }


        if ($existingDocument) {
            // Hapus file lama dari storage jika ada
            if (Storage::disk('public')->exists($existingDocument->file_path)) {
                Storage::disk('public')->delete($existingDocument->file_path);
            }
            // Perbarui entri database
            $existingDocument->update([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'uploaded_by_user_id' => Auth::id(),
            ]);
            $message = 'Dokumen ' . $documentType . ' berhasil diperbarui.';
        } else {
            // Buat entri database baru
            SchoolDocument::create([
                'sekolah_id' => $sekolah->id,
                'document_type' => $documentType,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'uploaded_by_user_id' => Auth::id(),
            ]);
            $message = 'Dokumen ' . $documentType . ' berhasil diunggah.';
        }

        return redirect()->route('sekolah.documents.index')->with('success', $message);
    }

    /**
     * Menghapus dokumen. (Tidak berubah dari sebelumnya)
     */
    public function destroy(SchoolDocument $document)
    {
        $sekolah = Auth::user()->sekolah;
        if (!$sekolah || $document->sekolah_id !== $sekolah->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus dokumen ini.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('sekolah.documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Mengunduh dokumen. (Tidak berubah dari sebelumnya)
     */
    public function download(SchoolDocument $document)
    {
        $sekolah = Auth::user()->sekolah;
        if (!$sekolah || $document->sekolah_id !== $sekolah->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengunduh dokumen ini.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    // --- Method Baru untuk Download Template Excel Peserta ---

    /**
     * Mengunduh template Excel untuk data peserta.
     */
    public function downloadParticipantTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Daftar Peserta');

        // Header Kolom untuk template daftar peserta
        $headers = [
            'Nama Lengkap',
            'Email',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Jenis Kelamin (Pria/Wanita)',
            'Nomor Telepon',
            'Alamat',
            'Jenjang Pendidikan',
            'kelas',
            // Tambahkan kolom lain jika dibutuhkan oleh perusahaan (misal: "Kursus ID", "Kelas")
        ];

        $sheet->fromArray([$headers], NULL, 'A1');

        // Styling header
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFA0A0A0'); // Abu-abu
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Instruksi di bawah header (opsional)
        $sheet->setCellValue('A2', 'Instruksi: Isi data peserta di baris-baris berikutnya. Jangan mengubah header kolom.');
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $sheet->mergeCells('A2:' . $sheet->getHighestColumn() . '2');


        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_daftar_peserta.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName); // Buat file sementara
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}