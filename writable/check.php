<?php
$pdo = new PDO('mysql:host=localhost;dbname=restocrm;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SELECT id, email, name, role, is_active, restaurant_id FROM users');
echo "=== USERS TABLE ===\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
    echo "ID:{$u['id']} | {$u['email']} | {$u['name']} | role:{$u['role']} | active:{$u['is_active']} | resto_id:" . ($u['restaurant_id'] ?? 'NULL') . "\n";
}

echo "\n=== VERIFY PASSWORD ===\n";
$stmt2 = $pdo->query("SELECT password FROM users WHERE email='admin@restocrm.local'");
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $ok = password_verify('admin123', $row['password']);
    echo "Password 'admin123' verify: " . ($ok ? "MATCH ✓" : "FAIL ✗") . "\n";
} else {
    echo "User not found!\n";
}

echo "\n=== SESSION DIR ===\n";
$sessionPath = 'd:/prototype-project-2026/writable/sessions';
echo "Exists: " . (is_dir($sessionPath) ? "YES" : "NO") . "\n";
echo "Writable: " . (is_writable($sessionPath) ? "YES" : "NO") . "\n";
