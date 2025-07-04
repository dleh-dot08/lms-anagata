<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Kwitansi Pembayaran</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        form { display: flex; margin-bottom: 20px; }
        form input[type="text"] { flex-grow: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px 0 0 4px; }
        form button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 0 4px 4px 0; cursor: pointer; }
        form button:hover { background-color: #0056b3; }
        .result-box { border: 1px solid #eee; padding: 15px; border-radius: 5px; background-color: #e9ecef; }
        .error-message { color: red; font-weight: bold; text-align: center; }
        .success-message { color: green; font-weight: bold; text-align: center; }
        .detail-item { margin-bottom: 8px; }
        .detail-item strong { display: inline-block; width: 120px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cek Kwitansi Pembayaran</h1>

        <form action="{{ route('receipt.check') }}" method="POST">
            @csrf
            <input type="text" name="receipt_number" placeholder="Masukkan Nomor Kwitansi Anda (mis: REC0001-AA/07/2025-TOT)" value="{{ old('receipt_number', $receiptNumber) }}" required>
            <button type="submit">Cek Kwitansi</button>
        </form>

        @if ($errorMessage)
            <div class="error-message">
                <p>{{ $errorMessage }}</p>
            </div>
        @elseif ($receiptDetails)
            <div class="result-box success-message">
                <p>Kwitansi ditemukan!</p>
            </div>
            <div class="result-box">
                <div class="detail-item"><strong>Nomor Kwitansi:</strong> {{ $receiptDetails['nomor_receipt'] }}</div>
                <div class="detail-item"><strong>Nama Sekolah:</strong> {{ $receiptDetails['nama_sekolah'] }}</div>
                <div class="detail-item"><strong>NPSN:</strong> {{ $receiptDetails['npsn'] }}</div>
                <div class="detail-item"><strong>Nomor Invoice:</strong> {{ $receiptDetails['no_invoice'] }}</div>
                <div class="detail-item"><strong>Email Pendaftar:</strong> {{ $receiptDetails['email'] }}</div>
                <div class="detail-item">
                    <strong>Link Kwitansi (PDF):</strong> 
                    @if ($receiptDetails['url_pdf'])
                        <a href="{{ $receiptDetails['url_pdf'] }}" target="_blank">Lihat Kwitansi</a>
                    @else
                        Tidak tersedia
                    @endif
                </div>
                </div>
        @elseif ($receiptNumber && !$receiptDetails)
            <div class="result-box error-message">
                <p>Nomor Kwitansi "{{ $receiptNumber }}" tidak ditemukan.</p>
                <p>Pastikan nomor yang Anda masukkan sudah benar.</p>
            </div>
        @endif
    </div>
</body>
</html>