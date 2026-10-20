<?php
// admin/menu.php
$admin_page_title = 'Kelola Menu Makanan & Minuman';
$active_menu = 'menu';
require_once __DIR__ . '/header.php';

// Ambil Kategori untuk dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY urutan ASC")->fetchAll();

// Handle Form Tambah Menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nama = sanitize($_POST['nama_menu'] ?? '');
    $cat_id = (int)($_POST['category_id'] ?? 1);
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 50);
    $gambar = sanitize($_POST['gambar'] ?? '');
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_promo = isset($_POST['is_promo']) ? 1 : 0;
    $diskon_persen = (int)($_POST['diskon_persen'] ?? 0);

    if (empty($gambar)) {
        $gambar = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80';
    }

    $st = $pdo->prepare("INSERT INTO menus (nama_menu, category_id, deskripsi, harga, stok, gambar, is_popular, is_promo, diskon_persen, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'tersedia')");
    $st->execute([$nama, $cat_id, $deskripsi, $harga, $stok, $gambar, $is_popular, $is_promo, $diskon_persen]);

    echo "<script>window.location.href='menu.php';</script>";
    exit;
}

// Handle Form Edit Menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['menu_id'];
    $nama = sanitize($_POST['nama_menu'] ?? '');
    $cat_id = (int)($_POST['category_id'] ?? 1);
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 50);
    $gambar = sanitize($_POST['gambar'] ?? '');
    $status = sanitize($_POST['status'] ?? 'tersedia');
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_promo = isset($_POST['is_promo']) ? 1 : 0;
    $diskon_persen = (int)($_POST['diskon_persen'] ?? 0);

    $st = $pdo->prepare("UPDATE menus SET nama_menu = ?, category_id = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ?, is_popular = ?, is_promo = ?, diskon_persen = ?, status = ? WHERE id = ?");
    $st->execute([$nama, $cat_id, $deskripsi, $harga, $stok, $gambar, $is_popular, $is_promo, $diskon_persen, $status, $id]);

    echo "<script>window.location.href='menu.php';</script>";
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM menus WHERE id = ?")->execute([$del_id]);
    echo "<script>window.location.href='menu.php';</script>";
    exit;
}

// Query List Menu
$menus = $pdo->query("SELECT m.*, c.nama_kategori FROM menus m JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC")->fetchAll();
?>

