<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class InvoiceController extends Controller
{
    const FORM_RESPONSES_CSV_URL = 'https://docs.google.com/spreadsheets/d/1zwJA7YZJTDqV3O5S31Y4rl93skO-kfZ71sBemM8xiS4/export?format=csv&gid=2102280756';
    const NPSN_MASTER_CSV_URL = 'https://docs.google.com/spreadsheets/d/1zwJA7YZJTDqV3O5S31Y4rl93skO-kfZ71sBemM8xiS4/export?format=csv&gid=1103969526';

    public function checkInvoice(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string',
        ]);

        $inputNPSN = trim($request->input('npsn'));
        $client = new Client();

        try {
            // --- Ambil data dari "Form Responses 1" ---
            $response1 = $client->get(self::FORM_RESPONSES_CSV_URL);
            $csvData1 = (string) $response1->getBody();
            $rows1 = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData1));
            $headers1 = array_shift($rows1);

            $formHeaderMap = [
                'Timestamp'       => array_search('Timestamp', $headers1),
                'EMAIL'           => array_search('EMAIL', $headers1),
                'NPSN_Form'       => array_search('NPSN', $headers1),
                'Nomor Invoice'   => array_search('Column 1', $headers1),
                'URL PDF Invoice' => array_search('Column 2', $headers1),
            ];

            foreach ($formHeaderMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom '{$key}' tidak ditemukan di 'Form Responses 1' CSV.");
                }
            }

            $foundFormResponse = null;
            // Scan dari atas ke bawah, tapi simpan hanya data terakhir yang valid
            foreach ($rows1 as $row) {
                $maxIndex = max(array_values($formHeaderMap));
                if (count($row) > $maxIndex && !empty($row[$formHeaderMap['NPSN_Form']])) {
                    if (strcasecmp(trim($row[$formHeaderMap['NPSN_Form']]), $inputNPSN) === 0) {
                        $nomorInvoice = trim($row[$formHeaderMap['Nomor Invoice']]);
                        $urlPdfInvoice = trim($row[$formHeaderMap['URL PDF Invoice']]);
                        if (!empty($nomorInvoice) && !empty($urlPdfInvoice)) {
                            $foundFormResponse = [
                                'timestamp'         => $row[$formHeaderMap['Timestamp']],
                                'email'             => $row[$formHeaderMap['EMAIL']],
                                'npsn'              => trim($row[$formHeaderMap['NPSN_Form']]),
                                'no_invoice'        => $nomorInvoice,
                                'url_pdf_invoice'   => $urlPdfInvoice,
                            ];
                            // Simpan, tapi terus lanjut loop untuk pastikan data terakhir
                        }
                    }
                }
            }

            if (!$foundFormResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN ditemukan, namun tidak ada data invoice lengkap (Nomor Invoice dan URL PDF) yang valid.',
                ]);
            }

            // --- Ambil data dari sheet "NPSN" ---
            $response2 = $client->get(self::NPSN_MASTER_CSV_URL);
            $csvData2 = (string) $response2->getBody();
            $rows2 = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData2));
            $headers2 = array_shift($rows2);

            $npsnMasterHeaderMap = [
                'NPSN_Master'       => array_search('NPSN', $headers2),
                'Nama Sekolah'      => array_search('Nama Sekolah', $headers2),
                'Provinsi'          => array_search('Provinsi', $headers2),
                'Lokus'             => array_search('Lokus', $headers2),
                'Lokasi Pelatihan'  => array_search('Lokasi Pelatihan', $headers2),
            ];

            foreach ($npsnMasterHeaderMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom '{$key}' tidak ditemukan di 'NPSN' CSV.");
                }
            }

            $foundNPSNMasterData = null;
            foreach ($rows2 as $row) {
                $maxIndex2 = max(array_values($npsnMasterHeaderMap));
                if (count($row) > $maxIndex2 && !empty($row[$npsnMasterHeaderMap['NPSN_Master']])) {
                    if (strcasecmp(trim($row[$npsnMasterHeaderMap['NPSN_Master']]), $inputNPSN) === 0) {
                        $foundNPSNMasterData = [
                            'nama_sekolah'      => $row[$npsnMasterHeaderMap['Nama Sekolah']],
                            'provinsi'          => $row[$npsnMasterHeaderMap['Provinsi']],
                            'lokus'             => $row[$npsnMasterHeaderMap['Lokus']],
                            'lokasi_pelatihan'  => $row[$npsnMasterHeaderMap['Lokasi Pelatihan']],
                        ];
                        break;
                    }
                }
            }

            if (!$foundNPSNMasterData) {
                $foundNPSNMasterData = [
                    'nama_sekolah'      => 'Tidak Ditemukan',
                    'provinsi'          => 'Tidak Ditemukan',
                    'lokus'             => 'Tidak Ditemukan',
                    'lokasi_pelatihan'  => 'Tidak Ditemukan',
                ];
            }

            $invoiceDetails = array_merge($foundFormResponse, $foundNPSNMasterData);

            return response()->json([
                'success'    => true,
                'npsn'       => $invoiceDetails['npsn'],
                'sekolahNama'=> $invoiceDetails['nama_sekolah'],
                'noInvoice'  => $invoiceDetails['no_invoice'],
                'pdfUrl'     => $invoiceDetails['url_pdf_invoice'],
                'email'      => $invoiceDetails['email'],
                'provinsi'   => $invoiceDetails['provinsi'],
                'lokus'      => $invoiceDetails['lokus'],
                'lokasi_pelatihan' => $invoiceDetails['lokasi_pelatihan'],
                'timestamp'  => $invoiceDetails['timestamp'],
            ]);

        } catch (Exception $e) {
            \Log::error("Invoice Check Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function invoicePaudView()
    {
        return view('kka-paud.invoice');
    }

    public function cekInvoicePaud(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string',
        ]);

        $inputNPSN = trim($request->input('npsn'));
        $client = new Client();

        try {
            $FORM_RESPONSES_PAUD_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vReWEoxjTD_Qvtygf2doavEexLwHB19qwrruKfKNaPIWnDKdRmNyePbcuC4dKSElsioM7sKgbxmvQ4A/pub?gid=995897769&single=true&output=csv';
            $MASTER_NPSN_PAUD_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vReWEoxjTD_Qvtygf2doavEexLwHB19qwrruKfKNaPIWnDKdRmNyePbcuC4dKSElsioM7sKgbxmvQ4A/pub?gid=1723572662&single=true&output=csv';

            $response1 = $client->get($FORM_RESPONSES_PAUD_CSV_URL);
            $csvData1 = (string) $response1->getBody();
            $rows1 = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData1));
            $headers1 = array_shift($rows1);

            $formHeaderMap = [
                'NPSN_Form'       => array_search('NPSN', $headers1),
                'Nomor Invoice'   => array_search('Column 1', $headers1),
                'URL PDF Invoice' => array_search('Column 2', $headers1),
            ];

            foreach ($formHeaderMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom '{$key}' tidak ditemukan di CSV Form Responses PAUD.");
                }
            }

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

            $response2 = $client->get($MASTER_NPSN_PAUD_CSV_URL);
            $csvData2 = (string) $response2->getBody();
            $rows2 = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData2));
            $headers2 = array_shift($rows2);

            $npsnIndex2 = array_search('NPSN', $headers2);
            if ($npsnIndex2 === false) {
                throw new Exception("Kolom 'NPSN' tidak ditemukan di CSV Master NPSN PAUD.");
            }

            $npsnExists = false;
            foreach ($rows2 as $row) {
                if (isset($row[$npsnIndex2]) && strcasecmp(trim($row[$npsnIndex2]), $inputNPSN) === 0) {
                    $npsnExists = true;
                    break;
                }
            }

            return response()->json([
                'success' => true,
                'npsn'    => $inputNPSN,
                'status'  => $found ? 'sudah' : 'belum',
                'exists'  => $npsnExists,
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
