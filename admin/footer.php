<?php
// admin/footer.php
?>
        </main>

        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-4 px-8 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400 dark:text-slate-500 gap-2 transition-colors">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(getSetting('nama_resto', 'Dapur Lezat Express')) ?> • Panel Manajemen Restoran</p>
            <div class="flex items-center gap-3">
                <span>Fitur Delivery & Tracking</span>
                <span class="text-slate-600 dark:text-slate-700">•</span>
                <button type="button" onclick="toggleAdminTheme()" class="hover:text-brand-500 transition">
                    <i class="fa-solid fa-circle-half-stroke text-amber-400 mr-1"></i> Mode Siang / Malam
                </button>
            </div>
        </footer>

    </div>

</body>
</html>
