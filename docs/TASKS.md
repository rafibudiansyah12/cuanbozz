# TASKS — CuanBozz

Kerjakan berurutan, satu task per branch. Aturan lengkap ada di `AGENTS.md`. Tahap berikutnya boleh dimulai setelah tahap sebelumnya selesai.

Format: **ID — Judul** lalu "Selesai jika".

## Tahap 0 — Fondasi

**T0.1 — Inisialisasi proyek**
Laravel + Breeze (Blade) + Pest + Tailwind + Pint, `.env.example` lengkap (termasuk variabel Google dan Midtrans), README kerangka.
Selesai jika: `composer install`, `php artisan migrate`, `php artisan test`, dan `npm run build` berjalan bersih dari repo baru.

**T0.2 — Layout mobile**
Layout aplikasi mobile first dengan bottom navigation (Kasir, Produk, Riwayat, Laporan), komponen dasar (tombol, input, kartu, modal).
Selesai jika: semua halaman contoh tampil rapi di lebar 360 px dan tombol berukuran sentuh minimal 44 px.

## Tahap 1 — Autentikasi dan Toko

**T1.1 — Model toko dan isolasi data**
Tabel `stores` dan `store_members` (role `owner`/`cashier`), trait `BelongsToStore` dengan global scope, helper toko aktif.
Selesai jika: ada test yang membuktikan data toko A tidak terlihat dari toko B.

**T1.2 — Login Google**
Tombol "Masuk dengan Google", callback Socialite, buat atau hubungkan user berdasarkan `google_id` dan email, kolom `password` nullable.
Selesai jika: user baru via Google langsung login dengan email terverifikasi; user lama dengan email sama terhubung ke akun yang sama tanpa duplikat; ada test dengan Socialite yang di-mock.

**T1.3 — Onboarding buat toko**
Middleware yang mengarahkan user tanpa toko ke `/onboarding/store`; form nama, alamat, telepon, logo opsional.
Selesai jika: user (email maupun Google) tanpa toko tidak bisa membuka halaman lain sebelum membuat toko.

**T1.4 — Profil toko**
Pemilik dapat mengubah data toko.
Selesai jika: kasir tidak bisa mengakses halaman ini (403) dan ada test peran.

**T1.5 — Kelola kasir**
Pemilik menambah kasir (undang lewat email atau buat akun) dan menonaktifkannya.
Selesai jika: kasir yang dibuat hanya punya akses sesuai peran, dan terikat ke satu toko.

## Tahap 2 — Produk dan Stok

**T2.1 — Kategori**
CRUD kategori per toko.
Selesai jika: ada test lintas toko dan nama kategori unik per toko.

**T2.2 — Produk**
CRUD produk: nama, SKU/barcode, kategori, harga (integer), foto, stok, ambang stok.
Selesai jika: kasir hanya bisa melihat produk, tidak bisa mengubah harga; test lintas toko lulus.

**T2.3 — Pergerakan stok**
Tabel `stock_movements`, penyesuaian stok manual dengan alasan, riwayat per produk.
Selesai jika: setiap perubahan stok tercatat dan stok tidak bisa menjadi minus.

**T2.4 — Pencarian dan indikator stok**
Pencarian, filter kategori, dan penanda stok menipis.
Selesai jika: produk di bawah ambang stok ditandai jelas di daftar dan di layar kasir.

## Tahap 3 — Transaksi Tunai

**T3.1 — Order dan item order**
Migrasi dan model `orders`, `order_items` (snapshot nama dan harga), nomor order unik per toko, enum status.
Selesai jika: perubahan harga produk tidak mengubah riwayat order lama (ada test).

**T3.2 — Layar kasir**
Grid produk, pencarian, keranjang (tambah, ubah jumlah, hapus), diskon nominal atau persen.
Selesai jika: satu transaksi bisa disusun dalam kurang dari 30 detik di HP dan total dihitung benar.

**T3.3 — Checkout tunai**
Input uang diterima, kembalian otomatis, order dan stok diproses dalam satu transaksi database dengan `lockForUpdate`.
Selesai jika: dua checkout bersamaan untuk stok terakhir tidak membuat stok minus (ada test).

