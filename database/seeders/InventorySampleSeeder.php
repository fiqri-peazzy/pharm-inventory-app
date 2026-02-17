<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemBatch;
use App\Models\StockCard;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Receiving;
use App\Models\ReceivingDetail;
use App\Models\Distribution;
use App\Models\DistributionDetail;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\ServiceUnit;
use App\Services\StockSuggestionService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySampleSeeder extends Seeder
{
    public function run()
    {
        // 0. Cleanup
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ReceivingDetail::truncate();
        Receiving::truncate();
        DistributionDetail::truncate();
        Distribution::truncate();
        PrescriptionDetail::truncate();
        Prescription::truncate();
        ItemBatch::truncate();
        StockCard::truncate();
        DB::table('item_warehouse_settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = Item::all();
        $gdUtama = Warehouse::where('is_main', true)->first();
        $depos = Warehouse::where('is_main', false)->get();
        $suppliers = Supplier::all();
        $users = User::all();
        $serviceUnits = ServiceUnit::active()->get();

        if ($items->isEmpty() || !$gdUtama || $depos->isEmpty() || $suppliers->isEmpty() || $serviceUnits->isEmpty()) {
            $this->command->warn('Essential data is missing. Ensure MasterData, Warehouse, ServiceUnits, and Users are seeded first.');
            return;
        }

        $this->command->info('Seeding Inventory Samples (PBF -> Gudang Utama -> Depo -> Pasien)...');

        // Sample Items for detailed trace
        $sampleItems = $items->take(15); 
        $distCounter = 1;
        $rcvCounter = 1;
        $rxCounter = 1;

        foreach ($sampleItems as $item) {
            // --- STEP 1: RECEIVING (PBF -> GD-UTAMA) ---
            $supplier = $suppliers->random();
            $rcvQty = rand(500, 1000);
            $price = rand(2000, 50000);
            $batchNum = 'BCH-' . strtoupper(substr(md5($item->id . microtime()), 0, 8));
            $expiry = Carbon::now()->addMonths(rand(12, 36));

            $receiving = Receiving::create([
                'receiving_number' => 'RCV/' . date('Y/m') . '/' . str_pad($rcvCounter++, 5, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'warehouse_id' => $gdUtama->id,
                'receiving_date' => Carbon::now()->subDays(15),
                'invoice_number' => 'INV-' . rand(1000, 9999) . '-' . $rcvCounter,
                'invoice_date' => Carbon::now()->subDays(17),
                'total_amount' => $rcvQty * $price,
                'ppn_amount' => ($rcvQty * $price) * 0.11,
                'grand_total' => ($rcvQty * $price) * 1.11,
                'status' => 'posted',
                'created_by' => $users->where('username', 'gudang')->first()->id ?? 1,
            ]);

            $rcvDetail = $receiving->details()->create([
                'item_id' => $item->id,
                'batch_number' => $batchNum,
                'expired_date' => $expiry,
                'qty_received' => $rcvQty,
                'purchase_price' => $price,
                'ppn_amount' => ($rcvQty * $price) * 0.11,
                'subtotal' => ($rcvQty * $price) * 1.11,
            ]);

            // Create Initial Batch in Gd Utama
            $batchGd = ItemBatch::create([
                'item_id' => $item->id,
                'warehouse_id' => $gdUtama->id,
                'batch_number' => $batchNum,
                'expired_date' => $expiry,
                'initial_qty' => $rcvQty,
                'current_qty' => $rcvQty,
                'purchase_price' => $price,
                'is_active' => true,
            ]);

            // Stock Card Gd Utama (IN)
            StockCard::create([
                'item_id' => $item->id,
                'warehouse_id' => $gdUtama->id,
                'item_batch_id' => $batchGd->id,
                'transaction_date' => $receiving->receiving_date,
                'transaction_type' => 'receiving',
                'reference_type' => Receiving::class,
                'reference_id' => $receiving->id,
                'qty_in' => $rcvQty,
                'qty_out' => 0,
                'last_stock' => $rcvQty,
                'notes' => 'Penerimaan dari ' . $supplier->name . ' No Faktur: ' . $receiving->invoice_number,
            ]);

            // --- STEP 2: DISTRIBUTION (GD-UTAMA -> DEPO) ---
            foreach ($depos as $depo) {
                $distQty = rand(50, 150);
                if ($batchGd->current_qty >= $distQty) {
                    $distribution = Distribution::create([
                        'distribution_number' => 'DIST/' . date('Ymd') . '/' . str_pad($distCounter++, 5, '0', STR_PAD_LEFT),
                        'origin_warehouse_id' => $gdUtama->id,
                        'destination_warehouse_id' => $depo->id,
                        'status' => 'received',
                        'type' => 'request',
                        'total_items' => 1,
                        'total_qty' => $distQty,
                        'requested_at' => Carbon::now()->subDays(10),
                        'sent_at' => Carbon::now()->subDays(9),
                        'received_at' => Carbon::now()->subDays(8),
                        'created_by' => $users->where('username', 'gudang')->first()->id ?? 1,
                    ]);

                    $distDetail = $distribution->details()->create([
                        'item_id' => $item->id,
                        'item_batch_id' => $batchGd->id,
                        'qty_requested' => $distQty,
                        'qty_sent' => $distQty,
                        'qty_received' => $distQty,
                        'unit_price' => $price,
                    ]);

                    // Deduct from Gd Utama
                    $batchGd->decrement('current_qty', $distQty);
                    StockCard::create([
                        'item_id' => $item->id,
                        'warehouse_id' => $gdUtama->id,
                        'item_batch_id' => $batchGd->id,
                        'transaction_date' => $distribution->sent_at,
                        'transaction_type' => 'distribution_out',
                        'reference_type' => Distribution::class,
                        'reference_id' => $distribution->id,
                        'qty_in' => 0,
                        'qty_out' => $distQty,
                        'last_stock' => $batchGd->current_qty,
                        'notes' => 'Kirim ke ' . $depo->name,
                    ]);

                    // Add to Depo
                    $batchDepo = ItemBatch::create([
                        'item_id' => $item->id,
                        'warehouse_id' => $depo->id,
                        'batch_number' => $batchNum,
                        'expired_date' => $expiry,
                        'initial_qty' => $distQty,
                        'current_qty' => $distQty,
                        'purchase_price' => $price,
                        'is_active' => true,
                    ]);

                    StockCard::create([
                        'item_id' => $item->id,
                        'warehouse_id' => $depo->id,
                        'item_batch_id' => $batchDepo->id,
                        'transaction_date' => $distribution->received_at,
                        'transaction_type' => 'distribution_in',
                        'reference_type' => Distribution::class,
                        'reference_id' => $distribution->id,
                        'qty_in' => $distQty,
                        'qty_out' => 0,
                        'last_stock' => $distQty,
                        'notes' => 'Terima dari Gudang Utama',
                    ]);

                    // --- STEP 3: DISPENSING (DEPO -> PASIEN) ---
                    // Multiple usage per depo
                    for ($i = 0; $i < rand(3, 8); $i++) {
                        $useQty = rand(1, 10);
                        if ($batchDepo->current_qty >= $useQty) {
                            // Randomize prescription scenarios
                            $payerTypes = ['umum', 'bpjs', 'asuransi_lain'];
                            $payerType = $payerTypes[array_rand($payerTypes)];
                            
                            // Get random service unit and auto-detect patient type
                            $serviceUnit = $serviceUnits->random();
                            $patientType = $serviceUnit->getPatientTypeCode();
                            
                            // Generate room/bed number for RI patients
                            $roomBedNumber = null;
                            if ($patientType === 'ri') {
                                $roomBedNumber = rand(101, 399) . '-' . chr(rand(65, 68)); // e.g., 201-A
                            }
                            
                            // Determine payment status based on payer type
                            $paymentStatus = 'paid';
                            if ($payerType === 'umum') {
                                $paymentStatuses = ['unpaid', 'partial', 'paid'];
                                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                            }
                            
                            // Determine prescription status
                            $statuses = ['submitted', 'processing', 'completed'];
                            $status = $statuses[array_rand($statuses)];
                            $processedAt = null;
                            if ($status === 'completed') {
                                $processedAt = Carbon::now()->subDays(rand(0, 6));
                            } elseif ($status === 'processing') {
                                $processedAt = Carbon::now()->subHours(rand(1, 12));
                            }
                            
                            $rxNumber = 'RX-' . date('ymd') . '-' . str_pad($rxCounter++, 6, '0', STR_PAD_LEFT);
                            $prescription = Prescription::create([
                                'prescription_number' => $rxNumber,
                                'patient_name' => 'Pasien ' . rand(100, 999),
                                'medical_record_number' => 'MR-' . rand(10000, 99999),
                                'doctor_id' => $users->where('role', 'doctor')->first()->id ?? 1,
                                'doctor_name' => 'dr. Sample',
                                'service_unit_id' => $serviceUnit->id,
                                'warehouse_id' => $depo->id,
                                'prescription_date' => Carbon::now()->subDays(rand(1, 7)),
                                'status' => $status,
                                'processed_at' => $processedAt,
                                // New fields
                                'payer_type' => $payerType,
                                'patient_type' => $patientType,
                                'room_bed_number' => $roomBedNumber,
                                'payment_status' => $paymentStatus,
                                'is_returnable' => $patientType === 'ri',
                            ]);

                            $rxDetail = $prescription->details()->create([
                                'item_id' => $item->id,
                                'item_batch_id' => $batchDepo->id,
                                'qty' => $useQty,
                                'price_per_unit' => $price * 1.25,
                                'subtotal' => ($price * 1.25) * $useQty,
                                'instruction' => '3 x 1 Hari',
                            ]);

                            // Only deduct stock and create stock card for completed prescriptions
                            if ($status === 'completed') {
                                $batchDepo->decrement('current_qty', $useQty);
                                StockCard::create([
                                    'item_id' => $item->id,
                                    'warehouse_id' => $depo->id,
                                    'item_batch_id' => $batchDepo->id,
                                    'transaction_date' => $prescription->processed_at,
                                    'transaction_type' => 'prescription',
                                    'reference_type' => Prescription::class,
                                    'reference_id' => $prescription->id,
                                    'qty_in' => 0,
                                    'qty_out' => $useQty,
                                    'last_stock' => $batchDepo->current_qty,
                                    'notes' => 'Resep No: ' . $rxNumber . ' (Pasien: ' . $prescription->patient_name . ')',
                                ]);
                            }
                        }
                    }
                }
            }
        }

        echo "\n📊 Calculating stock thresholds (Reorder Points & Min Stock)...\n";
        
        $service = new StockSuggestionService();
        $updated = $service->calculateAllThresholds(null, 90);
        
        echo "✅ Updated thresholds for {$updated} item-warehouse combinations\n";
        echo "\n✅ Inventory Samples successfully seeded with full paper trail integrity.\n";
    }
}
