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
    <link rel="stylesheet" href="{{ asset('koding_ka25/style.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom fixed-top">
      <div class="container-fluid d-flex justify-content-between align-items-center mx-lg-5">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img src="{{ asset('koding_ka25/logo_all.png') }}" alt="" style="height: 70px" />
        </a>

        <!-- Menu tengah (hanya muncul di layar besar) -->
        <div class="d-none d-lg-flex mx-auto">
          <ul class="navbar-nav gap-4">
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="#">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="#fasilitator">Fasilitator</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id/faq">FAQ</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id">LMS-RuangAnagata</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Tambahkan style agar konten tidak tertutup navbar -->
    <style>
      body {
        padding-top: 80px;
      }
    </style>


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
              Anagata Academy adalah Lembaga Kursus dan Pelatihan (LKP) berizin resmi yang memberikan pelatihan digital dan pengembangan bakat serta kemampuan sumber daya manusia Indonesia.
            </p>
            <a
               href="#tabel-admin"
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
                src="/koding_ka25/section.jpg"
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
            <h4 class="fw-bold text-primary mb-3">Pendidikan Menengah Atas / Kejuruan (SMA/SMK)</h4>
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
              Anagata Academy adalah <strong>Lembaga Kursus dan Pelatihan (LKP)</strong> resmi yang menyediakan program peningkatan skill digital bagi SDM Indonesia.
            </p>
            <div class="mt-4">
              <h1 class="display-4 fw-bold">1,200+</h1>
              <p class="fs-5">Kreator CodingMU di seluruh Indonesia</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg">
              <iframe src="https://www.youtube.com/embed/EN2CaOJf1To?si=I3JIFY9ukNxpc48T&amp;controls=0"
                      title="Yuk Ngulik Coding, Mulai dari Sejarahnya!" allowfullscreen></iframe>
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
              src="/koding_ka25/mengapa1.png"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 1"
            />
          </div>
          <div class="col-md-6">
            <img
              src="/koding_ka25/mengapa2.JPG"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 2"
            />
          </div>

          <!-- Baris bawah -->
          <div class="col-md-4">
            <img
              src="/koding_ka25/mengapa3.png"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 3"
            />
          </div>
          <div class="col-md-4">
            <img
              src="/koding_ka25/mengapa4.jpg"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 4"
            />
          </div>
          <div class="col-md-4">
            <img
              src="/koding_ka25/mengapa5.jpg"
              class="img-fluid rounded-4 w-100 h-100 object-fit-cover"
              alt="Pelatihan 5"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Section 1: Judul -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="row mb-4">
            <h2 class="text-primary fw-bold fs-1">Fasilitator Kami</h2>
            <p class="fs-5 text-muted">
              Pelatihan ini didampingi oleh fasilitator profesional dari Anagata Academy (CodingMU), yang terdiri dari <strong>tim ahli di bidang teknologi, pendidikan, dan pengembangan kurikulum digital</strong>. Para fasilitator merupakan praktisi dan edukator yang telah berpengalaman luas dalam mengajar coding kepada berbagai jenjang pendidikan, mulai dari anak-anak hingga pendidik profesional. Dengan latar belakang sebagai pengembang, instruktur, dan perancang program pembelajaran interaktif, tim ini menggabungkan pendekatan pedagogis modern dengan penguasaan teknis untuk memastikan proses pelatihan berjalan efektif, aplikatif, dan menyenangkan.
            </p>
          </div>
      </div>
    </section>

    <section class="py-5 bg-light">
      <div class="container">
        <div class="row mb-4">
            <h2 class="text-primary fw-bold fs-1">Alur Pendaftaran</h2>
            <div class="text-center">
              <img src="/koding_ka25/alur_pendaftaran.jpeg" alt="Alur Pendaftaran" class="img-fluid rounded-4" style="max-width: 75%; height: auto;">
            </div>
          </div>
      </div>
    </section>
    <!-- Section 1: Judul -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="row mb-4">
          <h2 class="text-primary fw-bold fs-1">Lokasi kegiatan</h2>
          <p class="fs-5 text-muted">
            Pelatihan didampingi oleh fasilitator profesional dari 
            <strong>Anagata Academy (CodingMU)</strong> yang berpengalaman dalam dunia pendidikan dan teknologi.
          </p>
          <div class="table-container mt-4">
            <table class="table table-bordered table-striped" id="lokasiTable">
              <thead class="table-primary">
                <tr>
                  <th>Provinsi</th>
                  <th>Kabupaten/Kota</th>
                  <th>Jenjang</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 1: Judul -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="row mb-4 text-center">
          <h2 class="text-primary fw-bold fs-1">Admin Wilayah</h2>
        </div>

        <!-- Tabel Wilayah Admin -->
        <div id="tabel-admin" class="table-responsive">
          <table class="table table-bordered table-hover table-striped align-middle text-center bg-white shadow-sm">
            <thead class="table-primary">
              <tr>
                <th>No</th>
                <th>Wilayah</th>
                <th>Nama Admin</th>
                <th>No. Kontak</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>1</td><td>Bengkulu, Riau, Banten</td><td>Admin 1</td><td><a href="https://wa.me/6285173085391?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 851-7308-5391</a></td></tr>
              <tr><td>2</td><td>Sumatera Selatan</td><td>Admin 2</td><td><a href="https://wa.me/6285173101589?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 851-7310-1589</a></td></tr>
              <tr><td>3</td><td>Lampung, Sulawesi Selatan</td><td>Admin 3</td><td><a href="https://wa.me/6285121241229?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 851-2124-1229</a></td></tr>
              <tr><td>4</td><td>DKI Jakarta, Jambi</td><td>Admin 4</td><td><a href="https://wa.me/6285128012198?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 851-2801-2198</a></td></tr>
              <tr><td>5</td><td>Jawa Barat</td><td>Admin 5</td><td><a href="https://wa.me/6285880808822?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 858-8080-8822</a></td></tr>
              <tr><td>6</td><td>Jawa Timur</td><td>Admin 6</td><td><a href="https://wa.me/6289696127612?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 896-9612-7612</a></td></tr>
              <tr><td>7</td><td>D.I Yogyakarta</td><td>Admin 7</td><td><a href="https://wa.me/6285121261643?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 851-2126-1643</a></td></tr>
              <tr><td>8</td><td>NTB, Aceh</td><td>Admin 8</td><td><a href="https://wa.me/6285173101589?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 851-7310-1589</a></td></tr>
              <tr><td>9</td><td>Maluku Utara</td><td>Admin 9</td><td><a href="https://wa.me/6281287385104?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 812-8738-5104</a></td></tr>
              <tr><td>10</td><td>Kalimantan Timur, Jawa Tengah</td><td>Admin 10</td><td><a href="https://wa.me/6281271966076?text=Halo%2C%20Perkenalkan%20saya%20dari%0ANama%3A%20%0ASekolah%3A%20%0AKeperluan%3A%20" target="_blank"><i class="fab fa-whatsapp text-success me-1"></i>+62 812-7196-6076</a></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>


    <script>
      const lokasiData = [
          { "provinsi": "Aceh", "kabupaten": "Kota Lhoksumawe", "jenjang": "SD" },
          { "provinsi": "Aceh", "kabupaten": "Kota Lhoksumawe", "jenjang": "SMP" },
          { "provinsi": "Aceh", "kabupaten": "Kota Lhoksumawe", "jenjang": "SMA" },

          { "provinsi": "Banten", "kabupaten": "Kab. Tanggerang", "jenjang": "SMP" },
          { "provinsi": "Banten", "kabupaten": "Kota Tanggerang", "jenjang": "SMA/K" },

          { "provinsi": "D.I Yogyakarta", "kabupaten": "Kab Bantul", "jenjang": "SD" },
          { "provinsi": "D.I Yogyakarta", "kabupaten": "Kab Bantul", "jenjang": "SMP" },
          { "provinsi": "D.I Yogyakarta", "kabupaten": "Kab. Kulon Progo", "jenjang": "SMA/K" },

          { "provinsi": "D.K.I Jakarta", "kabupaten": "Jakarta Selatan", "jenjang": "SMP" },

          { "provinsi": "Jawa Barat", "kabupaten": "Kab. Bogor", "jenjang": "SD" },
          { "provinsi": "Jawa Barat", "kabupaten": "Kab. Bogor", "jenjang": "SMA/K" },
          { "provinsi": "Jawa Barat", "kabupaten": "Kab. kuningan", "jenjang": "SD" },
          { "provinsi": "Jawa Barat", "kabupaten": "Kab. kuningan", "jenjang": "SMP" },
          { "provinsi": "Jawa Barat", "kabupaten": "Kab. Garut", "jenjang": "SMA/K" },
          { "provinsi": "Jawa Barat", "kabupaten": "Kab. Karawang", "jenjang": "SMA/K" },

          { "provinsi": "Jawa Tengah", "kabupaten": "Kab. Wonosobo", "jenjang": "SD" },
          { "provinsi": "Jawa Tengah", "kabupaten": "Kab. Wonosobo", "jenjang": "SMP" },,
          { "provinsi": "Jawa Tengah", "kabupaten": "Kab. Wonosobo", "jenjang": "SMA/K" },
          { "provinsi": "Jawa Tengah", "kabupaten": "Kota Salatiga", "jenjang": "SMA/K" },

          { "provinsi": "Jawa Timur", "kabupaten": "Kab. Jombang", "jenjang": "SD" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kab. Magetan", "jenjang": "SMA/K" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kab. Malang", "jenjang": "SD" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kab. Malang", "jenjang": "SMA/K" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kab. Sidoarjo", "jenjang": "SD" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kab. Tulong agung", "jenjang": "SD" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kota Madiun", "jenjang": "SD" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kota Madiun", "jenjang": "SMP" },
          { "provinsi": "Jawa Timur", "kabupaten": "Kota Madiun", "jenjang": "SMA/K" },

          { "provinsi": "Sumatera Selatan", "kabupaten": "Kab. Ogan Komering Ulu Timur", "jenjang": "SD" },
          { "provinsi": "Sumatera Selatan", "kabupaten": "Kab. Ogan Komering Ulu Timur", "jenjang": "SMP" },
          { "provinsi": "Sumatera Selatan", "kabupaten": "Kab. Ogan Komering Ulu Timur", "jenjang": "SMA/K" },

          { "provinsi": "Lampung", "kabupaten": "Kab. Lampung Barat", "jenjang": "SD" },
          { "provinsi": "Lampung", "kabupaten": "Kab. Lampung Barat", "jenjang": "SMA/K" },
          { "provinsi": "Lampung", "kabupaten": "Kab. Lampung Barat", "jenjang": "SMP" },
          { "provinsi": "Lampung", "kabupaten": "Kab. Lampung Utara", "jenjang": "SMP" },
          { "provinsi": "Lampung", "kabupaten": "Kab. Lampung Utara", "jenjang": "SMA/K" },
          { "provinsi": "Lampung", "kabupaten": "Kab. Lampung Utara", "jenjang": "SD" },

          { "provinsi": "NTB", "kabupaten": "Kab. Bima", "jenjang": "SD" },
          { "provinsi": "NTB", "kabupaten": "Kab. Bima", "jenjang": "SMA/K" },
          { "provinsi": "NTB", "kabupaten": "Kab. Bima", "jenjang": "SMP" },

          { "provinsi": "Riau", "kabupaten": "Kota Pekanbaru", "jenjang": "SMA/K" },

          { "provinsi": "Sulawesi Selatan", "kabupaten": "Bulukumba", "jenjang": "SD" },
          { "provinsi": "Sulawesi Selatan", "kabupaten": "Bulukumba", "jenjang": "SMP" },

          { "provinsi": "Maluku Utara", "kabupaten": "Kota Tidore Kepulauan", "jenjang": "SMA/K" },
      ];

      const tbody = document.querySelector("#lokasiTable tbody");

      lokasiData.forEach(item => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${item.provinsi}</td>
          <td>${item.kabupaten}</td>
          <td>${item.jenjang}</td>
        `;
        tbody.appendChild(row);
      });
    </script>



  </body>
</html>
