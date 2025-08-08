<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class CekPembayaranPaudController extends Controller
{
    const FORM_RESPONSES_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vReWEoxjTD_Qvtygf2doavEexLwHB19qwrruKfKNaPIWnDKdRmNyePbcuC4dKSElsioM7sKgbxmvQ4A/pub?gid=995897769&single=true&output=csv';

    // Fungsi helper untuk cari kolom secara case-insensitive
    private function findColumnIndex($headers, $name)
    {
        foreach ($headers as $index => $header) {
            if (strcasecmp(trim($header), trim($name)) === 0) {
                return $index;
            }
        }
        return false;
    }

    public function showInvoiceForm()
    {
        return view('kka-paud.invoicepaud');
    }

    public function cekInvoicePaud(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string',
        ]);

        $inputNPSN = trim($request->input('npsn'));
        $client = new Client();

        try {
            // Ambil CSV
            $response1 = $client->get(self::FORM_RESPONSES_CSV_URL);
            $csvData1 = (string) $response1->getBody();
            $rows1 = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData1));
            $headers1 = array_shift($rows1);

            // Map kolom berdasarkan nama yang ada di CSV
            $formHeaderMap = [
                'NPSN_Form'       => $this->findColumnIndex($headers1, 'NPSN SEKOLAH'),
                'Nomor Invoice'   => $this->findColumnIndex($headers1, 'NO INVOICE'),
                'URL PDF Invoice' => $this->findColumnIndex($headers1, 'URL'),
            ];

            // Validasi semua kolom ditemukan
            foreach ($formHeaderMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom untuk {$key} tidak ditemukan di CSV.");
                }
            }

            // Cari data NPSN
            $found = false;
            foreach ($rows1 as $row) {
                $maxIndex1 = max(array_values($formHeaderMap));
                if (count($row) > $maxIndex1 && !empty($row[$formHeaderMap['NPSN_Form']])) {
                    if (strcasecmp(trim($row[$formHeaderMap['NPSN_Form']]), $inputNPSN) === 0) {
                        $nomorInvoice = trim($row[$formHeaderMap['Nomor Invoice']]);
                        $urlPdfInvoice = trim($row[$formHeaderMap['URL PDF Invoice']]);
                        if (!empty($nomorInvoice) && !empty($urlPdfInvoice)) {
                            $found = true;
                            break;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'npsn'    => $inputNPSN,
                'status'  => $found ? 'sudah' : 'belum',
                'message' => $found
                    ? 'Invoice PAUD sudah pernah dibuat.'
                    : 'Invoice PAUD belum pernah dibuat.',
            ]);

        } catch (Exception $e) {
            \Log::error("Cek Invoice PAUD Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }
}
