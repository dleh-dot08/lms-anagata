<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function cariInvoice(Request $request)
    {
        $npsn = $request->input('npsn');

        // ✅ Ganti dengan link CSV kamu (yang format output=csv)
        $csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSNzBpELuuwAn8mGFO3f5iKnmOGB1TWToRYNTouAS5I7bP6mRPSR6GdyBhCjtybEtO6ftxv1REe5DQo/pub?gid=2102280756&single=true&output=csv';

        try {
            // Ambil CSV
            $response = Http::get($csvUrl);

            if (!$response->ok()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari spreadsheet.'
                ]);
            }

            // Parse CSV ke array
            $rows = array_map('str_getcsv', explode("\n", $response->body()));
            $header = array_map('trim', array_shift($rows));

            // Bersihkan dan gabungkan data
            $data = collect($rows)
                ->filter(function ($row) use ($header) {
                    return count($row) === count($header) && !empty(array_filter($row));
                })
                ->map(function ($row) use ($header) {
                    return array_combine(array_map('trim', $header), array_map('trim', $row));
                });

            // Cari berdasarkan NPSN
            $result = $data->firstWhere('NPSN', $npsn);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'noInvoice' => $result['Column 1'] ?? '(No Invoice Kosong)',
                    'sekolahNama' => $result['EMAIL'] ?? '(Nama tidak tersedia)',
                    'pdfUrl' => $result['Column 2'] ?? '',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk NPSN tersebut.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error saat memproses data: ' . $e->getMessage()
            ]);
        }
    }
}
