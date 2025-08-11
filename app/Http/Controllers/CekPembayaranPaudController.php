<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class CekPembayaranPaudController extends Controller
{
    const INVOICE_CSV_URL  = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vReWEoxjTD_Qvtygf2doavEexLwHB19qwrruKfKNaPIWnDKdRmNyePbcuC4dKSElsioM7sKgbxmvQ4A/pub?gid=995897769&single=true&output=csv';
    const KWITANSI_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQafhQuw8fTlxB3yFb5391UirQQixHd3WWjOjTscAZotO_SVg1U7qDwEWTZWa_b6DdST_W1IDIFVStZ/pub?gid=1667067318&single=true&output=csv';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Ambil data dari CSV Google Sheets dan cari berdasarkan NPSN
     */
    private function getDataByNPSN(string $url, array $headerMap, string $npsn)
    {
        $response = $this->client->get($url);
        $rows = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", (string) $response->getBody()));
        $headers = array_shift($rows);

        // Validasi header
        foreach ($headerMap as $key => $colName) {
            $index = array_search($colName, $headers);
            if ($index === false) {
                throw new Exception("Kolom '{$colName}' tidak ditemukan di CSV.");
            }
            $headerMap[$key] = $index; // ganti value colName jadi index
        }

        // Cari data
        foreach ($rows as $row) {
            $maxIndex = max($headerMap);
            if (count($row) > $maxIndex && !empty($row[$headerMap['npsn']])) {
                if (strcasecmp(trim($row[$headerMap['npsn']]), $npsn) === 0) {
                    $result = [];
                    foreach ($headerMap as $key => $index) {
                        $result[$key] = trim($row[$index]);
                    }
                    return $result;
                }
            }
        }
        return null; // tidak ketemu
    }

    public function cekPembayaran(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string',
        ]);

        $npsn = trim($request->input('npsn'));

        try {
            // ====== Cek Invoice ======
            $invoiceHeaderMap = [
                'npsn'         => 'NPSN SEKOLAH',
                'nama_paud'    => 'NAMA SEKOLAH ',
                'nama_peserta' => 'NAMA LENGKAP (GELAR LENGKAP)',
                'no_invoice'   => 'NO INVOICE',
                'url_invoice'  => 'URL',
            ];
            $invoiceData = $this->getDataByNPSN(self::INVOICE_CSV_URL, $invoiceHeaderMap, $npsn);

            // ====== Cek Kwitansi ======
            $kwitansiHeaderMap = [
                'npsn'          => 'NPSN',
                'nama_paud'     => 'NAMA SEKOLAH',
                'no_invoice'    => 'NO INVOICE',
                'bukti_transfer'=> 'BUKTI TRANSFER',
                'no_recipt'     => 'NO RECIPT',
                'url_kwitansi'  => 'URL',
            ];
            $kwitansiData = $this->getDataByNPSN(self::KWITANSI_CSV_URL, $kwitansiHeaderMap, $npsn);

            return response()->json([
                'success'         => true,
                'invoice_status'  => $invoiceData ? 'sudah' : 'belum',
                'kwitansi_status' => $kwitansiData ? 'sudah' : 'belum',
                'invoice_data'    => $invoiceData,
                'kwitansi_data'   => $kwitansiData,
            ]);

        } catch (Exception $e) {
            \Log::error("Cek Pembayaran PAUD Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
        
    }

    public function showInvoiceForm()
    {
        return view('invoicepaud');
    }
}
