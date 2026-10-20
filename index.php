<?php
// index.php
$page_title = 'Pesan Makanan Online & Delivery Cepat';
$active_nav = 'menu';
require_once __DIR__ . '/includes/header.php';

// Ambil Kategori
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY urutan ASC");
$categories = $cat_stmt->fetchAll();

// Filter & Pencarian
$selected_category = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'popular';

$sql = "SELECT m.*, c.nama_kategori FROM menus m JOIN categories c ON m.category_id = c.id WHERE m.status = 'tersedia'";
$params = [];

if ($selected_category > 0) {
    $sql .= " AND m.category_id = ?";
    $params[] = $selected_category;
}

if (!empty($keyword)) {
    $sql .= " AND (m.nama_menu LIKE ? OR m.deskripsi LIKE ?)";
    $params[] = "%{$keyword}%";
    $params[] = "%{$keyword}%";
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY m.harga ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY m.harga DESC";
        break;
    case 'promo':
        $sql .= " ORDER BY m.is_promo DESC, m.diskon_persen DESC";
        break;
    case 'popular':
    default:
        $sql .= " ORDER BY m.is_popular DESC, m.id ASC";
        break;
}

$menu_stmt = $pdo->prepare($sql);
$menu_stmt->execute($params);
$menus = $menu_stmt->fetchAll();

// Menu Populer / Promo untuk Highlight Section
$promo_stmt = $pdo->query("SELECT * FROM menus WHERE (is_popular = 1 OR is_promo = 1) AND status = 'tersedia' LIMIT 4");
$promo_menus = $promo_stmt->fetchAll();
?>

