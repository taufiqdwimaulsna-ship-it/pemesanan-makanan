# 🛵 Dapur Lezat Express - Aplikasi Pemesanan Makanan Delivery

Aplikasi Web Pemesanan Makanan Delivery (*Food Delivery Order & Tracking System*) yang dirancang khusus untuk **Presentasi Tugas Akhir / Sidang Akademik dan Portofolio Teknis**. 

Aplikasi ini menonjolkan 3 keunggulan utama:
1. **Desain Antarmuka (UI/UX) Modern & Menggugah Selera**: Katalog menu bersih, filter kategori interaktif, modal catatan khusus ke dapur, dan responsive sticky floating cart di mobile.
2. **Alur Checkout & Pemesanan yang Mulus**: Formulir alamat terstruktur (dengan patokan lokasi & catatan kurir), pilihan pembayaran tunai (COD), QRIS, maupun Transfer Bank.
3. **Simulasi Status Pengantaran Real-time (Fitur Unggulan Portofolio)**: Visual Stepper 4 tahap dengan status progres dinamis, countdown estimasi kedatangan kurir, rincian driver & plat nomor, serta **Mode Simulasi 1-Klik / Auto-Play Demo** khusus untuk demonstrasi pengujian di depan penguji atau klien.
4. **Portal Manajemen Resto & Dapur (Admin)**: Dashboard analitik omset harian, antrian pesanan live, CRUD menu makanan (dengan preset foto HD), cetak struk kasir thermal 58mm/80mm, dan konfigurasi ongkir.

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.x Native (Clean Structured Code, PDO MySQL, Session Management, Prepared Statements)
- **Database**: MySQL / MariaDB (`db_pemesanan_makanan`)
- **Frontend / Styling**: Tailwind CSS, FontAwesome 6 Icons, Google Fonts (*Plus Jakarta Sans*)
- **Interactivity**: Vanilla JavaScript, Chart.js, SweetAlert2
- **Printing**: Clean Thermal Struk Layout (kompatibel printer kasir & PDF export)

---

## 🚀 Fitur Lengkap Aplikasi

### 1. Fitur Mode Siang & Malam (Light / Dark Mode) ☀️🌙
- **Universal Theme Toggle**: Tombol pengubah tema di header navigasi, menu mobile, footer, serta dashboard admin.
- **Penyimpanan Preferensi (localStorage)**: Pilihan tema pengguna tersimpan otomatis di browser tanpa reload hilang.
- **Bebas Flicker (Zero FOUC)**: Script inisialisasi awal mencegah kedipan putih saat memuat halaman dalam mode malam.
- **Kontras Sesuai Standar**: Palet warna dark mode yang elegan (`slate-950` / `slate-900`) menjaga kenyamanan mata pelanggan saat memesan makanan di malam hari.

### 2. Sisi Pelanggan (Customer Frontend)
- **Hero Banner & Brand Resto**: Informasi jam operasional, jaminan kebersihan, dan promo delivery.
- **Katalog Menu Interaktif**:
  - Filter kategori cepat (Makanan Utama, Ayam & Bebek, Snack, Minuman Segar, Paket Hemat).
  - Fitur pencarian menu secara langsung (real-time).
  - Sorting urutan menu (Paling Populer, Promo Diskon, Harga Terendah, Harga Tertinggi).
  - Badge diskon promo & best seller.
  - Modal detail menu untuk menentukan porsi dan catatan khusus (misal: *"tidak pakai seledri, cabai dipisah"*).
- **Keranjang Belanja (Interactive Cart)**:
  - Sticky floating cart bar otomatis muncul di bagian bawah ketika ada item.
  - Pengaturan jumlah (tambah / kurang) via AJAX tanpa refresh berlebih.
  - Penghitungan subtotal menu & ongkir flat otomatis.
- **Alur Checkout Terstruktur**:
  - Input Nama Lengkap & Nomor WhatsApp aktif.
  - Alamat pengantaran lengkap, patokan lokasi, serta instruksi khusus untuk driver.
  - Pilihan metode pembayaran: **COD (Bayar di Tempat)**, **QRIS**, atau **Transfer Bank**.
- **Pelacakan Pengantaran (Delivery Tracking Page)**:
  - Stepper visual 4 tahap: `1. Menunggu Konfirmasi` ➔ `2. Dimasak di Dapur` ➔ `3. Dalam Pengantaran Kurir` ➔ `4. Selesai / Tiba`.
  - Kartu identitas kurir lengkap dengan nama, nomor plat kendaraan motor, dan tombol chat WhatsApp driver langsung.
  - Countdown timer estimasi waktu pengantaran.
  - **Toolbox Mode Simulasi Pengantaran**: Tombol demo cepat untuk menguji perubahan status seketika (sangat efektif untuk presentasi dosen / penguji).
