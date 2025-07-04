<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Invoice Sekolah Anagata Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #e0e6ed 100%); /* Subtle gradient background */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 60px 20px; /* More padding top/bottom */
            box-sizing: border-box;
        }
        .container {
            background-color: #ffffff;
            padding: 40px; /* Increased padding */
            border-radius: 16px; /* More rounded corners */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Deeper shadow */
            width: 100%;
            max-width: 800px; /* Slightly wider */
            margin-top: 0; /* Remove top margin as body padding handles it */
            border: 1px solid #e2e8f0; /* Subtle border */
        }
        h1 {
            font-size: 2.75rem; /* Larger title */
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px; /* More space */
            line-height: 1.3;
        }
        h1 br {
            display: block; /* Ensures line break */
        }
        .guide-section {
            background-color: #e6f7ff; /* Lighter blue for guide */
            border-left: 6px solid #3498db; /* Stronger border */
            padding: 25px; /* More padding */
            border-radius: 8px;
            margin-bottom: 35px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow for guide */
        }
        .guide-section h2 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #2c7bb1; /* Darker blue */
            margin-bottom: 18px;
            border-bottom: 1px dashed #a0d8f7; /* Dotted line separator */
            padding-bottom: 10px;
        }
        .guide-section ol {
            list-style: decimal;
            padding-left: 25px; /* More indent */
        }
        .guide-section ol li {
            margin-bottom: 12px;
            color: #4a5568;
            line-height: 1.6;
        }
        .guide-section ol li strong {
            color: #0d6e88; /* Darker emphasis */
        }
        .form-group {
            display: flex;
            margin-bottom: 25px; /* More space */
            border: 1px solid #cbd5e0; /* Border around the whole group */
            border-radius: 8px;
            overflow: hidden; /* Ensures input/button stay within rounded border */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .form-group input[type="text"] {
            flex-grow: 1;
            padding: 16px 20px; /* Larger padding */
            border: none; /* Remove individual border */
            outline: none;
            transition: all 0.3s ease-in-out;
            font-size: 1.05rem;
            color: #334155;
            background-color: #fcfcfc;
        }
        .form-group input[type="text"]:focus {
            background-color: #ffffff;
            box-shadow: inset 0 0 0 2px #3b82f6; /* Inner shadow on focus */
        }
        .form-group button {
            padding: 16px 32px; /* Larger padding */
            background-color: #3b82f6;
            color: white;
            border: none; /* Remove individual border */
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease-in-out, transform 0.1s ease;
            font-size: 1.05rem;
            border-left: 1px solid rgba(255,255,255,0.2); /* Subtle separator */
        }
        .form-group button:hover {
            background-color: #2563eb;
            transform: translateY(-1px); /* Subtle lift effect */
        }
        .form-group button:active {
            transform: translateY(0);
            background-color: #1d4ed8;
        }
        .message-box {
            padding: 20px 25px;
            border-radius: 8px;
            margin-top: 30px; /* More space */
            font-size: 1.1rem; /* Slightly larger text */
            text-align: center;
            font-weight: 500;
            line-height: 1.6;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow */
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
            border-radius: 12px; /* More rounded */
            padding: 30px 35px; /* More padding */
            margin-top: 30px; /* More space */
            line-height: 1.7; /* Increased line height for readability */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08); /* Consistent with container shadow */
        }
        /* Using CSS Grid for better alignment of detail items */
        .detail-item {
            display: grid;
            grid-template-columns: 180px 1fr; /* Fixed width for label, rest for value */
            gap: 10px; /* Gap between label and value */
            margin-bottom: 12px;
            align-items: baseline;
        }
        /* Adjust for smaller screens where grid might be too rigid */
        @media (max-width: 600px) {
            .detail-item {
                grid-template-columns: 1fr; /* Stack label and value on small screens */
                gap: 5px;
            }
            .detail-item strong {
                margin-bottom: 4px; /* Space between stacked label and value */
            }
        }
        .detail-item strong {
            color: #4a5568;
            font-weight: 600;
            font-size: 0.95rem; /* Slightly smaller for label */
            text-align: left; /* Align labels left */
        }
        .detail-item span {
            color: #2d3748;
            font-size: 1rem; /* Standard size for value */
            text-align: left; /* Align values left */
            word-break: break-word; /* Ensure long text wraps */
        }
        .detail-item a {
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.2s, text-decoration 0.2s;
            font-weight: 500;
        }
        .detail-item a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        hr {
            border: none;
            border-top: 1px dashed #d1d5db; /* Dashed line for separator */
            margin: 25px 0; /* More vertical space */
        }
        .spinner {
            width: 38px; /* Slightly larger spinner */
            height: 38px;
            border: 5px solid #bfdbfe;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite; /* Slightly faster spin */
            margin: 25px auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        /* Style for iframe preview */
        #pdf-preview iframe {
            border-radius: 8px;
            min-height: 400px; /* Ensure sufficient height */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            background-color: #fefefe; /* Light background for the iframe container */
            padding: 5px; /* Small padding inside the border */
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
                   class="w-full" placeholder="Masukkan NPSN Sekolah Anda (contoh: 12345678)"
                   value="{{ old('npsn') }}"
                   required>
            <button type="button" onclick="searchInvoice()">Cari Invoice</button>
        </div>

        <div id="loader" class="mt-4 text-center hidden">
            <div class="spinner"></div>
        </div>

        <div id="result-area" class="mt-6"></div>
        <div id="pdf-preview" class="mt-6 hidden">
            <iframe id="invoice-frame" src="" class="w-full rounded border border-gray-300"></iframe>
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
            resultArea.innerHTML = "";
            pdfPreview.classList.add('hidden');
            invoiceFrame.src = "";

            if (!npsn) {
                resultArea.innerHTML = `<div class="message-box error-message"><p>NPSN wajib diisi.</p></div>`;
                return;
            }

            loader.classList.remove('hidden');

            try {
                const response = await fetch("{{ route('invoice.check') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ npsn: npsn })
                });

                loader.classList.add('hidden');

                if (!response.ok) {
                    const errorJson = await response.json();
                    const errorMessage = errorJson.message || `Terjadi masalah saat mengambil data (HTTP status: ${response.status}).`;
                    throw new Error(errorMessage);
                }

                const data = await response.json();

                if (data.success) {
                    let formattedTimestamp = 'N/A';
                    if (data.timestamp) {
                        // Pecah string tanggal
                        const [datePart, timePart] = data.timestamp.split(' ');
                        const [day, month, year] = datePart.split('/');
                        // Buat string dalam format 'YYYY-MM-DDTHH:mm:ss' agar Date() object lebih mudah memparsingnya
                        const isoFormattedDate = `${year}-${month}-${day}T${timePart}`;
                        const dateObj = new Date(isoFormattedDate);

                        if (!isNaN(dateObj.getTime())) { // Use getTime() to check for valid date
                            const options = {
                                year: 'numeric', month: 'long', day: 'numeric',
                                hour: '2-digit', minute: '2-digit',
                                hour12: false // Use 24-hour format
                            };
                            formattedTimestamp = dateObj.toLocaleDateString('id-ID', options) + ' WIB';
                        }
                    }

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

                            <hr>

                            <div class="detail-item">
                                <strong>Link Invoice (PDF):</strong>
                                <span>
                    `;

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

                    if (data.pdfUrl && isValidUrl(data.pdfUrl)) {
                        invoiceFrame.src = data.pdfUrl;
                        pdfPreview.classList.remove('hidden');
                    }

                } else {
                    resultArea.innerHTML = `<div class="message-box error-message"><p>${escapeHtml(data.message || 'Invoice tidak ditemukan.')}</p></div>`;
                }
            } catch (error) {
                loader.classList.add('hidden');
                resultArea.innerHTML = `<div class="message-box error-message"><p>Terjadi error: ${escapeHtml(error.message)}</p></div>`;
                console.error("Fetch error:", error);
            }
        }

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

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (e) {
                return false;
            }
        }

        npsnInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                searchInvoice();
            }
        });
    </script>

</body>
</html>