<!-- Hero Banner Section -->
<section class="relative bg-gradient-to-b from-orange-50 via-white to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 border-b border-orange-100/60 dark:border-slate-800/80 overflow-hidden pt-8 pb-14 transition-colors">
    <!-- Decorative background elements -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-500/10 dark:bg-brand-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -left-24 w-72 h-72 bg-amber-400/15 dark:bg-amber-400/5 rounded-full blur-2xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-xs font-bold tracking-wide shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-ping"></span>
                    <span>🛵 Layanan Delivery Cepat & Hangat</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.15]">
                    Lapar? Pesan <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-amber-500">Makanan Favoritmu</span> Langsung Diantar!
                </h1>

                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Nikmati sajian spesial rempah nusantara pilihan, dimasak fresh setiap kali ada pesanan. Cepat, higienis, dan terlindungi packaging anti-tumpah.
                </p>

                <!-- Search Input Form -->
                <form action="index.php" method="GET" class="max-w-xl mx-auto lg:mx-0">
                    <div class="flex items-center bg-white dark:bg-slate-900 p-2 rounded-2xl shadow-lg shadow-slate-200/60 dark:shadow-none border border-slate-200 dark:border-slate-700 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100 dark:focus-within:ring-brand-950 transition">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 ml-3 mr-2"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari ayam geprek, nasi goreng, es kopi..." class="w-full text-sm bg-transparent outline-none text-slate-800 dark:text-white placeholder-slate-400">
                        <?php if ($selected_category > 0): ?>
                            <input type="hidden" name="cat" value="<?= $selected_category ?>">
                        <?php endif; ?>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-5 py-3 rounded-xl transition flex items-center gap-1.5 shrink-0 shadow-md shadow-brand-500/20">
                            <span>Cari</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>

                <!-- Value Props -->
                <div class="grid grid-cols-3 gap-3 pt-2 max-w-lg mx-auto lg:mx-0 text-left">
                    <div class="flex items-center gap-2.5 bg-white/70 dark:bg-slate-900/80 backdrop-blur-sm p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100">25 Menit</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Rata-rata kirim</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 bg-white/70 dark:bg-slate-900/80 backdrop-blur-sm p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100">100% Halal</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">& Higienis</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 bg-white/70 dark:bg-slate-900/80 backdrop-blur-sm p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Live Track</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Lacak kurir</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual Promo Card -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-sm sm:max-w-md">
                    <!-- Food Hero Image Card -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white dark:border-slate-800 bg-slate-900 group">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80" alt="Special Resto Dish" class="w-full h-80 sm:h-96 object-cover opacity-90 group-hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        
                        <!-- Floating Badge Diskon -->
                        <div class="absolute top-4 right-4 bg-rose-600 text-white font-extrabold text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 animate-pulse">
                            <i class="fa-solid fa-tag"></i>
                            <span>PROMO DELIVERY</span>
                        </div>

                        <!-- Card Bottom Description -->
                        <div class="absolute bottom-4 left-4 right-4 p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-amber-400">Paling Direkomendasikan</span>
                                    <h4 class="font-bold text-base sm:text-lg">Paket Kenyang Ayam Geprek</h4>
                                    <p class="text-xs text-slate-200">Ayam Geprek + Nasi Pulen + Es Teh Manis</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs line-through text-slate-400">Rp 37.500</span>
                                    <p class="font-extrabold text-lg text-emerald-400">Rp 30.000</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Mini Card Testimonial -->
                    <div class="absolute -bottom-5 -left-4 sm:-left-6 bg-white dark:bg-slate-800 p-3 sm:p-4 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base">
                            ⭐
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-amber-400 text-xs">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <span class="font-bold text-slate-700 dark:text-slate-200 text-xs ml-1">4.9 / 5.0</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">1.200+ Pesanan Delivery Tuntas</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Section Highlight Promo & Best Seller -->
<?php if (!empty($promo_menus) && empty($keyword) && $selected_category == 0): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Paling Laris & Diskon Khusus 🔥</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Pilihan terfavorit pelanggan yang siap menggoyang lidah Anda.</p>
        </div>
        <a href="#katalog" class="text-xs sm:text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 flex items-center gap-1">
            <span>Lihat Semua</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($promo_menus as $pmenu): 
            $final_p = $pmenu['harga'];
            if ($pmenu['is_promo'] && $pmenu['diskon_persen'] > 0) {
                $final_p = $pmenu['harga'] - ($pmenu['harga'] * ($pmenu['diskon_persen'] / 100));
            }
        ?>
        <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
            <div class="relative h-44 overflow-hidden bg-slate-100 dark:bg-slate-800">
                <img src="<?= htmlspecialchars($pmenu['gambar']) ?>" alt="<?= htmlspecialchars($pmenu['nama_menu']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <?php if ($pmenu['is_promo']): ?>
                    <span class="absolute top-3 left-3 bg-rose-600 text-white text-[11px] font-extrabold px-2.5 py-1 rounded-lg shadow">
                        HEMAT <?= $pmenu['diskon_persen'] ?>%
                    </span>
                <?php endif; ?>
                <?php if ($pmenu['is_popular']): ?>
                    <span class="absolute top-3 right-3 bg-amber-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-lg shadow flex items-center gap-1">
                        <i class="fa-solid fa-fire"></i> Best Seller
                    </span>
                <?php endif; ?>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition line-clamp-1">
                        <?= htmlspecialchars($pmenu['nama_menu']) ?>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1">
                        <?= htmlspecialchars($pmenu['deskripsi']) ?>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <?php if ($pmenu['is_promo']): ?>
                            <span class="text-[11px] line-through text-slate-400 block"><?= rupiah($pmenu['harga']) ?></span>
                        <?php endif; ?>
                        <span class="text-base font-extrabold text-slate-900 dark:text-white"><?= rupiah($final_p) ?></span>
                    </div>
                    <button onclick="openMenuModal(<?= htmlspecialchars(json_encode($pmenu)) ?>)" class="bg-brand-50 hover:bg-brand-600 dark:bg-slate-800 dark:hover:bg-brand-600 text-brand-600 dark:text-brand-400 hover:text-white dark:hover:text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Main Catalog Section -->
