<?php
// config/database.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database MySQL
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_pemesanan_makanan';
$db_port = 3306;

try {
    // Koneksi PDO dengan error mode Exception
    $pdo = new PDO("mysql:host={$db_host};port={$db_port};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Pastikan database terpilih
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$db_name}`;");

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Inisialisasi Cart di session jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil Pengaturan Restoran dari Database
function getSetting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Format Rupiah
function rupiah($angka) {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

// Sanitasi Input
function sanitize($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// Hitung Total Item di Keranjang
function getCartCount() {
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += (int)($item['qty'] ?? 1);
        }
    }
    return $total;
}

// Hitung Subtotal Keranjang
function getCartSubtotal() {
    $subtotal = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += ((int)$item['harga'] * (int)$item['qty']);
        }
    }
    return $subtotal;
}

// Helper Badge Status Pesanan
function getOrderStatusBadge($status) {
    switch ($status) {
        case 'menunggu':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200"><i class="fa-solid fa-hourglass-half animate-spin"></i> Menunggu Konfirmasi</span>';
        case 'dimasak':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200"><i class="fa-solid fa-fire-burner"></i> Sedang Dimasak</span>';
        case 'diantar':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200"><i class="fa-solid fa-motorcycle animate-bounce"></i> Dalam Pengantaran</span>';
        case 'selesai':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200"><i class="fa-solid fa-circle-check"></i> Pesanan Selesai</span>';
        case 'dibatalkan':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200"><i class="fa-solid fa-circle-xmark"></i> Dibatalkan</span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">' . htmlspecialchars((string)$status) . '</span>';
    }
}

// Helper Badge Status Pembayaran
function getPaymentStatusBadge($status) {
    switch ($status) {
        case 'lunas':
            return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check-circle"></i> Lunas</span>';
        case 'belum_bayar':
            return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-800 border border-yellow-200"><i class="fa-solid fa-clock"></i> Belum Bayar</span>';
        default:
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">' . htmlspecialchars((string)$status) . '</span>';
    }
}