- **Riwayat & Cek Pesanan**:
  - Cek status pesanan kapan saja hanya dengan memasukkan Nomor Invoice atau Nomor WhatsApp pemesan.

---

### 2. Sisi Dapur & Administrator (Admin Portal)
- **Autentikasi Aman**: Login admin terproteksi sesi session PHP.
- **Dashboard Analitik**:
  - Kartu metrik: Total Pendapatan / Omset, Total Pesanan Selesai, Antrian Pesanan Aktif, dan Total Menu.
  - Grafik distribusi status pesanan berbasis **Chart.js**.
  - Tabel antrian pesanan dapur dengan tombol aksi cepat (*Quick Action: Terima Masak ➔ Kirim Kurir ➔ Selesai*).
- **Manajemen Pesanan Delivery**:
  - Filter pesanan berdasarkan status (`Menunggu`, `Dimasak`, `Diantar`, `Selesai`, `Dibatalkan`).
  - Update status pesanan dan status pembayaran (*Lunas* / *Belum Bayar*).
  - Akses langsung ke live tracking dan cetak nota struk kasir.
- **CRUD Menu Makanan & Minuman**:
  - Tambah menu baru dengan kategori, deskripsi, harga, stok, dan URL foto HD.
  - Tombol preset gambar HD Unsplash siap pakai (*Nasi Goreng, Ayam Geprek, Es Kopi, dll*).
  - Fitur promo diskon bertingkat (persentase otomatis memotong harga katalog).
  - Status ketersediaan menu (*Tersedia* / *Habis*).
  - Edit & Hapus menu dengan konfirmasi pop-up.
- **Cetak Struk Kasir Thermal (58mm / 80mm)**:
  - Layout struk nota belanja siap cetak dengan CSS `@media print` untuk printer kasir thermal maupun disimpan sebagai PDF.
- **Pengaturan Profil Restoran**:
  - Ubah nama resto, slogan, nomor WhatsApp resmi, alamat outlet, tarif ongkos kirim default, dan jam buka.

---

## 🔑 Akun Demo Administrator

| Role | Email | Password |
|---|---|---|
| **Administrator Resto / Dapur** | `admin@dapurlezat.com` | `admin123` |

Akses portal admin dapat dibuka melalui URL:
`http://localhost/pemesanan_makanan/admin/login.php`

---

## 🗄️ Struktur Database (`db_pemesanan_makanan`)

1. **`users`**: Data admin & pengguna (id, nama, email, password hash bcrypt, no_hp, role).
2. **`categories`**: Kategori menu makanan & minuman (id, nama_kategori, slug, icon, urutan).
3. **`menus`**: Katalog hidangan (id, category_id, nama_menu, deskripsi, harga, stok, gambar, is_popular, is_promo, diskon_persen, status).
4. **`orders`**: Data transaksi pesanan delivery (id, no_invoice, customer_name, customer_phone, customer_address, patokan, delivery_notes, payment_method, payment_status, order_status, subtotal, ongkir, total_bayar, driver_name, driver_phone, driver_plate, estimasi_waktu, created_at, updated_at).
5. **`order_items`**: Rincian item dalam pesanan (id, order_id, menu_id, nama_menu, harga_satuan, qty, catatan, subtotal).
6. **`settings`**: Konfigurasi global resto (setting_key, setting_value).

---

## 💻 Cara Menjalankan di XAMPP

1. Pastikan folder proyek berada di:
   `C:\xampp\htdocs\pemesanan_makanan`
2. Jalankan modul **Apache** dan **MySQL** di panel XAMPP.
3. Database `db_pemesanan_makanan` telah dibuat dan dimigrasi secara otomatis beserta data sampel lengkap.
4. Buka browser:
   - **Halaman Pelanggan (Katalog & Delivery)**: [http://localhost/pemesanan_makanan/](http://localhost/pemesanan_makanan/)
   - **Halaman Lacak Pesanan Aktif (Sample Demo)**: [http://localhost/pemesanan_makanan/tracking.php?inv=INV-20261020-001](http://localhost/pemesanan_makanan/tracking.php?inv=INV-20261020-001)
   - **Halaman Admin Resto**: [http://localhost/pemesanan_makanan/admin/login.php](http://localhost/pemesanan_makanan/admin/login.php)