<section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Katalog Menu Restoran</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pilih menu favoritmu dan atur catatan khusus sesuai selera.</p>
            </div>

            <!-- Filter Sort Dropdown -->
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 hidden sm:inline">Urutkan:</span>
                <form action="index.php#katalog" method="GET" class="flex items-center">
                    <?php if ($selected_category > 0): ?>
                        <input type="hidden" name="cat" value="<?= $selected_category ?>">
                    <?php endif; ?>
                    <?php if (!empty($keyword)): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($keyword) ?>">
                    <?php endif; ?>
                    <select name="sort" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 text-xs sm:text-sm font-semibold border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand-500 text-slate-700 dark:text-slate-200 shadow-sm cursor-pointer">
                        <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>⭐ Paling Populer</option>
                        <option value="promo" <?= $sort == 'promo' ? 'selected' : '' ?>>🏷️ Promo & Diskon</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>💰 Harga Terendah</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>💎 Harga Tertinggi</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Category Horizontal Tabs -->
        <div class="mt-6 flex items-center gap-2 overflow-x-auto pb-2 hide-scrollbar">
            <a href="index.php?sort=<?= $sort ?>#katalog" class="shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 shadow-sm <?= $selected_category == 0 ? 'bg-brand-600 text-white shadow-brand-500/20' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' ?>">
                <i class="fa-solid fa-utensils"></i>
                <span>Semua Menu</span>
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="index.php?cat=<?= $cat['id'] ?>&sort=<?= $sort ?>#katalog" class="shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 shadow-sm <?= $selected_category == $cat['id'] ? 'bg-brand-600 text-white shadow-brand-500/20' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' ?>">
                <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?>"></i>
                <span><?= htmlspecialchars($cat['nama_kategori']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Menu Cards Grid -->
    <?php if (empty($menus)): ?>
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-dashed border-slate-300 dark:border-slate-700 max-w-lg mx-auto my-12">
        <div class="w-16 h-16 rounded-full bg-orange-100 dark:bg-orange-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center mx-auto text-2xl mb-4">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Menu Tidak Ditemukan</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Coba cari kata kunci lain atau pilih kategori yang berbeda.</p>
        <a href="index.php#katalog" class="inline-flex items-center gap-2 mt-4 bg-brand-600 text-white text-xs font-bold px-4 py-2 rounded-xl">
            <span>Reset Pencarian</span>
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($menus as $item): 
            $final_price = $item['harga'];
            if ($item['is_promo'] && $item['diskon_persen'] > 0) {
                $final_price = $item['harga'] - ($item['harga'] * ($item['diskon_persen'] / 100));
            }
        ?>
        <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col group relative">
            
            <!-- Food Image Thumbnail -->
            <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                <img src="<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_menu']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                
                <!-- Category Badge -->
                <span class="absolute bottom-2.5 left-2.5 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-semibold px-2 py-0.5 rounded-md">
                    <?= htmlspecialchars($item['nama_kategori']) ?>
                </span>

                <!-- Promo Badge -->
                <?php if ($item['is_promo']): ?>
                <span class="absolute top-2.5 left-2.5 bg-rose-600 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-md shadow">
                    -<?= $item['diskon_persen'] ?>%
                </span>
                <?php endif; ?>

                <?php if ($item['is_popular']): ?>
                <span class="absolute top-2.5 right-2.5 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow">
                    Favorit
                </span>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition line-clamp-1">
                        <?= htmlspecialchars($item['nama_menu']) ?>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1.5 leading-relaxed">
                        <?= htmlspecialchars($item['deskripsi']) ?>
                    </p>
                </div>

                <!-- Price & Order Action -->
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <?php if ($item['is_promo']): ?>
                            <span class="text-[11px] line-through text-slate-400 block"><?= rupiah($item['harga']) ?></span>
                        <?php endif; ?>
                        <span class="text-base sm:text-lg font-black text-slate-900 dark:text-white"><?= rupiah($final_price) ?></span>
                    </div>

                    <button type="button" onclick="openMenuModal(<?= htmlspecialchars(json_encode($item)) ?>)" class="bg-brand-600 hover:bg-brand-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-brand-500/20 active:scale-95">
                        <i class="fa-solid fa-plus"></i>
                        <span>Pesan</span>
                    </button>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- Modal Detail & Catatan Pesanan Menu -->
<div id="menu-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-800 transform transition-all animate-in fade-in zoom-in duration-200">
        
        <!-- Modal Image & Close -->
        <div class="relative h-56 bg-slate-100 dark:bg-slate-800">
            <img id="modal-image" src="" alt="Menu" class="w-full h-full object-cover">
            <button onclick="closeMenuModal()" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 flex items-center justify-center shadow-md transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <div class="absolute bottom-3 left-3">
                <span id="modal-category" class="bg-slate-900/80 backdrop-blur-sm text-white text-xs font-semibold px-2.5 py-1 rounded-lg"></span>
            </div>
        </div>

        <!-- Modal Body Form -->
        <div class="p-6 space-y-4">
            <div>
                <h3 id="modal-title" class="font-black text-xl text-slate-900 dark:text-white"></h3>
                <p id="modal-desc" class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed"></p>
                <div class="mt-2 flex items-center gap-2">
                    <span id="modal-price" class="text-xl font-black text-brand-600 dark:text-brand-400"></span>
                    <span id="modal-strike-price" class="text-xs line-through text-slate-400"></span>
                </div>
            </div>

            <!-- Notes Input -->
            <div class="space-y-1.5">
                <label for="modal-notes" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                    <span><i class="fa-solid fa-pen-to-square text-brand-500 mr-1"></i> Catatan Khusus untuk Dapur:</span>
                    <span class="text-[10px] text-slate-400 font-normal">Opsional</span>
                </label>
                <input type="text" id="modal-notes" placeholder="Contoh: Jangan pakai daun bawang, sambal pedas banget..." class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-brand-500 text-slate-800 dark:text-white">
            </div>

            <!-- Quantity Counter & Add to Cart Button -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-4">
                <!-- Counter -->
                <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 p-1">
                    <button type="button" onclick="changeQty(-1)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 font-bold flex items-center justify-center transition shadow-sm">
                        <i class="fa-solid fa-minus text-xs"></i>
                    </button>
                    <span id="modal-qty" class="w-10 text-center font-bold text-sm text-slate-800 dark:text-slate-100">1</span>
                    <button type="button" onclick="changeQty(1)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 font-bold flex items-center justify-center transition shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="button" id="modal-submit-btn" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="modal-total-btn">Tambahkan - Rp 0</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    let currentItem = null;
    let currentQty = 1;

    function openMenuModal(item) {
        currentItem = item;
        currentQty = 1;
        document.getElementById('modal-qty').innerText = currentQty;
        document.getElementById('modal-notes').value = '';

        document.getElementById('modal-title').innerText = item.nama_menu;
        document.getElementById('modal-desc').innerText = item.deskripsi;
        document.getElementById('modal-image').src = item.gambar;
        document.getElementById('modal-category').innerText = item.nama_kategori || 'Makanan';

        let finalPrice = parseInt(item.harga);
        if (item.is_promo == 1 && item.diskon_persen > 0) {
            finalPrice = item.harga - (item.harga * (item.diskon_persen / 100));
            document.getElementById('modal-strike-price').innerText = 'Rp ' + parseInt(item.harga).toLocaleString('id-ID');
            document.getElementById('modal-strike-price').classList.remove('hidden');
        } else {
            document.getElementById('modal-strike-price').classList.add('hidden');
        }

        document.getElementById('modal-price').innerText = 'Rp ' + finalPrice.toLocaleString('id-ID');
        updateModalTotal(finalPrice);

        document.getElementById('modal-submit-btn').onclick = function() {
            const notes = document.getElementById('modal-notes').value;
            addToCart(item.id, currentQty, notes);
            closeMenuModal();
        };

        document.getElementById('menu-modal').classList.remove('hidden');
    }

    function closeMenuModal() {
        document.getElementById('menu-modal').classList.add('hidden');
    }

    function changeQty(delta) {
        currentQty = Math.max(1, currentQty + delta);
        document.getElementById('modal-qty').innerText = currentQty;
        if (currentItem) {
            let finalPrice = parseInt(currentItem.harga);
            if (currentItem.is_promo == 1 && currentItem.diskon_persen > 0) {
                finalPrice = currentItem.harga - (currentItem.harga * (currentItem.diskon_persen / 100));
            }
            updateModalTotal(finalPrice);
        }
    }

    function updateModalTotal(unitPrice) {
        const total = unitPrice * currentQty;
        document.getElementById('modal-total-btn').innerText = 'Tambahkan (' + currentQty + ') - Rp ' + total.toLocaleString('id-ID');
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
