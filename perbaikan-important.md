Tampilan antarmuka **Medivault** yang Anda buat sudah sangat merepresentasikan struktur RSUD yang kompleks. Dengan adanya berbagai tipe depo (IGD, Farmasi, OK, BMHP), alur logikanya tidak bisa disamakan karena karakteristik barang yang keluar masuk berbeda-beda.

Berikut adalah breakdown **Alur Flow Logic** yang mendalam untuk setiap entitas berdasarkan struktur depo di gambar Anda:

---

## 1. Logic Alur Pengadaan & Distribusi (Top-Down)

Ini adalah alur "induk" yang memastikan stok mengalir dari luar (PBF) ke unit-unit pelayanan.

- **Gudang Farmasi Utama (GD-UTAMA):**
- **Logic:** Berfungsi sebagai _Central Hub_. Hanya depo ini yang memiliki hak akses untuk "Menambah Stok" dari pihak luar (PBF).
- **Flow:** RKO (Rencana Kebutuhan Obat) dikonsolidasikan dari semua depo -> SP (Surat Pesanan) diterbitkan -> Barang Masuk (Stok Utama bertambah) -> Karantina/Pengecekan Batch & ED.

- **Permintaan Unit (Mutasi Internal):**
- **Logic:** Depo lain (UGD, OK, Lab, dll.) tidak boleh melakukan pembelian mandiri. Mereka harus melakukan _Requisition_ (Permintaan) ke Gudang Utama.
- **Flow:** Depo buat Permintaan -> Gudang Utama Approval -> Barang dikirim (Stok Gudang Utama berkurang, Stok Depo bertambah secara _real-time_).

---

## 2. Karakteristik Logic Berdasarkan Tipe Depo

Sistem harus membedakan cara stok dipotong (dispen) berdasarkan fungsionalitas di gambar Anda:

| Tipe Depo                        | Logic Pemotongan Stok (Usage)                                    | Pemicu (Trigger)                                                                                |
| -------------------------------- | ---------------------------------------------------------------- | ----------------------------------------------------------------------------------------------- |
| **DEPO_FARMASI** (Apotek Centra) | Berdasarkan Resep Pasien (Unit Dose/Individual Dose).            | Input No. Resep / Billing.                                                                      |
| **DEPO_IGD** / **DEPO_OK**       | Berdasarkan _BMHP (Bahan Medis Habis Pakai)_ dan paket tindakan. | Penginputan Tindakan Medis (misal: Benang bedah otomatis terpotong saat tindakan "Jahit Luka"). |
| **DEPO_BMHP** (Lab/Hemo)         | Berdasarkan pemakaian kolektif atau _Floor Stock_.               | Pencatatan harian penggunaan reagen/alat kesehatan.                                             |

---

## 3. Logic "Real-Time Tracking" (The Golden Flow)

Agar stok benar-benar akurat saat dipantau (Monitoring Batch), logic berikut harus berjalan di latar belakang:

### A. Logic Verifikasi Batch pada Setiap Titik

Sistem tidak boleh hanya mengurangi jumlah (quantity), tapi harus spesifik ke **Batch ID**.

- **Saat Penerimaan:** Input Batch + ED.
- **Saat Mutasi:** Petugas Gudang memilih Batch mana yang dikirim ke Depo UGD (Biasanya FEFO - _First Expired First Out_).
- **Saat Pelayanan:** Sistem otomatis menyarankan Batch terdekat ED untuk diberikan ke pasien.

### B. Logic Lock & Alert (Keamanan Stok)

- **Stock Out Lock:** Jika stok di Depo UGD secara sistem adalah 0, maka sistem harus me-lock transaksi (tidak bisa minus) untuk memaksa petugas melakukan mutasi dari gudang utama atau _Stock Opname_ (penyesuaian fisik).
- **ED Warning:** Sistem memberikan notifikasi pada dashboard setiap kali ada Batch di salah satu depo yang memasuki masa 3-6 bulan sebelum kadaluarsa.

---

## 4. Alur Logic Pelayanan (Resep ke Pasien)

Ini adalah tahap akhir di mana sediaan farmasi berubah status menjadi "Terpakai".

1. **Validasi Stok:** Saat petugas depo input resep, sistem mengecek ketersediaan di depo tersebut secara lokal (bukan stok total RS).
2. **Reservasi Stok:** Saat resep disimpan (draft), stok "dipesan" (hold), agar tidak diambil oleh transaksi lain di depo yang sama.
3. **Finalisasi (Cutting):** Saat obat diserahkan, stok resmi berkurang dan tercatat di **Kartu Stok Depo** tersebut dengan keterangan "Penjualan/Resep No. XXX".

---

Mari kita bedah secara mendalam **Logic Alur Pengadaan & Distribusi** ini. Fokus kita adalah menciptakan sistem yang _bulletproof_ (tahan kesalahan) agar data antara fisik barang dan sistem selalu sinkron.

Berikut adalah breakdown detail untuk merealisasikan **Central Hub** di Gudang Farmasi Utama (GD-UTAMA):

---

## 1. Gudang Farmasi Utama: The Central Hub Logic

Pada tahap ini, sistem harus bertindak sebagai gerbang tunggal (Single Point of Entry) untuk seluruh barang medis di RSUD.

