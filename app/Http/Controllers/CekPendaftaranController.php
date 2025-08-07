<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class CekPendaftaranController extends Controller
{
    public function index()
    {
        // Ganti dengan API Key Anda atau path file JSON Service Account
        $client = new Client();
        $client->setApplicationName('Google Sheets API Anagata');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/google-service-account.json')); // Ubah jika pakai API Key

        $service = new Sheets($client);
        $spreadsheetId = 'YOUR_SPREADSHEET_ID'; // Ganti dengan ID Spreadsheet Anda
        $range = 'Sheet1!A1:H'; // Ganti dengan nama sheet dan range data Anda

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        // Ambil header dan data
        if (empty($values)) {
            $data = [];
        } else {
            $header = array_shift($values); // Baris pertama adalah header
            $data = $values;
        }

        return view('cekpendaftaran', compact('header', 'data'));
    }
}