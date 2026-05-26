<?php

/**
 * reset_passwords.php
 * Reset password user admin dan kasir untuk keperluan demo
 * Jalankan: php reset_passwords.php
 */

$mysqlHost = '127.0.0.1';
$mysqlPort = 3307;
$mysqlDB   = 'bengkel_app';
$mysqlUser = 'root';
$mysqlPass = '';

// Password baru untuk demo
$adminPassword = 'admin123';
$kasirPassword = 'kasir123';

try {
    $pdo = new PDO(
        "mysql:host=$mysqlHost;port=$mysqlPort;dbname=$mysqlDB;charset=utf8mb4",
        $mysqlUser, $mysqlPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Hash bcrypt manual (cost 12, sama seperti Laravel default)
    $adminHash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    $kasirHash = password_hash($kasirPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    // Update admin
    $stmtAdmin = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'admin'");
    $stmtAdmin->execute([$adminHash]);
    $adminRows = $stmtAdmin->rowCount();

    // Update kasir
    $stmtKasir = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'kasir'");
    $stmtKasir->execute([$kasirHash]);
    $kasirRows = $stmtKasir->rowCount();

    echo "\n✅ Password berhasil direset!\n\n";
    echo "┌─────────────────────────────────────────┐\n";
    echo "│          AKUN DEMO BENGKEL APP           │\n";
    echo "├─────────────────────────────────────────┤\n";

    // Tampilkan info lengkap
    $users = $pdo->query("SELECT username, role FROM users ORDER BY role")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        $pass = ($u['role'] === 'admin') ? $adminPassword : $kasirPassword;
        echo "│  Role     : {$u['role']}\n";
        echo "│  Username : {$u['username']}\n";
        echo "│  Password : $pass\n";
        echo "├─────────────────────────────────────────┤\n";
    }
    echo "└─────────────────────────────────────────┘\n\n";

    echo "Admin diupdate : $adminRows baris\n";
    echo "Kasir diupdate : $kasirRows baris\n\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