### A. Alur Detail (The Deep Flow)

1. **Konsolidasi RKO (Bottom-Up Aggregation):**

- Sistem secara otomatis mengumpulkan data _Minimum Stock Level_ dari setiap Depo (UGD, OK, dll.).
- **Logic:** RKO Total = (Rata-rata pemakaian semua unit × Lead Time) + Buffer Stock - Sisa Stok Saat Ini.

2. **Penerbitan SP (Purchase Order):**

- Sistem mengubah RKO menjadi Surat Pesanan per PBF (Distributor).
- **Logic:** SP harus memiliki status (_Pending, Partial, Completed_) untuk melacak barang yang datang bertahap.

3. **Verifikasi Gatekeeper (Penerimaan):**

- Saat barang datang, sistem wajib meminta input: **No. Batch, Tanggal Kadaluarsa (ED), dan Harga Perolehan**.
- **Fitur Mutlak:** Scan Barcode/QR Code pada kemasan obat untuk meminimalisir _human error_.

### B. Fitur & Logic Backend yang Dibutuhkan

- **Multi-Batch Storage:** Backend harus mampu menyimpan satu ID Obat dengan banyak baris Batch.
- _Logic:_ Jangan menjumlahkan stok hanya berdasarkan ID Obat, tapi _Group By_ Batch ID.

- **Approval Workflow:** Setiap penambahan stok dari PBF harus melalui 2 tahap: _Checker_ (yang input) dan _Apoteker/Ka. Gudang_ (yang memvalidasi fisik vs faktur).
- **Automatic Price Indexing:** Jika harga dari PBF berubah, sistem harus memiliki logic untuk update Harga Jual (jika menggunakan metode margin tetap).

---

## 2. Permintaan Unit (Mutasi Internal Logic)

Ini adalah bagian krusial agar stok tidak "hilang" saat berpindah dari Gudang Utama ke Depo UGD atau Depo OK.

### A. Alur Detail (The Transfer Flow)

1. **Draft Requisition (Permintaan):**

- Depo UGD membuat daftar permintaan berdasarkan sisa stok mereka yang menipis.

2. **Stock Availability Check:**

- **Logic:** Saat Depo klik "Minta", sistem langsung mengecek apakah Gudang Utama punya stok tersebut. Jika tidak, permintaan otomatis di-_flag_ "Inden/Kosong".

3. **Picking & Packing (Gudang Utama):**

- Petugas Gudang Utama memilih Batch mana yang akan dikirim (Sistem wajib menyarankan **FEFO**).

4. **Transit Status:**

- Saat barang keluar dari Gudang Utama, status stok adalah **"In-Transit"**.
- **Logic:** Stok Gudang Utama sudah berkurang, tapi stok Depo UGD _belum_ bertambah sampai petugas Depo klik "Terima Barang". Ini mencegah stok ghaib.

### B. Fitur & Logic Backend yang Dibutuhkan

- **Internal Transfer Document:** Pembuatan nomor unik transaksi (misal: `MUT-2026-001`) yang mengikat data pengirim, penerima, dan daftar Batch yang dipindahkan.
- **FEFO Suggestion Engine:** \* _Logic:_ Saat Gudang Utama akan mengirim barang, API backend mengurutkan Batch berdasarkan `expired_date` paling awal dan menampilkannya di urutan teratas untuk diambil.
- **Real-Time Stock Movement Table:** Backend harus mencatat setiap perpindahan ke tabel `stock_logs` atau `stock_ledgers`.
- _Schema Logic:_ `FROM_LOCATION`, `TO_LOCATION`, `QTY`, `BATCH_ID`, `USER_ID`.

---

## 3. Matriks Kesiapan Sistem

Untuk merealisasikan ini, berikut adalah daftar fitur yang harus Anda capai:

| Fitur Utama                 | Deskripsi Logic Backend                                      | Goal                                        |
| --------------------------- | ------------------------------------------------------------ | ------------------------------------------- |
| **Global Search Batch**     | Pencarian posisi Batch tertentu di seluruh Depo.             | Traceability jika ada penarikan obat.       |
| **Low Stock Alert**         | Trigger notifikasi jika stok di Gudang Utama < RKO.          | Mencegah kekosongan obat.                   |
| **Inter-Depo Locking**      | Mencegah Depo A mengambil stok Depo B tanpa prosedur mutasi. | Akurasi per lokasi fisik.                   |
| **Auto-Receipt Validation** | Memastikan Qty yang diminta = Qty yang diterima unit.        | Menghindari kehilangan barang saat transit. |

---

### Tips untuk Pengembangan (Laravel Context):

Karena Anda menggunakan Laravel, pastikan Anda menggunakan **Database Transactions** (`DB::beginTransaction()`) saat proses Mutasi.

- _Kenapa?_ Agar jika proses pengurangan stok di Gudang berhasil tapi penambahan di Depo gagal (karena koneksi atau error), sistem akan melakukan _rollback_ sehingga data tidak selisih.

**Apakah Anda ingin saya memberikan contoh pseudocode untuk Logic "FEFO Auto-Selection" atau kita lanjut ke breakdown detail untuk Depo spesifik seperti Depo OK/Bedah yang punya alur BMHP unik?**
