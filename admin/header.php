<?php
// admin/header.php
require_once __DIR__ . '/auth_check.php';

$current_admin = $_SESSION['admin_user'];
$resto_name = getSetting('nama_resto', 'Dapur Lezat Express');

// Hitung pesanan baru yang butuh tindakan (menunggu & dimasak)
$count_pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'menunggu'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($admin_page_title) ? htmlspecialchars($admin_page_title) . ' - ' : '' ?>Admin Resto</title>
    <!-- Script Deteksi Awal Mode Siang / Malam -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen flex transition-colors">

    <!-- Sidebar Desktop -->
    <aside class="w-64 bg-slate-900 dark:bg-black text-slate-300 flex-col justify-between shrink-0 hidden md:flex border-r border-slate-800">
        <div>
            <!-- Resto Brand -->
            <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-800">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-amber-500 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-brand-500/30">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-white text-base leading-tight tracking-tight"><?= htmlspecialchars($resto_name) ?></h2>
                    <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">Kitchen & Delivery</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5 text-xs font-semibold">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition <?= (!isset($active_menu) || $active_menu == 'dashboard') ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/20' : 'hover:bg-slate-800 text-slate-400 hover:text-white' ?>">
                    <i class="fa-solid fa-chart-pie text-base w-5"></i>
                    <span>Dashboard Analitik</span>
                </a>

                <a href="pesanan.php" class="flex items-center justify-between px-4 py-3 rounded-xl transition <?= (isset($active_menu) && $active_menu == 'pesanan') ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/20' : 'hover:bg-slate-800 text-slate-400 hover:text-white' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-clipboard-list text-base w-5"></i>
                        <span>Pesanan Delivery</span>
                    </div>
                    <?php if ($count_pending > 0): ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950 animate-pulse">
                        <?= $count_pending ?> Baru
                    </span>
                    <?php endif; ?>
                </a>

                <a href="menu.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition <?= (isset($active_menu) && $active_menu == 'menu') ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/20' : 'hover:bg-slate-800 text-slate-400 hover:text-white' ?>">
                    <i class="fa-solid fa-bowl-food text-base w-5"></i>
                    <span>Kelola Menu Makanan</span>
                </a>

                <a href="pengaturan.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition <?= (isset($active_menu) && $active_menu == 'pengaturan') ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/20' : 'hover:bg-slate-800 text-slate-400 hover:text-white' ?>">
                    <i class="fa-solid fa-sliders text-base w-5"></i>
                    <span>Profil Resto & Ongkir</span>
                </a>
            </nav>
        </div>

        <!-- Bottom Links -->
        <div class="p-4 border-t border-slate-800 space-y-2">
            <a href="../index.php" target="_blank" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-amber-400"></i>
                <span>Lihat Web Pelanggan</span>
            </a>
            <a href="logout.php" onclick="return confirm('Keluar dari portal admin?')" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl hover:bg-rose-900/40 text-rose-400 text-xs font-semibold transition">
                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                <span>Keluar (Logout)</span>
            </a>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 transition-colors">
            <!-- Mobile Menu Toggler -->
            <div class="flex items-center gap-3 md:hidden">
                <a href="index.php" class="font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center text-sm"><i class="fa-solid fa-utensils"></i></span>
                    <span>Admin Resto</span>
                </a>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <i class="fa-regular fa-calendar-check text-brand-600"></i>
                <span><?= date('l, d F Y') ?></span>
            </div>

            <!-- Profile & Theme Toggle Pill -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleAdminTheme()" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition" title="Ganti Mode Siang / Malam">
                    <i id="admin-theme-dark-icon" class="fa-solid fa-moon text-sm hidden"></i>
                    <i id="admin-theme-light-icon" class="fa-solid fa-sun text-sm hidden text-amber-400"></i>
                </button>

                <a href="pesanan.php" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <?php if ($count_pending > 0): ?>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
                    <?php endif; ?>
                </a>

                <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-800">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 dark:bg-brand-600 text-white flex items-center justify-center font-bold text-xs">
                        <?= strtoupper(substr($current_admin['nama'], 0, 2)) ?>
                    </div>
                    <div class="hidden sm:block text-left text-xs">
                        <p class="font-bold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($current_admin['nama']) ?></p>
                        <p class="text-[10px] text-slate-400">Resto Supervisor</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 sm:p-8">

    <script>
        function updateAdminThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const darkIcon = document.getElementById('admin-theme-dark-icon');
            const lightIcon = document.getElementById('admin-theme-light-icon');
            if (darkIcon && lightIcon) {
                if (isDark) {
                    darkIcon.classList.add('hidden');
                    lightIcon.classList.remove('hidden');
                } else {
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.remove('hidden');
                }
            }
        }

        function toggleAdminTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateAdminThemeIcons();
        }

        document.addEventListener('DOMContentLoaded', updateAdminThemeIcons);
    </script>
