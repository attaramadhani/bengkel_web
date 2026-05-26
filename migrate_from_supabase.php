<?php

/**
 * migrate_from_supabase.php
 * Menyalin data dari Supabase (PostgreSQL) ke MySQL lokal (Laragon)
 * Jalankan: php migrate_from_supabase.php
 */

// ── Helper loader .env ──────────────────────────────────────────────────────
function get_env_var(string $key, string $default = ''): string {
    static $env = null;
    if ($env === null) {
        $env = [];
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_contains($line, '=') && !str_starts_with(trim($line), '#')) {
                    list($k, $v) = explode('=', $line, 2);
                    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
                }
            }
        }
    }
    return $env[$key] ?? getenv($key) ?: $default;
}

// ── Konfigurasi Supabase (sumber) ──────────────────────────────────────────
$pgHost     = get_env_var('SUPABASE_DB_HOST', 'aws-1-ap-northeast-1.pooler.supabase.com');
$pgPort     = (int)get_env_var('SUPABASE_DB_PORT', '5432');
$pgDB       = get_env_var('SUPABASE_DB_DATABASE', 'postgres');
$pgUser     = get_env_var('SUPABASE_DB_USERNAME', 'postgres.hfycdhujksyyopzbcpiy');
$pgPass     = get_env_var('SUPABASE_DB_PASSWORD', ''); // Jangan hardcode password di sini!

// ── Konfigurasi MySQL lokal (tujuan) ───────────────────────────────────────
$mysqlHost  = get_env_var('DB_HOST', '127.0.0.1');
$mysqlPort  = (int)get_env_var('DB_PORT', '3307');
$mysqlDB    = get_env_var('DB_DATABASE', 'bengkel_app');
$mysqlUser  = get_env_var('DB_USERNAME', 'root');
$mysqlPass  = get_env_var('DB_PASSWORD', '');

// ── Helpers ────────────────────────────────────────────────────────────────
function log_info(string $msg): void  { echo "\e[32m[OK]\e[0m  $msg\n"; }
function log_warn(string $msg): void  { echo "\e[33m[WARN]\e[0m $msg\n"; }
function log_error(string $msg): void { echo "\e[31m[ERR]\e[0m $msg\n"; }
function log_head(string $msg): void  { echo "\n\e[36m══ $msg ══\e[0m\n"; }

// ── Koneksi ────────────────────────────────────────────────────────────────
log_head('Menghubungkan ke Supabase (PostgreSQL)');
try {
    $pg = new PDO(
        "pgsql:host=$pgHost;port=$pgPort;dbname=$pgDB",
        $pgUser,
        $pgPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 15]
    );
    log_info("Terhubung ke Supabase: $pgDB@$pgHost");
} catch (PDOException $e) {
    log_error("Gagal konek ke Supabase: " . $e->getMessage());
    exit(1);
}

log_head('Menghubungkan ke MySQL lokal');
try {
    $mysql = new PDO(
        "mysql:host=$mysqlHost;port=$mysqlPort;dbname=$mysqlDB;charset=utf8mb4",
        $mysqlUser,
        $mysqlPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    log_info("Terhubung ke MySQL: $mysqlDB@$mysqlHost:$mysqlPort");
} catch (PDOException $e) {
    log_error("Gagal konek ke MySQL: " . $e->getMessage());
    exit(1);
}

// ── Fungsi copy tabel ──────────────────────────────────────────────────────
function copyTable(PDO $src, PDO $dst, string $table, string $primaryKey, bool $hasFk = false): void
{
    log_head("Menyalin tabel: $table");

    // Baca dari Supabase
    $rows = $src->query("SELECT * FROM $table ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        log_warn("Tidak ada data di tabel $table (Supabase)");
        return;
    }

    log_info("Ditemukan " . count($rows) . " baris di Supabase");

    // Kosongkan tabel MySQL terlebih dahulu
    if ($hasFk) {
        $dst->exec("SET FOREIGN_KEY_CHECKS=0");
    }
    $dst->exec("TRUNCATE TABLE `$table`");
    if ($hasFk) {
        $dst->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    // Siapkan kolom dari baris pertama
    $columns = array_keys($rows[0]);
    $colList  = implode(', ', array_map(fn($c) => "`$c`", $columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));

    $stmt = $dst->prepare("INSERT INTO `$table` ($colList) VALUES ($placeholders)");

    $dst->beginTransaction();
    $inserted = 0;
    foreach ($rows as $row) {
        try {
            $stmt->execute(array_values($row));
            $inserted++;
        } catch (PDOException $e) {
            log_warn("Skip baris (duplikat/error): " . $e->getMessage());
        }
    }
    $dst->commit();

    log_info("Berhasil menyalin $inserted dari " . count($rows) . " baris ke MySQL");
}

// ── Disable FK checks untuk semua proses ──────────────────────────────────
$mysql->exec("SET FOREIGN_KEY_CHECKS=0");

// ── Urutan sesuai dependency (users dulu, lalu yang ada FK) ───────────────
copyTable($pg, $mysql, 'users',              'id_user');
copyTable($pg, $mysql, 'barangs',            'id_barang');
copyTable($pg, $mysql, 'jasas',              'id_jasa');
copyTable($pg, $mysql, 'transaksis',         'id_transaksi', true);
copyTable($pg, $mysql, 'detail_transaksis',  'id_detail',    true);

$mysql->exec("SET FOREIGN_KEY_CHECKS=1");

// ── Verifikasi ─────────────────────────────────────────────────────────────
log_head('Verifikasi Hasil');
$tables = ['users', 'barangs', 'jasas', 'transaksis', 'detail_transaksis'];
foreach ($tables as $t) {
    $pgCount    = $pg->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    $mysqlCount = $mysql->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    $status = ($pgCount == $mysqlCount) ? "\e[32m✓\e[0m" : "\e[31m✗\e[0m";
    echo "  $status  $t: Supabase=$pgCount  MySQL=$mysqlCount\n";
}

// ── Tampilkan daftar user ──────────────────────────────────────────────────
log_head('Daftar User di MySQL');
$users = $mysql->query("SELECT id_user, username, role, created_at FROM users ORDER BY role")->fetchAll(PDO::FETCH_ASSOC);
if (empty($users)) {
    log_warn("Tidak ada user! Pastikan ada data user di Supabase.");
} else {
    foreach ($users as $u) {
        echo "  → [{$u['role']}] username: \e[33m{$u['username']}\e[0m  (id: {$u['id_user']})\n";
    }
}

echo "\n\e[32m✅ Selesai! Data berhasil disalin dari Supabase ke MySQL lokal.\e[0m\n\n";