<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Katalog Menu Restoran</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tambah menu baru, atur harga promo diskon, dan monitor persediaan stok.</p>
        </div>
        <button onclick="openAddModal()" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 flex items-center gap-2 transition self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Menu Baru</span>
        </button>
    </div>

    <!-- Menu Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3.5 px-4">Menu & Gambar</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Harga & Diskon</th>
                        <th class="py-3.5 px-4">Stok</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($menus as $m): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-4 flex items-center gap-3">
                            <img src="<?= htmlspecialchars($m['gambar']) ?>" alt="Menu" class="w-14 h-14 rounded-xl object-cover border border-slate-100 shrink-0">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h4 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($m['nama_menu']) ?></h4>
                                    <?php if ($m['is_popular']): ?>
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Favorit</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-slate-400 text-[11px] line-clamp-1 max-w-xs mt-0.5"><?= htmlspecialchars($m['deskripsi']) ?></p>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg text-xs">
                                <?= htmlspecialchars($m['nama_kategori']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-extrabold text-sm text-slate-900 block"><?= rupiah($m['harga']) ?></span>
                            <?php if ($m['is_promo']): ?>
                                <span class="text-rose-600 font-bold text-[10px]">Promo Diskon <?= $m['diskon_persen'] ?>%</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-bold <?= $m['stok'] <= 10 ? 'text-rose-600' : 'text-slate-700' ?>">
                                <?= $m['stok'] ?> porsi
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?= $m['status'] == 'tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                                <?= strtoupper($m['status']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($m)) ?>)" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl inline-block" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <a href="menu.php?delete=<?= $m['id'] ?>" onclick="return confirm('Hapus menu <?= htmlspecialchars($m['nama_menu']) ?>?')" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl inline-block" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah / Edit Menu -->
<div id="modal-menu" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-150">
        <form method="POST" action="menu.php" id="form-menu" class="p-6 space-y-4">
            <input type="hidden" name="action" id="form-action" value="create">
            <input type="hidden" name="menu_id" id="form-menu-id" value="">

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 id="modal-heading" class="font-black text-lg text-slate-900">Tambah Menu Makanan</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Menu</label>
                <input type="text" name="nama_menu" id="input-nama" required placeholder="Contoh: Nasi Goreng Gila" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                    <select name="category_id" id="input-category" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" id="input-harga" required placeholder="25000" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Stok Porsi</label>
                    <input type="number" name="stok" id="input-stok" value="50" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500">
                </div>
                <div id="status-group" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Ketersediaan</label>
                    <select name="status" id="input-status" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white">
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">URL Foto Makanan (HD Unsplash / Web)</label>
                <input type="url" name="gambar" id="input-gambar" placeholder="https://images.unsplash.com/..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 focus:bg-white focus:outline-none focus:border-brand-500">
                <div class="flex flex-wrap gap-1 mt-1 text-[10px]">
                    <span class="text-slate-400">Preset Foto Cepat:</span>
                    <button type="button" onclick="setPreset('https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80')" class="text-brand-600 underline">Nasi Goreng</button>
                    <button type="button" onclick="setPreset('https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80')" class="text-brand-600 underline">Ayam Crispy</button>
                    <button type="button" onclick="setPreset('https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=600&q=80')" class="text-brand-600 underline">Es Kopi</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="input-desc" rows="2" placeholder="Komposisi bumbu, rasa, dan pelengkap..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:outline-none focus:border-brand-500"></textarea>
            </div>

            <!-- Promo & Popular Toggles -->
            <div class="p-3 bg-slate-50 rounded-xl space-y-2 border border-slate-100 text-xs">
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_popular" id="input-popular" class="rounded text-brand-600">
                        <span class="font-bold text-slate-700">Tandai Menu Favorit (Best Seller)</span>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_promo" id="input-promo" onchange="toggleDiskonInput(this.checked)" class="rounded text-rose-600">
                        <span class="font-bold text-rose-600">Aktifkan Promo Diskon (%)</span>
                    </label>
                    <input type="number" name="diskon_persen" id="input-diskon" placeholder="%" class="w-16 text-center text-xs bg-white border border-slate-200 rounded-lg p-1 hidden" min="0" max="90">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('form-menu').reset();
        document.getElementById('form-action').value = 'create';
        document.getElementById('modal-heading').innerText = 'Tambah Menu Makanan Baru';
        document.getElementById('status-group').classList.add('hidden');
        document.getElementById('input-diskon').classList.add('hidden');
        document.getElementById('modal-menu').classList.remove('hidden');
    }

    function openEditModal(m) {
        document.getElementById('form-action').value = 'edit';
        document.getElementById('form-menu-id').value = m.id;
        document.getElementById('modal-heading').innerText = 'Edit Menu: ' + m.nama_menu;
        document.getElementById('input-nama').value = m.nama_menu;
        document.getElementById('input-category').value = m.category_id;
        document.getElementById('input-harga').value = m.harga;
        document.getElementById('input-stok').value = m.stok;
        document.getElementById('input-gambar').value = m.gambar;
        document.getElementById('input-desc').value = m.deskripsi;
        document.getElementById('input-status').value = m.status;
        document.getElementById('status-group').classList.remove('hidden');

        document.getElementById('input-popular').checked = (m.is_popular == 1);
        document.getElementById('input-promo').checked = (m.is_promo == 1);
        if (m.is_promo == 1) {
            document.getElementById('input-diskon').classList.remove('hidden');
            document.getElementById('input-diskon').value = m.diskon_persen;
        } else {
            document.getElementById('input-diskon').classList.add('hidden');
        }

        document.getElementById('modal-menu').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-menu').classList.add('hidden');
    }

    function toggleDiskonInput(isChecked) {
        const d = document.getElementById('input-diskon');
        if (isChecked) {
            d.classList.remove('hidden');
            if (!d.value) d.value = 10;
        } else {
            d.classList.add('hidden');
            d.value = 0;
        }
    }

    function setPreset(url) {
        document.getElementById('input-gambar').value = url;
    }
</script>

<?php require_once __DIR__ . '/footer.php'; ?>

