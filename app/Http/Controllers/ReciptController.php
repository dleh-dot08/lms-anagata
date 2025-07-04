<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception; // Untuk menangani exception

class ReciptController extends Controller
{
    // GANTI URL INI dengan URL CSV yang Anda dapatkan dari publikasi Google Sheet
    // PASTIKAN INI URL YANG BERAKHIR DENGAN `output=csv`
    const GOOGLE_SHEET_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQOFbseWnJ4BQ_WCUx3fIi0KAGhOphAP2Lgwb5yf59n3jRvsMIIBWBwrsyq1y6_oSMXRdZ9_FpKdYeU/pub?gid=1957615865&single=true&output=csv';

    /**
     * Menampilkan form untuk mencari nomor receipt dan menampilkan hasilnya.
     * Menerima request GET untuk menampilkan form, dan POST untuk memproses pencarian.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function checkReceipt(Request $request)
    {
        $receiptNumber = $request->input('receipt_number'); // Mengambil input dari form
        $receiptDetails = null; // Akan menyimpan detail receipt jika ditemukan
        $errorMessage = null;   // Akan menyimpan pesan error jika terjadi

        // Hanya proses pencarian jika ada nomor receipt yang dimasukkan
        if ($receiptNumber) {
            try {
                // Inisialisasi Guzzle HTTP Client
                $client = new Client();
                // Mengambil data CSV dari URL Google Sheet
                $response = $client->get(self::GOOGLE_SHEET_CSV_URL);
                $csvData = (string) $response->getBody();

                // Ubah data CSV menjadi array of arrays (setiap baris menjadi array)
                // Menggunakan preg_split untuk menangani berbagai jenis line endings (\n, \r\n, \r)
                $rows = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData));

                // Baris pertama adalah header, kita pisahkan
                $headers = array_shift($rows);

                // --- PEMETAAN INDEKS KOLOM BERDASARKAN NAMA HEADER CSV ANDA ---
                // Ini penting! Pastikan nama string di bawah ini SAMA PERSIS
                // dengan header di baris pertama CSV yang dihasilkan Google Sheet Anda.
                $headerMap = [
                    'Timestamp'        => array_search('Timestamp', $headers),
                    'NPSN'             => array_search('NPSN', $headers),
                    'Email'            => array_search('Email', $headers),
                    'NAMA SEKOLAH'     => array_search('NAMA SEKOLAH', $headers),
                    'NAMA LOKUS'       => array_search('NAMA LOKUS', $headers),
                    'NO INVOICE'       => array_search('NO INVOICE', $headers),
                    'BUKTI TRANSFER'   => array_search('BUKTI TRANSFER', $headers),
                    'Nomor Receipt'    => array_search('Column 1', $headers), // SESUAIKAN: Ini untuk Kolom H
                    'URL PDF Receipt'  => array_search('Column 9', $headers), // SESUAIKAN: Ini untuk Kolom I
                ];

                // Lakukan validasi: Pastikan semua kolom penting ditemukan
                foreach ($headerMap as $key => $index) {
                    if ($index === false) {
                        throw new Exception("Kolom '{$key}' tidak ditemukan di CSV. Periksa kembali header sheet Anda (termasuk huruf besar/kecil dan spasi).");
                    }
                }

                // Iterasi setiap baris data untuk mencari nomor receipt
                foreach ($rows as $row) {
                    // Pastikan baris tidak kosong dan memiliki cukup kolom
                    // Mengambil indeks tertinggi yang kita butuhkan untuk mencegah error indeks
                    $maxRequiredIndex = max(array_values($headerMap));
                    if (count($row) > $maxRequiredIndex && !empty($row[$headerMap['Nomor Receipt']])) {
                        // Ambil nomor receipt dari baris saat ini dan bersihkan spasi
                        $currentReceipt = trim($row[$headerMap['Nomor Receipt']]);

                        // Bandingkan nomor receipt dari input dengan yang ada di sheet (case-insensitive)
                        if (strcasecmp($currentReceipt, $receiptNumber) === 0) {
                            // Jika cocok, simpan detailnya
                            $receiptDetails = [
                                'timestamp'      => $row[$headerMap['Timestamp']],
                                'npsn'           => $row[$headerMap['NPSN']],
                                'email'          => $row[$headerMap['Email']],
                                'nama_sekolah'   => $row[$headerMap['NAMA SEKOLAH']],
                                'nama_lokus'     => $row[$headerMap['NAMA LOKUS']],
                                'no_invoice'     => $row[$headerMap['NO INVOICE']],
                                'bukti_transfer' => $row[$headerMap['BUKTI TRANSFER']],
                                'nomor_receipt'  => $currentReceipt,
                                'url_pdf'        => trim($row[$headerMap['URL PDF Receipt']]),
                            ];
                            break; // Hentikan pencarian jika sudah ditemukan
                        }
                    }
                }

                // Jika pencarian selesai tapi receiptDetails masih null, berarti tidak ditemukan
                if (!$receiptDetails) {
                    $errorMessage = 'Nomor kwitansi "' . htmlspecialchars($receiptNumber) . '" tidak ditemukan. Pastikan Anda memasukkan nomor yang benar (contoh: REC0001-AA/07/2025-TOT).';
                }

            } catch (Exception $e) {
                // Tangani error saat fetching atau parsing data
                $errorMessage = 'Gagal mengambil data atau memproses sheet: ' . $e->getMessage();
                // Catat error ke log Laravel untuk debugging lebih lanjut
                \Log::error("Receipt Check Error: " . $e->getMessage());
            }
        }

        // Kirim data ke view
        return view('koding-ka25.recipt', compact('receiptNumber', 'receiptDetails', 'errorMessage'));
    }
}