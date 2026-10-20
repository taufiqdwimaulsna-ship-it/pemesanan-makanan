<?php
// cart.php
$page_title = 'Keranjang Belanja Delivery';
$active_nav = 'cart';
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$subtotal = getCartSubtotal();
$ongkir = (int)getSetting('ongkir_default', '10000');
$total = $subtotal > 0 ? ($subtotal + $ongkir) : 0;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Step Checkout Stepper -->
    <div class="max-w-2xl mx-auto mb-10">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 dark:bg-slate-800 -z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1/3 h-1 bg-brand-600 -z-0"></div>

            <!-- Step 1: Active -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-brand-500/30">
                    1
                </div>
                <span class="text-xs font-bold text-brand-600 dark:text-brand-400 mt-2">Keranjang</span>
            </div>

            <!-- Step 2: Checkout -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold text-sm">
                    2
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-2">Checkout & Kirim</span>
            </div>

            <!-- Step 3: Tracking -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold text-sm">
                    3
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-2">Lacak Driver</span>
            </div>
        </div>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Keranjang Belanja</h1>
        <?php if (!empty($cart)): ?>
            <a href="cart_action.php?action=clear" onclick="return confirm('Yakin ingin mengosongkan semua menu di keranjang?')" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-trash-can"></i> Kosongkan Keranjang
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($cart)): ?>
    <!-- Empty Cart State -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-800 shadow-sm max-w-lg mx-auto my-12">
        <div class="w-20 h-20 rounded-full bg-orange-100 dark:bg-orange-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center mx-auto text-3xl mb-4">
            <i class="fa-solid fa-basket-shopping"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Keranjang Belanja Masih Kosong</h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-sm mx-auto leading-relaxed">
            Yuk pilih menu makanan lezat atau minuman segar favoritmu sekarang! Kami siap antar langsung ke lokasimu.
        </p>
        <div class="mt-6">
            <a href="index.php#katalog" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-brand-500/30 transition">
                <i class="fa-solid fa-utensils"></i>
                <span>Lihat Menu Restoran</span>
            </a>
        </div>
    </div>
    <?php else: ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Cart Items List (Col 8) -->
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800 overflow-hidden">
                <?php foreach ($cart as $key => $item): 
                    $item_total = (int)$item['harga'] * (int)$item['qty'];
                ?>
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <!-- Image & Title -->
                    <div class="flex items-center gap-4">
                        <img src="<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover shrink-0 border border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white"><?= htmlspecialchars($item['nama']) ?></h3>
                            <p class="text-xs font-semibold text-brand-600 dark:text-brand-400 mt-0.5"><?= rupiah($item['harga']) ?> / porsi</p>
                            
                            <?php if (!empty($item['notes'])): ?>
                            <div class="mt-1.5 inline-flex items-center gap-1.5 bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-[11px] px-2.5 py-1 rounded-md border border-amber-200 dark:border-amber-800">
                                <i class="fa-solid fa-note-sticky text-amber-600 dark:text-amber-400"></i>
                                <span><?= htmlspecialchars($item['notes']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quantity Controls & Subtotal -->
                    <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-0 border-slate-100 dark:border-slate-800">
                        <!-- Counter -->
                        <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 p-1">
                            <button type="button" onclick="updateQty('<?= $key ?>', -1)" class="w-7 h-7 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 font-bold flex items-center justify-center transition shadow-sm">
                                <i class="fa-solid fa-minus text-[10px]"></i>
                            </button>
                            <span class="w-8 text-center font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100"><?= $item['qty'] ?></span>
                            <button type="button" onclick="updateQty('<?= $key ?>', 1)" class="w-7 h-7 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 font-bold flex items-center justify-center transition shadow-sm">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <div class="text-right min-w-[90px]">
                            <p class="text-xs text-slate-400">Total</p>
                            <p class="font-black text-sm sm:text-base text-slate-900 dark:text-white"><?= rupiah($item_total) ?></p>
                        </div>

                        <!-- Remove Button -->
                        <a href="cart_action.php?action=remove&cart_key=<?= urlencode($key) ?>" onclick="return confirm('Hapus menu ini dari keranjang?')" class="text-slate-400 hover:text-rose-600 transition p-2" title="Hapus">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center justify-between">
                <a href="index.php#katalog" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Tambah Menu Lain</span>
                </a>
            </div>
        </div>

        <!-- Order Summary (Col 4) -->
        <div class="lg:col-span-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 sticky top-28 space-y-6">
                <h2 class="font-black text-lg text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Ringkasan Pesanan</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Total Makanan (<?= getCartCount() ?> item)</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200"><?= rupiah($subtotal) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <span>Ongkos Kirim Flat</span>
                            <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold px-1.5 py-0.5 rounded">Promo</span>
                        </div>
                        <span class="font-semibold text-slate-800 dark:text-slate-200"><?= rupiah($ongkir) ?></span>
                    </div>
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-between items-baseline">
                        <span class="font-bold text-base text-slate-900 dark:text-white">Total Tagihan</span>
                        <span class="font-black text-xl text-brand-600 dark:text-brand-400"><?= rupiah($total) ?></span>
                    </div>
                </div>

                <!-- Checkout Button -->
                <a href="checkout.php" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-brand-500/30 transition flex items-center justify-center gap-2 text-sm">
                    <span>Lanjut ke Pengiriman</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>

                <!-- Guarantee box -->
                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-motorcycle text-brand-600 dark:text-brand-400"></i>
                        <span>Estimasi Sampai: 25 - 35 Menit</span>
                    </div>
                    <p class="text-[11px] leading-relaxed">
                        Makanan dimasak langsung saat pesanan masuk dan diantarkan oleh kurir resmi dengan thermal bag kedap udara.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <?php endif; ?>

</div>

<script>
    function updateQty(cartKey, delta) {
        const formData = new FormData();
        formData.append('action', 'update_qty');
        formData.append('cart_key', cartKey);
        formData.append('delta', delta);

        fetch('cart_action.php', {
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
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
