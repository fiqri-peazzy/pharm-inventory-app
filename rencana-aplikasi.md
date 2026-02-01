🔹 RUANG LINGKUP & KERANGKA TAHAPAN (WAJIB DIIKUTI)

Tahap Inisiasi & Perencanaan

Menjabarkan tujuan sistem pengelolaan obat & BMHP RS

Mengidentifikasi stakeholder: farmasi, gudang, keuangan BLUD, IT/SIMRS, manajemen, auditor

Mendefinisikan masalah eksisting (stok tidak realtime, expired, selisih, laporan lambat)

Menetapkan tujuan sistem (stok realtime, FEFO/FIFO, integrasi SIMRS, laporan keuangan)

Output hanya berupa TOR / Project Charter / kebutuhan awal, tanpa desain teknis kecuali diminta

Tahap Analisis Kebutuhan (Business Process Mapping)

Memetakan proses nyata RS dari hulu ke hilir:
perencanaan → pengadaan → penerimaan → penyimpanan → distribusi → pemakaian pasien → retur → opname → penghapusan

Mengidentifikasi kebutuhan tiap role (apoteker, gudang, keuangan, manajemen, auditor)

Menurunkan kebutuhan sistem (multi gudang, batch, ED, barcode, laporan BLUD, integrasi SIMRS)

Output berupa URS/SRS + flow proses, bukan kode

Tahap Desain Sistem

Mendesain arsitektur aplikasi (web-based, server lokal/cloud, API SIMRS)

Mendesain modul sistem secara terpisah dan saling terhubung

Mendesain struktur database (tabel master, batch, stok, transaksi, kartu stok, jurnal)

Mendesain wireframe UI berbasis Bootstrap

Tidak melakukan coding sebelum ada instruksi eksplisit

Tahap Pengembangan (Development)

Implementasi modul menggunakan Laravel + Livewire + Bootstrap

Kode harus minimalis, jelas, production-ready, tanpa library tambahan kecuali diperintahkan

Setiap modul dibuat terpisah dan terkontrol

Tidak mengaktifkan fitur lintas modul tanpa persetujuan saya

Tahap Pengujian (Testing)

Menyiapkan skenario uji unit & UAT

Simulasi transaksi farmasi nyata (terima, distribusi, resep, opname)

Validasi laporan stok & akuntansi

Output berupa hasil uji & perbaikan terkonfirmasi

Tahap Implementasi (Go-Live)

Migrasi data stok awal

Instalasi sistem

Penyusunan SOP penggunaan

Go-live bertahap (gudang → depo → unit pelayanan)

Tahap Operasional & Monitoring

Monitoring sistem & error

Backup & audit trail

Evaluasi KPI farmasi & logistik

Menjadi dasar pengembangan versi berikutnya

🔹 STRUKTUR MODUL YANG AKAN DIBANGUN

Sistem terdiri dari modul berikut dan tidak boleh menyimpang dari urutan logis RS:

Master Data (obat, BMHP, batch, gudang, supplier, user & role)

Perencanaan Kebutuhan (RKO/RBA)

Pengadaan & Purchasing (PR, PO, e-Catalog)

Penerimaan Barang

Manajemen Stok Gudang (FEFO/FIFO, kartu stok)

Distribusi ke Depo/Unit

Farmasi Klinik & Resep Pasien (integrasi SIMRS)

Stok Opname & Audit

Retur & Penghapusan

Akuntansi Persediaan BLUD

Pelaporan & Dashboard Manajemen

Keamanan, Audit Trail & Integrasi Sistem

Setiap modul:

Dibahas satu per satu

Dijelaskan fungsi, data, dan alur

Baru di-coding jika saya perintahkan

🔒 ATURAN OUTPUT & KONTROL

Output ringkas, langsung ke inti, tidak bertele-tele

Kode hemat token, fokus fungsi inti

Tidak menambahkan:

fitur medis

asumsi regulasi

optimasi lanjutan
tanpa konfirmasi saya

Jika ada saran → beri sebagai rekomendasi singkat, jangan eksekusi

🎯 TUJUAN AKHIR

Membangun aplikasi pengelolaan obat & BMHP RS yang:

sesuai praktik lapangan farmasi RS

patuh audit & BLUD

terintegrasi SIMRS

siap digunakan operasional harian

👉 Barang direncanakan → dibeli → diterima → disimpan → didistribusi → dipakai pasien → dicatat keuangan → diaudit.
