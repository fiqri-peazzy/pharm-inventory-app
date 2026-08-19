<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Bahasa Indonesia
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute harus disetujui.',
    'accepted_if' => ':attribute harus disetujui ketika :other bernilai :value.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus tanggal setelah :date.',
    'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'ascii' => ':attribute hanya boleh berisi karakter alfanumerik dan simbol satu byte.',
    'before' => ':attribute harus tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => ':attribute harus memiliki antara :min sampai :max item.',
        'file' => ':attribute harus antara :min sampai :max kilobyte.',
        'numeric' => ':attribute harus antara :min sampai :max.',
        'string' => ':attribute harus antara :min sampai :max karakter.',
    ],
    'boolean' => ':attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi salah.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak cocok dengan format :format.',
    'decimal' => ':attribute harus memiliki :decimal desimal.',
    'declined' => ':attribute harus ditolak.',
    'declined_if' => ':attribute harus ditolak ketika :other bernilai :value.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus antara :min sampai :max digit.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute memiliki nilai yang duplikat.',
    'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu dari berikut: :values.',
    'doesnt_start_with' => ':attribute tidak boleh diawali dengan salah satu dari berikut: :values.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'extensions' => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => ':attribute harus berupa file.',
    'filled' => ':attribute wajib diisi.',
    'gt' => [
        'array' => ':attribute harus memiliki lebih dari :value item.',
        'file' => ':attribute harus lebih besar dari :value kilobyte.',
        'numeric' => ':attribute harus lebih besar dari :value.',
        'string' => ':attribute harus lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus memiliki :value item atau lebih.',
        'file' => ':attribute harus lebih besar atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'string' => ':attribute harus lebih besar atau sama dengan :value karakter.',
    ],
    'hex_color' => ':attribute harus berupa warna heksadesimal yang valid.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ada di dalam :other.',
    'integer' => ':attribute harus berupa angka bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus berupa alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'lowercase' => ':attribute harus huruf kecil.',
    'lt' => [
        'array' => ':attribute harus memiliki kurang dari :value item.',
        'file' => ':attribute harus lebih kecil dari :value kilobyte.',
        'numeric' => ':attribute harus lebih kecil dari :value.',
        'string' => ':attribute harus lebih kecil dari :value karakter.',
    ],
    'lte' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :value item.',
        'file' => ':attribute harus lebih kecil atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus lebih kecil atau sama dengan :value.',
        'string' => ':attribute harus lebih kecil atau sama dengan :value karakter.',
    ],
    'mac_address' => ':attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
        'file' => ':attribute tidak boleh lebih besar dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits' => ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => ':attribute harus berupa file bertipe: :values.',
    'mimetypes' => ':attribute harus berupa file bertipe: :values.',
    'min' => [
        'array' => ':attribute harus memiliki minimal :min item.',
        'file' => ':attribute harus minimal :min kilobyte.',
        'numeric' => ':attribute harus minimal :min.',
        'string' => ':attribute harus minimal :min karakter.',
    ],
    'min_digits' => ':attribute harus memiliki minimal :min digit.',
    'missing' => ':attribute harus tidak ada.',
    'missing_if' => ':attribute harus tidak ada ketika :other bernilai :value.',
    'missing_unless' => ':attribute harus tidak ada kecuali :other bernilai :value.',
    'missing_with' => ':attribute harus tidak ada ketika :values ada.',
    'missing_with_all' => ':attribute harus tidak ada ketika :values ada.',
    'multiple_of' => ':attribute harus kelipatan dari :value.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => [
        'letters' => ':attribute harus mengandung minimal satu huruf.',
        'mixed' => ':attribute harus mengandung minimal satu huruf besar dan satu huruf kecil.',
        'numbers' => ':attribute harus mengandung minimal satu angka.',
        'symbols' => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute yang dimasukkan pernah bocor dalam insiden kebocoran data. Silakan pilih :attribute yang lain.',
    ],
    'present' => ':attribute wajib ada.',
    'present_if' => ':attribute wajib ada ketika :other bernilai :value.',
    'present_unless' => ':attribute wajib ada kecuali :other bernilai :value.',
    'present_with' => ':attribute wajib ada ketika :values ada.',
    'present_with_all' => ':attribute wajib ada ketika :values ada.',
    'prohibited' => ':attribute dilarang.',
    'prohibited_if' => ':attribute dilarang ketika :other bernilai :value.',
    'prohibited_unless' => ':attribute dilarang kecuali :other ada di dalam :values.',
    'prohibits' => ':attribute melarang :other untuk ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_array_keys' => ':attribute harus memiliki entri untuk: :values.',
    'required_if' => ':attribute wajib diisi ketika :other bernilai :value.',
    'required_if_accepted' => ':attribute wajib diisi ketika :other disetujui.',
    'required_unless' => ':attribute wajib diisi kecuali :other bernilai :values.',
    'required_with' => ':attribute wajib diisi ketika :values ada.',
    'required_with_all' => ':attribute wajib diisi ketika :values ada.',
    'required_without' => ':attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi ketika tidak ada satu pun dari :values yang ada.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'array' => ':attribute harus berisi :size item.',
        'file' => ':attribute harus :size kilobyte.',
        'numeric' => ':attribute harus :size.',
        'string' => ':attribute harus :size karakter.',
    ],
    'starts_with' => ':attribute harus diawali dengan salah satu dari berikut: :values.',
    'string' => ':attribute harus berupa teks.',
    'timezone' => ':attribute harus zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'uppercase' => ':attribute harus huruf besar.',
    'url' => ':attribute harus berupa URL yang valid.',
    'ulid' => ':attribute harus berupa ULID yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Kustom
    |--------------------------------------------------------------------------
    */

    'custom' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama Atribut Kustom (nama kolom dalam bahasa Indonesia)
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        // Umum
        'name' => 'nama',
        'code' => 'kode',
        'type' => 'tipe',
        'email' => 'email',
        'phone' => 'nomor telepon',
        'address' => 'alamat',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'username' => 'nama pengguna',
        'file' => 'file',
        'logo' => 'logo',
        'description' => 'deskripsi',
        'notes' => 'catatan',
        'status' => 'status',
        'date' => 'tanggal',
        'quantity' => 'jumlah',
        'price' => 'harga',

        // Settings
        'appName' => 'nama aplikasi',
        'hospitalName' => 'nama rumah sakit/instansi',
        'appAddress' => 'alamat',
        'appPhone' => 'nomor telepon',
        'appEmail' => 'email',

        // Master Data
        'item_category_id' => 'kategori item',
        'item_unit_id' => 'satuan item',
        'warehouse_id' => 'gudang',
        'supplier_id' => 'supplier',
        'selectedRoles' => 'peran (role)',
        'is_active' => 'status aktif',
        'employee_id' => 'NIP/ID pegawai',
        'contact_person' => 'nama kontak',
        'generic_name' => 'nama generik',
        'barcode' => 'barcode',

        // Stok / Inventory
        'item_name' => 'nama item',
        'category_code' => 'kode kategori',
        'unit_code' => 'kode satuan',
        'batch_number' => 'nomor batch',
        'expired_date' => 'tanggal kadaluarsa',
        'supplier_code' => 'kode supplier',
        'qty_received' => 'jumlah diterima',
        'qty_used' => 'jumlah terpakai',
        'purchase_price' => 'harga beli',
        'invoice_number' => 'nomor faktur',
        'invoice_date' => 'tanggal faktur',
        'invoice_file' => 'file faktur',
        'item_batch_id' => 'batch item',
        'qty_sent' => 'jumlah dikirim',
        'origin_warehouse_id' => 'gudang asal',
        'destination_warehouse_id' => 'gudang tujuan',
        'from_warehouse_id' => 'gudang asal',
        'to_warehouse_id' => 'gudang tujuan',
        'reason_category' => 'kategori alasan',
        'reason' => 'alasan',
        'evidence_file' => 'file bukti',
        'investigation_report' => 'laporan investigasi',
        'corrective_action' => 'tindakan korektif',

        // Procurement
        'item_id' => 'item',
        'price_type' => 'tipe harga',
        'ppn_percentage' => 'persentase PPN',
        'effective_date' => 'tanggal berlaku',
        'end_date' => 'tanggal berakhir',
        'requested_qty' => 'jumlah diminta',
        'rejectionReason' => 'alasan penolakan',
        'is_triangulated' => 'triangulasi',

        // Clinical
        'patient_name' => 'nama pasien',
        'payer_type' => 'tipe penjamin',
        'service_unit_id' => 'unit layanan',
        'room_bed_number' => 'nomor kamar/bed',

        // Accounting
        'normal_balance' => 'saldo normal',
        'account_id' => 'akun',
        'entries' => 'entri jurnal',
        'debit' => 'debit',
        'credit' => 'kredit',
        'journal_date' => 'tanggal jurnal',

        // Stock adjustment / opname
        'adjusted_qty' => 'jumlah penyesuaian',
        'adjustment_date' => 'tanggal penyesuaian',
        'batch_id' => 'batch',
        'qty' => 'jumlah',

        // Disposal
        'disposal_date' => 'tanggal pemusnahan',
        'disposal_type' => 'tipe pemusnahan',

        // Procurement
        'items' => 'daftar item',
        'default_warehouse_id' => 'gudang default',
        'discount_percentage' => 'persentase diskon',
        'expected_delivery_date' => 'perkiraan tanggal kirim',
        'po_date' => 'tanggal PO',
        'purchase_order_id' => 'purchase order',
        'qty_ordered' => 'jumlah dipesan',
        'qty_requested' => 'jumlah diminta',
        'receiving_date' => 'tanggal penerimaan',
        'request_date' => 'tanggal permintaan',
        'sp_type' => 'tipe surat pesanan',
        'payment_term' => 'termin pembayaran',
        'period_month' => 'bulan periode',
        'period_year' => 'tahun periode',

        // Clinical
        'patient_type' => 'tipe pasien',
        'prescription_date' => 'tanggal resep',
    ],
];
