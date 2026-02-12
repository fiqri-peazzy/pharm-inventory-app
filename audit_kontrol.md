📋 BREAKDOWN LENGKAP FASE 5: AUDIT & CONTROL
A. OVERVIEW FASE 5
Tujuan Fase Ini:
✅ Memastikan akurasi stok (sistem vs fisik)
✅ Menangani penyimpangan/selisih stok
✅ Mengelola barang rusak, expired, hilang
✅ Retur barang ke supplier atau internal
✅ Compliance audit & regulasi
✅ Transparansi & accountability
4 Modul Utama:

1. Stock Opname (Perhitungan Fisik Stok)
2. Stock Adjustments (Penyesuaian Manual)
3. Returns (Retur Supplier & Internal)
4. Disposals (Penghapusan Barang)

B. MODUL 1: STOCK OPNAME (PERHITUNGAN FISIK)
B.1. KONSEP DASAR
Apa itu Stock Opname?
Kegiatan menghitung fisik barang di gudang/depo dan
membandingkan dengan data sistem untuk:

- Validasi akurasi stok
- Identifikasi selisih (shortage/overage)
- Koreksi data sistem sesuai fisik
- Compliance audit internal/eksternal
  Frekuensi:
- Monthly: Depo (IGD, OK, farmasi)
- Quarterly: Gudang Utama
- Yearly: Full opname (semua item semua warehouse)
- Ad-hoc: Saat ada indikasi masalah

B.2. FITUR STOCK OPNAME

1. Create Stock Opname
   Input Header:

- Opname Number (auto: OPN/2026/001)
- Warehouse (pilih gudang/depo yang di-opname)
- Opname Date
- Opname Type:
    - Full Opname (semua item)
    - Partial Opname (item tertentu saja)
    - Cycle Count (rotasi berkala)
- PIC Opname (petugas yang menghitung)
- Team Members (tim opname, bisa multi-user)
- Notes
  Generate Opname Items:
  Option A: Auto-populate semua item yang ada stok di warehouse
  → Ambil dari stock_cards (item yang current stock > 0)
  → Group by item_id, batch_number

Option B: Manual select items
→ User pilih kategori/item spesifik
→ Untuk partial opname

Data yang di-generate per item:

- Item Name
- Batch Number
- Expired Date
- System Stock (dari stock_cards latest)
- Physical Stock (input manual saat counting)
- Difference (physical - system)
- Difference Value (difference × price)
- Notes per item

2. Counting Process (Proses Hitung Fisik)
   Flow:
1. Print Stock Opname Form (PDF)
   ┌────────────────────────────────────────────┐
   │ FORM STOCK OPNAME │
   │ No: OPN/2026/001 │
   │ Warehouse: Gudang Utama │
   │ Date: 15 Feb 2026 │
   ├────┬──────────┬───────┬──────┬──────┬─────┤
   │ No │ Item │ Batch │ ED │ Sys │ Phys│
   ├────┼──────────┼───────┼──────┼──────┼─────┤
   │ 1 │ Paracet │ A123 │12/26 │ 1000 │ ... │
   │ 2 │ Amox │ B456 │06/27 │ 500 │ ... │
   └────┴──────────┴───────┴──────┴──────┴─────┘

1. Tim opname hitung fisik barang
    - Scan barcode (opsional)
    - Hitung manual
    - Catat di form atau input langsung di tablet/mobile

1. Input Physical Stock ke sistem
    - Via web interface (desktop)
    - Via mobile app (future)
    - Bulk upload Excel (import hasil counting)

1. Sistem auto-calculate difference
    - Difference = Physical - System
    - Flag: - Surplus (physical > system) → warna hijau - Shortage (physical < system) → warna merah - Match (physical = system) → warna abu
      Real-time Tracking:
      Progress bar:
      ┌──────────────────────────────────────────┐
      │ Counting Progress: 45/120 items (37.5%) │
      │ ████████░░░░░░░░░░░░░░░░░░░░░░░░░░░ │
      └──────────────────────────────────────────┘

Status per item:
✅ Counted (physical stock sudah diisi)
⏳ Pending (belum dihitung)
⚠️ Variance (ada selisih > threshold)

