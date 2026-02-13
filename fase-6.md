FASE 6: ACCOUNTING & REPORTING
A. OVERVIEW FASE 6
Tujuan Fase Ini:
✅ Integrasi inventory dengan akuntansi (real-time)
✅ Auto-posting jurnal dari setiap transaksi
✅ Tracking nilai persediaan (inventory valuation)
✅ Laporan manajemen untuk decision making
✅ Compliance BLUD & regulasi pemerintah
✅ Transparansi keuangan untuk stakeholder
✅ Analytics untuk optimasi cost
3 Sub-Modul Utama:

1. Journal Entries (Jurnal Akuntansi)
2. Reports & Analytics (Laporan)
3. Export & Print System

B. MODUL 1: JOURNAL ENTRIES (JURNAL AKUNTANSI)
B.1. KONSEP DASAR AKUNTANSI INVENTORY
Prinsip:
Setiap transaksi inventory memiliki impact finansial:
✅ Receiving → Aset bertambah (inventory), Utang bertambah (AP)
✅ Distribution → Transfer aset (antar warehouse)
✅ Prescription → Aset berkurang, COGS bertambah (expense)
✅ Disposal → Aset berkurang, Loss bertambah (expense)
✅ Adjustment → Koreksi nilai aset
✅ Return → Aset berkurang, Utang berkurang (AP)
Inventory Valuation Method:
Sistem support 2 metode:

1. FIFO (First In First Out) ← DEFAULT, recommended
2. Weighted Average (rata-rata tertimbang)

Setting per warehouse atau global

B.2. CHART OF ACCOUNTS (COA)
Structure CoA:
ASSETS (1xxxx)
├── Current Assets (11xxx)
│ ├── Inventory - Medicines (11100)
│ ├── Inventory - Medical Supplies (11200)
│ ├── Inventory - Reagents (11300)
│ ├── Inventory - Medical Equipment (11400)
│ └── Inventory - Others (11900)

LIABILITIES (2xxxx)
├── Current Liabilities (21xxx)
│ ├── Accounts Payable - Suppliers (21100)
│ ├── Accounts Payable - Others (21900)

EQUITY (3xxxx)
└── (Standard equity accounts)

REVENUE (4xxxx)
├── Pharmacy Revenue (41000)
├── Other Revenue (49000)
└── Adjustment Income (49100)

EXPENSES (5xxxx)
├── Cost of Goods Sold (51000)
│ ├── COGS - Medicines (51100)
│ ├── COGS - Medical Supplies (51200)
│ └── COGS - Others (51900)
├── Operating Expenses (52xxx)
│ ├── Stock Loss (52100)
│ ├── Disposal Loss (52200)
│ ├── Shrinkage (52300)
│ └── Adjustment Expense (52900)
CoA Management Features:
CRUD Chart of Accounts:

- Account Code (unique)
- Account Name
- Account Type (asset/liability/equity/revenue/expense)
- Parent Account (hierarchical)
- Normal Balance (debit/credit)
- Is Active
- Description

Validations:
✅ Account code format: 5 digits (xxxxx)
✅ Parent-child hierarchy validation
✅ Cannot delete if has transactions
✅ Cannot change type if has journal entries

B.3. AUTO-POSTING JOURNAL ENTRIES

1. Receiving (Penerimaan Barang)
   Trigger: Saat receiving status = approved/posted
   Jurnal Entry:
   Date: Receiving Date
   Reference: RCV/2026/001

Debit: Inventory - Medicines Rp 10,000,000
Credit: Accounts Payable - PBF XYZ Rp 10,000,000

Description: Penerimaan barang dari PBF XYZ, Faktur INV-001

Detail per item:

- Paracetamol 500mg: Rp 5,000,000
- Amoxicillin 500mg: Rp 5,000,000
  Business Logic:
  phpforeach ($receiving->details as $detail) {
    $inventoryAccount = getInventoryAccountByCategory($detail->item->category);
  $apAccount = getAPAccountBySupplier($receiving->supplier);
        $amount = $detail->qty_received * $detail->purchase_price + $detail->ppn_amount;

        createJournalEntry([
            'date' => $receiving->receiving_date,
            'reference' => $receiving->receiving_number,
            'transaction_type' => 'receiving',
            'transaction_id' => $receiving->id,
            'entries' => [
                ['account_id' => $inventoryAccount, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $apAccount, 'debit' => 0, 'credit' => $amount],
            ]
        ]);
    }

```

---

#### **2. Distribution (Distribusi Internal)**

**Trigger:** Saat distribution status = received

**Jurnal Entry:**
```

OPTION A: No Journal (Internal Transfer Only)
→ Hanya update stock_cards
→ Tidak ada impact P&L
→ Recommended untuk simple tracking

OPTION B: With Journal (Track Movement Cost)
Date: Distribution Date
Reference: DST/2026/001

Debit: Inventory - Depo IGD Rp 2,000,000
Credit: Inventory - Gudang Utama Rp 2,000,000

Description: Distribusi ke Depo IGD

Note: Nilai tetap sama (cost transfer)

```

**Configuration:**
```

Setting: Enable/Disable journal for distributions
Default: Disabled (cukup stock_cards saja)
Enable jika: Butuh track cost per warehouse detail

```

---

#### **3. Prescription (Dispensing Resep)**

**Trigger:** Saat prescription status = dispensed/completed

**Jurnal Entry:**
```

Date: Prescription Date
Reference: RX/2026/001

Debit: Cost of Goods Sold (COGS) Rp 50,000
Credit: Inventory - Medicines Rp 50,000

Description: Dispensing resep untuk Pasien John Doe, RM-12345

FIFO Calculation:

- Paracetamol: 10 pcs × Rp 5,000 (batch A123, HPP)
  = Rp 50,000

Revenue Journal (jika integrated dengan billing):
Debit: Cash / Accounts Receivable Rp 75,000
Credit: Pharmacy Revenue Rp 75,000

