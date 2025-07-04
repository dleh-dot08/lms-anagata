<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Invoice Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Cek Invoice Sekolah</h2>
        <p class="text-center text-sm text-gray-500 mb-6">Masukkan NPSN untuk mencari invoice sekolah</p>

        <div class="mb-4">
            <label for="npsnInput" class="block text-sm font-medium text-gray-700 mb-1">NPSN:</label>
            <input type="text" id="npsnInput" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 12345678">
        </div>

        <button onclick="searchInvoice()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Cari Invoice</button>

        <div id="loader" class="mt-4 text-center hidden">
            <div class="w-8 h-8 border-4 border-blue-300 border-t-blue-600 rounded-full animate-spin mx-auto"></div>
        </div>

        <div id="result-area" class="mt-6 text-sm text-gray-800"></div>
    </div>

    <script>
        const npsnInput = document.getElementById("npsnInput");
        const resultArea = document.getElementById("result-area");
        const loader = document.getElementById("loader");

        async function searchInvoice() {
            const npsn = npsnInput.value.trim();
            resultArea.innerHTML = "";
            if (!npsn) {
                resultArea.innerHTML = `<p class="text-red-500 font-semibold">NPSN wajib diisi.</p>`;
                return;
            }

            loader.classList.remove('hidden');

            try {
                const response = await fetch("{{ url('/cek-invoice') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ npsn: npsn })
                });

                loader.classList.add('hidden');

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();

                if (data.success) {
                    resultArea.innerHTML = `
                        <div class="text-green-600 font-semibold mb-2">Invoice ditemukan:</div>
                        <p><strong>Nama Sekolah:</strong> ${data.sekolahNama}</p>
                        <p><strong>No Invoice:</strong> ${data.noInvoice}</p>
                        <a href="${data.pdfUrl}" target="_blank" class="inline-block mt-4 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Lihat / Unduh Invoice</a>
                    `;
                } else {
                    resultArea.innerHTML = `<p class="text-red-500 font-semibold">${data.message}</p>`;
                }
            } catch (error) {
                loader.classList.add('hidden');
                resultArea.innerHTML = `<p class="text-red-500 font-semibold">Terjadi error: ${error.message}</p>`;
                console.error("Fetch error:", error);
            }
        }

        // Enter key support
        npsnInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") searchInvoice();
        });
    </script>

</body>
</html>
