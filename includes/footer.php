<?php
// includes/footer.php
$wa_resto = getSetting('no_wa_resto', '628123456789');
$resto_name = getSetting('nama_resto', 'Dapur Lezat Express');
$cart_count = getCartCount();
$cart_subtotal = getCartSubtotal();
?>
    </main>

    <!-- Floating Sticky Cart Bar -->
    <?php if ($cart_count > 0 && !in_array(basename($_SERVER['PHP_SELF']), ['cart.php', 'checkout.php', 'tracking.php'])): ?>
    <div id="floating-cart-bar" class="fixed bottom-20 md:bottom-6 left-0 right-0 z-40 px-4 sm:px-6 pointer-events-none transition-all duration-300">
        <div class="max-w-3xl mx-auto bg-slate-900/95 dark:bg-slate-800/95 backdrop-blur-md text-white rounded-2xl shadow-2xl p-4 flex items-center justify-between pointer-events-auto border border-slate-700 dark:border-slate-600">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-brand-600 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm sm:text-base"><?= $cart_count ?> Item di Keranjang</span>
                    </div>
                    <p class="text-xs text-orange-400 font-semibold"><?= rupiah($cart_subtotal) ?> (Belum Ongkir)</p>
                </div>
            </div>
            <a href="cart.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-amber-500 hover:from-brand-500 hover:to-amber-400 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 transition hover:scale-[1.02]">
                <span>Lihat Keranjang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 z-50 py-2 px-4 flex justify-around items-center shadow-lg">
        <a href="index.php" class="flex flex-col items-center gap-1 text-xs <?= (!isset($active_nav) || $active_nav == 'menu') ? 'text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-500 dark:text-slate-400' ?>">
            <i class="fa-solid fa-house text-lg"></i>
            <span>Menu</span>
        </a>
        <a href="cart.php" class="flex flex-col items-center gap-1 text-xs relative <?= (isset($active_nav) && $active_nav == 'cart') ? 'text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-500 dark:text-slate-400' ?>">
            <div class="relative">
                <i class="fa-solid fa-cart-shopping text-lg"></i>
                <?php if ($cart_count > 0): ?>
                <span class="absolute -top-1.5 -right-2.5 bg-brand-600 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                    <?= $cart_count ?>
                </span>
                <?php endif; ?>
            </div>
            <span>Keranjang</span>
        </a>
        <a href="riwayat.php" class="flex flex-col items-center gap-1 text-xs <?= (isset($active_nav) && $active_nav == 'riwayat') ? 'text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-500 dark:text-slate-400' ?>">
            <i class="fa-solid fa-clock-rotate-left text-lg"></i>
            <span>Pesanan</span>
        </a>
        <button type="button" onclick="toggleTheme()" class="flex flex-col items-center gap-1 text-xs text-slate-500 dark:text-slate-400 focus:outline-none">
            <i class="fa-solid fa-circle-half-stroke text-lg text-brand-600 dark:text-amber-400"></i>
            <span id="mobile-theme-text">Tema</span>
        </button>
        <a href="https://wa.me/<?= $wa_resto ?>?text=Halo%20<?= urlencode($resto_name) ?>%2C%20saya%20ingin%20tanya%20menu" target="_blank" class="flex flex-col items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
            <i class="fa-brands fa-whatsapp text-lg"></i>
            <span>Bantuan</span>
        </a>
    </div>

    <!-- Main Footer -->
    <footer class="bg-slate-900 dark:bg-black text-slate-400 text-sm mt-20 pt-16 pb-28 md:pb-12 border-t border-slate-800 dark:border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-utensils text-xl"></i>
                        </div>
                        <span class="font-extrabold text-xl text-white tracking-tight"><?= htmlspecialchars($resto_name) ?></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Layanan pesan antar makanan cepat saji dengan cita rasa istimewa. Bahan baku segar pilihan, dimasak higienis, dan diantar hangat sampai ke depan pintu Anda.
                    </p>
                    <div class="flex items-center gap-3 text-slate-400">
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://wa.me/<?= $wa_resto ?>" target="_blank" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Col 2: Jam Operasional & Layanan Delivery -->
                <div class="space-y-3">
                    <h3 class="font-bold text-white uppercase text-xs tracking-wider">Layanan Delivery</h3>
                    <ul class="space-y-2 text-xs">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Packaging Anti-Tumpah Aman</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Thermal Bag Menjaga Suhu</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Live Status Tracking</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Pembayaran QRIS / COD</li>
                    </ul>
                    <div class="pt-2">
                        <span class="text-xs text-slate-300 font-semibold block">Jam Operasional:</span>
                        <span class="text-xs text-amber-400">Setiap Hari: <?= htmlspecialchars(getSetting('jam_operasional', '10:00 - 22:00 WIB')) ?></span>
                    </div>
                </div>

                <!-- Col 3: Navigasi Cepat -->
                <div class="space-y-3">
                    <h3 class="font-bold text-white uppercase text-xs tracking-wider">Akses Cepat</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="index.php" class="hover:text-white transition">Katalog Menu</a></li>
                        <li><a href="cart.php" class="hover:text-white transition">Keranjang Belanja</a></li>
                        <li><a href="riwayat.php" class="hover:text-white transition">Cek Status Pesanan Saya</a></li>
                        <li><a href="admin/login.php" class="hover:text-white transition text-slate-500">Login Administrator Resto</a></li>
                    </ul>
                </div>

                <!-- Col 4: Kontak & Alamat -->
                <div class="space-y-3">
                    <h3 class="font-bold text-white uppercase text-xs tracking-wider">Hubungi Restoran</h3>
                    <div class="space-y-2.5 text-xs">
                        <p class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-brand-500 mt-0.5"></i>
                            <span><?= htmlspecialchars(getSetting('alamat_resto', 'Jl. Merdeka No. 45, Jakarta Selatan')) ?></span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                            <span>WhatsApp: <?= htmlspecialchars($wa_resto) ?></span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-slate-400"></i>
                            <span>halo@dapurlezat.com</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($resto_name) ?>. Aplikasi Pemesanan Makanan Delivery.</p>
                <div class="flex items-center gap-3">
                    <span>Mode:</span>
                    <button type="button" onclick="toggleTheme()" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-half-stroke text-amber-400"></i>
                        <span id="footer-theme-text">Siang / Malam</span>
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Cart, Toast, & Theme (Siang / Malam) Scripts -->
    <script>
        // Update Ikon Mode Siang & Malam
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const footerText = document.getElementById('footer-theme-text');
            const mobileText = document.getElementById('mobile-theme-text');

            if (darkIcon && lightIcon) {
                if (isDark) {
                    darkIcon.classList.add('hidden');
                    lightIcon.classList.remove('hidden');
                } else {
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.remove('hidden');
                }
            }

            if (footerText) {
                footerText.innerText = isDark ? 'Mode Malam (Aktif)' : 'Mode Siang (Aktif)';
            }
            if (mobileText) {
                mobileText.innerText = isDark ? 'Malam' : 'Siang';
            }

            // Sync with admin theme toggle if present
            const adminDarkIcon = document.getElementById('admin-theme-dark-icon');
            const adminLightIcon = document.getElementById('admin-theme-light-icon');
            if (adminDarkIcon && adminLightIcon) {
                if (isDark) {
                    adminDarkIcon.classList.add('hidden');
                    adminLightIcon.classList.remove('hidden');
                } else {
                    adminLightIcon.classList.add('hidden');
                    adminDarkIcon.classList.remove('hidden');
                }
            }
        }

        // Toggle Switch Mode Siang / Malam
        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateThemeIcons();

            // Tampilkan toast kecil
            showNotification('info', document.documentElement.classList.contains('dark') ? '🌙 Mode Malam Aktif' : '☀️ Mode Siang Aktif');
        }

        // Pasang event saat DOM siap
        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcons();
        });

        function showNotification(type, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }

        // Quick add to cart function
        function addToCart(menuId, qty = 1, notes = '') {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('menu_id', menuId);
            formData.append('qty', qty);
            formData.append('notes', notes);

            fetch('cart_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message || 'Menu ditambahkan ke keranjang!');
                    const badge = document.getElementById('cart-badge-count');
                    if (badge) badge.innerText = data.total_items;

                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification('error', data.message || 'Gagal menambahkan menu');
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('error', 'Terjadi kesalahan sistem');
            });
        }
    </script>
</body>
</html>