Gross Profit = Revenue - COGS = Rp 75,000 - Rp 50,000 = Rp 25,000
FIFO Cost Calculation:
phpfunction calculateCOGS($item_id, $warehouse_id, $qty) {
$batches = ItemBatch::where('item_id', $item_id)
->where('warehouse_id', $warehouse_id)
->where('current_stock', '>', 0)
->orderBy('created_at', 'asc') // FIFO
->get();

    $totalCost = 0;
    $remainingQty = $qty;

    foreach ($batches as $batch) {
        $qtyFromBatch = min($remainingQty, $batch->current_stock);
        $cost = $qtyFromBatch * $batch->total_price; // HPP per unit
        $totalCost += $cost;

        $remainingQty -= $qtyFromBatch;
        if ($remainingQty <= 0) break;
    }

    return $totalCost;

}

```

---

#### **4. Stock Adjustment (Plus/Minus)**

**Trigger:** Saat adjustment status = posted

**Jurnal Entry:**

**PLUS (Found Stock / Surplus):**
```

Date: Adjustment Date
Reference: ADJ/2026/001

Debit: Inventory - Medicines Rp 500,000
Credit: Adjustment Income Rp 500,000

Description: Found stock - Paracetamol 100 pcs

```

**MINUS (Loss / Shortage):**
```

Date: Adjustment Date
Reference: ADJ/2026/002

Debit: Adjustment Expense Rp 300,000
Credit: Inventory - Medicines Rp 300,000

Description: Stock loss - Damaged item

```

---

#### **5. Disposal (Pemusnahan)**

**Trigger:** Saat disposal status = posted

**Jurnal Entry:**
```

Date: Disposal Date
Reference: DSP/2026/001

Debit: Disposal Loss Rp 11,000,000
Credit: Inventory - Medicines Rp 11,000,000

Description: Disposal of expired items - BA/DSP/2026/001

Detail:

- Paracetamol (expired): Rp 5,000,000
- Amoxicillin (expired): Rp 6,000,000

```

---

#### **6. Return to Supplier**

**Trigger:** Saat return status = completed

**Jurnal Entry:**
```

Date: Return Date
Reference: RTN/SUP/2026/001

Debit: Accounts Payable - PBF XYZ Rp 2,000,000
Credit: Inventory - Medicines Rp 2,000,000

Description: Return damaged goods to PBF XYZ

Saat dapat credit note/refund:
Debit: Cash / Bank Rp 2,000,000
Credit: Accounts Payable - PBF XYZ Rp 2,000,000

```

---

#### **7. Stock Opname (Adjustment from Opname)**

**Trigger:** Saat opname posted (generate adjustment)

**Jurnal Entry:**

**Surplus:**
```

Debit: Inventory Rp 1,500,000
Credit: Adjustment Income (Opname) Rp 1,500,000

```

**Shortage:**
```

Debit: Shrinkage Expense Rp 2,000,000
Credit: Inventory Rp 2,000,000

```

---

### **B.4. MANUAL JOURNAL ENTRY**

#### **Use Cases:**
```

✅ Koreksi jurnal (salah posting)
✅ Accrual/prepayment
✅ Adjustment akhir periode
✅ Jurnal khusus yang tidak auto-generate

```

#### **Create Manual Journal:**
```

Header:

- Journal Number (auto: JE/2026/001)
- Journal Date
- Journal Type:
    - Standard (regular)
    - Adjusting (koreksi)
    - Closing (penutup)
    - Opening (pembukaan)
- Period (month/year)
- Description
- Reference (opsional)
- Attachment (upload PDF/image)

Details:

- Account (dropdown CoA)
- Debit Amount
- Credit Amount
- Description per line
- Cost Center (opsional)
- Department (opsional)

Validations:
✅ Total Debit = Total Credit (balance)
✅ Minimal 2 lines (1 debit, 1 credit)
✅ Amount > 0
✅ Account must be active
✅ Cannot use parent account (must leaf)

```

#### **Approval Workflow:**
```

Status: draft → submitted → reviewed → approved → posted

Approval Rules:

- Manual journal < Rp 1M → Kepala Keuangan
- Manual journal Rp 1M - 10M → Direktur Keuangan
- Manual journal > Rp 10M → Direktur + Board

Saat posted:

- Lock journal (cannot edit/delete)
- Update account balances
- Generate journal number sequence

```

---

### **B.5. JOURNAL ENTRY FEATURES**

#### **1. Journal Entry List (Index):**
```

Columns:

- Journal Number
- Date
- Type (auto/manual, transaction type)
- Reference (transaction number)
- Description
- Total Amount
- Status (draft/posted)
- Created By
- Actions (view, edit, delete, post)

Filters:

- Date Range
- Journal Type
- Status
- Account
- Transaction Type
- Created By

Search:

- Journal Number
- Description
- Reference

```

#### **2. Journal Entry Detail (Show):**
```

Header:

- Journal Number, Date, Type
- Transaction Reference (link to original transaction)
- Status, Created By, Posted By

Details Table:
┌─────────────┬────────────┬──────────┬──────────┬──────────┐
│ Account │ Debit │ Credit │ Balance │ Desc │
├─────────────┼────────────┼──────────┼──────────┼──────────┤
│ Inventory │ 10,000,000 │ 0 │ 10,000,000│ Receiving│
│ AP - PBF XYZ│ 0 │10,000,000│-10,000,000│ Receiving│
├─────────────┼────────────┼──────────┼──────────┼──────────┤
│ TOTAL │ 10,000,000 │10,000,000│ 0 │ │
└─────────────┴────────────┴──────────┴──────────┴──────────┘

Actions:

- Print Journal Entry
- Export to PDF
- Reverse Journal (if posted, create reversing entry)
- Attachment (view uploaded docs)

```

#### **3. Reverse Journal Entry:**
```

Untuk koreksi jurnal yang sudah posted:

- Cannot delete posted journal
- Create reversing entry (kebalikan)
- Original entry tetap ada
- Reversing entry reference to original
- Effect: Net zero impact

Example:
Original (JE/2026/001):
Debit: Inventory 10,000,000
Credit: AP 10,000,000

Reversing (JE/2026/002):
Debit: AP 10,000,000
Credit: Inventory 10,000,000

Net Effect: 0

```

---

### **B.6. GENERAL LEDGER (BUKU BESAR)**

#### **GL by Account:**
```

Account: 11100 - Inventory - Medicines
Period: January 2026

