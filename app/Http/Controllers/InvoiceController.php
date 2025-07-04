<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class InvoiceController extends Controller
{
    // URL publik CSV untuk sheet "Form Responses 1"
    const FORM_RESPONSES_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSNzBpELuuwAn8mGFO3f5iKnmOGB1TWToRYNTouAS5I7bP6mRPSR6GdyBhCjtybEtO6ftxv1REe5DQo/pub?gid=2102280756&single=true&output=csv';

    // URL publik CSV untuk sheet "NPSN"
    const NPSN_MASTER_CSV_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSNzBpELuuwAn8mGFO3f5iKnmOGB1TWToRYNTouAS5I7bP6mRPSR6GdyBhCjtybEtO6ftxv1REe5DQo/pub?gid=1103969526&single=true&output=csv';

    /**
     * Memproses permintaan AJAX untuk mencari invoice berdasarkan NPSN.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkInvoice(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string',
        ]);

        $inputNPSN = trim($request->input('npsn'));
        $client = new Client();
        $invoiceDetails = [];

        try {
            // --- 1. Ambil data dari "Form Responses 1" ---
            $response1 = $client->get(self::FORM_RESPONSES_CSV_URL);
            $csvData1 = (string) $response1->getBody();
            $rows1 = array_map('str_getcsv', preg_split("/((\r?\n)|(\r\n?))/", $csvData1));
            $headers1 = array_shift($rows1);

            $formHeaderMap = [
                'Timestamp'       => array_search('Timestamp', $headers1),
                'EMAIL'           => array_search('EMAIL', $headers1),
                'NPSN_Form'       => array_search('NPSN', $headers1),
                'Nomor Invoice'   => array_search('Column 1', $headers1), // Asumsi Column 1 adalah Nomor Invoice
                'URL PDF Invoice' => array_search('Column 2', $headers1), // Asumsi Column 2 adalah URL PDF Invoice
            ];

            foreach ($formHeaderMap as $key => $index) {
                if ($index === false) {
                    throw new Exception("Kolom '{$key}' tidak ditemukan di 'Form Responses 1' CSV. Mohon periksa header kolom.");
                }
            }

            $validFormResponses = [];
            foreach ($rows1 as $row) {
                $maxIndex1 = max(array_values($formHeaderMap));
                if (count($row) > $maxIndex1 && !empty($row[$formHeaderMap['NPSN_Form']])) {
                    if (strcasecmp(trim($row[$formHeaderMap['NPSN_Form']]), $inputNPSN) === 0) {
                        $nomorInvoice = trim($row[$formHeaderMap['Nomor Invoice']]);
                        $urlPdfInvoice = trim($row[$formHeaderMap['URL PDF Invoice']]);

                        // Hanya tambahkan jika Nomor Invoice DAN URL PDF Invoice tidak kosong
                        if (!empty($nomorInvoice) && !empty($urlPdfInvoice)) {
                            $validFormResponses[] = [
                                'timestamp'      => $row[$formHeaderMap['Timestamp']],
                                'email'          => $row[$formHeaderMap['EMAIL']],
                                'npsn'           => trim($row[$formHeaderMap['NPSN_Form']]),
                                'no_invoice'     => $nomorInvoice,
                                'url_pdf_invoice'=> $urlPdfInvoice,
                                // Convert timestamp to a comparable format (e.g., Unix timestamp) for sorting
                                'parsed_timestamp' => \DateTime::createFromFormat('d/m/Y H:i:s', $row[$formHeaderMap['Timestamp']])->getTimestamp(),
                            ];
                        }
                    }
                }
            }

            if (empty($validFormResponses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN ditemukan, namun tidak ada data invoice lengkap (Nomor Invoice atau URL PDF) yang valid untuk NPSN ini.',
                ]);
            }

            // Sort by timestamp descending (latest first)
            usort($validFormResponses, function($a, $b) {
                return $b['parsed_timestamp'] - $a['parsed_timestamp'];
            });

            // Ambil entri terbaru yang valid
            $foundFormResponse = $validFormResponses[0];

            // --- 2. Ambil data dari sheet "NPSN" ---
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
                    throw new Exception("Kolom '{$key}' tidak ditemukan di 'NPSN' CSV. Mohon periksa header kolom.");
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
                // NPSN tidak ditemukan di master data sekolah, tapi mungkin invoice validnya ada.
                // Tetap kembalikan invoice, tapi dengan detail sekolah 'N/A'
                $foundNPSNMasterData = [
                    'nama_sekolah'      => 'Tidak Ditemukan',
                    'provinsi'          => 'Tidak Ditemukan',
                    'lokus'             => 'Tidak Ditemukan',
                    'lokasi_pelatihan'  => 'Tidak Ditemukan',
                ];
            }

            // --- 3. Gabungkan dan kembalikan data ---
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
}