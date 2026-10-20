<?php
// admin/cetak_nota.php
require_once __DIR__ . '/../config/database.php';

$no_invoice = sanitize($_GET['inv'] ?? '');
if (empty($no_invoice)) {
    die("Nomor invoice tidak disertakan.");
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE no_invoice = ?");
$stmt->execute([$no_invoice]);
$order = $stmt->fetch();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

$item_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$item_stmt->execute([$order['id']]);
$items = $item_stmt->fetchAll();

$resto_name = getSetting('nama_resto', 'DAPUR LEZAT EXPRESS');
$resto_address = getSetting('alamat_resto', 'Jl. Merdeka No. 45, Jakarta Selatan');
$resto_wa = getSetting('no_wa_resto', '08123456789');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan - <?= htmlspecialchars($order['no_invoice']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f3f4f6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .receipt-card {
            background: #fff;
            width: 340px;
            padding: 20px 18px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            color: #111;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #444;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .header p {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
            line-height: 1.3;
        }
        .meta-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .divider {
            border-top: 1px dashed #444;
            margin: 8px 0;
        }
        .items-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            padding: 4px 0;
            border-bottom: 1px dashed #444;
        }
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals-table {
            width: 100%;
            font-size: 11px;
            margin-top: 6px;
        }
        .totals-table td {
            padding: 2px 0;
        }
        .totals-table .bold {
            font-weight: bold;
            font-size: 13px;
        }
        .delivery-box {
            margin-top: 10px;
            padding: 8px;
            background: #fafafa;
            border: 1px dashed #888;
            border-radius: 4px;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px dashed #444;
            font-size: 11px;
        }
        .btn-print {
            margin-top: 20px;
            padding: 10px 24px;
            background: #ea580c;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 13px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                width: 100%;
                padding: 5px;
            }
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-card">
        <div class="header">
            <h1><?= htmlspecialchars($resto_name) ?></h1>
            <p><?= htmlspecialchars($resto_address) ?></p>
            <p>WhatsApp: <?= htmlspecialchars($resto_wa) ?></p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 70px;">INVOICE</td>
                <td>: <strong><?= htmlspecialchars($order['no_invoice']) ?></strong></td>
            </tr>
            <tr>
                <td>TANGGAL</td>
                <td>: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?> WIB</td>
            </tr>
            <tr>
                <td>PEMESAN</td>
                <td>: <?= htmlspecialchars($order['customer_name']) ?></td>
            </tr>
            <tr>
                <td>NO. HP</td>
                <td>: <?= htmlspecialchars($order['customer_phone']) ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>MENU</th>
                    <th class="text-right">QTY</th>
                    <th class="text-right">HARGA</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                <tr>
                    <td colspan="4" style="font-weight: bold;"><?= htmlspecialchars($it['nama_menu']) ?></td>
                </tr>
                <tr>
                    <td style="color:#666; font-size:10px;"><?= !empty($it['catatan']) ? '(' . htmlspecialchars($it['catatan']) . ')' : '' ?></td>
                    <td class="text-right"><?= $it['qty'] ?>x</td>
                    <td class="text-right"><?= number_format($it['harga_satuan'], 0, ',', '.') ?></td>
                    <td class="text-right" style="font-weight: bold;"><?= number_format($it['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <table class="totals-table">
            <tr>
                <td>SUBTOTAL MENU</td>
                <td class="text-right">Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>ONGKOS KIRIM DELIVERY</td>
                <td class="text-right">Rp <?= number_format($order['ongkir'], 0, ',', '.') ?></td>
            </tr>
            <tr class="bold">
                <td>TOTAL TAGIHAN</td>
                <td class="text-right">Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>METODE BAYAR</td>
                <td class="text-right" style="font-weight: bold;"><?= htmlspecialchars($order['payment_method']) ?> (<?= strtoupper($order['payment_status']) ?>)</td>
            </tr>
        </table>

        <div class="delivery-box">
            <strong>INFO PENGANTARAN:</strong><br>
            Alamat: <?= htmlspecialchars($order['customer_address']) ?><br>
            <?php if (!empty($order['patokan'])): ?>
                Patokan: <?= htmlspecialchars($order['patokan']) ?><br>
            <?php endif; ?>
            Kurir Delivery: <?= htmlspecialchars($order['driver_name']) ?> (<?= htmlspecialchars($order['driver_plate']) ?>)
        </div>

        <div class="footer">
            <p>*** TERIMA KASIH TELAH MEMESAN ***</p>
            <p style="margin-top: 4px;">Makanan Higienis • Hangat • Cepat</p>
            <p style="margin-top: 8px; font-size: 10px; letter-spacing: 2px;">|||| | ||||| |||| || |||||||</p>
        </div>
    </div>

    <button onclick="window.print()" class="btn-print">
        🖨️ Cetak Struk Sekarang
    </button>

</body>
</html>