┌──────┬───────────┬─────────────┬──────────┬──────────┬───────────┐
│ Date │ Reference │ Description │ Debit │ Credit │ Balance │
├──────┼───────────┼─────────────┼──────────┼──────────┼───────────┤
│ 01/01│ Beginning │ Balance │ 0 │ 0 │ 0 │
│ 05/01│ RCV/001 │ Receiving │10,000,000│ 0 │10,000,000 │
│ 10/01│ RX/001 │ Prescription│ 0 │ 50,000 │ 9,950,000 │
│ 15/01│ DSP/001 │ Disposal │ 0 │ 500,000 │ 9,450,000 │
├──────┼───────────┼─────────────┼──────────┼──────────┼───────────┤
│ │ TOTAL │ │10,000,000│ 550,000 │ 9,450,000 │
└──────┴───────────┴─────────────┴──────────┴──────────┴───────────┘

Features:

- Filter by date range
- Filter by transaction type
- Export to Excel/PDF
- Drill-down to source transaction

```

---

### **B.7. TRIAL BALANCE**
```

Trial Balance
As of: January 31, 2026

┌──────┬────────────────────────┬────────────┬────────────┐
│ Code │ Account Name │ Debit │ Credit │
├──────┼────────────────────────┼────────────┼────────────┤
│ 11100│ Inventory - Medicines │ 9,450,000 │ 0 │
│ 11200│ Inventory - Supplies │ 2,000,000 │ 0 │
│ 21100│ Accounts Payable │ 0 │ 10,000,000 │
│ 41000│ Pharmacy Revenue │ 0 │ 750,000 │
│ 51000│ COGS │ 500,000 │ 0 │
│ 52100│ Stock Loss │ 50,000 │ 0 │
│ 52200│ Disposal Loss │ 500,000 │ 0 │
├──────┼────────────────────────┼────────────┼────────────┤
│ │ TOTAL │ 12,500,000 │ 10,750,000 │
└──────┴────────────────────────┴────────────┴────────────┘

Validation:
✅ Total Debit = Total Credit (balanced)
❌ If not balanced → show error, investigate

Export:

- Excel (dengan formula)
- PDF (print-ready)

```

---

### **B.8. ROLES & PERMISSIONS - ACCOUNTING**
```

1. Keuangan BLUD:
   ✅ journal-entries.view-all
   ✅ journal-entries.create-manual
   ✅ journal-entries.update-manual (draft)
   ✅ journal-entries.submit
   ✅ journal-entries.export
   ✅ coa.view
   ✅ coa.create
   ✅ coa.update
   ✅ general-ledger.view
   ✅ trial-balance.view
   ❌ journal-entries.approve
   ❌ journal-entries.post

2. Kepala Keuangan:
   ✅ All Keuangan BLUD permissions
   ✅ journal-entries.approve (< Rp 1M)
   ✅ journal-entries.post
   ✅ journal-entries.reverse

3. Direktur Keuangan:
   ✅ All Kepala Keuangan permissions
   ✅ journal-entries.approve (all amounts)
   ✅ coa.delete

4. Auditor:
   ✅ journal-entries.view-all
   ✅ general-ledger.view-all
   ✅ trial-balance.view
   ✅ journal-entries.export
   ❌ journal-entries.create/update/delete

5. Kepala Farmasi:
   ✅ journal-entries.view (pharmacy-related)
   ❌ journal-entries.create/update (auto only)

6. Super Admin:
   ✅ ALL permissions

```

---

## C. MODUL 2: REPORTS & ANALYTICS

### **C.1. STOCK REPORTS**

#### **1. Stock Summary Report (Laporan Persediaan)**

**Purpose:** Overview stok saat ini per warehouse/category
```

Report Parameters:

- As of Date (default: today)
- Warehouse (all/specific)
- Category (all/specific)
- Item Type (obat/alkes/bmhp/all)
- Storage Condition (all/specific)
- Stock Status:
    - All
    - Active (stock > 0)
    - Below Min
    - Above Max
    - Out of Stock

Output:
┌────────────┬─────────┬─────────┬────────┬──────────┬───────────┐
│ Item │ Category│ Unit │ Stock │ Value │ Status │
├────────────┼─────────┼─────────┼────────┼──────────┼───────────┤
│ Paracet │ Obat │ Tablet │ 10,000 │ 50,000,000│ Active │
│ Amox │ Obat │ Kapsul │ 5,000 │ 60,000,000│ Active │
│ Insulin │ Obat │ Vial │ 50 │ 5,000,000│ Below Min│
├────────────┼─────────┼─────────┼────────┼──────────┼───────────┤
│ TOTAL │ │ │ 15,050 │115,000,000│ │
└────────────┴─────────┴─────────┴────────┴──────────┴───────────┘

Grouping Options:

- By Warehouse
- By Category
- By Storage Condition
- By ABC Classification (value-based)

Charts:

- Pie Chart: Stock value by category
- Bar Chart: Stock qty by warehouse
- Donut Chart: Stock distribution

```

---

#### **2. Stock Valuation Report**

**Purpose:** Nilai persediaan untuk neraca/audit
```

Parameters:

- As of Date
- Warehouse (all/specific)
- Valuation Method (FIFO/Average)
- Detail Level (summary/detail)

Output (Summary):
┌─────────────────┬──────────┬────────────┬─────────────┐
│ Warehouse │ Qty │ Avg Price │ Total Value │
├─────────────────┼──────────┼────────────┼─────────────┤
│ Gudang Utama │ 100,000 │ 5,500 │ 550,000,000 │
│ Depo IGD │ 25,000 │ 6,000 │ 150,000,000 │
│ Depo Rajal │ 50,000 │ 5,200 │ 260,000,000 │
├─────────────────┼──────────┼────────────┼─────────────┤
│ TOTAL │ 175,000 │ 5,486 │ 960,000,000 │
└─────────────────┴──────────┴────────────┴─────────────┘

Output (Detail):
┌──────────┬───────┬──────┬─────────┬────────┬──────────┐
│ Item │ Batch │ ED │ Qty │ Price │ Value │
├──────────┼───────┼──────┼─────────┼────────┼──────────┤
│ Paracet │ A123 │12/26 │ 5,000 │ 5,000 │25,000,000│
│ Paracet │ B456 │06/27 │ 5,000 │ 5,200 │26,000,000│
├──────────┼───────┼──────┼─────────┼────────┼──────────┤
│ Subtotal │ │ │ 10,000 │ 5,100 │51,000,000│
└──────────┴───────┴──────┴─────────┴────────┴──────────┘

FIFO vs Average Comparison:

- FIFO Value: Rp 960,000,000
- Average Value: Rp 955,000,000
- Difference: Rp 5,000,000 (0.5%)

```

---

#### **3. Aging Analysis (Fast/Slow/Dead Stock)**

**Purpose:** Identifikasi pergerakan stok untuk optimasi
```

Parameters:

- As of Date
- Warehouse
- Period (last 30/60/90/180 days)

Classification:
┌───────────────┬─────────────────────────────────────┐
│ Category │ Criteria │
├───────────────┼─────────────────────────────────────┤
│ Fast Moving │ Turnover > 10x/year (usage > 83%) │
│ Slow Moving │ Turnover 1-10x/year (usage 10-83%) │
│ Dead Stock │ No movement > 180 days │
│ Near Expired │ ED < 90 days │
└───────────────┴─────────────────────────────────────┘

Output:
┌──────────┬─────────┬─────────┬──────────┬─────────┬────────┐
│ Item │ Qty │ Value │ Turnover │ Category│ Action │
├──────────┼─────────┼─────────┼──────────┼─────────┼────────┤
│ Paracet │ 10,000 │ 50M │ 15x/year │ Fast │ OK │
│ Amox │ 5,000 │ 60M │ 8x/year │ Slow │ Monitor│
│ Insulin │ 100 │ 10M │ 2x/year │ Slow │ Reduce │
│ VitaminX │ 500 │ 5M │ 0x/year │ Dead │ Dispose│
└──────────┴─────────┴─────────┴──────────┴─────────┴────────┘

Actions Recommended:

- Fast: Maintain, ensure stock availability
- Slow: Reduce order qty, monitor usage
- Dead: Dispose, stop ordering
- Near Expired: Urgent usage push or return to supplier

```

---

#### **4. Stock Movement Report (Mutasi)**

**Purpose:** Tracking pergerakan stok (in/out) per periode
```

Parameters:

- Date Range (from - to)
- Warehouse
- Item (all/specific)
- Transaction Type (all/specific)

Output:
Item: Paracetamol 500mg
Warehouse: Gudang Utama
Period: 01 Jan - 31 Jan 2026

┌──────┬───────────┬────────┬────────┬────────┬─────────┐
│ Date │ Trans Type│ Ref │ In │ Out │ Balance │
├──────┼───────────┼────────┼────────┼────────┼─────────┤
│ 01/01│ Beginning │ │ 0 │ 0 │ 0 │
│ 05/01│ Receiving │ RCV001 │ 1,000 │ 0 │ 1,000 │
│ 10/01│ Distribut │ DST001 │ 0 │ 500 │ 500 │
│ 15/01│ Receiving │ RCV002 │ 2,000 │ 0 │ 2,500 │
│ 20/01│ Prescriptn│ RX001 │ 0 │ 50 │ 2,450 │
├──────┼───────────┼────────┼────────┼────────┼─────────┤
│ │ TOTAL │ │ 3,000 │ 550 │ 2,450 │
└──────┴───────────┴────────┴────────┴────────┴─────────┘

Summary:

- Beginning Balance: 0
- Total In: 3,000
- Total Out: 550
- Ending Balance: 2,450
- Turnover Ratio: 0.22 (monthly)

Chart: Line chart showing balance trend over time

```

---

### **C.2. TRANSACTION REPORTS**

#### **1. Receiving Report (Laporan Penerimaan)**

**Purpose:** Monitor pembelian & penerimaan barang
```

Parameters:

- Date Range
- Warehouse
- Supplier (all/specific)
- Status (all/approved/posted)

Output (Summary):
┌──────────┬────────────┬─────────┬──────────────┬──────────┐
│ Date │ Supplier │ PO No │ Total Items │ Value │
├──────────┼────────────┼─────────┼──────────────┼──────────┤
│ 05/01/26 │ PBF ABC │ PO/001 │ 50 items │ 10,000,000│
│ 12/01/26 │ PBF XYZ │ PO/002 │ 30 items │ 8,000,000│
│ 20/01/26 │ Distributor│ PO/003 │ 20 items │ 5,000,000│
├──────────┼────────────┼─────────┼──────────────┼──────────┤
│ TOTAL │ │ 3 POs │ 100 items │ 23,000,000│
└──────────┴────────────┴─────────┴──────────────┴──────────┘

Output (Detail per Receiving):
Receiving: RCV/2026/001
Supplier: PBF ABC
Date: 05 Jan 2026

┌──────────┬───────┬──────┬─────────┬──────┬──────────┐
│ Item │ Batch │ ED │ Qty │ Price│ Value │
├──────────┼───────┼──────┼─────────┼──────┼──────────┤
│ Paracet │ A123 │12/26 │ 1,000 │ 5,000│ 5,000,000│
│ Amox │ B456 │06/27 │ 500 │10,000│ 5,000,000│
├──────────┼───────┼──────┼─────────┼──────┼──────────┤
│ Subtotal │ │ │ 1,500 │ │10,000,000│
│ PPN 11% │ │ │ │ │ 1,100,000│
│ TOTAL │ │ │ │ │11,100,000│
└──────────┴───────┴──────┴─────────┴──────┴──────────┘

Analytics:

- Avg receiving value: Rp 7,666,667/PO
- Top supplier by value
- Top items by qty/value
- Receiving frequency trend (chart)

```

---

#### **2. Distribution Report (Laporan Distribusi)**

**Purpose:** Monitor distribusi internal antar warehouse
```

Parameters:

- Date Range
- From Warehouse
- To Warehouse
- Status

Output:
┌──────────┬───────────────┬─────────────┬──────────┬──────────┐
│ Date │ From │ To │ Items │ Value │
├──────────┼───────────────┼─────────────┼──────────┼──────────┤
│ 08/01/26 │ Gudang Utama │ Depo IGD │ 25 items │ 5,000,000│
│ 15/01/26 │ Gudang Utama │ Depo Rajal │ 30 items │ 6,000,000│
│ 22/01/26 │ Depo Rajal │ Depo OK │ 5 items │ 1,000,000│
├──────────┼───────────────┼─────────────┼──────────┼──────────┤
│ TOTAL │ │ │ 60 items │12,000,000│
└──────────┴───────────────┴─────────────┴──────────┴──────────┘

Analytics:

- Most active warehouse (sender/receiver)
- Distribution frequency
- Avg distribution value
- Distribution pattern (network diagram)

