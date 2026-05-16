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
        }

        if ($users) {
            \DB::table('users')->insert($users);
        }

        // 2. Barangs
        \DB::table('barangs')->insert([
            ['id_barang' => \Str::uuid(), 'nama_barang' => 'Ban Dalam Swallow', 'harga_jual' => 35000, 'stok' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id_barang' => \Str::uuid(), 'nama_barang' => 'Pentil Tubeless', 'harga_jual' => 5000, 'stok' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['id_barang' => \Str::uuid(), 'nama_barang' => 'Lem Tambal', 'harga_jual' => 10000, 'stok' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Jasas
        \DB::table('jasas')->insert([
            ['id_jasa' => \Str::uuid(), 'nama_jasa' => 'Tambal Ban Biasa', 'harga_jasa' => 15000, 'created_at' => now(), 'updated_at' => now()],
            ['id_jasa' => \Str::uuid(), 'nama_jasa' => 'Tambal Tubeless', 'harga_jasa' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['id_jasa' => \Str::uuid(), 'nama_jasa' => 'Isi Angin Biasa', 'harga_jasa' => 2000, 'created_at' => now(), 'updated_at' => now()],
            ['id_jasa' => \Str::uuid(), 'nama_jasa' => 'Isi Angin Nitrogen', 'harga_jasa' => 5000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
