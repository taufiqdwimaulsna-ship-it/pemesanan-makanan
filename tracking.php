<?php
// tracking.php
$page_title = 'Pelacakan Pengantaran Makanan';
$active_nav = 'riwayat';
require_once __DIR__ . '/includes/header.php';

$no_invoice = sanitize($_GET['inv'] ?? '');

if (empty($no_invoice)) {
    header('Location: riwayat.php');
    exit;
}

// Ambil data order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE no_invoice = ?");
$stmt->execute([$no_invoice]);
$order = $stmt->fetch();

if (!$order) {
    ?>
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <div class="w-20 h-20 bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto text-3xl mb-4">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Pesanan Tidak Ditemukan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Nomor invoice <strong><?= htmlspecialchars($no_invoice) ?></strong> tidak terdaftar dalam sistem.</p>
        <div class="mt-6">
            <a href="riwayat.php" class="bg-brand-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md">Kembali Cek Pesanan</a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Ambil item order
$item_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$item_stmt->execute([$order['id']]);
$items = $item_stmt->fetchAll();

$current_status = $order['order_status'];

// Indeks tahapan status:
// 1 = menunggu, 2 = dimasak, 3 = diantar, 4 = selesai
$status_steps = [
    'menunggu' => 1,
    'dimasak' => 2,
    'diantar' => 3,
    'selesai' => 4,
    'dibatalkan' => 0
];
$current_step = $status_steps[$current_status] ?? 1;

$resto_phone = getSetting('no_wa_resto', '628123456789');
$is_new = isset($_GET['new']);
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Notifikasi SweetAlert jika baru selesai checkout -->
    <?php if ($is_new): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Pesanan Berhasil Dibuat!',
                text: 'Pesanan Anda dengan nomor <?= $no_invoice ?> telah kami terima dan segera disiapkan.',
                confirmButtonColor: '#ea580c',
                confirmButtonText: 'Pantau Pengantaran'
            });
        });
    </script>
    <?php endif; ?>

    <!-- Header Invoice Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-extrabold text-slate-400 tracking-wider">No. Invoice</span>
                    <span class="text-xs px-2 py-0.5 rounded font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200"><?= htmlspecialchars($order['no_invoice']) ?></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">Status Pengantaran</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Waktu Pesan: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WIB</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:self-center">
                <?= getOrderStatusBadge($order['order_status']) ?>
                <?= getPaymentStatusBadge($order['payment_status']) ?>
            </div>
        </div>

        <!-- SIMULATOR TOOLBAR: SANGAT UNGGUL UNTUK PRESENTASI & SIDANG TUGAS -->
        <div class="mt-6 p-4 rounded-2xl bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 dark:from-slate-800/90 dark:via-slate-800 dark:to-slate-800/90 border border-brand-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fa-solid fa-gamepad"></i>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">Mode Simulasi Presentasi Pengantaran</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Ubah status pesanan secara real-time untuk demonstrasi pengujian:</p>
                    </div>
                </div>

                <!-- Simulation Quick Status Buttons -->
                <div class="flex flex-wrap items-center gap-1.5 w-full md:w-auto">
                    <button onclick="simulasiStatus('menunggu')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold <?= $current_status == 'menunggu' ? 'bg-amber-600 text-white shadow' : 'bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600' ?>">
                        1. Tunggu
                    </button>
                    <button onclick="simulasiStatus('dimasak')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold <?= $current_status == 'dimasak' ? 'bg-orange-600 text-white shadow' : 'bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600' ?>">
                        2. Masak
                    </button>
                    <button onclick="simulasiStatus('diantar')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold <?= $current_status == 'diantar' ? 'bg-blue-600 text-white shadow' : 'bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600' ?>">
                        3. Antar (Kurir)
                    </button>
                    <button onclick="simulasiStatus('selesai')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold <?= $current_status == 'selesai' ? 'bg-emerald-600 text-white shadow' : 'bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600' ?>">
                        4. Selesai
                    </button>
                    <button onclick="autoPlaySimulation()" id="btn-autoplay" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-900 dark:bg-brand-600 text-amber-300 dark:text-white hover:bg-slate-800 dark:hover:bg-brand-700 transition shadow">
                        <i class="fa-solid fa-play text-[10px] mr-1"></i> Auto Demo
                    </button>
                </div>
            </div>
        </div>

        <!-- VISUAL STATUS STEPPER TIMELINE -->
        <div class="mt-10 py-4">
            <div class="relative">
                <!-- Progress Line Background -->
                <div class="hidden sm:block absolute left-10 top-7 right-10 h-1.5 bg-slate-100 dark:bg-slate-800 -z-0 rounded-full"></div>
                <!-- Active Progress Line -->
                <div class="hidden sm:block absolute left-10 top-7 h-1.5 bg-gradient-to-r from-brand-600 to-emerald-500 -z-0 rounded-full transition-all duration-700" 
                     style="width: <?= $current_step == 1 ? '10%' : ($current_step == 2 ? '40%' : ($current_step == 3 ? '70%' : ($current_step >= 4 ? '92%' : '0%'))) ?>;"></div>

                <!-- 4 Steps Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative z-10">
                    
                    <!-- Step 1: Menunggu -->
                    <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl transition-all duration-300 shadow-md <?= $current_step >= 1 ? 'bg-amber-500 text-white shadow-amber-500/30 ring-4 ring-amber-100 dark:ring-amber-950/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' ?>">
                            <i class="fa-solid fa-bell-concierge"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">1. Pesanan Diterima</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Menunggu antrian dapur</p>
                        </div>
                    </div>

                    <!-- Step 2: Dimasak -->
                    <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl transition-all duration-300 shadow-md <?= $current_step >= 2 ? 'bg-orange-600 text-white shadow-orange-600/30 ring-4 ring-orange-100 dark:ring-orange-950/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' ?>">
                            <i class="fa-solid fa-fire-burner <?= $current_step == 2 ? 'animate-bounce' : '' ?>"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">2. Sedang Dimasak</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Chef meracik hidangan</p>
                        </div>
                    </div>

                    <!-- Step 3: Diantar Kurir -->
                    <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl transition-all duration-300 shadow-md <?= $current_step >= 3 ? 'bg-blue-600 text-white shadow-blue-600/30 ring-4 ring-blue-100 dark:ring-blue-950/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' ?>">
                            <i class="fa-solid fa-motorcycle <?= $current_step == 3 ? 'animate-pulse' : '' ?>"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">3. Sedang Diantar</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kurir meluncur ke lokasi</p>
                        </div>
                    </div>

                    <!-- Step 4: Selesai -->
                    <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl transition-all duration-300 shadow-md <?= $current_step >= 4 ? 'bg-emerald-600 text-white shadow-emerald-600/30 ring-4 ring-emerald-100 dark:ring-emerald-950/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' ?>">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">4. Makanan Tiba</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Selamat menikmati!</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- LIVE DELIVERY SIMULATION CARD -->
        <?php if ($current_status == 'diantar'): ?>
        <div class="mt-8 p-6 rounded-2xl bg-gradient-to-r from-blue-900 to-indigo-900 dark:from-blue-950 dark:to-indigo-950 text-white shadow-xl relative overflow-hidden border border-blue-800">
            <!-- Background Map Grid Graphic -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Driver Info Left -->
                <div class="flex items-center gap-4 text-center md:text-left">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl border border-white/30 text-amber-300 shadow-inner">
                            🛵
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-400 border-2 border-blue-900 rounded-full flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        </span>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/30 text-blue-200 text-[10px] font-bold uppercase tracking-wider mb-1">
                            Kurir Delivery Anda
                        </div>
                        <h3 class="text-lg font-extrabold"><?= htmlspecialchars($order['driver_name']) ?></h3>
                        <p class="text-xs text-blue-200">Plat Motor: <strong class="text-white"><?= htmlspecialchars($order['driver_plate']) ?></strong></p>
                    </div>
                </div>

                <!-- Live ETA Timer -->
                <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20 text-center">
                    <span class="text-[11px] text-blue-200 block uppercase tracking-wider font-semibold">Estimasi Tiba di Alamat</span>
                    <div class="text-2xl font-black text-amber-300 mt-0.5">
                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> <span id="eta-countdown"><?= $order['estimasi_waktu'] ?></span> Menit
                    </div>
                    <span class="text-[10px] text-slate-300">Kurir sedang dalam perjalanan</span>
                </div>

                <!-- Call Driver Button -->
                <div>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order['driver_phone']) ?>?text=Halo%20Mas%20<?= urlencode($order['driver_name']) ?>%2C%20saya%20pemesan%20makanan%20invoice%20<?= urlencode($order['no_invoice']) ?>" target="_blank" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl shadow-lg shadow-emerald-500/30 transition">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        <span>Hubungi Driver</span>
                    </a>
                </div>
            </div>
        </div>
        <?php elseif ($current_status == 'dimasak'): ?>
        <div class="mt-8 p-6 rounded-2xl bg-gradient-to-r from-orange-600 to-amber-600 dark:from-orange-700 dark:to-amber-700 text-white shadow-xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl text-white">
                    <i class="fa-solid fa-fire-flame-curved animate-pulse"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg">Pesanan Sedang Dimasak di Dapur</h3>
                    <p class="text-xs text-orange-100 mt-0.5">Chef kami sedang memasak pesanan Anda dengan higienis dan bahan segar pilihan.</p>
                </div>
            </div>
            <div class="hidden sm:block text-right">
                <span class="text-xs font-bold text-amber-200">Estimasi Masak</span>
                <p class="text-xl font-black">~10-15 Menit</p>
            </div>
        </div>
        <?php elseif ($current_status == 'selesai'): ?>
        <div class="mt-8 p-6 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 dark:from-emerald-700 dark:to-teal-800 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl">
                    🎉
                </div>
                <div>
                    <h3 class="font-black text-lg">Pesanan Telah Tiba & Selesai!</h3>
                    <p class="text-xs text-emerald-100 mt-0.5">Terima kasih telah memesan di restoran kami. Selamat menikmati hidangan lezat!</p>
                </div>
            </div>
            <a href="index.php" class="bg-white text-emerald-800 hover:bg-emerald-50 font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition">
                Pesan Makanan Lagi
            </a>
        </div>
        <?php endif; ?>

    </div>

    <!-- Details Grid (Col 12 -> 7 & 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Order Items & Notes (Col 7) -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h3 class="font-black text-base text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <span>Menu yang Dipesan</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400"><?= count($items) ?> Menu</span>
                </h3>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($items as $item): ?>
                    <div class="py-3.5 flex items-start justify-between gap-3 text-sm">
                        <div class="flex items-start gap-3">
                            <span class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-brand-600 dark:text-brand-400 font-extrabold text-xs flex items-center justify-center shrink-0">
                                <?= $item['qty'] ?>x
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($item['nama_menu']) ?></h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400"><?= rupiah($item['harga_satuan']) ?> / porsi</p>
                                <?php if (!empty($item['catatan'])): ?>
                                    <p class="text-[11px] text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-800 inline-block mt-1">
                                        <i class="fa-solid fa-pen-nib text-[10px] mr-1"></i><?= htmlspecialchars($item['catatan']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white shrink-0"><?= rupiah($item['subtotal']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Subtotal Menu</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200"><?= rupiah($order['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Biaya Pengantaran Delivery</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200"><?= rupiah($order['ongkir']) ?></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex justify-between items-baseline text-sm">
                        <span class="font-black text-slate-900 dark:text-white">Total Pembayaran</span>
                        <span class="font-black text-lg text-brand-600 dark:text-brand-400"><?= rupiah($order['total_bayar']) ?></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Delivery Destination & Support (Col 5) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Address Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                <h3 class="font-black text-base text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-brand-600 dark:text-brand-400"></i>
                    <span>Tujuan Pengantaran</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Penerima:</span>
                        <p class="font-bold text-slate-800 dark:text-slate-100 text-sm"><?= htmlspecialchars($order['customer_name']) ?></p>
                        <p class="text-slate-500 dark:text-slate-400 font-mono">+<?= htmlspecialchars($order['customer_phone']) ?></p>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Pengiriman:</span>
                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-medium"><?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
                    </div>

                    <?php if (!empty($order['patokan'])): ?>
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400 block text-[10px] font-bold">Patokan:</span>
                        <p class="text-slate-800 dark:text-slate-200 font-medium"><?= htmlspecialchars($order['patokan']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($order['delivery_notes'])): ?>
                    <div class="p-2.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-900 text-blue-900 dark:text-blue-300">
                        <span class="text-blue-700 dark:text-blue-400 block text-[10px] font-bold">Catatan untuk Driver:</span>
                        <p class="font-medium"><?= htmlspecialchars($order['delivery_notes']) ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Metode Pembayaran:</span>
                        <div class="flex items-center justify-between mt-1">
                            <span class="font-bold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($order['payment_method']) ?></span>
                            <?= getPaymentStatusBadge($order['payment_status']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Support -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-3">
                <a href="https://wa.me/<?= $resto_phone ?>?text=Halo%20Resto%2C%20mau%20konfirmasi%20pesanan%20dengan%20invoice%20<?= urlencode($order['no_invoice']) ?>" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm transition">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>WhatsApp Restoran</span>
                </a>

                <a href="admin/cetak_nota.php?inv=<?= urlencode($order['no_invoice']) ?>" target="_blank" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Struk / Bukti Pesanan</span>
                </a>
            </div>

        </div>

    </div>

</div>

<!-- JavaScript Simulasi Delivery Cepat -->
<script>
    function simulasiStatus(statusBaru) {
        const formData = new FormData();
        formData.append('invoice', '<?= $no_invoice ?>');
        formData.append('status', statusBaru);

        fetch('update_status_demo.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Status pengantaran diubah ke: ' + statusBaru.toUpperCase());
                setTimeout(() => {
                    window.location.reload();
                }, 400);
            }
        });
    }

    // Auto Play Demo mode (looping through steps for live demonstration)
    let autoInterval = null;
    function autoPlaySimulation() {
        const btn = document.getElementById('btn-autoplay');
        if (autoInterval) {
            clearInterval(autoInterval);
            autoInterval = null;
            btn.innerHTML = '<i class="fa-solid fa-play text-[10px] mr-1"></i> Auto Demo';
            showNotification('info', 'Simulasi otomatis dihentikan');
            return;
        }

        btn.innerHTML = '<i class="fa-solid fa-pause text-[10px] mr-1"></i> Berjalan...';
        showNotification('info', 'Simulasi otomatis dimulai! Status akan berganti tiap 3 detik.');

        const steps = ['menunggu', 'dimasak', 'diantar', 'selesai'];
        let idx = steps.indexOf('<?= $current_status ?>');
        if (idx === -1 || idx >= steps.length - 1) idx = -1;

        idx++;
        simulasiStatus(steps[idx]);
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