```

---

#### **3. Prescription Report (Laporan Dispensing)**

**Purpose:** Usage tracking untuk farmasi klinik
```

Parameters:

- Date Range
- Warehouse/Depo
- Service Unit (all/specific poli/ruangan)
- Item (all/specific)

Output (by Service Unit):
┌───────────────┬──────────┬─────────┬────────────┬──────────┐
│ Service Unit │ Rx Count │ Items │ COGS │ Revenue │
├───────────────┼──────────┼─────────┼────────────┼──────────┤
│ Poli Umum │ 150 │ 500 pcs │ 5,000,000 │ 7,500,000│
│ IGD │ 80 │ 300 pcs │ 4,000,000 │ 6,000,000│
│ Ruang Mawar │ 50 │ 200 pcs │ 2,000,000 │ 3,000,000│
├───────────────┼──────────┼─────────┼────────────┼──────────┤
│ TOTAL │ 280 │1000 pcs │ 11,000,000 │16,500,000│
└───────────────┴──────────┴─────────┴────────────┴──────────┘

Gross Profit: Rp 5,500,000 (33% margin)

Output (Top Items):
┌──────────┬──────────┬────────┬──────────┬──────────┬────────┐
│ Item │ Rx Count │ Qty │ COGS │ Revenue │ Margin │
├──────────┼──────────┼────────┼──────────┼──────────┼────────┤
│ Paracet │ 200 │ 2,000 │ 10,000,000│15,000,000│ 33% │
│ Amox │ 100 │ 1,000 │ 12,000,000│18,000,000│ 33% │
└──────────┴──────────┴────────┴──────────┴──────────┴────────┘

Analytics:

- Prescription frequency trend
- Peak hours/days
- Top prescribed items
- Profitability by item/service unit

```

---

#### **4. Adjustment Report (Laporan Penyesuaian)**

**Purpose:** Track all stock adjustments untuk audit
```

Parameters:

- Date Range
- Warehouse
- Adjustment Type (plus/minus/opname)
- Reason Category

Output:
┌──────────┬────────┬─────────┬────────┬──────────┬─────────┐
│ Date │ Adj No │ Type │ Reason │ Items │ Value │
├──────────┼────────┼─────────┼────────┼──────────┼─────────┤
│ 10/01/26 │ ADJ001 │ Minus │ Damaged│ 5 items │ -500,000│
│ 15/01/26 │ OPN001 │ Opname │ Opname │ 23 items │ -2.5M │
│ 20/01/26 │ ADJ002 │ Plus │ Found │ 2 items │ +200,000│
├──────────┼────────┼─────────┼────────┼──────────┼─────────┤
│ TOTAL │ 3 adjs │ │ │ 30 items │ -2.8M │
└──────────┴────────┴─────────┴────────┴──────────┴─────────┘

Summary by Reason:

- Damaged: -Rp 500,000
- Opname Shortage: -Rp 2,500,000
- Found Stock: +Rp 200,000
- Net Adjustment: -Rp 2,800,000

Red Flags:
⚠️ High adjustment value (> Rp 1M)
⚠️ Frequent adjustments (same item)
⚠️ Warehouse with high shrinkage

```

---

### **C.3. FINANCIAL REPORTS**

#### **1. Inventory Value Report (Nilai Persediaan)**

**Purpose:** Snapshot nilai inventory untuk neraca
```

Parameters:

- As of Date (end of period)
- Grouping (category/warehouse/both)
- Comparison (current vs previous period)

Output:
As of: January 31, 2026

┌─────────────────┬──────────────┬──────────────┬──────────┐
│ Category │ Current │ Previous │ Change │
├─────────────────┼──────────────┼──────────────┼──────────┤
│ Medicines │ 500,000,000 │ 480,000,000 │ +4.2% │
│ Medical Supplies│ 200,000,000 │ 195,000,000 │ +2.6% │
│ Reagents │ 100,000,000 │ 98,000,000 │ +2.0% │
│ Medical Equip │ 80,000,000 │ 80,000,000 │ 0.0% │
├─────────────────┼──────────────┼──────────────┼──────────┤
│ TOTAL INVENTORY │ 880,000,000 │ 853,000,000 │ +3.2% │
└─────────────────┴──────────────┴──────────────┴──────────┘

Inventory Turnover Ratio:
= COGS / Average Inventory
= Rp 600,000,000 / Rp 866,500,000
= 0.69 (annual turnover)
= 18.5 months of inventory on hand

Chart: Trend line of inventory value (12 months)

```

---

#### **2. COGS Report (Harga Pokok Penjualan)**

**Purpose:** Tracking cost untuk P&L
```

Parameters:

- Period (month/quarter/year)
- Warehouse/Depo
- Category
- Detail Level (summary/detail)

Output (Summary):
Period: January 2026

┌─────────────────┬──────────────┬──────────────┬──────────┐
│ Category │ COGS │ Revenue │ Margin % │
├─────────────────┼──────────────┼──────────────┼──────────┤
│ Medicines │ 50,000,000 │ 75,000,000 │ 33% │
│ Medical Supplies│ 20,000,000 │ 28,000,000 │ 29% │
│ Reagents │ 10,000,000 │ 14,000,000 │ 29% │
├─────────────────┼──────────────┼──────────────┼──────────┤
│ TOTAL │ 80,000,000 │ 117,000,000 │ 32% │
└─────────────────┴──────────────┴──────────────┴──────────┘

Gross Profit: Rp 37,000,000

COGS Components:

- Beginning Inventory: Rp 850,000,000
- Purchases: Rp 100,000,000
- Available for Sale: Rp 950,000,000
- Ending Inventory: Rp 870,000,000
- COGS: Rp 80,000,000

Chart: COGS vs Revenue trend (12 months)

```

---

#### **3. Purchase Analysis Report**

**Purpose:** Analisis pengadaan untuk cost optimization
```

Parameters:

- Date Range
- Supplier (all/specific)
- Category
- Analysis Type (by supplier/by item/by period)

Output (by Supplier):
Period: January 2026

