<?php
// admin/index.php
$admin_page_title = 'Dashboard Analitik Restoran';
$active_menu = 'dashboard';
require_once __DIR__ . '/header.php';

// Metrik 1: Total Omset (dari order selesai)
$total_omset = $pdo->query("SELECT SUM(total_bayar) FROM orders WHERE order_status = 'selesai'")->fetchColumn() ?: 0;

// Metrik 2: Total Pesanan
$total_pesanan = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;

// Metrik 3: Pesanan Aktif (menunggu / dimasak / diantar)
$pesanan_aktif = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('menunggu', 'dimasak', 'diantar')")->fetchColumn() ?: 0;

// Metrik 4: Total Menu Tersedia
$total_menu = $pdo->query("SELECT COUNT(*) FROM menus WHERE status = 'tersedia'")->fetchColumn() ?: 0;

// Daftar Pesanan Aktif yang membutuhkan tindakan
$stmt_active = $pdo->query("SELECT * FROM orders WHERE order_status IN ('menunggu', 'dimasak', 'diantar') ORDER BY id DESC LIMIT 5");
$active_orders = $stmt_active->fetchAll();

// Menu Terlaris (Top Selling)
$stmt_top = $pdo->query("SELECT oi.nama_menu, SUM(oi.qty) as total_terjual, SUM(oi.subtotal) as total_pendapatan 
                         FROM order_items oi 
                         JOIN orders o ON oi.order_id = o.id 
                         WHERE o.order_status = 'selesai' 
                         GROUP BY oi.nama_menu 
                         ORDER BY total_terjual DESC LIMIT 4");
$top_menus = $stmt_top->fetchAll();

// Data status untuk Chart ringkasan
$status_counts = [
    'menunggu' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'menunggu'")->fetchColumn(),
    'dimasak' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'dimasak'")->fetchColumn(),
    'diantar' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'diantar'")->fetchColumn(),
    'selesai' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'selesai'")->fetchColumn(),
];
?>

<div class="space-y-8">
    
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-black dark:to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-800">
        <div class="space-y-1">
            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Kitchen & Delivery Control Center</span>
            <h1 class="text-2xl sm:text-3xl font-black">Selamat Datang, <?= htmlspecialchars($current_admin['nama']) ?>! 👋</h1>
            <p class="text-xs sm:text-sm text-slate-300">Pantau pesanan masuk secara real-time, percepat proses dapur, dan pastikan pengantaran tepat waktu.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="pesanan.php" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl shadow-lg shadow-brand-500/25 transition flex items-center gap-2">
                <i class="fa-solid fa-list-check"></i>
                <span>Kelola Semua Pesanan</span>
            </a>
        </div>
    </div>

    <!-- 4 Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Stat 1: Total Omset -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1"><?= rupiah($total_omset) ?></h3>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> Pesanan lunas/selesai
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-rupiah-sign"></i>
            </div>
        </div>

        <!-- Stat 2: Pesanan Aktif -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Aktif</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1"><?= $pesanan_aktif ?> Antrian</h3>
                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-fire"></i> Sedang diproses & diantar
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Stat 3: Total Pesanan Masuk -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pesanan</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1"><?= $total_pesanan ?> Order</h3>
                <span class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-truck-fast"></i> Sejak sistem aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        </div>

        <!-- Stat 4: Total Menu -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menu Restoran</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1"><?= $total_menu ?> Pilihan</h3>
                <span class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-circle-check"></i> Siap dipesan online
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-utensils"></i>
            </div>
        </div>

    </div>

    <!-- Active Orders Live Queue & Chart (Col 8 & 4) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Live Orders Queue (Col 8) -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-600 animate-ping"></span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Antrian Pesanan Delivery Aktif</h2>
                </div>
                <a href="pesanan.php" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700">Lihat Semua &rarr;</a>
            </div>

            <?php if (empty($active_orders)): ?>
            <div class="text-center py-12">
                <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto text-xl mb-3">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-700 dark:text-slate-200">Tidak ada antrian pesanan aktif saat ini.</h4>
                <p class="text-xs text-slate-400 mt-0.5">Semua pesanan telah selesai dikirimkan ke pelanggan.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 text-[10px]">
                            <th class="py-3 px-3">No Invoice</th>
                            <th class="py-3 px-3">Pelanggan & Lokasi</th>
                            <th class="py-3 px-3">Total</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <?php foreach ($active_orders as $ao): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3.5 px-3">
                                <span class="font-mono font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($ao['no_invoice']) ?></span>
                                <span class="block text-[10px] text-slate-400"><?= date('H:i', strtotime($ao['created_at'])) ?> WIB</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-slate-900 dark:text-white block"><?= htmlspecialchars($ao['customer_name']) ?></span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 max-w-[180px]"><?= htmlspecialchars($ao['customer_address']) ?></span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-slate-900 dark:text-white"><?= rupiah($ao['total_bayar']) ?></span>
                                <span class="block text-[10px] text-slate-400"><?= htmlspecialchars($ao['payment_method']) ?></span>
                            </td>
                            <td class="py-3.5 px-3">
                                <?= getOrderStatusBadge($ao['order_status']) ?>
                            </td>
                            <td class="py-3.5 px-3 text-right space-x-1 whitespace-nowrap">
                                <?php if ($ao['order_status'] == 'menunggu'): ?>
                                <button onclick="quickUpdate('<?= $ao['no_invoice'] ?>', 'dimasak')" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-lg shadow-sm">
                                    <i class="fa-solid fa-fire mr-1"></i> Masak
                                </button>
                                <?php elseif ($ao['order_status'] == 'dimasak'): ?>
                                <button onclick="quickUpdate('<?= $ao['no_invoice'] ?>', 'diantar')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-lg shadow-sm">
                                    <i class="fa-solid fa-motorcycle mr-1"></i> Antar
                                </button>
                                <?php elseif ($ao['order_status'] == 'diantar'): ?>
                                <button onclick="quickUpdate('<?= $ao['no_invoice'] ?>', 'selesai')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-lg shadow-sm">
                                    <i class="fa-solid fa-check mr-1"></i> Selesai
                                </button>
                                <?php endif; ?>

                                <a href="cetak_nota.php?inv=<?= urlencode($ao['no_invoice']) ?>" target="_blank" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg inline-block text-[11px]" title="Cetak Nota Kasir">
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

        <!-- Order Breakdown Chart (Col 4) -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-base font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Distribusi Status Pesanan</h2>
            <div class="h-56 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2 text-xs">
                <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-center">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] block">Menunggu:</span>
                    <span class="font-extrabold text-amber-700 dark:text-amber-400 text-sm"><?= $status_counts['menunggu'] ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800 text-center">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] block">Dimasak:</span>
                    <span class="font-extrabold text-orange-700 dark:text-orange-400 text-sm"><?= $status_counts['dimasak'] ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-center">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] block">Diantar:</span>
                    <span class="font-extrabold text-blue-700 dark:text-blue-400 text-sm"><?= $status_counts['diantar'] ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] block">Selesai:</span>
                    <span class="font-extrabold text-emerald-700 dark:text-emerald-400 text-sm"><?= $status_counts['selesai'] ?></span>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    // Quick Update Order Status
    function quickUpdate(invoice, status) {
        const formData = new FormData();
        formData.append('invoice', invoice);
        formData.append('status', status);

        fetch('../update_status_demo.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }

    // Chart.js Status
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Menunggu', 'Dimasak', 'Diantar', 'Selesai'],
            datasets: [{
                data: [
                    <?= $status_counts['menunggu'] ?>,
                    <?= $status_counts['dimasak'] ?>,
                    <?= $status_counts['diantar'] ?>,
                    <?= $status_counts['selesai'] ?>
                ],
                backgroundColor: ['#f59e0b', '#ea580c', '#2563eb', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 10 } }
                }
            },
            cutout: '70%'
        }
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
