<?php
// order_process.php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$customer_name = sanitize($_POST['customer_name'] ?? '');
$customer_phone = sanitize($_POST['customer_phone'] ?? '');
$customer_address = sanitize($_POST['customer_address'] ?? '');
$patokan = sanitize($_POST['patokan'] ?? '');
$delivery_notes = sanitize($_POST['delivery_notes'] ?? '');
$payment_method = sanitize($_POST['payment_method'] ?? 'COD');

// Normalisasi no WhatsApp
$customer_phone = preg_replace('/[^0-9]/', '', $customer_phone);
if (substr($customer_phone, 0, 1) === '0') {
    $customer_phone = '62' . substr($customer_phone, 1);
} elseif (substr($customer_phone, 0, 2) !== '62') {
    $customer_phone = '62' . $customer_phone;
}

if (empty($customer_name) || empty($customer_phone) || empty($customer_address)) {
    die("Mohon lengkapi nama, nomor telepon, dan alamat pengantaran.");
}

$subtotal = getCartSubtotal();
$ongkir = (int)getSetting('ongkir_default', '10000');
$total_bayar = $subtotal + $ongkir;

// Generate Nomor Invoice Unik
$no_invoice = 'INV-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));

// Data Driver Default untuk Simulasi Pengantaran Delivery
$drivers = [
    ['nama' => 'Budi Santoso', 'phone' => '081234567890', 'plat' => 'B 4567 XYZ'],
    ['nama' => 'Rian Hidayat', 'phone' => '085712349988', 'plat' => 'B 6890 PQR'],
    ['nama' => 'Ahmad Fauzi', 'phone' => '089876543210', 'plat' => 'B 3124 KLP']
];
$assigned_driver = $drivers[array_rand($drivers)];

try {
    $pdo->beginTransaction();

    // Insert Order
    $stmt = $pdo->prepare("INSERT INTO orders 
        (no_invoice, customer_name, customer_phone, customer_address, patokan, delivery_notes, payment_method, payment_status, order_status, subtotal, ongkir, total_bayar, driver_name, driver_phone, driver_plate, estimasi_waktu) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'belum_bayar', 'menunggu', ?, ?, ?, ?, ?, ?, 25)");
    
    $stmt->execute([
        $no_invoice,
        $customer_name,
        $customer_phone,
        $customer_address,
        $patokan,
        $delivery_notes,
        $payment_method,
        $subtotal,
        $ongkir,
        $total_bayar,
        $assigned_driver['nama'],
        $assigned_driver['phone'],
        $assigned_driver['plat']
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert Order Items & Update Stock
    $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_id, nama_menu, harga_satuan, qty, catatan, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stock_stmt = $pdo->prepare("UPDATE menus SET stok = GREATEST(0, stok - ?) WHERE id = ?");

    foreach ($cart as $item) {
        $menu_id = (int)$item['id'];
        $qty = (int)$item['qty'];
        $price = (int)$item['harga'];
        $item_subtotal = $price * $qty;
        $notes = $item['notes'] ?? '';

        $item_stmt->execute([
            $order_id,
            $menu_id,
            $item['nama'],
            $price,
            $qty,
            $notes,
            $item_subtotal
        ]);

        $stock_stmt->execute([$qty, $menu_id]);
    }

    $pdo->commit();

    // Kosongkan keranjang
    $_SESSION['cart'] = [];

    // Redirect ke Tracking Page
    header("Location: tracking.php?inv=" . urlencode($no_invoice) . "&new=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal memproses pesanan: " . $e->getMessage());
}