┌──────────────┬────────┬──────────┬──────────────┬─────────┐
│ Supplier │ PO Qty │ Total │ Avg PO Value │ On Time%│
├──────────────┼────────┼──────────┼──────────────┼─────────┤
│ PBF ABC │ 15 │ 150,000,000│ 10,000,000│ 95% │
│ PBF XYZ │ 10 │ 100,000,000│ 10,000,000│ 80% │
│ Distributor │ 8 │ 50,000,000│ 6,250,000│ 90% │
├──────────────┼────────┼──────────┼──────────────┼─────────┤
│ TOTAL │ 33 │ 300,000,000│ 9,090,909│ 88% │
└──────────────┴────────┴──────────┴──────────────┴─────────┘

Supplier Performance:
┌──────────────┬─────────┬───────────┬──────────┬─────────┐
│ Supplier │ Quality │ Delivery │ Price │ Score │
├──────────────┼─────────┼───────────┼──────────┼─────────┤
│ PBF ABC │ 98% │ 95% │ Compet │ 4.5/5 │
│ PBF XYZ │ 95% │ 80% │ Good │ 4.0/5 │
└──────────────┴─────────┴───────────┴──────────┴─────────┘

Price Variance Analysis:

- Items with price increase > 5%
- Items with price decrease
- Best price comparison across suppliers

Recommendations:
✅ Consolidate orders to PBF ABC (better performance)
⚠️ Review PBF XYZ delivery issues
✅ Negotiate volume discount with top suppliers

```

---

#### **4. Variance Analysis (Budget vs Actual)**

**Purpose:** Compare actual vs planned untuk budget control
```

Parameters:

- Period (month/quarter/year)
- Budget Version (if multiple versions)
- Category/Item

Output:
Period: Q1 2026 (Jan-Mar)

┌──────────────┬────────────┬────────────┬────────────┬─────────┐
│ Category │ Budget │ Actual │ Variance │ % │
├──────────────┼────────────┼────────────┼────────────┼─────────┤
│ Purchases │ 900,000,000│ 850,000,000│ -50,000,000│ -5.6% │
│ COGS │ 250,000,000│ 240,000,000│ -10,000,000│ -4.0% │
│ Adj/Loss │ 10,000,000│ 15,000,000│ +5,000,000│ +50.0%⚠️│
│ Disposal │ 5,000,000│ 8,000,000│ +3,000,000│ +60.0%⚠️│
├──────────────┼────────────┼────────────┼────────────┼─────────┤
│ TOTAL │1,165,000,000│1,113,000,000│ -52,000,000│ -4.5%✅│
└──────────────┴────────────┴────────────┴────────────┴─────────┘

Favorable Variance (✅): Under budget
Unfavorable Variance (⚠️): Over budget

Analysis:
✅ Total spending under budget Rp 52M (good)
✅ Purchases efficiency 5.6% savings
⚠️ Adjustment/Loss 50% over budget → Investigate
⚠️ Disposal 60% over budget → Review expired handling

Action Items:

1. Investigate high loss (Rp 5M over budget)
2. Review expired items management
3. Consider extending shelf-life procurement

```

---

### **C.4. BLUD REPORTS**

#### **1. Laporan Persediaan BLUD**

**Format:** Sesuai Permendagri 61/2007 & revisi
```

PEMERINTAH KABUPATEN [NAMA]
RUMAH SAKIT UMUM DAERAH [NAMA]
LAPORAN PERSEDIAAN
Per: 31 Januari 2026

┌──────┬────────────────┬───────┬────────┬────────────┬─────────┐
│ No │ Nama Barang │ Satuan│ Jumlah │ Harga │ Nilai │
├──────┼────────────────┼───────┼────────┼────────────┼─────────┤
│ I │ OBAT-OBATAN │ │ │ │ │
│ 1 │ Paracetamol │ Tab │ 10,000 │ 5,000 │50,000,000│
│ 2 │ Amoxicillin │ Kaps │ 5,000 │ 12,000 │60,000,000│
│ │ Sub Total Obat │ │ │ │110,000,000│
│ │ │ │ │ │ │
│ II │ ALAT KESEHATAN │ │ │ │ │
│ 1 │ Spuit 3cc │ Pcs │ 2,000 │ 2,500 │ 5,000,000│
│ │ Sub Total Alkes│ │ │ │ 5,000,000│
│ │ │ │ │ │ │
│ III │ BMHP │ │ │ │ │
│ 1 │ Handscoon │ Box │ 500 │ 50,000 │25,000,000│
│ │ Sub Total BMHP │ │ │ │25,000,000│
├──────┼────────────────┼───────┼────────┼────────────┼─────────┤
│ │ TOTAL PERSEDIAAN│ │ │ │140,000,000│
└──────┴────────────────┴───────┴────────┴────────────┴─────────┘

Mengetahui,
Direktur RSUD Kepala Farmasi

[TTD] [TTD]
dr. [Nama], Sp.[X] [Nama], S.Farm, Apt
NIP. [NIP] NIP. [NIP]

```

---

#### **2. Laporan Mutasi Barang BLUD**
```

LAPORAN MUTASI PERSEDIAAN
Periode: 01 Januari - 31 Januari 2026

┌────┬──────────┬────────┬────────┬────────┬────────┬────────┐
│ No │ Nama │ Satuan │ Saldo │ Masuk │ Keluar │ Saldo │
│ │ Barang │ │ Awal │ │ │ Akhir │
├────┼──────────┼────────┼────────┼────────┼────────┼────────┤
│ 1 │ Paracet │ Tab │ 8,000 │ 3,000 │ 1,000 │ 10,000 │
│ 2 │ Amox │ Kaps │ 4,500 │ 1,000 │ 500 │ 5,000 │
├────┼──────────┼────────┼────────┼────────┼────────┼────────┤
│ │ TOTAL │ │ 12,500 │ 4,000 │ 1,500 │ 15,000 │
└────┴──────────┴────────┴────────┴────────┴────────┴────────┘

Rincian Mutasi Keluar:

- Resep Pasien: 1,200 pcs
- Distribusi Internal: 200 pcs
- Adjustment: 50 pcs
- Disposal: 50 pcs

Mengetahui,
Direktur RSUD Kepala Farmasi

```

---

#### **3. Laporan Nilai Persediaan BLUD**
```

LAPORAN NILAI PERSEDIAAN
Per: 31 Januari 2026