3. Review & Validation
   Variance Analysis:
   Summary:
   ┌─────────────────┬────────┬──────────┐
   │ Variance Type │ Items │ Value │
   ├─────────────────┼────────┼──────────┤
   │ Surplus │ 15 │ +Rp 2.5M │
   │ Shortage │ 23 │ -Rp 5.2M │
   │ Match │ 82 │ Rp 0 │
   ├─────────────────┼────────┼──────────┤
   │ Total Variance │ 38 │ -Rp 2.7M │
   └─────────────────┴────────┴──────────┘

Detail Table:
┌──────────┬───────┬────────┬──────┬──────────┬────────┐
│ Item │ Batch │ System │ Phys │ Diff │ Value │
├──────────┼───────┼────────┼──────┼──────────┼────────┤
│ Paracet │ A123 │ 1000 │ 980 │ -20 (2%) │ -100K │
│ Insulin │ B456 │ 100 │ 95 │ -5 (5%) │ -250K │
│ Amox │ C789 │ 500 │ 510 │ +10 (2%) │ +120K │
└──────────┴───────┴────────┴──────┴──────────┴────────┘
Investigation Required:
Flag items dengan variance > threshold:

- Variance > 5% → Yellow alert (perlu investigasi)
- Variance > 10% → Red alert (wajib investigasi)
- High value item variance → Escalate to management

Investigation fields per item:

- Root Cause (dropdown):
    - Counting error
    - Data entry error
    - Theft/loss
    - Expired (sudah dibuang tapi belum input)
    - Damaged (rusak tapi belum tercatat)
    - Unknown
- Investigation Notes
- Investigated By
- Investigation Date

4. Approval Workflow
   Multi-level Approval:
   Status Flow:
   draft → submitted → reviewed → approved → posted

Level 1: Petugas Gudang (Create & Submit)
↓
Level 2: Kepala Gudang (Review & Approve minor variance)
↓
Level 3: Kepala Farmasi (Approve major variance > 5%)
↓
Level 4: Direktur (Approve high value variance > Rp 10 juta)
Approval Actions:
□ Approve (terima semua variance)
□ Approve with Notes (approve tapi ada catatan)
□ Request Recount (minta hitung ulang item tertentu)
□ Reject (tolak, minta opname ulang)

Approval Notes:

- Reason for approval/rejection
- Recommendations
- Action items

5. Posting (Generate Adjustment)
   Saat Approve → Auto Create Stock Adjustment:
   Untuk setiap item yang ada variance:

IF difference != 0 THEN
Create stock_adjustment_detail:

- item_id
- batch_id
- system_stock (dari opname)
- adjusted_stock (physical stock)
- difference (physical - system)
- adjustment_type (plus/minus)
- reason: "Stock Opname Adjustment - OPN/2026/001"

Create stock_cards:

- transaction_type: 'opname'
- transaction_id: opname_id
- stock_in: (jika surplus)
- stock_out: (jika shortage)
- Update current_stock di item_batches
  Journal Entry (Auto):
  IF shortage (physical < system):
  Debit: Stock Loss/Shrinkage Expense
  Credit: Inventory

IF surplus (physical > system):
Debit: Inventory
Credit: Stock Gain/Other Income (or Adjustment)

B.3. REPORTS & ANALYTICS

1. Opname Summary Report:

- Total items counted
- Total variance (qty & value)
- Variance by category
- Variance by storage condition
- High variance items (top 10)
- Investigation summary

2. Historical Comparison:
   Compare variance trend:

- Current opname vs previous opname
- Identify repeat offenders (item yang selalu selisih)
- Warehouse accuracy score (% items match)

3. Berita Acara Opname:
   Official document:

- Opname details (date, team, warehouse)
- Summary table (total items, match, variance)
- Variance detail (significant variances)
- Signatures:
    - Tim Opname
    - Kepala Gudang
    - Kepala Farmasi
    - Auditor (if applicable)

B.4. ROLES & PERMISSIONS - STOCK OPNAME
ROLES:

1. Petugas Gudang:
   ✅ stock-opname.create
   ✅ stock-opname.update (draft only)
   ✅ stock-opname.input-physical
   ✅ stock-opname.submit
   ❌ stock-opname.approve

