<?php
// update_status_demo.php
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$invoice = sanitize($_POST['invoice'] ?? '');
$status = sanitize($_POST['status'] ?? '');
$allowed_statuses = ['menunggu', 'dimasak', 'diantar', 'selesai', 'dibatalkan'];

if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

try {
    // Jika status selesai, otomatis set payment_status ke lunas jika COD
    if ($status === 'selesai') {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = 'lunas' WHERE no_invoice = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE no_invoice = ?");
    }
    
    $stmt->execute([$status, $invoice]);

    echo json_encode([
        'success' => true,
        'message' => 'Status berhasil diubah',
        'status' => $status
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

