<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Invoice - LPD Anagata Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6; /* Warna abu-abu muda */
        }
        .container-custom {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #2563eb; /* Biru */
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
            font-size: 1em;
        }
        .btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }
        .btn-danger {
            background-color: #dc2626; /* Merah */
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
        }
        .btn-danger:hover {
            background-color: #b91c1c;
        }
        #result-area {
            margin-top: 30px;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .success-message {
            color: #16a34a; /* Hijau */
            font-weight: bold;
            margin-bottom: 15px;
        }
        .error-message {
            color: #dc2626; /* Merah */
            font-weight: bold;
            margin-bottom: 15px;
        }
        #loader {
            display: none;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .container-custom {
                padding: 20px;
            }
            h1 {
                font-size: 1.5em;
            }
            p {
                font-size: 1em;
            }
            .btn {
                padding: 10px 20px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="container-custom text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Cari Invoice Anda</h1>
        <p class="text-gray-600 mb-6">Masukkan NPSN sekolah Anda untuk melihat atau mengunduh invoice.</p>

        <div class="form-group">
            <label for="npsnInput" class="block text-gray-700 text-sm font-bold mb-2">NPSN:</label>
            <input type="text" id="npsnInput" placeholder="Contoh: 10101010" maxlength="10"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <button class="btn" onclick="searchInvoice()">Cari Invoice</button>

        <div id="loader"></div>
        <div id="result-area">
            </div>
    </div>

    <script>
        const npsnInput = document.getElementById('npsnInput');
        const resultArea = document.getElementById('result-area');
        const loader = document.getElementById('loader');

        // !!! PENTING: GANTI DENGAN URL API EXECUTABLE DARI DEPLOYMENT APPS SCRIPT ANDA !!!
        const APPS_SCRIPT_API_URL = '{{ url("/cek-invoice") }}'

        // URL Publik Google Form Anda
        const GOOGLE_FORM_URL = 'https://docs.google.com/forms/d/e/1FAIpQLSdX4uYgDk3eX9c2qZ0tJ8R6cWvV_7M2hY7pL9Q/viewform'; // GANTI INI

        async function searchInvoice() {
            const npsn = npsnInput.value.trim();
            resultArea.innerHTML = ''; // Clear previous results

            if (!npsn) {
                resultArea.innerHTML = '<p class="error-message">Mohon masukkan NPSN.</p>';
                return;
            }

            loader.style.display = 'block'; // Show loader

            try {
                const response = await fetch(APPS_SCRIPT_API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        functionName: 'searchInvoiceByNPSN', // Nama fungsi Apps Script yang ingin dipanggil
                        params: [npsn] // Array parameter yang dikirim ke fungsi
                    }),
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json(); // Assuming Apps Script returns JSON

                loader.style.display = 'none'; // Hide loader

                if (data.success) {
                    resultArea.innerHTML = `
                        <p class="success-message">Invoice ditemukan untuk NPSN ${npsn}:</p>
                        <p><b>Nomor Invoice:</b> ${data.noInvoice}</p>
                        <p><b>Nama Sekolah:</b> ${data.sekolahNama}</p>
                        <a href="${data.pdfUrl}" target="_blank" class="btn">Lihat / Unduh Invoice</a>
                        <a href="${GOOGLE_FORM_URL}" class="btn btn-danger">Submit Respons Lain</a>
                    `;
                } else {
                    resultArea.innerHTML = `
                        <p class="error-message">${data.message}</p>
                        <a href="${GOOGLE_FORM_URL}" class="btn btn-danger">Submit Ulang Formulir</a>
                    `;
                }
            } catch (error) {
                loader.style.display = 'none'; // Hide loader
                resultArea.innerHTML = `<p class="error-message">Terjadi kesalahan: ${error.message}. Mohon coba lagi nanti.</p>`;
                console.error("Error calling Apps Script API:", error);
            }
        }

        // Add event listener for Enter key
        npsnInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                searchInvoice();
            }
        });
    </script>
</body>
</html>