<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CekPendaftaranController extends Controller
{
    public function index()
    {
        // Ganti dengan ID Spreadsheet dan Sheet ID Anda
        $spreadsheetId = 'YOUR_SPREADSHEET_ID';
        $sheetId = '0'; // Ganti jika sheet Anda memiliki ID berbeda

        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/gviz/tq?tqx=out:json&gid={$sheetId}";

        try {
            // Mengambil data dari Google Spreadsheet
            $jsonString = file_get_contents($url);

            // Menghapus string yang tidak perlu
            $jsonString = preg_replace('/.*google.visualization.Query.setResponse\({(.*?)}\);.*/s', '{$1}', $jsonString);

            $responseData = json_decode($jsonString, true);

            $header = [];
            $data = [];

            if (isset($responseData['table']['cols'])) {
                // Mengambil header
                $header = array_map(function($col) {
                    return $col['label'];
                }, $responseData['table']['cols']);

                // Mengambil data
                if (isset($responseData['table']['rows'])) {
                    $data = array_map(function($row) {
                        return array_map(function($cell) {
                            return $cell['v'] ?? null;
                        }, $row['c']);
                    }, $responseData['table']['rows']);
                }
            }
            
            return view('cekpendaftaran', compact('header', 'data'));

        } catch (\Exception $e) {
            // Tangani error jika gagal mengambil atau memproses data
            return view('cekpendaftaran', [
                'header' => [],
                'data' => [],
                'error_message' => 'Gagal memuat data dari Google Spreadsheet. Pastikan URL benar dan spreadsheet dapat diakses publik.'
            ]);
        }
    }
}