┌─────────────────────┬────────────────┬─────────────────┐
│ Uraian │ Jumlah (Rp) │ Persentase (%) │
├─────────────────────┼────────────────┼─────────────────┤
│ ASET LANCAR: │ │ │
│ Persediaan Obat │ 500,000,000 │ 50% │
│ Persediaan Alkes │ 200,000,000 │ 20% │
│ Persediaan BMHP │ 200,000,000 │ 20% │
│ Persediaan Reagen │ 100,000,000 │ 10% │
├─────────────────────┼────────────────┼─────────────────┤
│ TOTAL PERSEDIAAN │1,000,000,000 │ 100% │
└─────────────────────┴────────────────┴─────────────────┘

Perbandingan dengan Periode Sebelumnya:

- Periode Sebelumnya: Rp 950,000,000
- Periode Ini: Rp 1,000,000,000
- Kenaikan: Rp 50,000,000 (5.3%)

Rasio Keuangan:

- Inventory Turnover: 0.8x
- Days Inventory Outstanding: 450 hari
- Inventory to Total Assets: 15%

[Signature blocks]

```

---

### **C.5. TRANSPARENCY REPORTS (untuk Bupati/Publik)**

#### **1. Executive Summary Dashboard**
```

EXECUTIVE SUMMARY - FARMASI RSUD
Periode: Q1 2026

┌─────────────────────────────────────────────────────────┐
│ KEY METRICS │
├─────────────────────────────────────────────────────────┤
│ │
│ Total Inventory Value: Rp 1,000,000,000 │
│ Monthly Purchase: Rp 100,000,000 │
│ Monthly Usage: Rp 80,000,000 │
│ Budget Utilization: 85% (Rp 850M / Rp 1,000M) │
│ │
│ Efficiency Metrics: │
│ - Stock Turnover: 3.2x/year │
│ - Waste Rate: 2.1% (expired/damaged) │
│ - Fill Rate: 98% (availability) │
│ - Cost Savings: Rp 50M (vs budget) │
│ │
│ Patient Impact: │
│ - Prescriptions Filled: 12,500 │
│ - Average Wait Time: 15 minutes │
│ - Stockout Incidents: 5 (0.04%) │
│ │
│ Financial Health: │
│ - Gross Margin: 32% │
│ - ROI: 15% │
│ - Days Payable: 45 days │
└─────────────────────────────────────────────────────────┘

Traffic Light System:
🟢 Green: On target/good performance
🟡 Yellow: Needs attention
🔴 Red: Critical issue

Current Status:
🟢 Budget: Under control
🟢 Availability: High
🟡 Waste: Slightly high (target <2%)
🟢 Efficiency: Good

```

---

#### **2. Purchase Overview (Transparansi Pengadaan)**
```

LAPORAN TRANSPARANSI PENGADAAN
Periode: Q1 2026

Total Pengadaan: Rp 300,000,000
Jumlah PO: 33
Rata-rata PO: Rp 9,090,909

Top 5 Suppliers:
┌────┬──────────────┬────────┬──────────────┬──────────┐
│ No │ Supplier │ PO Qty │ Total Value │ % Share │
├────┼──────────────┼────────┼──────────────┼──────────┤
│ 1 │ PBF ABC │ 15 │ 150,000,000 │ 50% │
│ 2 │ PBF XYZ │ 10 │ 100,000,000 │ 33% │
│ 3 │ Distributor │ 8 │ 50,000,000 │ 17% │
└────┴──────────────┴────────┴──────────────┴──────────┘

Metode Pengadaan:

- E-Purchasing: 70% (Rp 210M)
- Direct Purchase: 30% (Rp 90M - emergency)

Compliance:
✅ All PO dengan approval proper
✅ Supplier verification complete
✅ No conflict of interest detected
✅ Price comparison documented

```

---

#### **3. Stock Value Trend**
```

TREN NILAI PERSEDIAAN
12 Months Trend

[Line Chart showing inventory value over 12 months]

| Month  | Value (Rp M) | Change |
| ------ | ------------ | ------ |
| Jan'25 | 900          | -      |
| Feb'25 | 920          | +2.2%  |
| Mar'25 | 950          | +3.3%  |

...
Dec'25 | 980 | +1.0%
Jan'26 | 1,000 | +2.0%

Analysis:

- Steady growth trend (healthy)
- Seasonal peak: Dec (year-end stock up)
- Average monthly growth: 2.1%
- Within budget projection

```

---

#### **4. Budget Utilization Report**
```

REALISASI ANGGARAN FARMASI
FY 2026 - YTD (Year to Date)

┌─────────────────┬─────────────┬─────────────┬──────────┐
│ Item │ Budget │ Actual │ % │
├─────────────────┼─────────────┼─────────────┼──────────┤
│ Medicine Procur │ 600,000,000 │ 510,000,000 │ 85% │
│ Alkes Procure │ 200,000,000 │ 170,000,000 │ 85% │
│ BMHP Procure │ 150,000,000 │ 130,000,000 │ 87% │
│ Reagen Procure │ 50,000,000 │ 40,000,000 │ 80% │
├─────────────────┼─────────────┼─────────────┼──────────┤
│ TOTAL PROCURE │1,000,000,000 │ 850,000,000 │ 85% │
└─────────────────┴─────────────┴─────────────┴──────────┘

Remaining Budget: Rp 150,000,000
Months Remaining: 9 months
Projected Year-End: 95% utilization

Status: 🟢 On Track

```

---

### **C.6. ROLES & PERMISSIONS - REPORTS**
```

1. Petugas Gudang:
   ✅ stock-reports.view (own warehouse)
   ✅ transaction-reports.view (own)
   ❌ financial-reports.view
   ❌ reports.export

2. Kepala Gudang:
   ✅ stock-reports.view (all warehouses)
   ✅ stock-reports.export
   ✅ transaction-reports.view-all
   ❌ financial-reports.view

3. Kepala Farmasi:
   ✅ ALL stock-reports permissions
   ✅ ALL transaction-reports permissions
   ✅ financial-reports.view (pharmacy-related)
   ✅ blud-reports.view
   ✅ reports.export

4. Keuangan BLUD:
   ✅ ALL financial-reports permissions
   ✅ blud-reports.view
   ✅ blud-reports.generate
   ✅ blud-reports.export
   ✅ transparency-reports.view

5. Direktur:
   ✅ ALL reports permissions
   ✅ transparency-reports.view
   ✅ executive-dashboard.view

