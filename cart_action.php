<?php
// cart_action.php
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $notes = sanitize($_POST['notes'] ?? '');

    // Cari menu di DB
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ? AND status = 'tersedia'");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();

    if (!$menu) {
        echo json_encode(['success' => false, 'message' => 'Menu tidak ditemukan atau sedang habis!']);
        exit;
    }

    // Hitung harga setelah diskon jika promo
    $final_price = $menu['harga'];
    if ($menu['is_promo'] && $menu['diskon_persen'] > 0) {
        $final_price = $menu['harga'] - ($menu['harga'] * ($menu['diskon_persen'] / 100));
    }

    // Unik key berdasarkan id menu dan catatan (agar catatan beda jadi baris beda)
    $cart_key = $menu_id . '_' . md5($notes);

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['qty'] += $qty;
        if (!empty($notes)) {
            $_SESSION['cart'][$cart_key]['notes'] = $notes;
        }
    } else {
        $_SESSION['cart'][$cart_key] = [
            'id' => $menu['id'],
            'nama' => $menu['nama_menu'],
            'harga' => $final_price,
            'harga_asli' => $menu['harga'],
            'gambar' => $menu['gambar'],
            'qty' => $qty,
            'notes' => $notes
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Berhasil ditambahkan ke keranjang!',
        'total_items' => getCartCount(),
        'subtotal' => getCartSubtotal(),
        'subtotal_format' => rupiah(getCartSubtotal())
    ]);
    exit;
}

if ($action === 'update_qty') {
    $cart_key = sanitize($_POST['cart_key'] ?? '');
    $delta = (int)($_POST['delta'] ?? 0);

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['qty'] += $delta;
        if ($_SESSION['cart'][$cart_key]['qty'] <= 0) {
            unset($_SESSION['cart'][$cart_key]);
        }
    }

    echo json_encode([
        'success' => true,
        'total_items' => getCartCount(),
        'subtotal' => getCartSubtotal(),
        'subtotal_format' => rupiah(getCartSubtotal())
    ]);
    exit;
}

if ($action === 'remove') {
    $cart_key = sanitize($_POST['cart_key'] ?? $_GET['cart_key'] ?? '');
    if (isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
    }

    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Menu dihapus dari keranjang',
            'total_items' => getCartCount(),
            'subtotal' => getCartSubtotal(),
            'subtotal_format' => rupiah(getCartSubtotal())
        ]);
        exit;
    } else {
        header('Location: cart.php');
        exit;
    }
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}

echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);