2. Kepala Gudang:
   ✅ stock-opname.view
   ✅ stock-opname.review
   ✅ stock-opname.approve (variance < 5%)
   ✅ stock-opname.request-recount
   ❌ stock-opname.approve (variance > 5%)

3. Kepala Farmasi:
   ✅ stock-opname.view-all
   ✅ stock-opname.approve (all variances)
   ✅ stock-opname.reject
   ✅ stock-opname.export

4. Direktur:
   ✅ stock-opname.view-all
   ✅ stock-opname.approve (high value > Rp 10M)
   ✅ stock-opname.export

5. Auditor:
   ✅ stock-opname.view-all
   ✅ stock-opname.export
   ❌ stock-opname.approve (read-only)

6. Super Admin:
   ✅ ALL permissions

C. MODUL 2: STOCK ADJUSTMENTS (PENYESUAIAN MANUAL)
C.1. KONSEP DASAR
Kapan Pakai Stock Adjustment?
Adjustment untuk selisih kecil tanpa perlu opname formal:
✅ Data entry error (salah input qty)
✅ Damaged item found (rusak tapi belum tercatat)
✅ Expired item disposal (kecil, ad-hoc)
✅ Found stock (ketemu barang yang hilang)
✅ Minor theft/loss
✅ Koreksi dari opname (manual create)

❌ TIDAK untuk:

- Receiving (pakai module Receiving)
- Distribution (pakai module Distribution)
- Prescription (pakai module Prescription)
- Mass disposal (pakai module Disposal)
  Perbedaan dengan Opname:
  Stock Opname:
- Formal, berkala (monthly/quarterly)
- Hitung semua item
- Ada tim, ada BA
- Multi-approval

Stock Adjustment:

- Ad-hoc, case-by-case
- Item-specific
- Quick fix
- Single approval

C.2. FITUR STOCK ADJUSTMENTS

1. Create Adjustment
   Input Header:

- Adjustment Number (auto: ADJ/2026/001)
- Warehouse
- Adjustment Date
- Adjustment Type:
    - Plus (tambah stok)
    - Minus (kurangi stok)
- Reason Category (dropdown):
    - Data Entry Error
    - Found Stock
    - Damaged Item
    - Expired Item
    - Theft/Loss
    - System Correction
    - Others
- Detailed Reason (text)
- Reference Document (opsional):
    - Upload foto/scan dokumen pendukung
- Notes
  Add Items:
  Per item:
- Item (searchable dropdown)
- Batch (dropdown batches available di warehouse)
- System Stock (auto-fill dari stock_cards)
- Adjusted Stock (input manual)
- Difference (auto-calculate: adjusted - system)
- Unit Price (auto dari batch)
- Total Value (difference × price)
- Item Notes
  Validations:
  ✅ Adjusted stock tidak boleh negatif
  ✅ Jika minus, difference tidak boleh > system stock
  ✅ Harus ada reason
  ✅ High value adjustment (> Rp 1M) wajib upload dokumen

2. Approval Workflow
   Simple Approval:
   Status: draft → submitted → approved/rejected → posted

Approval Rules:

- Adjustment < Rp 500K → Kepala Gudang approve
- Adjustment Rp 500K - 5M → Kepala Farmasi approve
- Adjustment > Rp 5M → Direktur approve
  Approval Actions:
  □ Approve (post adjustment)
  □ Reject (decline, minta revisi)
  □ Request Evidence (minta dokumen pendukung)

3. Posting
   Saat Approve:
   Create stock_cards:

- transaction_type: 'adjustment'
- transaction_id: adjustment_id
- reference_number: adjustment_number
- stock_in: (jika plus)
- stock_out: (jika minus)

Update item_batches:

- current_stock += difference (jika plus)
- current_stock -= difference (jika minus)

Create journal_entry (auto):
IF plus:
Debit: Inventory
Credit: Adjustment Income / Found Stock

IF minus:
Debit: Adjustment Expense / Loss
Credit: Inventory

C.3. BERITA ACARA ADJUSTMENT
For High Value Adjustment (> Rp 5M):
Document includes:

