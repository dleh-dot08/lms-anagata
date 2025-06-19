<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Pelatihan Guru Koding & KA – Anagata Academy (CodingMU)</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
      <div class="container-fluid d-flex justify-content-between align-items-center mx-lg-5">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img src="./img/logo-asn.png" alt="Koding Next" style="height: 70px" />
        </a>

        <!-- Menu tengah -->
        <div class="d-none d-lg-flex mx-auto">
          <ul class="navbar-nav gap-4">
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="#">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="#">Fasilitator</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="#">FAQ</a>
            </li>
          </ul>
        </div>

        <!-- Tombol kanan -->
        <div>
          <a href="#" class="btn btn-dark fw-bold rounded-2 px-4">Hubungi Kami</a>
        </div>
      </div>
    </nav>

    <!-- Hero Section Full Width Background -->
    <section class="position-relative overflow-hidden" style="background: linear-gradient(to bottom right, #fffaf3, #fff0e1);">
      <div class="container py-5 position-relative z-2">
        <div class="row align-items-center">
          
          <!-- Kolom Teks -->
          <div class="col-lg-6 mb-5 mb-lg-0">
            <h1 class="fw-bold display-4 mb-3">
              Anagata Academy <strong>(CodingMU)</strong> 
            </h1>
            <p class="fs-5 text-muted">
              Merupakan LPD resmi yang mengadakan diklat guru Koding dan KA untuk jenjang SD - SMA/SMK dengan metode praktik dan proyek.
            </p>
            <a
              href="#"
              class="btn btn-lg mt-3 px-4 py-2 rounded-pill text-white shadow"
              style="background: linear-gradient(135deg, #ff9900, #ff6600); border: none;"
            >
              Daftarkan Guru Anda Sekarang
            </a>
          </div>

          <!-- Kolom Gambar -->
          <div class="col-lg-6 position-relative">
            <div class="position-relative">
              <img
                src="./img/section.jpg"
                alt="Pelatihan Guru"
                class="img-fluid rounded-4 shadow-lg"
                style="max-height: 500px; object-fit: cover; position: relative; z-index: 2;"
              />
              <!-- Shape background -->
              <div
                class="position-absolute top-50 start-50 translate-middle z-1"
                style="width: 120%; height: 120%; background: radial-gradient(circle, #ffe7cc 20%, transparent 70%); border-radius: 50%;">
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Skema Pelatihan Section -->
    <section class="py-5" style="background-color: #f7fafd;">
      <div class="container">
        <h2 class="text-center fw-bold mb-5">
          Skema <span style="color: #002B5B;">Pelatihan</span>
        </h2>

        <div class="row g-4">
          <!-- Card 1 -->
          <div class="col-md-4">
            <div class="card h-100 shadow border-0">
              <div class="card-body text-white text-center"
                  style="background-color: #002B5B; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <div class="mb-3">
                  <i class="bi bi-journal-bookmark-fill fs-2"></i>
                </div>
                <h5 class="card-title fw-bold mb-3">IN-1 Pelatihan dan Pendalaman Konsep</h5>
                <span class="badge bg-light text-dark px-3 py-2">Durasi: 40 JP</span>
              </div>
              <div class="card-body pt-3">
                <p class="card-text">
                  Peserta pelatihan akan memperoleh pemahaman fundamental mengenai dasar-dasar coding dan kecerdasan artifisial (AI), serta merancang strategi pembelajaran digital yang kontekstual dan sesuai dengan jenjang pendidikan masing-masing.
                </p>
              </div>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="col-md-4">
            <div class="card h-100 shadow border-0">
              <div class="card-body text-white text-center"
                  style="background-color: #FF8C00; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <div class="mb-3">
                  <i class="bi bi-house-door-fill fs-2"></i>
                </div>
                <h5 class="card-title fw-bold mb-3">ON: Praktik Implementasi di Sekolah</h5>
                <span class="badge bg-light text-dark px-3 py-2">Durasi: 120 JP</span>
              </div>
              <div class="card-body pt-3">
                <p class="card-text">
                  Peserta pelatihan mengintegrasikan materi ke praktik pembelajaran di sekolah. Didampingi fasilitator melalui mentoring, pemantauan perkembangan, serta umpan balik terarah.
                </p>
              </div>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="col-md-4">
            <div class="card h-100 shadow border-0">
              <div class="card-body text-white text-center"
                  style="background-color: #002B5B; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <div class="mb-3">
                  <i class="bi bi-people-fill fs-2"></i>
                </div>
                <h5 class="card-title fw-bold mb-3">IN-2: Refleksi, Berbagi Praktik Baik, dan Penguatan</h5>
                <span class="badge bg-light text-dark px-3 py-2">Durasi: 20 JP</span>
              </div>
              <div class="card-body pt-3">
                <p class="card-text">
                  Peserta kembali ke sesi tatap muka untuk refleksi lapangan, berbagi praktik baik, dan memperkuat strategi implementasi berkelanjutan guna mendukung transformasi pembelajaran digital.
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Program Berdasarkan Jenjang Pendidikan -->
    <section class="container py-5 mt-4">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Program Berdasarkan <span style="color: #002B5B">Jenjang Pendidikan</span></h2>
        <p class="text-muted">Materi dirancang sesuai tingkat dan kebutuhan peserta didik dari jenjang SD, SMP hingga SMA/SMK</p>
      </div>

      <div class="row g-4">
        <!-- Jenjang SD -->
        <div class="col-md-4">
          <div class="p-4 h-100 rounded-4 border shadow-sm bg-white">
            <h4 class="fw-bold text-primary mb-3">Pendidikan Dasar (SD)</h4>
            <ul class="ps-3">
              <li>Pengenalan perangkat teknologi & fungsi sehari-hari</li>
              <li>Literasi digital & etika dunia maya</li>
              <li>Berpikir komputasional dasar (pola, dekomposisi, algoritma)</li>
              <li>Konsep dasar AI & contoh nyata (mis: rekomendasi film)</li>
              <li>Memahami dampak AI dengan cara sederhana</li>
            </ul>
          </div>
        </div>

        <!-- Jenjang SMP -->
        <div class="col-md-4">
          <div class="p-4 h-100 rounded-4 border shadow-sm bg-white">
            <h4 class="fw-bold text-primary mb-3">Pendidikan Menengah Pertama (SMP)</h4>
            <ul class="ps-3">
              <li>Koding visual (Scratch, Blockly) untuk eksplorasi logika</li>
              <li>Konsep struktur data sederhana (variabel, array)</li>
              <li>Logika pemrograman: percabangan & perulangan</li>
              <li>Pengenalan klasifikasi data & dasar Machine Learning</li>
              <li>Etika & pengaruh AI terhadap masyarakat</li>
            </ul>
          </div>
        </div>

        <!-- Jenjang SMA/SMK -->
        <div class="col-md-4">
          <div class="p-4 h-100 rounded-4 border shadow-sm bg-white">
            <h4 class="fw-bold text-primary mb-3">Pendidikan Menengah Atas (SMA/SMK)</h4>
            <ul class="ps-3">
              <li>Pemrograman teks (Python) & pengembangan aplikasi sederhana</li>
              <li>Struktur data lanjutan (list, dict, set) & algoritma efisien</li>
              <li>Pengenalan Machine Learning: klasifikasi & regresi dasar</li>
              <li>Dasar Natural Language Processing (NLP) & Prompt Engineering</li>
              <li>Pengantar Computer Vision & etika tata kelola AI</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Video Section -->
    <section>
      <div class="mx-auto my-5" style="max-width: 720px">
        <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow">
          <iframe
            src="https://www.youtube.com/embed/6df39018-dffb-435e-bd4b-3553950b7d80"
            title="Empowering Educators with AI"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </section>

    <!-- Fasilitas Pelatihan -->
    <section class="py-5" style="background-color: #f8fafc;">
      <div class="container">
        <h2 class="text-center fw-bold mb-5">
          Fasilitas <span style="color: #002B5B;">Program</span> Kami
        </h2>

        <div class="row g-4">
          <!-- Fasilitas 1 -->
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border shadow-sm h-100 transition">
              <div class="mb-3 text-primary fs-1">
                <i class="bi bi-book-half"></i>
              </div>
              <h5 class="fw-semibold">Materi Terstruktur & Siap Pakai</h5>
              <p class="mb-0 text-muted">
                Modul belajar lengkap dan tersusun sistematis sesuai kebutuhan kurikulum digital masa kini.
              </p>
            </div>
          </div>

          <!-- Fasilitas 2 -->
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border shadow-sm h-100 transition">
              <div class="mb-3 text-warning fs-1">
                <i class="bi bi-display"></i>
              </div>
              <h5 class="fw-semibold">Platform Akses Belajar Online</h5>
              <p class="mb-0 text-muted">
                Sistem pembelajaran daring fleksibel yang dapat diakses di berbagai perangkat kapan saja.
              </p>
            </div>
          </div>

          <!-- Fasilitas 3 -->
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border shadow-sm h-100 transition">
              <div class="mb-3 text-success fs-1">
                <i class="bi bi-award"></i>
              </div>
              <h5 class="fw-semibold">Sertifikasi Resmi Digital</h5>
              <p class="mb-0 text-muted">
                Peserta mendapatkan sertifikat digital sebagai bukti kompetensi di bidang teknologi dan edukasi.
              </p>
            </div>
          </div>

          <!-- Fasilitas 4 -->
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border shadow-sm h-100 transition">
              <div class="mb-3 text-danger fs-1">
                <i class="bi bi-person-video3"></i>
              </div>
              <h5 class="fw-semibold">Pendampingan Belajar Langsung</h5>
              <p class="mb-0 text-muted">
                Dapatkan bimbingan intensif dari fasilitator profesional sepanjang pelaksanaan program.
              </p>
            </div>
          </div>

          <!-- Fasilitas 5 -->
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border shadow-sm h-100 transition">
              <div class="mb-3 text-info fs-1">
                <i class="bi bi-chat-dots"></i>
              </div>
              <h5 class="fw-semibold">Forum & Komunitas Diskusi</h5>
              <p class="mb-0 text-muted">
                Wadah kolaborasi peserta untuk berbagi pengalaman, bertanya, dan saling mendukung secara aktif.
              </p>
            </div>
          </div>

          <!-- Fasilitas 6 (Opsional / Tambahan) -->
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border shadow-sm h-100 transition">
              <div class="mb-3 text-secondary fs-1">
                <i class="bi bi-lightbulb"></i>
              </div>
              <h5 class="fw-semibold">Studi Kasus & Proyek Nyata</h5>
              <p class="mb-0 text-muted">
                Peserta akan menyelesaikan tugas berbasis proyek yang merefleksikan permasalahan dunia nyata.
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Mengapa Anagata Academy -->
    <section class="py-5 text-white" style="background: linear-gradient(to bottom right, #0d1b2a, #1b263b);">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <h2 class="fw-bold display-5 mb-4">Kenapa <span style="color: #fca311;">Anagata Academy?</span></h2>
            <p class="fs-5 mb-3">
              Anagata Academy adalah <strong>Lembaga Kursus dan Pelatihan (LKP)</strong> resmi yang menyediakan program peningkatan skill digital bagi SDM Indonesia, baik formal maupun informal.
            </p>
            <p class="fs-6 text-white-50 mb-2"><strong>2021</strong> – Kami mulai memberikan layanan pelatihan digital melalui platform <strong>CodingMU</strong></p>
            <p class="fs-6 text-white-50 mb-2"><strong>2022 - sekarang</strong> – CodingMU berkembang menjadi platform pengembangan <em>skill digital</em> nasional, mendukung pendidikan Indonesia secara berkelanjutan.</p>
            <div class="mt-4">
              <h1 class="display-4 fw-bold">1,200+</h1>
              <p class="fs-5">Kreator CodingMU di seluruh Indonesia</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg">
              <iframe src="https://www.youtube.com/embed/6df39018-dffb-435e-bd4b-3553950b7d80"
                      title="Empowering Educators with AI" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-light py-5">
      <div class="container text-center">
        <div class="row g-3">
          <!-- Baris atas -->
          <div class="col-md-6">
            <img
              src="./img/mengapa1.png"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 1"
            />
          </div>
          <div class="col-md-6">
            <img
              src="./img/mengapa2.JPG"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 2"
            />
          </div>

          <!-- Baris bawah -->
          <div class="col-md-4">
            <img
              src="./img/mengapa3.png"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 3"
            />
          </div>
          <div class="col-md-4">
            <img
              src="./img/mengapa4.jpg"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 4"
            />
          </div>
          <div class="col-md-4">
            <img
              src="./img//mengapa5.jpg"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 5"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Fasilitator -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="row align-items-start">
          <!-- Kolom Kiri (Teks) -->
          <div class="col-lg-4 mb-4">
            <h2 class="text-primary fw-bold mb-3 fs-1">Fasilitator Kami</h2>
            <p class="fs-5" style="text-align: justify">
              Pelatihan didampingi oleh fasilitator profesional dari Koding Next yang berpengalaman
              dalam dunia pendidikan dan teknologi.
            </p>
            <ul class="list-unstyled fs-5">
              <li>✅ Instruktur Bersertifikat</li>
              <li>✅ Latar Belakang Teknologi & Pendidikan</li>
              <li>✅ Pendekatan Interaktif dan Aplikatif</li>
            </ul>
          </div>

          <!-- Kolom Kanan (Dinamis) -->
          <div class="col-lg-8">
            <div id="fasilitator-container" class="d-flex flex-nowrap overflow-auto gap-3 pb-3"></div>
          </div>
        </div>
      </div>
    </section>


    <script src="./js/index.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Taruh ini sebelum </body> -->
    <script>
      fetch('./fasilitator.json')
        .then((res) => res.json())
        .then((data) => {
          const container = document.getElementById('fasilitator-container');
          data.forEach((fasil) => {
            const card = `
              <div class="card shadow-sm rounded-4" style="max-width: 20rem; flex: 0 0 auto">
                <img src="${fasil.foto}" class="card-img-top rounded-top-4" style="height: 350px; object-fit: cover" />
                <div class="card-body">
                  <p class="text-muted mb-1">${fasil.kota}</p>
                  <h5 class="fw-bold text-primary mb-1">${fasil.nama}</h5>
                  <p class="mb-1">🎓 ${fasil.pendidikan}</p>
                  <p class="mb-1">🏫 ${fasil.kampus}</p>
                  <p class="text-muted">${fasil.pengalaman}</p>
                </div>
              </div>
            `;
            container.innerHTML += card;
          });
        });
    </script>
  </body>
</html>
