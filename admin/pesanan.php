<?php
// admin/pesanan.php
$admin_page_title = 'Kelola Semua Pesanan Delivery';
$active_menu = 'pesanan';
require_once __DIR__ . '/header.php';

// Filter
$status_filter = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['q'] ?? '');

$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND order_status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (no_invoice LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="space-y-6">
    
    <!-- Title & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pesanan Delivery</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola antrian masak, assign kurir, dan monitor status pengantaran.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.location.reload()" class="bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Refresh Antrian</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Status Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 text-xs">
            <a href="pesanan.php" class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap <?= empty($status_filter) ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Semua
            </a>
            <a href="pesanan.php?status=menunggu" class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap <?= $status_filter == 'menunggu' ? 'bg-amber-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Menunggu
            </a>
            <a href="pesanan.php?status=dimasak" class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap <?= $status_filter == 'dimasak' ? 'bg-orange-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Dimasak
            </a>
            <a href="pesanan.php?status=diantar" class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap <?= $status_filter == 'diantar' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Diantar
            </a>
            <a href="pesanan.php?status=selesai" class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap <?= $status_filter == 'selesai' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Selesai
            </a>
        </div>

        <!-- Search Input -->
        <form action="pesanan.php" method="GET" class="w-full md:w-72">
            <?php if (!empty($status_filter)): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
            <?php endif; ?>
            <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus-within:bg-white focus-within:border-brand-500 transition">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari invoice/nama..." class="w-full text-xs bg-transparent outline-none">
            </div>
        </form>

    </div>

    <!-- Orders Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <?php if (empty($orders)): ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto text-2xl mb-3">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="font-bold text-sm text-slate-700">Tidak Ada Pesanan Ditemukan</h3>
            <p class="text-xs text-slate-400 mt-1">Belum ada pesanan dengan kriteria filter saat ini.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3.5 px-4">Invoice & Waktu</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Alamat Pengantaran</th>
                        <th class="py-3.5 px-4">Menu & Total</th>
                        <th class="py-3.5 px-4">Kurir Driver</th>
                        <th class="py-3.5 px-4">Status Delivery</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($orders as $ord): 
                        // Ambil items
                        $st = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                        $st->execute([$ord['id']]);
                        $ord_items = $st->fetchAll();
                    ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        
                        <!-- Invoice -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="font-mono font-bold text-slate-900 block"><?= htmlspecialchars($ord['no_invoice']) ?></span>
                            <span class="text-[10px] text-slate-400"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                            <div class="mt-1">
                                <?= getPaymentStatusBadge($ord['payment_status']) ?>
                            </div>
                        </td>

                        <!-- Customer -->
                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-900 block"><?= htmlspecialchars($ord['customer_name']) ?></span>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ord['customer_phone']) ?>" target="_blank" class="text-[11px] text-emerald-600 hover:underline flex items-center gap-1 font-mono mt-0.5">
                                <i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($ord['customer_phone']) ?>
                            </a>
                        </td>

                        <!-- Address -->
                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-slate-800 line-clamp-2"><?= htmlspecialchars($ord['customer_address']) ?></p>
                            <?php if (!empty($ord['patokan'])): ?>
                                <span class="text-[10px] text-slate-400 italic block mt-0.5">Patokan: <?= htmlspecialchars($ord['patokan']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($ord['delivery_notes'])): ?>
                                <span class="text-[10px] text-amber-700 font-semibold block mt-0.5"><i class="fa-solid fa-note-sticky mr-1"></i><?= htmlspecialchars($ord['delivery_notes']) ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Items & Total -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="font-extrabold text-sm text-slate-900 block"><?= rupiah($ord['total_bayar']) ?></span>
                            <span class="text-[11px] text-slate-500"><?= count($ord_items) ?> item menu • <?= htmlspecialchars($ord['payment_method']) ?></span>
                        </td>

                        <!-- Driver -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block"><?= htmlspecialchars($ord['driver_name']) ?></span>
                            <span class="text-[10px] font-mono text-slate-400"><?= htmlspecialchars($ord['driver_plate']) ?></span>
                        </td>

                        <!-- Status Selector -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            <select onchange="updateOrderStatus('<?= $ord['no_invoice'] ?>', this.value)" class="text-xs font-bold rounded-xl px-2.5 py-1.5 border border-slate-200 focus:outline-none cursor-pointer <?= $ord['order_status'] == 'menunggu' ? 'bg-amber-50 text-amber-800 border-amber-300' : ($ord['order_status'] == 'dimasak' ? 'bg-orange-50 text-orange-800 border-orange-300' : ($ord['order_status'] == 'diantar' ? 'bg-blue-50 text-blue-800 border-blue-300' : ($ord['order_status'] == 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-slate-50 text-slate-800'))) ?>">
                                <option value="menunggu" <?= $ord['order_status'] == 'menunggu' ? 'selected' : '' ?>>⏳ Menunggu</option>
                                <option value="dimasak" <?= $ord['order_status'] == 'dimasak' ? 'selected' : '' ?>>🍳 Dimasak</option>
                                <option value="diantar" <?= $ord['order_status'] == 'diantar' ? 'selected' : '' ?>>🛵 Diantar</option>
                                <option value="selesai" <?= $ord['order_status'] == 'selesai' ? 'selected' : '' ?>>✅ Selesai</option>
                                <option value="dibatalkan" <?= $ord['order_status'] == 'dibatalkan' ? 'selected' : '' ?>>❌ Batal</option>
                            </select>
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="../tracking.php?inv=<?= urlencode($ord['no_invoice']) ?>" target="_blank" class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl inline-block" title="Lihat Live Tracking">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </a>
                            <a href="cetak_nota.php?inv=<?= urlencode($ord['no_invoice']) ?>" target="_blank" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl inline-block" title="Cetak Nota">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
    function updateOrderStatus(invoice, newStatus) {
        const formData = new FormData();
        formData.append('invoice', invoice);
        formData.append('status', newStatus);

        fetch('../update_status_demo.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Status pesanan ' + invoice + ' diubah ke: ' + newStatus.toUpperCase(),
                    showConfirmButton: false,
                    timer: 2000
                });
                setTimeout(() => window.location.reload(), 800);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/footer.php'; ?>

