<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BengkelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Users
        $users = [];

        if (env('SEED_ADMIN_USERNAME') && env('SEED_ADMIN_PASSWORD')) {
            $users[] = [
                'id_user' => \Str::uuid(),
                'username' => env('SEED_ADMIN_USERNAME'),
                'password' => \Hash::make(env('SEED_ADMIN_PASSWORD')),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            // Default seed fallback if not set in environment
            $users[] = [
                'id_user' => \Str::uuid(),
                'username' => 'admin1',
                'password' => \Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (env('SEED_KASIR_USERNAME') && env('SEED_KASIR_PASSWORD')) {
            $users[] = [
                'id_user' => \Str::uuid(),
                'username' => env('SEED_KASIR_USERNAME'),
                'password' => \Hash::make(env('SEED_KASIR_PASSWORD')),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            // Default seed fallback if not set in environment
            $users[] = [
                'id_user' => \Str::uuid(),
                'username' => 'kasir1',
                'password' => \Hash::make('kasir123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($users) {
            \DB::table('users')->insert($users);
        }

        // 2. Barangs (100 items)
        $barangNames = [
            'Ban Dalam 14"', 'Ban Dalam 16"', 'Ban Dalam 17"', 'Ban Dalam 18"', 'Ban Dalam 20"',
            'Ban Luar Tubeless 14"', 'Ban Luar Tubeless 16"', 'Ban Luar Tubeless 17"', 'Ban Luar Racing', 'Ban Luar Off-road',
            'Oli Mesin SAE 10W-40', 'Oli Mesin SAE 20W-50', 'Oli Mesin SAE 15W-40', 'Oli Mesin Matic', 'Oli Gardan',
            'Oli Samping 2-Tak', 'Oli Transmisi Manual', 'Oli Fork', 'Oli Rantai', 'Oli Rem',
            'Kampas Rem Depan', 'Kampas Rem Belakang', 'Kampas Rem Cakram', 'Kampas Rem Tromol', 'Kampas Kopling',
            'Busi NGK Standard', 'Busi NGK Iridium', 'Busi Denso', 'Busi Racing', 'Busi 2-Tak',
            'Rantai Motor 428', 'Rantai Motor 420', 'Rantai Motor 520', 'Gear Set Depan', 'Gear Set Belakang',
            'Bearing Roda Depan', 'Bearing Roda Belakang', 'Bearing Komstir', 'Seal Shock Depan', 'Seal Shock Belakang',
            'Lampu Depan LED', 'Lampu Depan Halogen', 'Lampu Belakang LED', 'Lampu Sein LED', 'Bohlam Sein Standard',
            'Accu/Aki 5A', 'Accu/Aki 7A', 'Accu/Aki 9A', 'Accu Kering MF', 'Accu Basah Konvensional',
            'Filter Udara Standard', 'Filter Udara Racing', 'Filter Oli', 'Saringan Bensin', 'Kabel Gas',
            'Kabel Kopling', 'Kabel Speedometer', 'Kabel Rem Depan', 'Kabel Rem Belakang', 'Kabel Body Set',
            'Pentil Ban Tubeless', 'Pentil Ban Standard', 'Patch Tambal Ban Kecil', 'Patch Tambal Ban Besar', 'Lem Tambal Ban',
            'Cairan Tambal Tubeless', 'Pompa Angin Mini', 'Dongkrak Motor', 'Spion Standar', 'Spion Variasi',
            'Knalpot Standard', 'Knalpot Racing', 'Piston Kit', 'Ring Piston', 'Blok Seher',
            'Karburator PE 28', 'Karburator PE 24', 'Karburator Standard', 'Injektor Fuel Injection', 'Throttle Body',
            'CDI Standard', 'CDI Racing', 'Coil Ignition', 'Regulator Kiprok', 'Stator Spul',
            'V-Belt Matic', 'Roller Matic 8g', 'Roller Matic 10g', 'Roller Matic 12g', 'Per CVT',
            'Grip Handle Karet', 'Grip Handle Busa', 'Handguard Motor', 'Footstep Standard', 'Footstep Racing',
            'Velg Racing Depan', 'Velg Racing Belakang', 'Jari-Jari Velg', 'Tromol Depan', 'Tromol Belakang',
        ];

        $barangIds = [];
        foreach ($barangNames as $name) {
            $id = \Str::uuid();
            $barangIds[] = $id;
            \DB::table('barangs')->insert([
                'id_barang' => $id,
                'nama_barang' => $name,
                'harga_jual' => rand(5, 500) * 1000,
                'stok' => rand(5, 40),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Jasas (90+ services)
        $jasaNames = [
            ['Tambal Ban Biasa', 10000], ['Tambal Ban Tubeless', 20000], ['Tambal Ban Press Panas', 25000],
            ['Ganti Ban Dalam', 15000], ['Ganti Ban Luar', 20000], ['Ganti Ban Dalam + Luar', 30000],
            ['Isi Angin Ban', 2000], ['Isi Nitrogen Ban', 10000], ['Balancing Ban', 15000],
            ['Spooring Ban', 50000], ['Cabut Pentil', 5000],
            ['Ganti Oli Mesin', 15000], ['Ganti Oli Matic', 20000], ['Ganti Oli Gardan', 15000],
            ['Ganti Oli Samping', 10000], ['Ganti Oli Transmisi', 15000], ['Ganti Oli Fork', 25000],
            ['Ganti Kampas Rem Depan', 20000], ['Ganti Kampas Rem Belakang', 20000], ['Setel Rem', 10000],
            ['Ganti Busi', 10000], ['Bersihkan Busi', 5000], ['Cek Kelistrikan', 15000],
            ['Ganti Rantai + Gear', 30000], ['Stel Rantai', 10000], ['Pelumasan Rantai', 5000],
            ['Ganti Bearing Roda', 25000], ['Ganti Bearing Komstir', 30000], ['Ganti Seal Shock', 35000],
            ['Ganti Lampu Depan', 10000], ['Ganti Lampu Belakang', 10000], ['Ganti Lampu Sein', 10000],
            ['Ganti Accu/Aki', 15000], ['Cas Accu/Aki', 10000], ['Cek Tegangan Accu', 5000],
            ['Ganti Filter Udara', 10000], ['Bersihkan Filter Udara', 5000], ['Ganti Saringan Bensin', 10000],
            ['Ganti Kabel Gas', 15000], ['Ganti Kabel Kopling', 15000], ['Ganti Kabel Rem', 15000],
            ['Tune Up Mesin', 50000], ['Overhaul Mesin Ringan', 150000], ['Overhaul Mesin Berat', 350000],
            ['Service Karburator', 35000], ['Bersihkan Karburator', 25000], ['Setting Karburator', 20000],
            ['Service Injeksi/FI', 50000], ['Reset ECU', 30000], ['Scanning Motor Injeksi', 25000],
            ['Ganti CDI', 15000], ['Ganti Coil', 15000], ['Ganti Kiprok/Regulator', 20000],
            ['Ganti Spul/Stator', 30000], ['Ganti V-Belt Matic', 25000], ['Ganti Roller Matic', 20000],
            ['Service CVT Matic', 40000], ['Ganti Per CVT', 15000], ['Bersihkan CVT', 20000],
            ['Ganti Piston', 50000], ['Ganti Ring Piston', 30000], ['Boring Silinder', 100000],
            ['Las Knalpot', 25000], ['Ganti Knalpot', 20000], ['Modifikasi Knalpot', 50000],
            ['Ganti Spion', 10000], ['Ganti Grip Handle', 10000], ['Ganti Footstep', 15000],
            ['Pasang Alarm Motor', 75000], ['Pasang GPS Tracker', 100000], ['Pasang Lampu LED', 25000],
            ['Pasang Aksesoris', 15000], ['Cat Velg', 50000], ['Polish Body Motor', 35000],
            ['Cuci Motor Standar', 15000], ['Cuci Motor Premium', 25000], ['Salon Motor', 50000],
            ['Ganti Handguard', 20000], ['Ganti Visor', 15000], ['Pasang Windshield', 30000],
            ['Ganti Standar Samping', 15000], ['Ganti Standar Tengah', 20000], ['Ganti Jok Motor', 40000],
            ['Ganti Velg Racing', 30000], ['Ganti Jari-Jari', 40000], ['Truing Velg', 25000],
            ['Ganti Tromol', 25000], ['Bersihkan Injektor', 30000], ['Cek Kompresi Mesin', 15000],
            ['Servis Starter Elektrik', 25000], ['Ganti Kunci Kontak', 30000],
            ['Duplikat Kunci Motor', 20000], ['Ganti Engkol Kick Starter', 20000], ['Pemasangan Box Motor', 25000],
            ['Service Rem Cakram', 25000], ['Bleed Minyak Rem', 15000], ['Ganti Master Rem', 35000],
            ['Ganti Selang Rem', 20000],
        ];

        $jasaIds = [];
        foreach ($jasaNames as $jasa) {
            $id = \Str::uuid();
            $jasaIds[] = $id;
            \DB::table('jasas')->insert([
                'id_jasa' => $id,
                'nama_jasa' => $jasa[0],
                'harga_jasa' => $jasa[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Dummy Transaksis (100 transactions)
        $userIds = \DB::table('users')->pluck('id_user')->toArray();
        if (empty($userIds)) {
            return;
        }

        $metodeBayar = ['cash', 'cash', 'cash', 'midtrans'];

        for ($t = 0; $t < 100; $t++) {
            $trxId = \Str::uuid();
            $userId = $userIds[array_rand($userIds)];
            $metode = $metodeBayar[array_rand($metodeBayar)];

            $daysAgo = rand(0, 30);
            $hour = rand(8, 20);
            $minute = rand(0, 59);
            $createdAt = now()->subDays($daysAgo)->setHour($hour)->setMinute($minute)->setSecond(0);

            \DB::table('transaksis')->insert([
                'id_transaksi' => $trxId,
                'id_user' => $userId,
                'total_pembayaran' => 0,
                'metode_bayar' => $metode,
                'status_bayar' => 'lunas',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $totalPembayaran = 0;
            $numDetails = rand(1, 4);

            for ($d = 0; $d < $numDetails; $d++) {
                $isBarang = rand(0, 1);
                $qty = rand(1, 3);
                $subtotal = 0;
                $idBarang = null;
                $idJasa = null;

                if ($isBarang && !empty($barangIds)) {
                    $idBarang = $barangIds[array_rand($barangIds)];
                    $harga = \DB::table('barangs')->where('id_barang', $idBarang)->value('harga_jual');
                    $subtotal = $harga * $qty;
                } else if (!empty($jasaIds)) {
                    $idJasa = $jasaIds[array_rand($jasaIds)];
                    $harga = \DB::table('jasas')->where('id_jasa', $idJasa)->value('harga_jasa');
                    $subtotal = $harga * $qty;
                }

                \DB::table('detail_transaksis')->insert([
                    'id_detail' => \Str::uuid(),
                    'id_transaksi' => $trxId,
                    'id_barang' => $idBarang,
                    'id_jasa' => $idJasa,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $totalPembayaran += $subtotal;
            }

            \DB::table('transaksis')->where('id_transaksi', $trxId)->update([
                'total_pembayaran' => $totalPembayaran
            ]);
        }
    }
}
