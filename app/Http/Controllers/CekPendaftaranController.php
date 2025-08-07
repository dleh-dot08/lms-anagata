<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
// use Google\Service\Sheets\ValueRange; // Tidak selalu diperlukan jika hanya mengambil data

class CekPendaftaranController extends Controller
{
    public function index()
    {
        try {
            // Inisialisasi Google Client
            $client = new Client();
            $client->setApplicationName('Google Sheets API Anagata');
            $client->setScopes([Sheets::SPREADSHEETS_READONLY]);

            // --- PENTING: Pilih salah satu metode otentikasi di bawah ini ---

            // Metode 1: Menggunakan Service Account (Disarankan untuk aplikasi server-to-server)
            // Pastikan file JSON service account ada di storage/app/google-service-account.json
            // Dan email service account sudah diberi akses "Viewer" atau "Editor" di Google Spreadsheet Anda.
            $client->setAuthConfig(storage_path('app/google-service-account.json'));

            // Metode 2: Menggunakan API Key (Lebih sederhana, hanya untuk akses publik/read-only)
            // Ganti 'YOUR_API_KEY' dengan API Key yang Anda dapatkan dari Google Cloud Console
            // $client->setDeveloperKey('YOUR_API_KEY');


            $service = new Sheets($client);

            // Ganti 'YOUR_SPREADSHEET_ID' dengan ID Spreadsheet Google Anda
            // ID Spreadsheet ada di URL-nya, contoh: https://docs.google.com/spreadsheets/d/INI_ID_SPREADSHEET_ANDA/edit
            $spreadsheetId = 'YOUR_SPREADSHEET_ID';

            // Ganti 'Sheet1!A1:H' dengan nama sheet dan range data yang ingin Anda ambil
            // Contoh: 'DataPendaftaran!A1:Z'
            $range = 'Sheet1!A1:H';

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            $header = [];
            $data = [];

            if (!empty($values)) {
                $header = array_shift($values); // Baris pertama adalah header
                $data = $values; // Sisa baris adalah data
            }

            // Mengirim data ke view 'cekpendaftaran'
            return view('cekpendaftaran', compact('header', 'data'));

        } catch (\Google\Exception $e) {
            // Tangani error jika terjadi masalah dengan Google API
            // Misalnya, API Key tidak valid, spreadsheet tidak ditemukan, atau masalah jaringan
            return view('cekpendaftaran', [
                'header' => [],
                'data' => [],
                'error_message' => 'Gagal memuat data dari Google Spreadsheet: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            // Tangani error umum lainnya
            return view('cekpendaftaran', [
                'header' => [],
                'data' => [],
                'error_message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}