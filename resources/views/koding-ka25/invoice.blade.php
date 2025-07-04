<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Invoice Sekolah Anagata Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Konten mulai dari atas */
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
        .spinner {
            width: 32px;
            height: 32px;
            border: 4px solid #bfdbfe;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="container">
        <h1>Cek Invoice Sekolah <br> Anagata Academy</h1>

        <div class="guide-section">
            <h2>Panduan Pencarian Invoice</h2>
            <ol>
                <li>
                    Silakan masukkan **Nomor Pokok Sekolah Nasional (NPSN)** Anda pada kolom di bawah ini.
                    NPSN adalah identitas unik sekolah Anda.
                </li>
                <li>
                    Pastikan NPSN yang Anda masukkan sudah benar dan sesuai dengan data yang Anda daftarkan.
                </li>
                <li>
                    Klik tombol **"Cari Invoice"**.
                </li>
                <li>
                    Jika NPSN Anda ditemukan dan memiliki invoice, detail invoice terbaru akan ditampilkan di halaman ini.
                    Anda dapat melihat atau mengunduh file PDF invoice Anda langsung dari sini.
                </li>
            </ol>
        </div>

        <div class="form-group">
            <label for="npsnInput" class="sr-only">NPSN:</label>
            <input type="text" id="npsnInput"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Masukkan NPSN Sekolah Anda (contoh: 12345678)"
                   value="{{ old('npsn') }}"
                   required>
            <button type="button" onclick="searchInvoice()">Cari Invoice</button>
        </div>

        <div id="loader" class="mt-4 text-center hidden">
            <div class="spinner"></div>
        </div>

        <div id="result-area" class="mt-6"></div>
        <div id="pdf-preview" class="mt-6 hidden">
            <iframe id="invoice-frame" src="" class="w-full h-96 rounded border border-gray-300"></iframe>
        </div>
    </div>

    <script>
        const npsnInput = document.getElementById("npsnInput");
        const resultArea = document.getElementById("result-area");
        const loader = document.getElementById("loader");
        const pdfPreview = document.getElementById("pdf-preview");
        const invoiceFrame = document.getElementById("invoice-frame");

        async function searchInvoice() {
            const npsn = npsnInput.value.trim();
            resultArea.innerHTML = ""; // Bersihkan area hasil sebelumnya
            pdfPreview.classList.add('hidden'); // Sembunyikan preview PDF
            invoiceFrame.src = ""; // Bersihkan src iframe

            if (!npsn) {
                resultArea.innerHTML = `<div class="message-box error-message"><p>NPSN wajib diisi.</p></div>`;
                return;
            }

            loader.classList.remove('hidden'); // Tampilkan loader

            try {
                const response = await fetch("{{ route('invoice.check') }}", { // Menggunakan helper route()
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ npsn: npsn })
                });

                loader.classList.add('hidden'); // Sembunyikan loader

                if (!response.ok) {
                    const errorJson = await response.json(); // Coba parse JSON untuk detail error
                    const errorMessage = errorJson.message || `Terjadi masalah saat mengambil data (HTTP status: ${response.status}).`;
                    throw new Error(errorMessage);
                }

                const data = await response.json(); // Parse respons JSON

                if (data.success) {
                    // Format timestamp dari 'DD/MM/YYYY HH:mm:ss' ke format yang lebih mudah dibaca JS
                    let formattedTimestamp = 'N/A';
                    if (data.timestamp) {
                        // Pecah string tanggal
                        const [datePart, timePart] = data.timestamp.split(' ');
                        const [day, month, year] = datePart.split('/');
                        // Buat string dalam format 'YYYY-MM-DDTHH:mm:ss' agar Date() object lebih mudah memparsingnya
                        const isoFormattedDate = `${year}-${month}-${day}T${timePart}`;
                        const dateObj = new Date(isoFormattedDate);

                        if (!isNaN(dateObj)) { // Pastikan tanggal valid
                            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                            formattedTimestamp = dateObj.toLocaleDateString('id-ID', options) + ' WIB';
                        }
                    }

                    // Bangun HTML untuk pesan sukses dan detail card
                    let detailsHtml = `
                        <div class="message-box success-message">
                            <p>Invoice ditemukan untuk NPSN <strong>${escapeHtml(data.npsn)}</strong>!</p>
                            <p>Detail pembayaran terbaru Anda:</p>
                        </div>
                        <div class="detail-card">
                            <div class="detail-item"><strong>NPSN:</strong> <span>${escapeHtml(data.npsn || 'N/A')}</span></div>
                            <div class="detail-item"><strong>Nama Sekolah:</strong> <span>${escapeHtml(data.sekolahNama || 'N/A')}</span></div>
                            <div class="detail-item"><strong>Provinsi:</strong> <span>${escapeHtml(data.provinsi || 'N/A')}</span></div>
                            <div class="detail-item"><strong>Lokus:</strong> <span>${escapeHtml(data.lokus || 'N/A')}</span></div>
                            <div class="detail-item"><strong>Lokasi Pelatihan:</strong> <span>${escapeHtml(data.lokasi_pelatihan || 'N/A')}</span></div>
                            <div class="detail-item"><strong>Email Pendaftar:</strong> <span>${escapeHtml(data.email || 'N/A')}</span></div>
                            <div class="detail-item"><strong>No Invoice:</strong> <span>${escapeHtml(data.noInvoice || 'N/A')}</span></div>
                            <div class="detail-item"><strong>Tanggal Input:</strong> <span>${formattedTimestamp}</span></div>

                            <hr class="my-4 border-gray-300">

                            <div class="detail-item">
                                <strong>Link Invoice (PDF):</strong>
                                <span>
                    `;

                    // Tambahkan link PDF jika tersedia dan valid
                    if (data.pdfUrl && isValidUrl(data.pdfUrl)) {
                        detailsHtml += `<a href="${escapeHtml(data.pdfUrl)}" target="_blank">Lihat / Unduh Invoice</a>`;
                    } else {
                        detailsHtml += `Tidak tersedia`;
                    }
                    detailsHtml += `
                                </span>
                            </div>
                        </div>
                    `;
                    resultArea.innerHTML = detailsHtml;

                    // Tampilkan iframe preview jika URL PDF valid
                    if (data.pdfUrl && isValidUrl(data.pdfUrl)) {
                        invoiceFrame.src = data.pdfUrl;
                        pdfPreview.classList.remove('hidden');
                    }

                } else {
                    // Pesan error dari server
                    resultArea.innerHTML = `<div class="message-box error-message"><p>${escapeHtml(data.message || 'Invoice tidak ditemukan.')}</p></div>`;
                }
            } catch (error) {
                loader.classList.add('hidden');
                resultArea.innerHTML = `<div class="message-box error-message"><p>Terjadi error: ${escapeHtml(error.message)}</p></div>`;
                console.error("Fetch error:", error);
            }
        }

        // Fungsi helper untuk mencegah XSS
        function escapeHtml(text) {
            if (text === null || text === undefined) return 'N/A';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Fungsi helper untuk validasi URL sederhana
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (e) {
                return false;
            }
        }

        // Tekan enter untuk mencari
        npsnInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // Mencegah form submission default
                searchInvoice();
            }
        });
    </script>

</body>
</html>