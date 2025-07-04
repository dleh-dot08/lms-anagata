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
        $inputNPSN = $request->input('npsn'); // Mengambil input NPSN dari form
        $receiptDetails = null; // Akan menyimpan detail kwitansi jika ditemukan
        $errorMessage = null;   // Akan menyimpan pesan error jika terjadi

        // Hanya proses pencarian jika ada NPSN yang dimasukkan
        if ($inputNPSN) {
            try {
                $client = new Client();
                $response = $client->get(self::GOOGLE_SHEET_CSV_URL);
                $csvData = (string) $response->getBody();

                // Memecah CSV menjadi baris dan kolom
                $rows = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData));
                $headers = array_shift($rows); // Ambil baris pertama sebagai header

                // --- PEMETAAN INDEKS KOLOM BERDASARKAN NAMA HEADER CSV ANDA ---
                // Pastikan nama string di bawah ini SAMA PERSIS dengan header di baris pertama CSV Anda.
                // Jika Anda mengubah nama 'Column 1' atau 'Column 9' di Google Sheet, sesuaikan di sini.
                $headerMap = [
                    'Timestamp'        => array_search('Timestamp', $headers),
                    'NPSN'             => array_search('NPSN', $headers),
                    'Email'            => array_search('Email', $headers),
                    'NAMA SEKOLAH'     => array_search('NAMA SEKOLAH', $headers),
                    'NAMA LOKUS'       => array_search('NAMA LOKUS', $headers),
                    'NO INVOICE'       => array_search('NO INVOICE', $headers),
                    'BUKTI TRANSFER'   => array_search('BUKTI TRANSFER', $headers),
                    'Nomor Receipt'    => array_search('Column 1', $headers), // Asumsi Kolom H adalah 'Column 1'
                    'URL PDF Receipt'  => array_search('Column 9', $headers), // Asumsi Kolom I adalah 'Column 9'
                ];

                // Lakukan validasi: Pastikan semua kolom penting ditemukan di header CSV
                foreach ($headerMap as $key => $index) {
                    if ($index === false) {
                        throw new Exception("Kolom '{$key}' tidak ditemukan di CSV. Periksa kembali header sheet Anda (termasuk huruf besar/kecil dan spasi).");
                    }
                }

                $validReceiptResponses = []; // Array untuk menampung semua receipt valid yang ditemukan
                
                // Iterasi setiap baris data untuk mencari NPSN
                foreach ($rows as $row) {
                    // Pastikan baris memiliki jumlah kolom yang memadai untuk indeks yang dibutuhkan
                    $maxRequiredIndex = max(array_values($headerMap));
                    if (count($row) > $maxRequiredIndex && !empty($row[$headerMap['NPSN']])) {
                        $currentNPSN = trim($row[$headerMap['NPSN']]);

                        // Bandingkan NPSN dari input dengan yang ada di sheet
                        if (strcasecmp($currentNPSN, $inputNPSN) === 0) {
                            $nomorReceipt = trim($row[$headerMap['Nomor Receipt']]);
                            $urlPdf = trim($row[$headerMap['URL PDF Receipt']]);

                            // Hanya tambahkan ke daftar jika Nomor Receipt DAN URL PDF Receipt tidak kosong
                            if (!empty($nomorReceipt) && !empty($urlPdf)) {
                                // Parse timestamp untuk bisa diurutkan
                                $timestampString = $row[$headerMap['Timestamp']];
                                // Format 'DD/MM/YYYY HH:mm:ss'
                                $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $timestampString);
                                $parsedTimestamp = $dateTime ? $dateTime->getTimestamp() : 0; // Default 0 jika gagal parse

                                $validReceiptResponses[] = [
                                    'timestamp'        => $timestampString,
                                    'npsn'             => $currentNPSN,
                                    'email'            => $row[$headerMap['Email']],
                                    'nama_sekolah'     => $row[$headerMap['NAMA SEKOLAH']],
                                    'nama_lokus'       => $row[$headerMap['NAMA LOKUS']],
                                    'no_invoice'       => $row[$headerMap['NO INVOICE']],
                                    'bukti_transfer'   => $row[$headerMap['BUKTI TRANSFER']],
                                    'nomor_receipt'    => $nomorReceipt,
                                    'url_pdf'          => $urlPdf,
                                    'parsed_timestamp' => $parsedTimestamp, // Digunakan untuk sorting
                                ];
                            }
                        }
                    }
                }

                // Jika ada receipt valid yang ditemukan, urutkan berdasarkan timestamp (terbaru dulu)
                if (!empty($validReceiptResponses)) {
                    usort($validReceiptResponses, function($a, $b) {
                        return $b['parsed_timestamp'] - $a['parsed_timestamp']; // Urutkan dari yang terbaru
                    });
                    $receiptDetails = $validReceiptResponses[0]; // Ambil yang paling terbaru
                } else {
                    $errorMessage = 'NPSN "' . htmlspecialchars($inputNPSN) . '" ditemukan, namun tidak ada data kwitansi lengkap (Nomor Kwitansi atau URL PDF) yang valid untuk NPSN ini.';
                }

            } catch (Exception $e) {
                // Tangani error saat fetching atau parsing data
                $errorMessage = 'Gagal mengambil data atau memproses sheet: ' . $e->getMessage();
                // Catat error ke log Laravel untuk debugging lebih lanjut
                \Log::error("Receipt Check Error: " . $e->getMessage());
            }
        }

        // Kirim data ke view
        return view('koding-ka25.recipt', compact('inputNPSN', 'receiptDetails', 'errorMessage'));
    }
}