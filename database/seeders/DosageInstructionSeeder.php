<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosageInstructionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructions = [
            // Aturan pakai umum - Tablet/Kapsul
            ['code' => '3X1-SM', 'instruction' => '3 kali sehari 1 tablet sesudah makan', 'frequency' => '3x1', 'timing' => 'sesudah_makan'],
            ['code' => '3X1-SBM', 'instruction' => '3 kali sehari 1 tablet sebelum makan', 'frequency' => '3x1', 'timing' => 'sebelum_makan'],
            ['code' => '3X1-BM', 'instruction' => '3 kali sehari 1 tablet bersama makan', 'frequency' => '3x1', 'timing' => 'bersama_makan'],
            ['code' => '2X1-SM', 'instruction' => '2 kali sehari 1 tablet sesudah makan', 'frequency' => '2x1', 'timing' => 'sesudah_makan'],
            ['code' => '2X1-SBM', 'instruction' => '2 kali sehari 1 tablet sebelum makan', 'frequency' => '2x1', 'timing' => 'sebelum_makan'],
            ['code' => '1X1-MLM', 'instruction' => '1 kali sehari 1 tablet malam sebelum tidur', 'frequency' => '1x1', 'timing' => 'bebas'],
            ['code' => '1X1-PGI', 'instruction' => '1 kali sehari 1 tablet pagi hari', 'frequency' => '1x1', 'timing' => 'bebas'],

            // Aturan pakai khusus
            ['code' => 'PRN', 'instruction' => 'Bila perlu (jika ada keluhan)', 'frequency' => 'PRN', 'timing' => 'bebas'],
            ['code' => '4X1-SM', 'instruction' => '4 kali sehari 1 tablet sesudah makan', 'frequency' => '4x1', 'timing' => 'sesudah_makan'],
            ['code' => '3X2-SM', 'instruction' => '3 kali sehari 2 tablet sesudah makan', 'frequency' => '3x2', 'timing' => 'sesudah_makan'],
            ['code' => '2X2-SM', 'instruction' => '2 kali sehari 2 tablet sesudah makan', 'frequency' => '2x2', 'timing' => 'sesudah_makan'],

            // Aturan pakai sirup
            ['code' => '3X1-SDM-SM', 'instruction' => '3 kali sehari 1 sendok makan sesudah makan', 'frequency' => '3x1', 'timing' => 'sesudah_makan'],
            ['code' => '3X1-SDT-SM', 'instruction' => '3 kali sehari 1 sendok teh sesudah makan', 'frequency' => '3x1', 'timing' => 'sesudah_makan'],
            ['code' => '2X1-SDM-SM', 'instruction' => '2 kali sehari 1 sendok makan sesudah makan', 'frequency' => '2x1', 'timing' => 'sesudah_makan'],

            // Aturan pakai tetes
            ['code' => '3X-TETES', 'instruction' => '3 kali sehari sesuai dosis tetes', 'frequency' => '3x', 'timing' => 'bebas'],
            ['code' => '2X-TETES', 'instruction' => '2 kali sehari sesuai dosis tetes', 'frequency' => '2x', 'timing' => 'bebas'],

            // Aturan pakai salep/krim
            ['code' => '2X-OLES', 'instruction' => '2 kali sehari dioleskan pada area yang sakit', 'frequency' => '2x', 'timing' => 'bebas'],
            ['code' => '3X-OLES', 'instruction' => '3 kali sehari dioleskan pada area yang sakit', 'frequency' => '3x', 'timing' => 'bebas'],
        ];

        foreach ($instructions as $instruction) {
            DB::table('dosage_instructions')->updateOrInsert(
                ['code' => $instruction['code']],
                array_merge($instruction, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
