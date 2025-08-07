<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Cek Status Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .status-badge {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-waiting {
            background-color: #ffc107;
            color: #fff;
        }
        .status-approved {
            background-color: #28a745;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="fw-bold mb-0">Cek Status Pendaftaran Guru</h2>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="registrationTable">
                            <thead>
                                <tr></tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <td colspan="100" class="text-center py-4">
                                      <div class="spinner-border text-primary" role="status">
                                          <span class="visually-hidden">Loading...</span>
                                      </div>
                                      <p class="mt-2">Memuat data pendaftaran...</p>
                                  </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ganti 'YOUR_WEB_APP_URL_HERE' dengan URL Apps Script yang Anda salin di Langkah 1
        const spreadsheetUrl = 'YOUR_WEB_APP_URL_HERE';

        fetch(spreadsheetUrl)
            .then(response => {
              if (!response.ok) {
                throw new Error('Gagal mengambil data: ' + response.statusText);
              }
              return response.json();
            })
            .then(data => {
                const table = document.getElementById('registrationTable');
                const thead = table.querySelector('thead tr');
                const tbody = table.querySelector('tbody');

                if (data.length > 0) {
                    const header = Object.keys(data[0]);
                    thead.innerHTML = '';
                    header.forEach(col => {
                        const th = document.createElement('th');
                        th.textContent = col;
                        thead.appendChild(th);
                    });

                    tbody.innerHTML = '';
                    data.forEach(item => {
                        const tr = document.createElement('tr');
                        header.forEach(col => {
                            const td = document.createElement('td');
                            const cellValue = item[col];

                            if (cellValue && typeof cellValue === 'string') {
                                if (cellValue.toLowerCase() === 'menunggu') {
                                    td.innerHTML = `<span class="status-badge status-waiting">Menunggu</span>`;
                                } else if (cellValue.toLowerCase() === 'disetujui') {
                                    td.innerHTML = `<span class="status-badge status-approved">Disetujui</span>`;
                                } else {
                                    td.textContent = cellValue;
                                }
                            } else {
                                td.textContent = cellValue;
                            }
                            tr.appendChild(td);
                        });
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="100" class="text-center py-4">Data pendaftaran tidak ditemukan.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                const table = document.getElementById('registrationTable');
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = `<tr><td colspan="100" class="text-center text-danger py-4">Gagal memuat data. Silakan coba lagi.</td></tr>`;
            });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>