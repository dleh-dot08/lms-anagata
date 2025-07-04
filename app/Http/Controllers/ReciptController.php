<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client; // Import Guzzle
use Exception; // Untuk menangani exception

class ReciptController extends Controller
{
    // GANTI URL INI dengan URL CSV yang Anda dapatkan dari Langkah 1
    const GOOGLE_SHEET_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQOFbseWnJ4BQ_WCUx3fIi0KAGhOphAP2Lgwb5yf59n3jRvsMIIBWBwrsyq1y_oSMXRdZ9_FpKdYeU/pub?output=csv&gid=1957615865';

    /**
     * Menampilkan form untuk mencari nomor receipt dan menampilkan hasilnya.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function checkReceipt(Request $request)
    {
        $receiptNumber = $request->input('receipt_number');
        $receiptDetails = null;
        $errorMessage = null;

        if ($receiptNumber) {
            try {
                $client = new Client();
                $response = $client->get(self::GOOGLE_SHEET_CSV_URL);
                $csvData = (string) $response->getBody();

                // Ubah data CSV menjadi array
                $rows = array_map('str_getcsv', explode("\n", $csvData));

                // Asumsi baris pertama adalah header
                $headers = array_shift($rows);

                // Temukan indeks kolom "Nomor Receipt" (Kolom H) dan "URL PDF Receipt" (Kolom I)
                // Pastikan nama header di CSV sesuai dengan yang ada di sheet Anda
                $receiptNumberColumnIndex = array_search('Nomor Receipt', $headers); // Sesuaikan jika nama headernya berbeda
                $pdfUrlColumnIndex = array_search('URL PDF Receipt', $headers);     // Sesuaikan jika nama headernya berbeda
                $namaSekolahColumnIndex = array_search('NAMA SEKOLAH', $headers); // Contoh lain untuk detail

                if ($receiptNumberColumnIndex === false || $pdfUrlColumnIndex === false) {
                    throw new Exception("Kolom 'Nomor Receipt' atau 'URL PDF Receipt' tidak ditemukan di CSV. Periksa header sheet Anda.");
                }

                foreach ($rows as $row) {
                    // Pastikan baris tidak kosong dan memiliki jumlah kolom yang memadai
                    if (count($row) > max($receiptNumberColumnIndex, $pdfUrlColumnIndex, $namaSekolahColumnIndex) && !empty($row[$receiptNumberColumnIndex])) {
                        // Bersihkan spasi atau karakter tak terlihat jika ada
                        $currentReceipt = trim($row[$receiptNumberColumnIndex]);

                        // Cek apakah nomor receipt cocok (case-insensitive)
                        if (strcasecmp($currentReceipt, $receiptNumber) === 0) {
                            $receiptDetails = [
                                'nomor_receipt' => $currentReceipt,
                                'url_pdf'       => trim($row[$pdfUrlColumnIndex]),
                                'nama_sekolah'  => trim($row[$namaSekolahColumnIndex]),
                                // Anda bisa menambahkan detail lain dari kolom lain yang Anda butuhkan
                                'npsn'          => trim($row[array_search('NPSN', $headers)]),
                                'email'         => trim($row[array_search('Email', $headers)]),
                                'no_invoice'    => trim($row[array_search('NO INVOICE', $headers)]),
                                // ... tambahkan kolom lain sesuai kebutuhan
                            ];
                            break; // Hentikan pencarian jika sudah ditemukan
                        }
                    }
                }

                if (!$receiptDetails) {
                    $errorMessage = 'Nomor receipt tidak ditemukan.';
                }

            } catch (Exception $e) {
                $errorMessage = 'Gagal mengambil data atau memproses sheet: ' . $e->getMessage();
                \Log::error("Receipt Check Error: " . $e->getMessage()); // Catat error ke log Laravel
            }
        }

        return view('koding-ka25.recipt', compact('receiptNumber', 'receiptDetails', 'errorMessage'));
    }
}