6. Bupati:
   ✅ transparency-reports.view
   ✅ executive-dashboard.view
   ✅ reports.export (transparency only)
   ❌ detail operational reports

7. Auditor:
   ✅ ALL reports view permissions
   ✅ ALL reports export permissions
   ❌ reports.generate/modify

8. Super Admin:
   ✅ ALL permissions

```

---

## D. MODUL 3: EXPORT & PRINT SYSTEM

### **D.1. EXPORT TO EXCEL**

#### **Features:**
```

✅ All reports exportable to Excel (.xlsx)
✅ Preserve formatting (colors, borders, alignment)
✅ Multiple sheets (summary + detail)
✅ Charts embedded
✅ Formula-based calculations
✅ Freeze panes, filters
✅ Auto-width columns

```

#### **Implementation Options:**
```

1. Laravel Excel (recommended)
    - Pros: Native Laravel, flexible, fast
    - Cons: Require package

2. PhpSpreadsheet
    - Pros: Pure PHP, no dependencies
    - Cons: Slightly slower

3. Simple CSV export
    - Pros: Fastest, simple
    - Cons: No formatting, no charts

```

#### **Excel Template:**
```

Sheet 1: Summary

- Header (logo, title, period)
- Summary table dengan formatting
- Charts (embedded images)

Sheet 2: Detail

- Raw data table
- Filters enabled
- Freeze header row

Sheet 3: Notes

- Legend, methodology
- Contact info

```

---

### **D.2. EXPORT TO PDF**

#### **Features:**
```

✅ Print-ready format (A4/Letter)
✅ Header/footer with page number
✅ Logo & letterhead
✅ Signature blocks
✅ Charts as images
✅ Page breaks intelligent

```

#### **Implementation Options:**
```

1. DomPDF (simple)
    - Pros: Easy setup, pure PHP
    - Cons: Limited CSS support

2. Snappy (wkhtmltopdf)
    - Pros: Full HTML/CSS support, best quality
    - Cons: Require binary installation

3. mPDF
    - Pros: Good balance, UTF-8 support
    - Cons: Heavier

```

#### **PDF Template:**
```

Header (every page):

- Logo RS
- Report title
- Period
- Page number

Body:

- Executive summary (page 1)
- Charts (dedicated pages)
- Data tables (paginated)
- Notes/appendix

Footer:

- Signature blocks (last page)
- Generated date/time
- Generated by (user)

```

---

### **D.3. PRINT PREVIEW**
```

Features:
✅ Show PDF preview before print
✅ Page navigation
✅ Zoom in/out
✅ Print settings (paper size, orientation)
✅ Download or direct print

Implementation:

- Generate PDF on-the-fly
- Embed PDF viewer (PDF.js)
- Print button triggers browser print dialog

```

---

### **D.4. SCHEDULE AUTO-EMAIL REPORTS**

#### **Features:**
```

✅ Schedule reports (daily/weekly/monthly)
✅ Multiple recipients (email list)
✅ Attach Excel/PDF
✅ Custom email template
✅ Retry failed send
✅ Send log/history

```

#### **Configuration:**
```

Report Schedule Setup:

- Report Type (select from list)
- Frequency:
    - Daily (every day at HH:MM)
    - Weekly (every Monday/etc at HH:MM)
    - Monthly (every 1st/15th/last day at HH:MM)
    - Custom (cron expression)
- Recipients:
    - Direktur: direktur@rs.com
    - Kepala Farmasi: farmasi@rs.com
    - Keuangan: keuangan@rs.com
    - Bupati: bupati@pemda.go.id
- Format: Excel / PDF / Both
- Active: Yes/No

Email Template:
Subject: [Automated] {Report Name} - {Period}
Body:
Dear {Recipient Name},

Please find attached the {Report Name} for period {Period}.

Summary:

- Total Items: {total_items}
- Total Value: {total_value}

For more details, please see the attached file.

Best regards,
Pharmacy Information System
RSUD {Name}

```

#### **Implementation:**
```

Laravel Scheduler:

- Use cron jobs
- Queue jobs for email sending
- Retry failed emails (3 attempts)
- Log all sent emails

Monitoring:

- Dashboard widget: Scheduled reports
- Last sent status (success/failed)
- Next scheduled send time
- Send history (last 30 days)

```

---

### **D.5. EXPORT/PRINT PERMISSIONS**
```

1. Basic Users:
   ✅ export.own-data (data mereka sendiri)
   ❌ export.all-data

2. Supervisors (Kepala Gudang, etc):
   ✅ export.department-data
   ✅ print.reports

3. Management (Kepala Farmasi, Direktur):
   ✅ export.all-reports
   ✅ print.all-reports
   ✅ schedule.email-reports

4. Finance/Accounting:
   ✅ export.financial-reports
   ✅ export.blud-reports

5. Auditor:
   ✅ export.all-reports (audit purposes)
   ❌ schedule.email-reports

6. Bupati:
   ✅ export.transparency-reports only
   ❌ operational details

```

---

## E. SUMMARY DATABASE TABLES FASE 6
```

ACCOUNTING:

1. chart_of_accounts (CoA master)
2. journal_entries (header)
3. journal_entry_details (lines)
4. fiscal_periods (period definition)
5. account_balances (running balances per period)

REPORTING: 6. report_schedules (scheduled reports config) 7. report_send_logs (email send history) 8. report_cache (cached report data for performance)

SETTINGS: 9. inventory_valuation_config (FIFO/Average settings) 10. report_templates (custom report templates)

```

---

## F. INTEGRATION SUMMARY FASE 6
```

Fase 6 Connects To:
✅ All transaction modules (Receiving, Distribution, Prescription, etc)
✅ Stock Cards (for movement tracking)
✅ Item Batches (for valuation)
✅ Users (for permissions & audit)
✅ Warehouses (for grouping)
✅ Categories (for classification)

Fase 6 Provides To:
✅ Management: Decision support data
✅ Finance: Accounting integration
✅ Auditor: Audit trail & compliance
✅ Government: Transparency reporting
✅ Stakeholders: Performance metrics

```

---

## G. PERFORMANCE CONSIDERATIONS
```

Report Generation Optimization:

1. Cache expensive queries (5-15 min)
2. Background job for large reports
3. Pagination for detail data
4. Index database properly
5. Aggregate tables for dashboards
6. Export to queue for large files
7. Limit date range for complex reports
8. Use database views for common joins
