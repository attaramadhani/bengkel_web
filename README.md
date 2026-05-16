# bengkel_web

`bengkel_web` adalah aplikasi web manajemen bengkel berbasis Laravel. Aplikasi ini membantu pengelolaan data barang, data jasa, transaksi kasir, stok barang, laporan pendapatan, dan pembayaran melalui cash atau Midtrans.

## Fitur Utama

- Login pengguna dengan role `admin` dan `kasir`
- Dashboard ringkasan barang, jasa, transaksi, pendapatan, transaksi terbaru, dan stok menipis
- Manajemen master data barang
- Manajemen master data jasa
- Manajemen user oleh admin
- Transaksi penjualan barang dan jasa
- Pengurangan stok barang otomatis saat transaksi
- Pembayaran cash
- Integrasi pembayaran Midtrans Snap
- Callback Midtrans untuk memperbarui status pembayaran
- Laporan harian, bulanan, dan tahunan
- Statistik barang terlaris dan jasa terpopuler

## Teknologi

- PHP `^8.3`
- Laravel `^13.8`
- Vite `^8.0`
- Tailwind CSS `^4.0`
- Midtrans PHP SDK `^2.6`
- PHPUnit `^12.5`

## Struktur Modul

```text
app/Http/Controllers   Controller aplikasi
app/Models             Model Eloquent
database/migrations    Struktur tabel database
database/seeders       Data awal aplikasi
resources/views        Tampilan Blade
routes/web.php         Route web aplikasi
public/css             CSS tambahan dashboard
```

## Hak Akses

| Role | Akses |
| --- | --- |
| `admin` | Dashboard, barang, jasa, user, transaksi, laporan |
| `kasir` | Transaksi |

## Akun Awal

Seeder dapat membuat akun awal dari konfigurasi `.env`. Isi variabel berikut sebelum menjalankan `php artisan migrate --seed`:

```env
SEED_ADMIN_USERNAME=
SEED_ADMIN_PASSWORD=
SEED_KASIR_USERNAME=
SEED_KASIR_PASSWORD=
```

Kosongkan variabel tersebut jika tidak ingin membuat akun dummy dari seeder. Jangan commit username dan password asli ke repository.

## Instalasi

Pastikan PHP, Composer, Node.js, NPM, dan database sudah tersedia.

1. Clone atau salin project ke server lokal.

2. Masuk ke folder project.

```bash
cd bengkel_web
```

3. Install dependency PHP.

```bash
composer install
```

4. Install dependency frontend.

```bash
npm install
```

5. Salin file environment.

```bash
cp .env.example .env
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

6. Generate application key.

```bash
php artisan key:generate
```

7. Konfigurasikan database di file `.env`.

Contoh SQLite:

```env
DB_CONNECTION=sqlite
```

Jika memakai MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bengkel_web
DB_USERNAME=root
DB_PASSWORD=
```

Isi akun awal jika ingin langsung login setelah seeding:

```env
SEED_ADMIN_USERNAME=admin-lokal
SEED_ADMIN_PASSWORD=password-lokal-yang-kuat
SEED_KASIR_USERNAME=kasir-lokal
SEED_KASIR_PASSWORD=password-lokal-yang-kuat
```

8. Jalankan migrasi dan seeder.

```bash
php artisan migrate --seed
```

9. Jalankan server Laravel.

```bash
php artisan serve
```

10. Jalankan Vite di terminal lain.

```bash
npm run dev
```

Buka aplikasi di browser:

```text
http://127.0.0.1:8000
```

## Konfigurasi Midtrans

Tambahkan konfigurasi berikut di `.env` jika ingin menggunakan pembayaran Midtrans:

```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

Isi key Midtrans hanya di `.env` lokal atau server. File `.env` sudah masuk `.gitignore`, jadi key asli tidak perlu dan tidak boleh diupload ke GitHub.

Callback pembayaran tersedia di:

```text
POST /midtrans/callback
```

Pada mode lokal, gunakan tunneling seperti Ngrok atau Cloudflare Tunnel agar Midtrans dapat mengakses callback dari internet.

## Perintah Pengembangan

Menjalankan server Laravel:

```bash
php artisan serve
```

Menjalankan Vite:

```bash
npm run dev
```

Build asset frontend:

```bash
npm run build
```

Menjalankan test:

```bash
composer test
```

Format kode dengan Laravel Pint:

```bash
./vendor/bin/pint
```

## Alur Penggunaan Singkat

1. Login sebagai admin menggunakan akun yang dibuat melalui konfigurasi seeder.
2. Kelola data barang, jasa, dan user.
3. Login sebagai kasir atau admin.
4. Buat transaksi baru dari menu transaksi.
5. Pilih barang atau jasa, tentukan jumlah, lalu pilih metode pembayaran.
6. Cek transaksi dan laporan melalui dashboard atau menu laporan.

## Catatan

- Barang hanya muncul di transaksi jika stok lebih dari `0`.
- Transaksi barang otomatis mengurangi stok.
- Jika pembuatan token Midtrans gagal, transaksi akan disimpan sebagai pembayaran cash.
- Route master data dan laporan hanya dapat diakses oleh admin.
