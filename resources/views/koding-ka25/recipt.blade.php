<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Kwitansi Pembayaran Anagata Academy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
            box-sizing: border-box;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 768px;
            margin-top: 20px;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.2;
        }
        .guide-section {
            background-color: #ecf8f8;
            border-left: 5px solid #20c997;
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .guide-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #00897b;
            margin-bottom: 15px;
        }
        .guide-section ol {
            list-style: decimal;
            padding-left: 20px;
        }
        .guide-section ol li {
            margin-bottom: 10px;
            color: #334155;
            line-height: 1.5;
        }
        .guide-section ol li strong {
            color: #0d9488;
        }
        .form-group {
            display: flex;
            margin-bottom: 20px;
        }
        .form-group input[type="text"] {
            flex-grow: 1;
            padding: 14px 18px;
            border: 1px solid #d1d5db;
            border-radius: 8px 0 0 8px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 1rem;
        }
        .form-group input[type="text"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .form-group button {
            padding: 14px 28px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
            font-size: 1rem;
        }
        .form-group button:hover {
            background-color: #2563eb;
        }
        .message-box {
            padding: 18px 25px;
            border-radius: 8px;
            margin-top: 25px;
            font-size: 1.05rem;
            text-align: center;
            font-weight: 500;
            line-height: 1.6;
        }
        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #ef4444;
        }
        .success-message {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #10b981;
        }
        .detail-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px 30px;
            margin-top: 25px;
            line-height: 1.6;
        }
        .detail-item {
            display: flex;
            margin-bottom: 12px;
            align-items: baseline;
            flex-wrap: wrap;
        }
        .detail-item strong {
            min-width: 170px;
            color: #4a5568;
            font-weight: 600;
            margin-right: 10px;
        }
        .detail-item span {
            flex-grow: 1;
            color: #2d3748;
        }
        .detail-item a {
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.2s;
        }
        .detail-item a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cek Kwitansi Pembayaran <br> Anagata Academy</h1>

        <div class="guide-section">
            <h2>Panduan Pencarian Kwitansi</h2>
            <ol>
                <li>
                    Silakan masukkan **Nomor Pokok Sekolah Nasional (NPSN)** Anda pada kolom di bawah ini.
                    NPSN adalah identitas unik sekolah Anda.
                </li>
                <li>
                    Pastikan NPSN yang Anda masukkan sudah benar dan sesuai dengan data yang Anda daftarkan.
                </li>
                <li>
                    Klik tombol **"Cari Kwitansi"**.
                </li>
                <li>
                    Jika NPSN Anda ditemukan dan memiliki kwitansi, detail kwitansi terbaru akan ditampilkan di halaman ini.
                    Anda dapat mengunduh file PDF kwitansi Anda langsung dari sini.
                </li>
            </ol>
        </div>

        <form action="{{ route('receipt.check') }}" method="POST" class="form-group">
            @csrf
            <input
                type="text"
                name="npsn" placeholder="Masukkan NPSN Sekolah Anda (contoh: 12345678)"
                value="{{ old('npsn', $inputNPSN) }}" required
            >
            <button type="submit">Cari Kwitansi</button>
        </form>

        @if ($errorMessage)
            <div class="message-box error-message">
                <p>{{ $errorMessage }}</p>
            </div>
        @elseif ($receiptDetails)
            <div class="message-box success-message">
                <p>Kwitansi ditemukan untuk NPSN **{{ $receiptDetails['npsn'] }}**!</p>
                <p>Detail pembayaran terbaru Anda:</p>
            </div>
            <div class="detail-card">
                <div class="detail-item"><strong>Nomor Kwitansi:</strong> <span>{{ $receiptDetails['nomor_receipt'] }}</span></div>
                <div class="detail-item"><strong>Nama Sekolah:</strong> <span>{{ $receiptDetails['nama_sekolah'] }}</span></div>
                <div class="detail-item"><strong>NPSN:</strong> <span>{{ $receiptDetails['npsn'] }}</span></div>
                <div class="detail-item"><strong>Nomor Invoice:</strong> <span>{{ $receiptDetails['no_invoice'] }}</span></div>
                <div class="detail-item"><strong>Email Pendaftar:</strong> <span>{{ $receiptDetails['email'] }}</span></div>
                <div class="detail-item"><strong>Lokasi Pelatihan:</strong> <span>{{ $receiptDetails['nama_lokus'] }}</span></div>
                <div class="detail-item"><strong>Tanggal Input:</strong> <span>{{ \Carbon\Carbon::parse($receiptDetails['timestamp'])->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</span></div>

                <hr class="my-4 border-gray-300">

                <div class="detail-item"><strong>Bukti Transfer:</strong> <span>
                    @if ($receiptDetails['bukti_transfer'] && filter_var($receiptDetails['bukti_transfer'], FILTER_VALIDATE_URL))
                        <a href="{{ $receiptDetails['bukti_transfer'] }}" target="_blank">Lihat Bukti Transfer</a>
                    @else
                        Tidak tersedia
                    @endif
                </span></div>
                <div class="detail-item">
                    <strong>Link Kwitansi (PDF):</strong>
                    <span>
                        @if ($receiptDetails['url_pdf'] && filter_var($receiptDetails['url_pdf'], FILTER_VALIDATE_URL))
                            <a href="{{ $receiptDetails['url_pdf'] }}" target="_blank">Unduh Kwitansi (PDF)</a>
                        @else
                            Tidak tersedia
                        @endif
                    </span>
                </div>
            </div>
        @elseif ($inputNPSN && !$receiptDetails)
            <div class="message-box error-message">
                <p>NPSN "{{ htmlspecialchars($inputNPSN) }}" tidak ditemukan atau belum memiliki kwitansi.</p>
                <p>Pastikan NPSN yang Anda masukkan sudah benar dan proses kwitansi sudah selesai.</p>
            </div>
        @endif
    </div>
</body>
</html>