- Adjustment details
- Item list dengan foto (jika ada)
- Investigation report (kenapa ada selisih)
- Corrective action
- Signatures:
    - Petugas yang menemukan
    - Kepala Gudang
    - Kepala Farmasi
    - Direktur (jika high value)

C.4. ROLES & PERMISSIONS - ADJUSTMENTS

1. Petugas Gudang:
   ✅ stock-adjustment.create
   ✅ stock-adjustment.update (draft)
   ✅ stock-adjustment.submit
   ❌ stock-adjustment.approve

2. Kepala Gudang:
   ✅ stock-adjustment.view
   ✅ stock-adjustment.approve (< Rp 500K)
   ❌ stock-adjustment.approve (> Rp 500K)

3. Kepala Farmasi:
   ✅ stock-adjustment.view-all
   ✅ stock-adjustment.approve (< Rp 5M)
   ✅ stock-adjustment.reject

4. Direktur:
   ✅ stock-adjustment.view-all
   ✅ stock-adjustment.approve (all)

5. Auditor:
   ✅ stock-adjustment.view-all
   ✅ stock-adjustment.export
   ❌ stock-adjustment.approve

6. Super Admin:
   ✅ ALL permissions

D. MODUL 3: RETURNS (RETUR)
D.1. KONSEP DASAR
Jenis Returns:

1. Return to Supplier (Retur ke Pemasok)
    - Barang rusak/cacat
    - Barang tidak sesuai PO
    - Expired on arrival
    - Overage receiving (terima lebih dari order)

2. Internal Return (Retur Internal)
    - Depo → Gudang Utama (kelebihan stok)
    - Depo → Depo lain (salah kirim)
    - Ruangan → Depo (floor stock tidak terpakai)

D.2. FITUR RETURNS

1. Return to Supplier
   Create Return:
   Header:

- Return Number (auto: RTN/SUP/2026/001)
- Return Type: to_supplier
- Warehouse (lokasi barang yang diretur)
- Supplier (dropdown)
- Return Date
- Reason Category:
    - Damaged
    - Wrong Item
    - Quality Issue
    - Expired
    - Overage
    - Others
- Reference Documents:
    - Original Receiving Number
    - Original PO Number
    - Original Invoice Number
    - Supplier DO Number
- Notes
- Upload Evidence (foto barang rusak, dll)
  Add Items:
- Item (dari receiving yang diretur)
- Batch Number
- Expired Date
- Qty to Return
- Return Price (dari receiving price)
- Total Value
- Item Reason
  Approval:
  Status: draft → submitted → supplier_notified → approved → picked_up → completed

Workflow:

1. Petugas buat return (draft)
2. Kepala Farmasi approve (submitted)
3. Supplier di-notify (email/WA)
4. Supplier pick up barang
5. Dapat credit note/refund
6. Return completed
   Impact:
   Saat approved:

- Stock keluar dari warehouse (stock_cards out)
- Create journal:
  Debit: Accounts Payable (AP)
  Credit: Inventory
- Track credit note dari supplier

2. Internal Return
   Create Internal Return:
   Header:

- Return Number (auto: RTN/INT/2026/001)
- Return Type: internal
- From Warehouse (asal)
- To Warehouse (tujuan)
- Return Date
- Reason:
    - Overage Stock
    - Wrong Delivery
    - Near Expired (mau ditukar yang lama)
    - Consolidation
- Notes
  Flow:
  Similar to Distribution, but reverse:

1. From warehouse create return request
2. To warehouse approve
3. Items picked up
4. Stock update (out from source, in to destination)

D.3. SUPPLIER CREDIT NOTE TRACKING
Credit Note:
Setelah return approved & picked up:

- Input credit note number dari supplier
- Credit amount
- Credit type:
    - Refund (uang kembali)
    - Replacement (ganti barang baru)
    - Credit memo (potong invoice berikutnya)
- Credit date
- Status: pending, received, applied

D.4. ROLES & PERMISSIONS - RETURNS

1. Petugas Gudang:
   ✅ returns.create
   ✅ returns.update (draft)
   ✅ returns.submit
   ❌ returns.approve

2. Kepala Gudang:
   ✅ returns.view
   ✅ returns.review

