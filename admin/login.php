<?php
// admin/login.php
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['admin_user'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Dapur Lezat Delivery</title>
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
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-900 dark:bg-black text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4 transition-colors relative">

    <!-- Day / Night Toggle Top Right -->
    <button type="button" onclick="toggleTheme()" class="absolute top-5 right-5 w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition border border-slate-700" title="Ganti Mode Siang / Malam">
        <i id="theme-icon" class="fa-solid fa-moon"></i>
    </button>

    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center text-2xl mx-auto shadow-lg shadow-orange-500/30">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Portal Restoran & Admin</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Kelola pesanan delivery dan menu restoran secara real-time.</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="p-3.5 bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Administrator</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-xs text-slate-400"></i>
                    <input type="email" name="email" id="input-email" required value="admin@dapurlezat.com" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl pl-9 pr-4 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-orange-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-xs text-slate-400"></i>
                    <input type="password" name="password" id="input-password" required value="admin123" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl pl-9 pr-4 py-2.5 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-orange-500">
                </div>
            </div>

            <!-- Demo Hint Box -->
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-[11px] text-amber-800 dark:text-amber-300 space-y-1.5">
                <div class="flex items-center justify-between">
                    <p class="font-bold flex items-center gap-1">
                        <i class="fa-solid fa-key"></i> Akun Demo Sidang / Presentasi:
                    </p>
                    <button type="button" onclick="autoFillDemo()" class="px-2 py-0.5 rounded bg-amber-200 dark:bg-amber-800 text-amber-900 dark:text-amber-100 hover:bg-amber-300 font-bold text-[10px] transition">
                        Klik Isi Otomatis
                    </button>
                </div>
                <p>Email: <span class="font-mono font-bold">admin@dapurlezat.com</span></p>
                <p>Password: <span class="font-mono font-bold">admin123</span></p>
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-orange-500/25 transition text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk ke Dashboard</span>
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <a href="../index.php" class="text-xs text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition font-semibold flex items-center justify-center gap-1">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali ke Halaman Pelanggan</span>
            </a>
        </div>

    </div>

    <script>
        function autoFillDemo() {
            document.getElementById('input-email').value = 'admin@dapurlezat.com';
            document.getElementById('input-password').value = 'admin123';
        }

        function updateIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = isDark ? 'fa-solid fa-sun text-amber-400' : 'fa-solid fa-moon text-slate-300';
            }
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateIcon();
        }

        document.addEventListener('DOMContentLoaded', updateIcon);
    </script>
</body>
</html>
