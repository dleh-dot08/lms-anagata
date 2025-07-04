<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Invoice Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Cek Invoice Sekolah</h2>
        <p class="text-center text-sm text-gray-500 mb-4">Gunakan fitur ini untuk melihat invoice berdasarkan NPSN sekolah.</p>

        <!-- ✅ Langkah-langkah -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-md text-sm text-gray-700">
            <ol class="list-decimal list-inside space-y-1">
                <li>Masukkan NPSN sekolah pada kolom di bawah.</li>
                <li>Klik tombol <strong>"Cari Invoice"</strong>.</li>
                <li>Jika ditemukan, klik tombol <strong>"Lihat / Unduh Invoice"</strong>.</li>
            </ol>
        </div>

        <!-- ✅ Input -->
        <div class="mb-4">
            <label for="npsnInput" class="block text-sm font-medium text-gray-700 mb-1">NPSN:</label>
            <input type="text" id="npsnInput" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 12345678">
        </div>

        <button onclick="searchInvoice()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Cari Invoice</button>

        <div id="loader" class="mt-4 text-center hidden">
            <div class="w-8 h-8 border-4 border-blue-300 border-t-blue-600 rounded-full animate-spin mx-auto"></div>
        </div>

        <!-- ✅ Hasil -->
        <div id="result-area" class="mt-6 text-sm text-gray-800"></div>

        <!-- ✅ Preview PDF -->
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
            resultArea.innerHTML = "";
            pdfPreview.classList.add('hidden');
            invoiceFrame.src = "";

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
                        <div class="text-green-600 font-semibold mb-2">✅ Invoice ditemukan:</div>
                        <p><strong>Nama Sekolah:</strong> ${data.sekolahNama}</p>
                        <p><strong>No Invoice:</strong> ${data.noInvoice}</p>
                        <a href="${data.pdfUrl}" target="_blank" class="inline-block mt-4 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Lihat / Unduh Invoice</a>
                    `;

                    // ✅ Tampilkan iframe preview
                    if (data.pdfUrl) {
                        invoiceFrame.src = data.pdfUrl;
                        pdfPreview.classList.remove('hidden');
                    }

                } else {
                    resultArea.innerHTML = `<p class="text-red-500 font-semibold">${data.message}</p>`;
                }
            } catch (error) {
                loader.classList.add('hidden');
                resultArea.innerHTML = `<p class="text-red-500 font-semibold">Terjadi error: ${error.message}</p>`;
                console.error("Fetch error:", error);
            }
        }

        // Tekan enter langsung cari
        npsnInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") searchInvoice();
        });
    </script>

</body>
</html>