3. Kepala Farmasi:
   ✅ returns.view-all
   ✅ returns.approve (supplier return)
   ✅ returns.reject
   ✅ returns.track-credit

4. Keuangan:
   ✅ returns.view-all
   ✅ returns.input-credit-note
   ✅ returns.track-refund

5. Auditor:
   ✅ returns.view-all
   ✅ returns.export

6. Super Admin:
   ✅ ALL permissions

E. MODUL 4: DISPOSALS (PENGHAPUSAN)
E.1. KONSEP DASAR
Kenapa Perlu Disposal?
✅ Barang expired harus dihapus dari stok
✅ Barang rusak tidak bisa dijual
✅ Barang hilang harus dicatat
✅ Compliance regulasi (pemusnahan obat)
✅ Inventory cleanup
Jenis Disposal:

1. Expired (kadaluarsa)
2. Damaged (rusak)
3. Lost (hilang/theft)
4. Recalled (ditarik pabrik)
5. Others

E.2. FITUR DISPOSALS

1. Create Disposal
   Header:

- Disposal Number (auto: DSP/2026/001)
- Warehouse
- Disposal Date
- Disposal Type (dropdown):
    - Expired
    - Damaged
    - Lost/Theft
    - Product Recall
    - Quality Failure
    - Others
- Disposal Method:
    - Incineration (dibakar)
    - Landfill (dikubur)
    - Return to Supplier
    - Donation (jika masih layak)
    - Others
- Berita Acara Number
- Witnesses (saksi pemusnahan):
    - Name 1
    - Name 2 (minimal 2 saksi)
    - Name 3 (opsional)
- Execution Date (tanggal pemusnahan fisik)
- Location (tempat pemusnahan)
- Notes
- Upload Evidence:
    - Foto proses pemusnahan
    - Dokumen pendukung
      Add Items:
- Item
- Batch Number
- Expired Date
- Qty to Dispose
- Unit Price (HPP)
- Total Value
- Condition (%)
- Item Reason
  Auto-suggest Expired Items:
  Button: [Load Expired Items]
  → Auto-populate items yang:
    - Expired date < today
    - Current stock > 0
    - Di warehouse ini
      → User tinggal review & confirm

2. Approval Workflow
   Status: draft → submitted → approved → executed → posted

Workflow:

1. Petugas buat disposal list (draft)
2. Kepala Farmasi review & approve
3. Direktur approve (high value > Rp 10M)
4. Eksekusi pemusnahan fisik (tim + saksi)
5. Update stok (posted)
   Approval Levels:

- Value < Rp 1M → Kepala Farmasi
- Value Rp 1M - 10M → Kepala Farmasi + Direktur
- Value > Rp 10M → Kepala Farmasi + Direktur + Board

3. Execution (Pemusnahan Fisik)
   Checklist:
   □ Tim pemusnah hadir
   □ Saksi hadir (min 2 orang)
   □ Foto before (barang yang akan dimusnahkan)
   □ Proses pemusnahan (sesuai metode)
   □ Foto after (hasil pemusnahan)
   □ BA ditandatangani semua pihak
   □ Update status: executed
   Upload Evidence:

- Foto barang (before)
- Foto proses
- Foto hasil (after)
- Scan BA yang sudah TTD

4. Posting
   Saat Posted:
   Create stock_cards:

- transaction_type: 'disposal'
- transaction_id: disposal_id
- stock_out: qty_disposed
- current_stock di batch berkurang

Create journal_entry:

- Debit: Loss on Disposal / Write-off Expense
- Credit: Inventory
- Amount: total_value

