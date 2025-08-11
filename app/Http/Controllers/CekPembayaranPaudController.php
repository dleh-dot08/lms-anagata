<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class CekPembayaranPaudController extends Controller
{
    const FORM_RESPONSES_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vReWEoxjTD_Qvtygf2doavEexLwHB19qwrruKfKNaPIWnDKdRmNyePbcuC4dKSElsioM7sKgbxmvQ4A/pub?gid=995897769&single=true&output=csv';
    const KWITANSI_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQafhQuw8fTlxB3yFb5391UirQQixHd3WWjOjTscAZotO_SVg1U7qDwEWTZWa_b6DdST_W1IDIFVStZ/pub?gid=1667067318&single=true&output=csv';

    /** ================= INVOICE ================= **/
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
            $response = $client->get(self::FORM_RESPONSES_CSV_URL);
            $csvData = (string) $response->getBody();
            $rows = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData));
            $headers = array_shift($rows);

            $headerMap = [
                'NPSN_Form'       => array_search('NPSN SEKOLAH', $headers),
                'Nama_Paud'       => array_search('NAMA SEKOLAH ', $headers),
                'Nama_Peserta'    => array_search('NAMA LENGKAP (GELAR LENGKAP)', $headers),
                'Nomor_Invoice'   => array_search('NO INVOICE', $headers),
                'URL_PDF'         => array_search('URL', $headers),
            ];

            foreach ($headerMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom '{$key}' tidak ditemukan di CSV Invoice.");
                }
            }

            $found = false;
            $resultData = [];
            foreach ($rows as $row) {
                $maxIndex = max(array_values($headerMap));
                if (count($row) > $maxIndex && !empty($row[$headerMap['NPSN_Form']])) {
                    if (strcasecmp(trim($row[$headerMap['NPSN_Form']]), $inputNPSN) === 0) {
                        if (!empty($row[$headerMap['Nomor_Invoice']]) && !empty($row[$headerMap['URL_PDF']])) {
                            $found = true;
                            $resultData = [
                                'nama_paud'      => $row[$headerMap['Nama_Paud']],
                                'nama_peserta'   => $row[$headerMap['Nama_Peserta']],
                                'npsn'           => $inputNPSN,
                                'nomor_invoice'  => $row[$headerMap['Nomor_Invoice']],
                                'url_invoice'    => $row[$headerMap['URL_PDF']],
                            ];
                            break;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'status'  => $found ? 'sudah' : 'belum',
                'message' => $found
                    ? 'Invoice PAUD sudah pernah dibuat.'
                    : 'Invoice PAUD belum pernah dibuat.',
                'data'    => $found ? $resultData : null
            ]);
        } catch (Exception $e) {
            \Log::error("Cek Invoice PAUD Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** ================= KWITANSI ================= **/
    public function showKwitansiForm()
    {
        return view('kka-paud.kwitansipaud');
    }

    public function cekKwitansiPaud(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string',
        ]);

        $inputNPSN = trim($request->input('npsn'));
        $client = new Client();

        try {
            $response = $client->get(self::KWITANSI_CSV_URL);
            $csvData = (string) $response->getBody();
            $rows = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData));
            $headers = array_shift($rows);

            // Normalisasi header (case-insensitive & trim)
            $normalizedHeaders = array_map(fn($h) => strtolower(trim($h)), $headers);

            $headerMap = [
                'NPSN'           => array_search('npsn', $normalizedHeaders),
                'Nama_Paud'      => array_search('nama sekolah', $normalizedHeaders),
                'Nama_Lokus'     => array_search('nama lokus', $normalizedHeaders),
                'Nomor_Invoice'  => array_search('no invoice', $normalizedHeaders),
                'Bukti_Transfer' => array_search('bukti transfer', $normalizedHeaders),
                'No_Recipt'      => array_search('no recipt', $normalizedHeaders),
                'URL_Kwitansi'   => array_search('url', $normalizedHeaders),
            ];

            foreach ($headerMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom '{$key}' tidak ditemukan di CSV Kwitansi.");
                }
            }

            $found = false;
            $resultData = [];
            $bestScore = -1;

            foreach ($rows as $row) {
                $maxIndex = max(array_values($headerMap));
                if (count($row) > $maxIndex && !empty($row[$headerMap['NPSN']])) {
                    if (strcasecmp(trim($row[$headerMap['NPSN']]), $inputNPSN) === 0) {

                        // Hitung skor kelengkapan
                        $score = 0;
                        foreach (['Nomor_Invoice', 'Bukti_Transfer', 'No_Recipt', 'URL_Kwitansi'] as $field) {
                            if (!empty(trim($row[$headerMap[$field]]))) {
                                $score++;
                            }
                        }

                        // Pilih baris dengan skor kelengkapan tertinggi
                        if ($score > $bestScore) {
                            $bestScore = $score;
                            $found = true;
                            $resultData = [
                                'npsn'           => $row[$headerMap['NPSN']],
                                'nama_paud'      => $row[$headerMap['Nama_Paud']],
                                'nama_lokus'     => $row[$headerMap['Nama_Lokus']],
                                'nomor_invoice'  => $row[$headerMap['Nomor_Invoice']],
                                'bukti_transfer' => $row[$headerMap['Bukti_Transfer']],
                                'no_recipt'      => $row[$headerMap['No_Recipt']],
                                'url_kwitansi'   => $row[$headerMap['URL_Kwitansi']],
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'status'  => $found ? 'sudah' : 'belum',
                'message' => $found
                    ? 'Kwitansi PAUD sudah pernah dibuat.'
                    : 'Kwitansi PAUD belum pernah dibuat.',
                'data'    => $found ? $resultData : null
            ]);
        } catch (Exception $e) {
            \Log::error("Cek Kwitansi PAUD Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }
}