**T3.4 — Struk digital**
Halaman struk lewat signed URL, tombol bagikan ke WhatsApp dan cetak dari browser.
Selesai jika: struk bisa dibuka tanpa login lewat tautan bertanda tangan dan tidak bisa ditebak.

**T3.5 — Pembatalan transaksi**
Pemilik membatalkan order dengan alasan; stok kembali dan tercatat di `stock_movements`.
Selesai jika: kasir tidak bisa membatalkan dan pembatalan ganda ditolak.

## Tahap 4 — Pembayaran Nontunai

**T4.1 — Konfigurasi Midtrans dan tabel pembayaran**
Tabel `payments`, service wrapper Midtrans, konfigurasi dari `.env`.
Selesai jika: service bisa dibuat tanpa memanggil jaringan di test (di-mock).

**T4.2 — Checkout nontunai**
Buat order `pending`, minta token Snap atau QRIS, tampilkan pembayaran di layar kasir.
Selesai jika: stok belum berkurang saat order masih `pending`.

**T4.3 — Webhook**
`POST /webhooks/midtrans` dengan verifikasi `signature_key`, idempoten, mengubah status dan mengurangi stok saat `paid`.
Selesai jika: signature salah ditolak, notifikasi ganda tidak mengurangi stok dua kali, dan semua kasus status (paid, expired, failed) punya test.

**T4.4 — Status otomatis di layar kasir**
Layar kasir memperbarui status pembayaran otomatis (polling ringan).
Selesai jika: setelah pembeli membayar di sandbox, layar berpindah ke "Lunas" tanpa refresh manual.

**T4.5 — Kedaluwarsa**
Scheduled command yang menandai order `pending` melewati batas waktu (15 menit) menjadi `expired`.
Selesai jika: order kedaluwarsa tidak bisa dibayar dan stok tidak berubah.

## Tahap 5 — Laporan

**T5.1 — Ringkasan hari ini**
Omzet, jumlah transaksi, rata-rata nilai transaksi.
Selesai jika: hanya order `paid` yang dihitung dan hanya data toko aktif.

**T5.2 — Grafik penjualan**
Grafik 7 hari dan 30 hari (Chart.js).
Selesai jika: grafik terbaca di layar 360 px.

**T5.3 — Produk terlaris dan riwayat**
Produk terlaris, riwayat transaksi dengan filter tanggal, metode bayar, dan status.
Selesai jika: kasir hanya melihat transaksi miliknya, pemilik melihat semua.

## Tahap 6 — Penyempurnaan

**T6.1 — PWA**
Manifest, ikon, tampilan layar penuh, dapat dipasang ke layar utama.
Selesai jika: bisa "Add to Home Screen" di Android Chrome.

**T6.2 — Panel admin platform**
Daftar pengguna dan toko, nonaktifkan toko atau akun.
Selesai jika: hanya `is_admin` yang bisa masuk; toko nonaktif tidak bisa dipakai login kasir.

**T6.3 — Audit mobile dan performa**
Periksa semua layar di 360 px, optimasi gambar, cek Lighthouse mobile.
Selesai jika: skor Lighthouse mobile minimal 80.

**T6.4 — Tinjauan keamanan**
Periksa rate limiting, otorisasi semua route, CSRF, dan pengecualian webhook; cek tidak ada secret di repo.
Selesai jika: ada daftar route beserta peran yang berhak, dan semua route bisnis punya test akses.

## Tahap 7 — Rilis

**T7.1 — Data demo**
Seeder dan akun demo (pemilik dan kasir) dengan produk dan transaksi contoh.
Selesai jika: `php artisan migrate:fresh --seed` di lokal menghasilkan demo yang siap dicoba.

**T7.2 — Deploy**
Deploy ke server publik dengan HTTPS, `.env` produksi, scheduler dan queue berjalan.
Selesai jika: tautan publik bisa dipakai login (email dan Google) dan pembayaran sandbox berjalan.

**T7.3 — README dan demo**
README berisi masalah yang diselesaikan, arsitektur multi-tenant, diagram, cara menjalankan, tangkapan layar, dan video demo singkat.
Selesai jika: orang yang belum mengenal proyek bisa memahami dan mencobanya dalam 5 menit.
