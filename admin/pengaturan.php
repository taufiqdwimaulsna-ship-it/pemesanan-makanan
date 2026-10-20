<?php
// admin/pengaturan.php
$admin_page_title = 'Pengaturan Restoran & Ongkir';
$active_menu = 'pengaturan';
require_once __DIR__ . '/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_to_update = [
        'nama_resto' => sanitize($_POST['nama_resto'] ?? ''),
        'slogan' => sanitize($_POST['slogan'] ?? ''),
        'alamat_resto' => sanitize($_POST['alamat_resto'] ?? ''),
        'no_wa_resto' => sanitize($_POST['no_wa_resto'] ?? ''),
        'ongkir_default' => (int)($_POST['ongkir_default'] ?? 10000),
        'jam_operasional' => sanitize($_POST['jam_operasional'] ?? '')
    ];

    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    foreach ($settings_to_update as $k => $v) {
        $stmt->execute([$k, $v]);
    }

    $message = 'Pengaturan berhasil diperbarui!';
}

// Ambil value saat ini
$nama_resto = getSetting('nama_resto', 'Dapur Lezat Express');
$slogan = getSetting('slogan', 'Pesan Cepat, Masak Hangat, Antar Cepat Sampai ke Mejamu!');
$alamat_resto = getSetting('alamat_resto', 'Jl. Merdeka No. 45, Jakarta Selatan');
$no_wa_resto = getSetting('no_wa_resto', '628123456789');
$ongkir_default = getSetting('ongkir_default', '10000');
$jam_operasional = getSetting('jam_operasional', '10:00 - 22:00 WIB');
?>

<div class="max-w-3xl space-y-6">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Resto & Biaya Antar</h1>
        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi informasi umum restoran yang tampil di katalog dan faktur pelanggan.</p>
    </div>

    <?php if (!empty($message)): ?>
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <form method="POST" action="pengaturan.php" class="space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Restoran / Brand</label>
                    <input type="text" name="nama_resto" value="<?= htmlspecialchars($nama_resto) ?>" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500 font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Resmi Resto</label>
                    <input type="text" name="no_wa_resto" value="<?= htmlspecialchars($no_wa_resto) ?>" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Slogan Promosi</label>
                <input type="text" name="slogan" value="<?= htmlspecialchars($slogan) ?>" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap Restoran</label>
                <textarea name="alamat_resto" rows="2" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:outline-none focus:border-brand-500"><?= htmlspecialchars($alamat_resto) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tarif Ongkos Kirim Delivery Flat (Rp)</label>
                    <input type="number" name="ongkir_default" value="<?= htmlspecialchars($ongkir_default) ?>" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Operasional Layanan</label>
                    <input type="text" name="jam_operasional" value="<?= htmlspecialchars($jam_operasional) ?>" required class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl shadow-lg shadow-brand-500/25 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>
    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>

