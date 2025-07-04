<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class ReciptController extends Controller
{
    // GANTI URL INI dengan URL CSV yang Anda dapatkan dari publikasi Google Sheet
    // PASTIKAN INI URL YANG BERAKHIR DENGAN `output=csv`
    const GOOGLE_SHEET_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQOFbseWnJ4BQ_WCUx3fIi0KAGhOphAP2Lgwb5yf59n3jRvsMIIBWBwrsyq1y6_oSMXRdZ9_FpKdYeU/pub?gid=1957615865&single=true&output=csv';

    /**
     * Menampilkan form untuk mencari NPSN dan menampilkan detail kwitansinya.
     * Menerima request GET untuk menampilkan form, dan POST untuk memproses pencarian.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function checkReceipt(Request $request)
    {
        $inputNPSN = $request->input('npsn'); // MENGUBAH: Mengambil input NPSN
        $receiptDetails = null; // Akan menyimpan detail kwitansi jika ditemukan
        $errorMessage = null;   // Akan menyimpan pesan error jika terjadi

        // Hanya proses pencarian jika ada NPSN yang dimasukkan
        if ($inputNPSN) {
            try {
                $client = new Client();
                $response = $client->get(self::GOOGLE_SHEET_CSV_URL);
                $csvData = (string) $response->getBody();

                $rows = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData));
                $headers = array_shift($rows);

                // --- PEMETAAN INDEKS KOLOM BERDASARKAN NAMA HEADER CSV ANDA ---
                // Pastikan nama string di bawah ini SAMA PERSIS dengan header di baris pertama CSV Anda.
                $headerMap = [
                    'Timestamp'        => array_search('Timestamp', $headers),
                    'NPSN'             => array_search('NPSN', $headers), // Kunci pencarian baru
                    'Email'            => array_search('Email', $headers),
                    'NAMA SEKOLAH'     => array_search('NAMA SEKOLAH', $headers),
                    'NAMA LOKUS'       => array_search('NAMA LOKUS', $headers),
                    'NO INVOICE'       => array_search('NO INVOICE', $headers),
                    'BUKTI TRANSFER'   => array_search('BUKTI TRANSFER', $headers),
                    'Nomor Receipt'    => array_search('Column 1', $headers), // Kwitansi ada di 'Column 1'
                    'URL PDF Receipt'  => array_search('Column 9', $headers), // URL PDF ada di 'Column 9'
                ];

                // Lakukan validasi: Pastikan semua kolom penting ditemukan
                foreach ($headerMap as $key => $index) {
                    if ($index === false) {
                        throw new Exception("Kolom '{$key}' tidak ditemukan di CSV. Periksa kembali header sheet Anda.");
                    }
                }

                // Iterasi setiap baris data untuk mencari NPSN
                foreach ($rows as $row) {
                    $maxRequiredIndex = max(array_values($headerMap));
                    if (count($row) > $maxRequiredIndex && !empty($row[$headerMap['NPSN']])) { // MENGUBAH: Cek kolom NPSN
                        $currentNPSN = trim($row[$headerMap['NPSN']]);

                        // Bandingkan NPSN dari input dengan yang ada di sheet
                        if (strcasecmp($currentNPSN, $inputNPSN) === 0) { // MENGUBAH: Pencarian berdasarkan NPSN
                            $receiptDetails = [
                                'timestamp'      => $row[$headerMap['Timestamp']],
                                'npsn'           => $currentNPSN, // Menggunakan NPSN yang ditemukan
                                'email'          => $row[$headerMap['Email']],
                                'nama_sekolah'   => $row[$headerMap['NAMA SEKOLAH']],
                                'nama_lokus'     => $row[$headerMap['NAMA LOKUS']],
                                'no_invoice'     => $row[$headerMap['NO INVOICE']],
                                'bukti_transfer' => $row[$headerMap['BUKTI TRANSFER']],
                                'nomor_receipt'  => trim($row[$headerMap['Nomor Receipt']]), // Tetap tampilkan nomor kwitansi
                                'url_pdf'        => trim($row[$headerMap['URL PDF Receipt']]), // Tetap tampilkan URL PDF
                            ];
                            break; // Hentikan pencarian jika sudah ditemukan (pertama cocok)
                        }
                    }
                }

                if (!$receiptDetails) {
                    $errorMessage = 'NPSN "' . htmlspecialchars($inputNPSN) . '" tidak ditemukan atau tidak memiliki kwitansi. Pastikan Anda memasukkan NPSN yang benar.'; // MENGUBAH: Pesan error
                }

            } catch (Exception $e) {
                $errorMessage = 'Gagal mengambil data atau memproses sheet: ' . $e->getMessage();
                \Log::error("Receipt Check Error: " . $e->getMessage());
            }
        }

        // Kirim data ke view
        return view('koding-ka25.recipt', compact('inputNPSN', 'receiptDetails', 'errorMessage')); // MENGUBAH: Kirim $inputNPSN
    }
}