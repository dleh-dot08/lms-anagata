<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CekPendaftaranController extends Controller
{
    public function index()
    {
        $csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRpzd8ICvBeDIPxZwOl1n7oTrsPWhfc_HX_B5YVDZzoWH60W4TLcQ5lqnWLo4CA5J6J9t1nDD1U19M0/pub?gid=0&single=true&output=csv';

        try {
            $csvData = file_get_contents($csvUrl);
            if ($csvData === false) {
                throw new \Exception("Tidak bisa mengakses URL CSV");
            }

            $rows = array_map('str_getcsv', explode("\n", $csvData));

            // Ambil header dari baris pertama
            $header = array_shift($rows);

            // Bersihkan baris kosong
            $data = array_filter($rows, function ($row) {
                return count(array_filter($row)) > 0;
            });

            return view('kka-paud.cekpendaftaran', compact('header', 'data'));

        } catch (\Exception $e) {
            return view('kka-paud.cekpendaftaran', [
                'header' => [],
                'data' => [],
                'error_message' => 'Gagal memuat data dari Google Spreadsheet CSV. Pastikan URL dapat diakses publik.'
            ]);
        }
    }
}
