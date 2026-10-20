<?php
// riwayat.php
$page_title = 'Cek Status & Riwayat Pesanan';
$active_nav = 'riwayat';
require_once __DIR__ . '/includes/header.php';

$search_query = sanitize($_GET['q'] ?? '');
$orders = [];

if (!empty($search_query)) {
    // Normalisasi no hp jika user input format no HP
    $clean_phone = preg_replace('/[^0-9]/', '', $search_query);
    
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE no_invoice = ? OR customer_phone LIKE ? OR customer_phone LIKE ? ORDER BY id DESC LIMIT 10");
    $stmt->execute([$search_query, "%{$clean_phone}%", "%{$search_query}%"]);
    $orders = $stmt->fetchAll();
} else {
    // Default tampilkan 5 pesanan terbaru sistem untuk kemudahan demo
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 6");
    $orders = $stmt->fetchAll();
}
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Title & Search Header -->
    <div class="text-center max-w-xl mx-auto mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-xs font-bold mb-3">
            <i class="fa-solid fa-truck-fast"></i>
            <span>Pelacakan Pesanan Real-time</span>
        </div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Cek Status Pesanan Delivery</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2">
            Masukkan Nomor Invoice (contoh: <span class="font-mono font-bold text-slate-700 dark:text-slate-300">INV-20261020-001</span>) atau Nomor WhatsApp saat pemesanan.
        </p>

        <!-- Search Form -->
        <form action="riwayat.php" method="GET" class="mt-6">
            <div class="flex items-center bg-white dark:bg-slate-900 p-2 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 focus-within:border-brand-500 transition">
                <i class="fa-solid fa-magnifying-glass text-slate-400 ml-3 mr-2"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" required placeholder="Ketik No Invoice atau No WhatsApp..." class="w-full text-xs sm:text-sm bg-transparent outline-none text-slate-800 dark:text-white placeholder-slate-400">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl transition flex items-center gap-1.5 shrink-0 shadow-md">
                    <span>Cari</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Order List -->
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-2">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                <?= !empty($search_query) ? 'Hasil Pencarian: "' . htmlspecialchars($search_query) . '"' : 'Pesanan Terbaru (Siap Lacak)' ?>
            </h2>
            <?php if (!empty($search_query)): ?>
                <a href="riwayat.php" class="text-xs text-brand-600 dark:text-brand-400 font-bold hover:underline">Tampilkan Semua</a>
            <?php endif; ?>
        </div>

        <?php if (empty($orders)): ?>
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800 dark:text-slate-200">Pesanan Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pastikan nomor invoice atau nomor telepon yang Anda masukkan sudah benar.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $ord): 
                // Hitung jumlah item
                $cnt_stmt = $pdo->prepare("SELECT COUNT(*) as total_menu, SUM(qty) as total_pcs FROM order_items WHERE order_id = ?");
                $cnt_stmt->execute([$ord['id']]);
                $count_res = $cnt_stmt->fetch();
            ?>
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-0.5 rounded">
                            <?= htmlspecialchars($ord['no_invoice']) ?>
                        </span>
                        <?= getOrderStatusBadge($ord['order_status']) ?>
                        <?= getPaymentStatusBadge($ord['payment_status']) ?>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400">
                        <span class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($ord['customer_name']) ?></span> • <?= $count_res['total_pcs'] ?? 1 ?> porsi makanan • <span class="font-bold text-slate-900 dark:text-slate-100"><?= rupiah($ord['total_bayar']) ?></span>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1">
                        <i class="fa-regular fa-clock"></i>
                        <span><?= date('d M Y, H:i', strtotime($ord['created_at'])) ?> WIB</span>
                        <span>•</span>
                        <span>Driver: <?= htmlspecialchars($ord['driver_name']) ?> (<?= htmlspecialchars($ord['driver_plate']) ?>)</span>
                    </p>
                </div>

                <div class="w-full sm:w-auto flex items-center gap-2">
                    <a href="tracking.php?inv=<?= urlencode($ord['no_invoice']) ?>" class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Lacak Pesanan</span>
                    </a>
                    <a href="admin/cetak_nota.php?inv=<?= urlencode($ord['no_invoice']) ?>" target="_blank" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs transition" title="Cetak Nota">
                        <i class="fa-solid fa-print"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
