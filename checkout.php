<?php
// checkout.php
$page_title = 'Checkout & Alamat Pengiriman';
$active_nav = 'cart';
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$subtotal = getCartSubtotal();
$ongkir = (int)getSetting('ongkir_default', '10000');
$total = $subtotal + $ongkir;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Step Checkout Stepper -->
    <div class="max-w-2xl mx-auto mb-10">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 dark:bg-slate-800 -z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-2/3 h-1 bg-brand-600 -z-0"></div>

            <!-- Step 1: Done -->
            <a href="cart.php" class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
                    <i class="fa-solid fa-check text-xs"></i>
                </div>
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400 mt-2">Keranjang</span>
            </a>

            <!-- Step 2: Active -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-brand-500/30">
                    2
                </div>
                <span class="text-xs font-bold text-brand-600 dark:text-brand-400 mt-2">Checkout & Kirim</span>
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

    <form action="order_process.php" method="POST" id="checkout-form">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Checkout Information (Col 8) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Section 1: Customer Contact -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h2 class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white">Informasi Pemesan</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="customer_name" required placeholder="Contoh: Rian Pratama" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">ID +62</span>
                                <input type="tel" name="customer_phone" required placeholder="81234567890" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl pl-16 pr-4 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-brand-500 transition">
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">Kurir akan menghubungi nomor ini saat pesanan tiba.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Delivery Location -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h2 class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white">Alamat Tujuan Pengantaran</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Alamat Lengkap (Jalan, Nomor Rumah/Gedung/Apartemen, Lantai) <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="customer_address" rows="3" required placeholder="Contoh: Jl. Kemang Timur No. 12B, Apartemen Kemang View Tower A Lantai 5 No 502" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl p-3.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-brand-500 transition"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Patokan Lokasi (Opsional)
                                </label>
                                <input type="text" name="patokan" placeholder="Contoh: Pagar hitam samping Alfamart" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-brand-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Catatan untuk Driver (Opsional)
                                </label>
                                <input type="text" name="delivery_notes" placeholder="Contoh: Titip satpam lobby / bel pintu berdering" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-brand-500 transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Payment Method -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <h2 class="font-extrabold text-base sm:text-lg text-slate-900 dark:text-white">Metode Pembayaran</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- COD Option -->
                        <label class="relative flex flex-col p-4 bg-slate-50 dark:bg-slate-800 hover:bg-orange-50/50 dark:hover:bg-slate-750 border-2 border-slate-200 dark:border-slate-700 has-[:checked]:border-brand-500 has-[:checked]:bg-orange-50 dark:has-[:checked]:bg-brand-950/30 rounded-2xl cursor-pointer transition">
                            <input type="radio" name="payment_method" value="COD" checked class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fa-solid fa-money-bill-wave text-xl text-emerald-600 dark:text-emerald-400"></i>
                                <span class="w-4 h-4 rounded-full border-2 border-slate-300 dark:border-slate-600 peer-checked:border-brand-600"></span>
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100">COD (Tunai)</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Bayar saat makanan tiba di tempat</span>
                        </label>

                        <!-- QRIS Option -->
                        <label class="relative flex flex-col p-4 bg-slate-50 dark:bg-slate-800 hover:bg-orange-50/50 dark:hover:bg-slate-750 border-2 border-slate-200 dark:border-slate-700 has-[:checked]:border-brand-500 has-[:checked]:bg-orange-50 dark:has-[:checked]:bg-brand-950/30 rounded-2xl cursor-pointer transition">
                            <input type="radio" name="payment_method" value="QRIS" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fa-solid fa-qrcode text-xl text-indigo-600 dark:text-indigo-400"></i>
                                <span class="text-[10px] bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 font-bold px-1.5 py-0.5 rounded">Instan</span>
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100">QRIS All Payment</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Scan GoPay, OVO, Dana, BCA, dll</span>
                        </label>

                        <!-- Transfer Bank Option -->
                        <label class="relative flex flex-col p-4 bg-slate-50 dark:bg-slate-800 hover:bg-orange-50/50 dark:hover:bg-slate-750 border-2 border-slate-200 dark:border-slate-700 has-[:checked]:border-brand-500 has-[:checked]:bg-orange-50 dark:has-[:checked]:bg-brand-950/30 rounded-2xl cursor-pointer transition">
                            <input type="radio" name="payment_method" value="TRANSFER_BANK" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fa-solid fa-building-columns text-xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100">Transfer Bank</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">BCA / Mandiri / BRI Virtual</span>
                        </label>
                    </div>

                    <!-- Alert / Catatan Tambahan Pembayaran -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200/60 dark:border-slate-700/80 text-xs text-slate-600 dark:text-slate-400 flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved text-emerald-500 text-base"></i>
                        <span>Transaksi 100% aman dan langsung masuk ke sistem antrian dapur kami.</span>
                    </div>
                </div>

            </div>

            <!-- Right: Order Review & Submit (Col 4) -->
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 sticky top-28 space-y-5">
                    <h2 class="font-black text-lg text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Daftar Makanan</h2>

                    <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 pr-1 space-y-2">
                        <?php foreach ($cart as $item): ?>
                        <div class="pt-2 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="font-bold text-brand-600 dark:text-brand-400"><?= $item['qty'] ?>x</span>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-100 line-clamp-1"><?= htmlspecialchars($item['nama']) ?></p>
                                    <?php if (!empty($item['notes'])): ?>
                                        <p class="text-[10px] text-amber-700 dark:text-amber-400 italic">"<?= htmlspecialchars($item['notes']) ?>"</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="font-bold text-slate-900 dark:text-white shrink-0"><?= rupiah((int)$item['harga'] * (int)$item['qty']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal Menu</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200"><?= rupiah($subtotal) ?></span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Ongkos Kirim Delivery</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200"><?= rupiah($ongkir) ?></span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex justify-between items-baseline text-sm">
                            <span class="font-bold text-slate-900 dark:text-white">Total Pembayaran</span>
                            <span class="font-black text-xl text-brand-600 dark:text-brand-400"><?= rupiah($total) ?></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 active:scale-98 text-white font-black py-4 px-4 rounded-xl shadow-lg shadow-brand-500/30 transition flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Pesan & Antar Sekarang</span>
                    </button>

                    <p class="text-[11px] text-center text-slate-400 dark:text-slate-500">
                        Dengan memesan, Anda setuju dengan konfirmasi estimasi waktu pengantaran 25-35 menit.
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
