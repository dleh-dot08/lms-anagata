<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function cariInvoice(Request $request)
    {
        $npsn = $request->input('npsn');

        $csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSNzBpELuuwAn8mGFO3f5iKnmOGB1TWToRYNTouAS5I7bP6mRPSR6GdyBhCjtybEtO6ftxv1REe5DQo/pubhtml?gid=2102280756&single=true';

        try {
            $response = Http::get($csvUrl);

            if (!$response->ok()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengambil data.']);
            }

            $rows = array_map('str_getcsv', explode("\n", $response->body()));
            $header = array_map('trim', array_shift($rows));
            $data = collect($rows)->map(function ($row) use ($header) {
                return array_combine($header, $row);
            });

            $result = $data->firstWhere('NPSN', $npsn);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'noInvoice' => $result['No Invoice'] ?? '',
                    'sekolahNama' => $result['Nama Sekolah'] ?? '',
                    'pdfUrl' => $result['Link Invoice'] ?? '',
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
                'message' => 'Terjadi error saat membaca data: ' . $e->getMessage()
            ]);
        }
    }
}
