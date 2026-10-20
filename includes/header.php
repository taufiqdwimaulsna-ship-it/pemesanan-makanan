<?php
// includes/header.php
require_once __DIR__ . '/../config/database.php';

$resto_name = getSetting('nama_resto', 'Dapur Lezat Express');
$resto_slogan = getSetting('slogan', 'Pesan Cepat, Masak Hangat, Antar Cepat Sampai ke Mejamu!');
$resto_address = getSetting('alamat_resto', 'Jakarta');
$cart_count = getCartCount();
$cart_subtotal = getCartSubtotal();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?><?= htmlspecialchars($resto_name) ?></title>
    <!-- Script Deteksi Awal Mode Siang / Malam (Cegah Flicker) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .dark body {
            background-color: #0b0f19;
            color: #f1f5f9;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .dark ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-between transition-colors duration-200">

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 dark:bg-black text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span> Buka Sekarang (10:00 - 22:00 WIB)
                </span>
                <span class="hidden md:inline text-slate-400">|</span>
                <span class="hidden md:inline"><i class="fa-solid fa-location-dot text-brand-500 mr-1"></i> <?= htmlspecialchars($resto_address) ?></span>
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <a href="riwayat.php" class="hover:text-white transition flex items-center gap-1">
                    <i class="fa-solid fa-receipt text-amber-400"></i> Cek Status Pesanan
                </a>
                <span class="text-slate-600">•</span>
                <a href="admin/login.php" class="hover:text-white transition flex items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-brand-500"></i> Portal Resto/Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-sm transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo & Name -->
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-utensils text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl tracking-tight text-slate-900 dark:text-white group-hover:text-brand-600 transition">
                                <?= htmlspecialchars($resto_name) ?>
                            </span>
                            <span class="bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Delivery</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 max-w-[240px] sm:max-w-xs font-normal">
                            <?= htmlspecialchars($resto_slogan) ?>
                        </p>
                    </div>
                </a>

                <!-- Nav Links Desktop -->
                <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600 dark:text-slate-300">
                    <a href="index.php" class="hover:text-brand-600 dark:hover:text-brand-400 transition flex items-center gap-2 py-2 <?= (!isset($active_nav) || $active_nav == 'menu') ? 'text-brand-600 dark:text-brand-400 font-semibold border-b-2 border-brand-600' : '' ?>">
                        <i class="fa-solid fa-fire text-amber-500"></i> Menu Populer
                    </a>
                    <a href="index.php#katalog" class="hover:text-brand-600 dark:hover:text-brand-400 transition flex items-center gap-2 py-2">
                        <i class="fa-solid fa-book-open"></i> Katalog Lengkap
                    </a>
                    <a href="riwayat.php" class="hover:text-brand-600 dark:hover:text-brand-400 transition flex items-center gap-2 py-2 <?= (isset($active_nav) && $active_nav == 'riwayat') ? 'text-brand-600 dark:text-brand-400 font-semibold border-b-2 border-brand-600' : '' ?>">
                        <i class="fa-solid fa-truck-fast text-blue-500"></i> Lacak Pengantaran
                    </a>
                </nav>

                <!-- Actions: Day/Night Toggle, Cart & CTA -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    
                    <!-- Day / Night Mode Toggle Button (Siang / Malam) -->
                    <button id="theme-toggle" type="button" onclick="toggleTheme()" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition shadow-sm border border-slate-200 dark:border-slate-700" title="Ganti Mode Siang / Malam">
                        <i id="theme-toggle-dark-icon" class="fa-solid fa-moon text-sm hidden"></i>
                        <i id="theme-toggle-light-icon" class="fa-solid fa-sun text-sm hidden text-amber-400 animate-spin-slow"></i>
                    </button>

                    <!-- Cart Button Desktop -->
                    <a href="cart.php" class="relative inline-flex items-center gap-2 px-3.5 sm:px-4 py-2.5 rounded-xl bg-orange-50 hover:bg-orange-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-brand-700 dark:text-brand-400 border border-brand-200/80 dark:border-slate-700 transition shadow-sm font-semibold text-xs sm:text-sm">
                        <i class="fa-solid fa-cart-shopping text-sm sm:text-base text-brand-600 dark:text-brand-400"></i>
                        <span class="hidden sm:inline">Keranjang</span>
                        <span id="cart-badge-count" class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-brand-600 rounded-full">
                            <?= $cart_count ?>
                        </span>
                    </a>

                    <!-- Fast Order / Track button -->
                    <a href="riwayat.php" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-brand-600 dark:hover:bg-brand-700 text-white font-semibold text-sm transition shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Cek Pesanan</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