E.3. BERITA ACARA PEMUSNAHAN
Official Document:
┌────────────────────────────────────────────────┐
│ BERITA ACARA PEMUSNAHAN BARANG │
│ No: BA/DSP/2026/001 │
├────────────────────────────────────────────────┤
│ │
│ Pada hari ini, Senin, 15 Februari 2026, │
│ telah dilakukan pemusnahan barang dengan │
│ detail sebagai berikut: │
│ │
│ Warehouse: Gudang Farmasi Utama │
│ Metode: Incineration │
│ Lokasi: Incinerator RS │
│ │
│ Daftar Barang: │
│ ┌────┬──────────┬───────┬──────┬────────┐ │
│ │ No │ Item │ Batch │ Qty │ Value │ │
│ ├────┼──────────┼───────┼──────┼────────┤ │
│ │ 1 │ Paracet │ A123 │ 1000 │ 5 juta │ │
│ │ 2 │ Amox │ B456 │ 500 │ 6 juta │ │
│ └────┴──────────┴───────┴──────┴────────┘ │
│ │
│ Total Value: Rp 11,000,000 │
│ │
│ Tim Pemusnah: │
│ 1. dr. Ahmad (Kepala Farmasi) │
│ 2. Siti (Apoteker) │
│ │
│ Saksi: │
│ 1. Budi (Petugas Gudang) │
│ 2. Citra (Keuangan) │
│ │
│ Foto terlampir. │
│ │
│ Mengetahui, │
│ │
│ Kepala Farmasi Direktur RS │
│ [TTD] [TTD] │
│ dr. Ahmad dr. Susi │
└────────────────────────────────────────────────┘

E.4. REPORTS

1. Disposal Summary Report:

- Total disposed (qty & value)
- By disposal type
- By warehouse
- By category
- Trend analysis (monthly)

2. Expired Items Tracking:

- Items will expire in 30/60/90 days
- Expired value by warehouse
- Prevention measures

E.5. ROLES & PERMISSIONS - DISPOSALS

1. Petugas Gudang:
   ✅ disposals.create
   ✅ disposals.update (draft)
   ✅ disposals.submit
   ✅ disposals.load-expired-items
   ❌ disposals.approve

2. Kepala Farmasi:
   ✅ disposals.view-all
   ✅ disposals.approve (< Rp 10M)
   ✅ disposals.reject
   ✅ disposals.mark-executed

3. Direktur:
   ✅ disposals.view-all
   ✅ disposals.approve (> Rp 10M)

4. Keuangan:
   ✅ disposals.view-all
   ✅ disposals.track-value
   ✅ disposals.export

5. Auditor:
   ✅ disposals.view-all
   ✅ disposals.export
   ✅ disposals.verify-execution

6. Super Admin:
   ✅ ALL permissions

F. SUMMARY PERMISSIONS FASE 5
Permission List (Total: ~40 permissions)
STOCK OPNAME (12):

- stock-opname.view
- stock-opname.view-all
- stock-opname.create
- stock-opname.update
- stock-opname.input-physical
- stock-opname.submit
- stock-opname.review
- stock-opname.approve
- stock-opname.reject
- stock-opname.request-recount
- stock-opname.export
- stock-opname.delete

STOCK ADJUSTMENT (10):

- stock-adjustment.view
- stock-adjustment.view-all
- stock-adjustment.create
- stock-adjustment.update
- stock-adjustment.submit
- stock-adjustment.approve
- stock-adjustment.reject
- stock-adjustment.export
- stock-adjustment.delete
- stock-adjustment.upload-evidence

RETURNS (10):

- returns.view
- returns.view-all
- returns.create
- returns.update
- returns.submit
- returns.approve
- returns.reject
- returns.track-credit
- returns.export
- returns.delete

DISPOSALS (12):

- disposals.view
- disposals.view-all
- disposals.create
- disposals.update
- disposals.submit
- disposals.approve
- disposals.reject
- disposals.mark-executed
- disposals.load-expired-items
- disposals.upload-evidence
- disposals.export
- disposals.delete

G. DATABASE TABLES FASE 5

1. stock_opnames (header)
2. stock_opname_details (items counted)
3. stock_adjustments (header)
4. stock_adjustment_details (items adjusted)
5. returns (header)
6. return_details (items returned)
7. return_credit_notes (supplier credits)
8. disposals (header)
9. disposal_details (items disposed)
10. disposal_witnesses (saksi pemusnahan)
11. disposal_evidences (foto/dokumen)

H. INTEGRATION POINTS
Fase 5 Connect To:
✅ Stock Cards → Update from opname/adjustment/disposal
✅ Item Batches → Update current_stock
✅ Journal Entries → Auto-post accounting
✅ Notifications → Alert approvers
✅ Reports → Summary & analytics
✅ Dashboard → Alerts for expired/loss
