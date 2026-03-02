<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manual Book Medivault</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #334155;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        /* ---- COVER PAGE ---- */
        .cover-page {
            text-align: center;
            padding-top: 150px;
            position: relative;
            height: 100vh;
        }

        .cover-header {
            margin-bottom: 60px;
        }

        .cover-title {
            font-size: 42pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cover-subtitle {
            font-size: 16pt;
            color: #64748b;
            font-weight: normal;
        }

        .cover-box {
            border: 1px solid #cbd5e1;
            padding: 40px;
            margin: 0 auto;
            width: 65%;
            background-color: #f8fafc;
            border-top: 5px solid #2563eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .cover-box h3 {
            font-size: 16pt;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .cover-box p {
            font-size: 12pt;
            color: #475569;
            margin-bottom: 5px;
        }

        .cover-footer {
            position: absolute;
            bottom: 50px;
            width: 100%;
            font-size: 10pt;
            color: #94a3b8;
            text-align: center;
        }

        /* ---- HEADERS ---- */
        .chapter-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 25px;
            margin-top: 20px;
        }

        .chapter-num {
            font-size: 10pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .chapter-title {
            font-size: 20pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 5px;
        }

        /* ---- SECTIONS ---- */
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .module-goal {
            background-color: #f1f5f9;
            padding: 12px 16px;
            border-left: 4px solid #3b82f6;
            margin-bottom: 20px;
            font-size: 10.5pt;
            color: #334155;
        }

        .module-goal strong {
            color: #1e40af;
        }

        p {
            margin-bottom: 12px;
            text-align: justify;
        }

        ul,
        ol {
            margin-bottom: 15px;
            padding-left: 20px;
            text-align: justify;
        }

        li {
            margin-bottom: 6px;
        }

        /* ---- TABLES ---- */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
            font-size: 10pt;
        }

        .table-data th {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
            font-weight: bold;
        }

        .table-data td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        /* ---- TOC (DAFTAR ISI) ---- */
        .toc-title {
            font-size: 24pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 30px;
            text-align: center;
        }

        .toc-group {
            font-weight: bold;
            font-size: 12pt;
            color: #1e293b;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .toc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-bottom: 3px;
        }

        .toc-table td {
            padding: 4px 0;
        }

        .toc-num {
            width: 6%;
            vertical-align: bottom;
            color: #475569;
        }

        .toc-name {
            width: 86%;
            border-bottom: 1px dotted #94a3b8;
            vertical-align: bottom;
            color: #334155;
        }

        .toc-page {
            width: 8%;
            text-align: right;
            vertical-align: bottom;
            font-weight: bold;
            color: #0f172a;
        }

        /* ---- HIGHLIGHTS ---- */
        .note {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 10pt;
            border-left: 4px solid #64748b;
        }

        .important {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 10pt;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
    </style>
</head>

<body>

    {{-- ===================== COVER ===================== --}}
    <div class="cover-page">
        <div class="cover-header">
            <div class="cover-title">{{ strtoupper($app_name) }}</div>
            <div class="cover-subtitle">Sistem Informasi Manajemen Farmasi</div>
        </div>

        <div class="cover-box">
            <h3>MANUAL BOOK</h3>
            <p>Buku Panduan Pengoperasian Sistem</p>
            <p style="margin-top: 20px; font-weight: bold;">{{ $hospital_name }}</p>
        </div>

        <div class="cover-footer">
            <p>Dokumen Resmi Intern - Dibuat secara otomatis</p>
            <p>Tanggal Cetak: {{ $generated_at }} | Versi: {{ $version }}</p>
        </div>
    </div>
    <div class="page-break"></div>

    {{-- ===================== DAFTAR ISI ===================== --}}
    <div class="toc-title">DAFTAR ISI</div>

    <div class="toc-group">BAB 1 — Pendahuluan</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">1.1</td>
            <td class="toc-name">Tentang Sistem Medivault</td>
            <td class="toc-page">3</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">1.2</td>
            <td class="toc-name">Cara Login & Akses Pertama</td>
            <td class="toc-page">3</td>
        </tr>
    </table>

    <div class="toc-group">BAB 2 — Dashboard (Halaman Utama)</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">2.1</td>
            <td class="toc-name">Membaca Dashboard Utama</td>
            <td class="toc-page">4</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">2.2</td>
            <td class="toc-name">Membaca Dashboard Stok</td>
            <td class="toc-page">4</td>
        </tr>
    </table>

    <div class="toc-group">BAB 3 — Master Data (Data Induk)</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">3.1</td>
            <td class="toc-name">Kategori & Satuan Item</td>
            <td class="toc-page">5</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">3.2</td>
            <td class="toc-name">Pendaftaran Item (Obat & Alat Kesehatan)</td>
            <td class="toc-page">5</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">3.3</td>
            <td class="toc-name">Data Supplier</td>
            <td class="toc-page">5</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">3.4</td>
            <td class="toc-name">Gudang dan Unit Layanan</td>
            <td class="toc-page">6</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">3.5</td>
            <td class="toc-name">Manajemen Hak Akses Pengguna</td>
            <td class="toc-page">6</td>
        </tr>
    </table>

    <div class="toc-group">BAB 4 — Pengadaan (Pembelian Barang ke Supplier)</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">4.1</td>
            <td class="toc-name">E-Catalog Harga Item</td>
            <td class="toc-page">7</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">4.2</td>
            <td class="toc-name">Perencanaan Kebutuhan Obat (RKO)</td>
            <td class="toc-page">7</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">4.3</td>
            <td class="toc-name">Permintaan Pengadaan (PR)</td>
            <td class="toc-page">7</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">4.4</td>
            <td class="toc-name">Pemesanan Barang (PO) & Penerimaan</td>
            <td class="toc-page">8</td>
        </tr>
    </table>

    <div class="toc-group">BAB 5 — Inventori (Pengelolaan Stok Gudang)</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">5.1</td>
            <td class="toc-name">Import Stok Awal (Sekali Pakai)</td>
            <td class="toc-page">9</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">5.2</td>
            <td class="toc-name">Melihat Riwayat Kartu Stok</td>
            <td class="toc-page">9</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">5.3</td>
            <td class="toc-name">Distribusi Barang antar Gudang/Depo</td>
            <td class="toc-page">9</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">5.4</td>
            <td class="toc-name">Melakukan Stock Opname</td>
            <td class="toc-page">10</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">5.5</td>
            <td class="toc-name">Penyesuaian (Adjustment) Stok</td>
            <td class="toc-page">10</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">5.6</td>
            <td class="toc-name">Pemusnahan & Retur Barang</td>
            <td class="toc-page">10</td>
        </tr>
    </table>

    <div class="toc-group">BAB 6 — Klinik (Pelayanan Pasien & Bangsal)</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">6.1</td>
            <td class="toc-name">Pembuatan & Penyiapan Resep (Dispensing)</td>
            <td class="toc-page">11</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">6.2</td>
            <td class="toc-name">Permintaan Obat dari Ruangan</td>
            <td class="toc-page">11</td>
        </tr>
    </table>

    <div class="toc-group">BAB 7 — Akuntansi & Laporan</div>
    <table class="toc-table">
        <tr>
            <td class="toc-num">7.1</td>
            <td class="toc-name">Mencatat Jurnal Keuangan</td>
            <td class="toc-page">12</td>
        </tr>
    </table>
    <table class="toc-table">
        <tr>
            <td class="toc-num">7.2</td>
            <td class="toc-name">Mencetak Laporan Stok & Keuangan</td>
            <td class="toc-page">12</td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ===================== BAB 1 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 1</div>
        <div class="chapter-title">Pendahuluan</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Membaca Bab Ini:</strong> Memahami fungsi dasar sistem, cara masuk (login) ke dalam aplikasi, dan
        apa yang harus dilakukan jika Anda lupa kata sandi.
    </div>

    <div class="section-title">1.1 Tentang Sistem Medivault</div>
    <p>Medivault adalah aplikasi komputer yang dibuat untuk mempermudah pekerjaan apoteker, petugas gudang farmasi,
        bagian pembelian, hingga manajemen rumah sakit <strong>{{ $hospital_name }}</strong>. Dengan aplikasi ini,
        seluruh proses pekerjaan mulai dari menerima barang, menyimpan di gudang, hingga memberikan obat ke pasien akan
        tercatat dengan otomatis dan aman di dalam komputer.</p>
    <p>Sistem ini menggantikan cara pencatatan manual di kertas menjadi pencatatan elektronik secara langsung
        (real-time). Artinya, jika obat dikeluarkan dari gudang saat ini, maka jumlah stok di layar komputer akan
        langsung berkurang pada detik itu juga.</p>

    <div class="section-title">1.2 Cara Login & Akses Pertama</div>
    <p>Aplikasi ini hanya dapat diakses oleh karyawan rumah sakit yang telah didaftarkan. Anda membutuhkan <strong>Nama
            Pengguna (Username)</strong> dan <strong>Kata Sandi (Password)</strong> untuk masuk.</p>
    <ol>
        <li>Buka aplikasi web browser di komputer Anda (Gunakan Google Chrome atau Mozilla Firefox).</li>
        <li>Ketikkan alamat situs web sistem pada kolom pencarian di bagian atas browser.</li>
        <li>Masukkan Username dan Password yang telah diberikan oleh pihak Administrasi/IT rumah sakit.</li>
        <li>Tekan tombol masuk.</li>
        <li><strong>Catatan Penting:</strong> Jika ini pertama kalinya Anda masuk, segera pergi ke menu Pengaturan di
            kiri bawah layar, lalu ubah <i>Password</i> Anda demi keamanan.</li>
    </ol>

    <div class="important">
        Jagalah kerahasiaan kata sandi Anda. Setiap kegiatan menambah, mengubah, atau menghapus data menggunakan akun
        Anda akan direkam selamanya oleh sistem sebagai pertanggungjawaban.
    </div>

    <div class="page-break"></div>

    {{-- ===================== BAB 2 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 2</div>
        <div class="chapter-title">Dashboard (Halaman Utama)</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Modul Ini:</strong> Memberikan ringkasan cepat sekilas pandang tentang keseluruhan operasional
        farmasi tanpa harus membuka menu satu per satu. Sangat berguna bagi manajer atau direktur untuk melihat status
        terkini secara cepat.
    </div>

    <div class="section-title">2.1 Membaca Dashboard Utama</div>
    <p>Saat Anda berhasil masuk ke dalam sistem, halaman pertama yang akan muncul adalah <strong>Dashboard
            Utama</strong>. Di halaman ini, Anda akan melihat beberapa kotak angka berukuran besar. Kotak-kotak ini
        merangkum total aktivitas penting:</p>
    <ul>
        <li><strong>Total Item:</strong> Menginformasikan ada berapa macam merk obat atau alat kesehatan yang terdaftar
            di sistem.</li>
        <li><strong>Item Kedaluwarsa:</strong> Menunjukkan peringatan ada berapa jenis obat yang masa pakainya sudah mau
            habis atau sudah lewat. Segera periksa jika angkanya lebih dari nol.</li>
        <li><strong>Item Stok Rendah:</strong> Memberi tahu berapa obat yang jumlahnya di gudang sudah terlalu sedikit
            dan perlu segera dibeli lagi ke supplier.</li>
        <li><strong>Permintaan Menunggu:</strong> Menandakan ada dokumen permintaan pembelian obat yang menunggu
            disetujui oleh atasan.</li>
    </ul>

    <div class="section-title">2.2 Membaca Dashboard Stok</div>
    <p>Selain gambaran umum operasional, terdapat juga <strong>Dashboard Stok</strong> khusus untuk memantau pergerakan
        barang. Anda bisa mengaksesnya di menu sebelah kiri melalui <strong>Kontrol Stok > Dashboard Stok</strong>.</p>
    <p>Di sini Anda bisa melihat grafik garis yang naik turun. Grafik tersebut menggambarkan seberapa banyak barang yang
        masuk (dibeli/diterima) dan keluar (diberikan ke bangsal/pasien) pada bulan tersebut. Ini membantu pihak
        manajemen memahami apakah bulan ini rumah sakit sedang boros mengeluarkan obat atau tidak.</p>

    <div class="page-break"></div>

    {{-- ===================== BAB 3 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 3</div>
        <div class="chapter-title">Master Data (Data Induk)</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Modul Ini:</strong> Menyimpan "kamus data" penting. Anda mendaftarkan nama obat, daftar supplier,
        dan nama gudang di modul ini <strong>SATU KALI SAJA</strong>. Tujuannya, saat Anda melakukan transaksi (misal:
        memesan barang), Anda tidak perlu mengetik nama obat panjang-panjang, cukup memilih dari daftar yang sudah
        dibuat di Data Master ini.
    </div>

    <div class="note">Modul ini sangat penting. Pastikan Anda tidak salah eja atau membuat data ganda saat
        mendaftarkan sesuatu di Master Data, karena dampaknya akan berkelanjutan ke semua modul lain.</div>

    <div class="section-title">3.1 Kategori & Satuan Item</div>
    <p>Sebelum memasukkan nama-nama obat, Anda harus mendaftarkan kelompok obatnya (Kategori) dan bentuk obatnya
        (Satuan).</p>
    <table class="table-data">
        <tr>
            <th width="30%">Jenis Data</th>
            <th width="70%">Contoh Pengisian</th>
        </tr>
        <tr>
            <td>Kategori Item</td>
            <td>Antibiotik, Vitamin, Sirup Penurun Panas, Alat Suntik, Cairan Infus.</td>
        </tr>
        <tr>
            <td>Satuan Item</td>
            <td>Tablet, Kapsul, Botol, Ampul, Kotak, Strip.</td>
        </tr>
    </table>
    <p>Cara menambah: Masuk ke menu Master Data > Kategori (atau Satuan). Klik tombol tambah, ketik namanya, lalu
        simpan.</p>

    <div class="section-title">3.2 Pendaftaran Item (Obat & Alat Kesehatan)</div>
    <p>Ini adalah daftar seluruh barang yang ada di farmasi rumah sakit.</p>
    <ol>
        <li>Masuk ke menu <strong>Master Data > Item Obat & BMHP</strong>.</li>
        <li>Klik tombol tambah.</li>
        <li>Isi kolom yang diminta: Nama Barang (misal: Paracetamol 500mg), kodenya, pilih masuk kategori apa, dan
            satuannya apa.</li>
        <li>Simpan data tersebut. Mulai saat ini, sistem akan mengenali Paracetamol 500mg tersebut untuk segala
            transaksi.</li>
    </ol>

    <div class="section-title">3.3 Data Supplier</div>
    <p>Supplier adalah pihak perusahaan distributor tempat pihak farmasi membeli barang. Anda harus mendaftarkan
        identitas supplier (Nama Perusahaan, Nomor HP perwakilan, Alamat, NPWP) di menu <strong>Master Data >
            Supplier</strong> agar nanti saat membuat nota pemesanan, Anda bisa mencetak data mereka secara otomatis.
    </p>

    <div class="section-title">3.4 Gudang dan Unit Layanan</div>
    <p>Sistem ini membedakan lokasi penyimpanan barang menjadi tempat persinggahan utama (Gudang Induk) dan tempat
        penyaluran harian (Depo atau Unit Bangsal).</p>
    <ul>
        <li><strong>Gudang:</strong> Tempat menyimpan stok besar. Contoh: Gudang Farmasi Pusat.</li>
        <li><strong>Unit Layanan / Depo:</strong> Tempat memberikan obat langsung ke tangan pasien medis atau ruang
            rawat. Contoh: Poli Gigi, ICU, Depo Rawat Jalan.</li>
    </ul>

    <div class="section-title">3.5 Manajemen Hak Akses Pengguna</div>
    <p>Mengatur siapa saja karyawan yang boleh membuka aplikasi ini. Administrator dapat membuatkan akun baru dan
        mengatur jabatannya. Jabatannya (Role) menentukan menu apa yang terlihat di layar. Contoh: Petugas gudang tidak
        bisa melihat laporan uang akuntansi, dan petugas perawat bangsal tidak bisa mengubah nama-nama obat di master
        data.</p>

    <div class="page-break"></div>

    {{-- ===================== BAB 4 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 4</div>
        <div class="chapter-title">Pengadaan (Pembelian Barang)</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Modul Ini:</strong> Menangani semua alur pembelian barang yang datangnya dari luar rumah sakit
        (supplier). Alurnya terstruktur agar rapi: mulai dari merencanakan pembelian, meminta persetujuan uang ke
        Direktur, membuat nota pesanan ke vendor, hingga mencatat ketika mobil supplier datang membawa kardus barang
        masuk ke gudang.
    </div>

    <div class="section-title">4.1 E-Catalog Harga Item</div>
    <p>Menu ini berfungsi sebagai buku catatan daftar harga barang-barang dari tiap supplier. Satu jenis obat (contoh:
        Amoxicillin) bisa disuplai oleh beberapa perusahaan yang berbeda dengan harga jual berbeda. Anda mencatat
        perbandingan harga tersebut disini, sehingga saat bagian pengadaan akan membeli, sistem akan memberikan harga
        dasar secara otomatis di nota.</p>

    <div class="section-title">4.2 Perencanaan Kebutuhan Obat (RKO)</div>
    <p>RKO adalah rencana belanja obat untuk 1 periode panjang (misalnya 1 tahun ke depan). Sistem akan secara otomatis
        menghitung obat apa saja yang sering laku di bulan-bulan sebelumnya, dan menyarankan prediksi jumlah yang harus
        dibeli tahun depan. Data ini kemudian bisa dicetak menjadi file PDF untuk rapat rencana anggaran tahunan dengan
        manajemen.</p>

    <div class="section-title">4.3 Permintaan Pengadaan (Purchase Request / PR)</div>
    <p>Jika gudang melihat stok mulai habis, bagian gudang tidak boleh langsung memesan ke distributor lewat telepon.
        Gudang harus membuat surat izin pembelian secara elektronik lewat menu <strong>Permintaan (PR)</strong>.</p>
    <ol>
        <li>Gudang memilih obat yang stoknya minim.</li>
        <li>Memasukkan jumlah yang ingin dieksekusi dibeli.</li>
        <li>Klik Simpan & Kirim. Status dokumen ini sekarang berubah menjadi menunggu persetujuan Direktur.</li>
    </ol>
    <p>Selanjutnya, Direktur akan melihat dokumen PR tersebut melalui akun beliau. Beliau dapat menekan tombol
        <strong>Setuju</strong> jika setuju, atau <strong>Tolak</strong> jika uang anggaran tidak cukup.</p>

    <div class="section-title">4.4 Pemesanan Barang (PO) & Penerimaan</div>
    <p>Setelah dokumen permintaan (PR) <strong>disetujui</strong> oleh Direktur, barulah bagian Pembelian dapat mengubah
        dokumen izin tersebut menjadi surat pemesanan resmi kepada perusahaan (Purchase Order / PO).</p>
    <ul>
        <li>Dokumen PO dapat dicetak ke selembar kertas resmi rumah sakit lalu dikirimkan ke pihak perusahaan PBF
            (Distributor).</li>
        <li>Ketika barang datang dibawa oleh kurir, gudang membuka menu <strong>Penerimaan Barang</strong>.</li>
        <li>Gudang wajib mengecek fisik barang: berapa kadaluwarsanya, apa nomor batch di bungkus kotaknya, lalu
            memasukkan data itu ke sistem agar sistem menjumlahkan stok tersebut ke komputer.</li>
    </ul>

    <div class="page-break"></div>

    {{-- ===================== BAB 5 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 5</div>
        <div class="chapter-title">Inventori (Pengelolaan Stok Gudang)</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Modul Ini:</strong> Menjaga keakuratan jumlah stok fisik yang ada di ruangan nyata dengan angka
        yang tertulis di dalam database komputer. Modul ini mengharuskan Anda disiplin: setiap ada barang berpindah,
        hilang, atau dikirim internal rumah sakit, HARUS dicatat lewat modul ini.
    </div>

    <div class="section-title">5.1 Import Stok Awal (Sekali Pakai)</div>
    <p>Fitur ini HANYA digunakan ketika aplikasi ini pertama kali dipasang di komputer rumah sakit Anda. Tujuannya untuk
        mencocokkan stok aplikasi kosong dengan barang yang sudah ada menumpuk di rak gudang saat ini. Anda mengunduh
        format kolom Excel, mengisinya, lalu mengunggahnya ke program agar angka berubah secara massal di komputer.</p>

    <div class="section-title">5.2 Melihat Riwayat Kartu Stok</div>
    <p>Kartu stok seperti buku rekening tabungan bank, tapi untuk obat. Di sini tercatat riwayat tanggal berapa obat
        bertambah dan tanggal berapa obat berkurang secara detail satu demi satu transaksi.</p>
    <p>Sistem ini juga memiliki <strong>Monitoring Batch</strong>. Setiap obat yang masuk diwajibkan memiliki nomor
        Batch dan tanggal Kedaluwarsa. Hal ini berguna supaya sistem bisa memperingatkan Anda: "Obat kelompok batch A
        ini akan kedaluwarsa bulan depan!".</p>

    <div class="section-title">5.3 Distribusi Barang antar Gudang/Depo</div>
    <p>Modul ini digunakan ketika Depo Rawat Inap (Depo A) meminta obat dari Gudang Farmasi Pusat demi kebutuhan pasien
        poli besok pagi.</p>
    <ol>
        <li>Depo meminta ke Gudang Utama lewat sistem.</li>
        <li>Gudang Utama membungkus barang fisik dan mengubah setujuh di sistem ke status "Proses". Di detik ini, stok
            Gudang Utama berkurang angkanya.</li>
        <li>Saat barang fisik di bawa pakai troli dan sampai ke tangan Depo A, Depo A mengeklik konfirmasi terima di
            sistem. Di detik ini stok di Depo A bertambah angkanya.</li>
    </ol>

    <div class="section-title">5.4 Melakukan Stock Opname</div>
    <p>Dilakukan rutin (misalnya sebulan sekali). Pekerjaan ini dilakukan dengan menghitung 1 per 1 barang di rak secara
        nyata sambil membawa catatan komputer. Tugasnya sederhana: jika komputer bilang ada 10, lalu di rak fisik hanya
        ada 9 (hilang/jatuh/mencair), maka angka 9 itulah yang ditulis ke dalam sistem opname. Sistem otomatis memotong
        1 barang yang hilang agar data komputer kembali jujur (sesuai asli).</p>

    <div class="section-title">5.5 Penyesuaian (Adjustment) Stok</div>
    <p>Sama prinsipnya dengan Opname, namun Adjustment digunakan untuk situasi tiba-tiba di luar rutinitas bulanan.
        Contoh: saat berjalan, perawat tidak sengaja memecahkan 2 botol sirup di lantai. Anda mencatat kerugian (-2
        botol) tersebut saat itu juga lengkap dengan menuliskan alasannya di kolom sistem.</p>

    <div class="section-title">5.6 Pemusnahan & Retur Barang</div>
    <p>Digunakan jika barang dikembalikan ke luar gudang dan angkanya harus hangus atau berkurang dari rumah sakit:</p>
    <ul>
        <li><strong>Retur:</strong> Mengembalikan barang ke Supplier karena saat dus dibuka obat ternyata cacat produksi
            atau belum lewat masa kadaluwarsa tapi hampir habis.</li>
        <li><strong>Disposal/Pemusnahan:</strong> Penghancuran obat secara legal (misalnya dengan disaksikan BPOM) untuk
            obat yang telat ditarik dan sudah keburu kedaluwarsa. Sistem menyediakan fasilitas untuk mencetak Berita
            Acara (selembar dokumen tanda bukti sah).</li>
    </ul>

    <div class="page-break"></div>

    {{-- ===================== BAB 6 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 6</div>
        <div class="chapter-title">Klinik (Pelayanan Pasien)</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Modul Ini:</strong> Merupakan garis depan (Front-end) dari sistem farmasi. Ketika Dokter
        meresepkan obat ke Pasien atau Perawat bangsal butuh obat habis pakai mendesak, semua proses pemberian barang
        itu dikoordinir dari modul ini.
    </div>

    <div class="section-title">6.1 Pembuatan & Penyiapan Resep (Dispensing)</div>
    <p>Karyawan di Apotek / Depo menerima kertas resep dokter fisik dari tangan pasien atau keluarga pasien. Kemudian,
        karyawan memindahkan data kertas terebut ke dalam aplikasi. Ini disebut pembuatan e-Resep di sistem.</p>
    <p>Proses selanjutnya adalah <strong>Dispensing Obat</strong>. Sistem akan melihat daftar obat yang dipesan, dan
        sistem akan mencarikan obat mana di gudang rak yang kadaluwarsanya paling mepet. Sistem akan menyuruh petugas
        apotek mengambil obat angkatan (Batch) tersebut terlebih dahulu. Ini dinamakan aturan pintar FEFO (First
        Expired, First Out).</p>
    <p>Seketika perawat menekan tombol "Selesai Penyerahan" di komputer, seketika itu jumlah stok depot potong secara
        ajaib.</p>

    <div class="section-title">6.2 Permintaan Obat dari Ruangan (Ward Request)</div>
    <p>Tidak semua obat keluar melalui loket pasien masuk angin biasa. Terkadang ruang operasi, IGD UGD, atau kasur
        rawat inap butuh stok plaster, cairan infus mendadak dalam skala ruang, bukan untuk satu orang. Proses operannya
        menggunakan menu ini bernama fitur Permintaan Ruangan.</p>

    <div class="page-break"></div>

    {{-- ===================== BAB 7 ===================== --}}
    <div class="chapter-header">
        <div class="chapter-num">BAB 7</div>
        <div class="chapter-title">Akuntansi & Laporan</div>
    </div>

    <div class="module-goal">
        <strong>Tujuan Modul Ini:</strong> Untuk mengubah dari bahasa "jumlah fisik kotak obat" menjadi bahasa "uang dan
        neraca perusahaan". Sangat dibutuhkan oleh tim akuntan atau pihak peninjau pajak rumah sakit untuk memastikan
        semua pergerakan bisnis tercatat seimbang nilai uangnya.
    </div>

    <div class="section-title">7.1 Mencatat Jurnal Keuangan</div>
    <p>Sistem dirancang cerdas. Ketika ada penerimaan pembelian obat, sistem diam-diam sudah mencatat nilai uang obat di
        balik layar ke dalam sistem Jurnal Akuntansi. Begitu juga kalau ada barang kedaluwarsa yang dibuang lantas rumah
        sakit rugi materil, aplikasinya otomatis memindahkan angka minus itu.</p>
    <p>Walaupun 90% aktivitas tercatat otomatis, sistem juga memiliki pintu manual murni. Pegawai Akuntansi bisa
        mencatat transaksi manual layaknya buku kas debit/kredit, yang harus bernilai seimbang sebelum bisa terunggah.
    </p>

    <div class="section-title">7.2 Mencetak Laporan Stok & Keuangan</div>
    <p>Laporan yang dicari setiap akhir bulan bisa didapat secara utuh melalui menu Laporan.</p>
    <ul>
        <li><strong>Laporan Keuangan:</strong> Terdapat Laporan Buku Besar dan Laporan Neraca Saldo (Trial Balance) per
            periode bulan tertentu yang dicetak untuk Direksi Keuangan.</li>
        <li><strong>Laporan Stok:</strong> Merekap mulai berapakah saldo awal barang tanggal 1 kemarin, lalu berapa
            masuk bulan ini, berapa diserahkan bulan ini, dan hasil saldo penutupan hari ini tanggal 30. Dicetak untuk
            serah laporan administrasi.</li>
    </ul>

    <div
        style="margin-top: 50px; text-align: center; border-top: 2px solid #cbd5e1; padding-top: 20px; color: #64748b; font-size: 10pt;">
        <p><strong>{{ $app_name }}</strong> &copy; Hak cipta dilindungi oleh RSUD Bumi Panua.</p>
        <p>Panduan ini dirancang untuk dapat dibaca oleh setiap petugas sistem informasi.</p>
    </div>

</body>

</html>
