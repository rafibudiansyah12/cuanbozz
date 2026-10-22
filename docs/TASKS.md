# TASKS — CuanBozz

Kerjakan berurutan, satu task per branch. Aturan lengkap ada di `AGENTS.md`. Tahap berikutnya boleh dimulai setelah tahap sebelumnya selesai.

Format: **ID — Judul** lalu "Selesai jika".

## Tahap 0 — Fondasi

**T0.1 — Inisialisasi proyek**
Laravel + Breeze (Blade) + Pest + Tailwind + Pint, `.env.example` lengkap (termasuk variabel Google dan Midtrans), README kerangka.
Selesai jika: `composer install`, `php artisan migrate`, `php artisan test`, dan `npm run build` berjalan bersih dari repo baru.

**T0.2 — Layout mobile dan design system**
Layout mobile first dengan bottom navigation (Kasir, Produk, Riwayat, Laporan), komponen dasar (tombol, input, kartu, modal), dan design system token tema melalui CSS variables. Token semantik: `primary-50` sampai `primary-900`, `accent`, `surface`, `text`, `border`, `success`, `warning`, dan `danger`. Mode v1 hanya terang; warna utama default emerald dan teks normal di atasnya memenuhi kontras WCAG AA minimal 4.5:1.
Selesai jika: komponen memakai token semantik tanpa warna hard-coded atau kelas warna Tailwind langsung, layout siap untuk lebar 360 px, dan target sentuh minimal 44 px.

**T0.3 — Layar UI statis**
Buat layar-layar utama dengan data contoh tanpa logika bisnis: login/registrasi, onboarding, dashboard, kasir/keranjang, daftar dan form produk beserta opsi, riwayat/struk, laporan, profil toko/pengaturan tema, dan anggota toko.
Selesai jika: semua layar utama dapat dinavigasi untuk review, terlihat rapi di lebar 360 px, memakai design system T0.2, dan jelas ditandai sebagai data contoh.
Catatan perubahan cakupan: layar "Admin platform" diganti menjadi "Panel developer" dan layar "Menunggu persetujuan" ditambahkan.

## Tahap 1 — Autentikasi dan Toko

**T1.1 — Model toko dan isolasi data**
Tabel `stores` dan `store_members` (role `owner`/`admin`/`cashier`), trait `BelongsToStore` dengan global scope, helper toko aktif.
Selesai jika: ada test yang membuktikan data toko A tidak terlihat dari toko B.

**T1.2 — Login Google**
Tombol "Masuk dengan Google", callback Socialite, buat atau hubungkan user berdasarkan `google_id` dan email, kolom `password` nullable. User baru Google dibuat dengan email terverifikasi dan status `pending`; akun dengan email yang sudah ada terhubung tanpa mengubah `is_developer`.
Selesai jika: user baru via Google login dengan email terverifikasi dan diarahkan ke onboarding/status persetujuan; user lama dengan email sama terhubung ke akun yang sama tanpa duplikat; akun biasa tidak menjadi developer; ada test dengan Socialite yang di-mock.

**T1.2a — Status persetujuan akun dan akun developer**
Migrasi kolom persetujuan user (`approval_status`, `approved_at`, `approved_by`, `rejection_reason`) dan `is_developer`; perintah Artisan `developer:create` yang meminta kata sandi secara interaktif; middleware `EnsureAccountApproved` dan `EnsureDeveloper`; halaman status akun.
Selesai jika: pengguna pending tidak bisa membuka halaman bisnis, approved bisa, kasir dari toko yang pemiliknya suspended terblokir, dan pengguna biasa tidak bisa menjadi developer lewat pendaftaran atau Google (ada test untuk semuanya).

**T1.2b — Panel developer**
Bangun `/developer/*` sesuai PRD 5.7: beranda, pendaftaran (filter/detail/setujui/tolak dengan alasan), daftar toko (pencarian/tangguhkan/aktifkan), daftar pengguna, log aktivitas append-only, dan akun saya (ubah kata sandi). Buat `developer_audit_logs` untuk mencatat setiap keputusan.
Selesai jika: setiap aksi setujui/tolak/tangguhkan/aktifkan mengubah status, tercatat di audit log, dan non-developer mendapat 404 (ada test).

**T1.3 — Onboarding buat toko**
Onboarding pemilik setelah daftar dan verifikasi email (bila memakai email); form nama, alamat, telepon, logo opsional. Setelah toko dibuat, pemilik berstatus pending diarahkan ke halaman status menunggu persetujuan. User yang belum memiliki toko tetap dapat kembali ke onboarding; halaman bisnis hanya dapat diakses setelah persetujuan developer.
Selesai jika: user (email maupun Google) tanpa toko hanya dapat mengakses onboarding, status akun, dan logout; setelah toko dibuat ia melihat status menunggu persetujuan; persetujuan membuka akses aplikasi penuh; akses selain onboarding/status/logout diblokir selama belum approved (test).

**T1.4 — Profil toko**
Pemilik dapat mengubah data toko.
Selesai jika: kasir tidak bisa mengakses halaman ini (403) dan ada test peran.

**T1.5 — Kelola anggota toko**
Pemilik menambah admin toko atau kasir (undang lewat email atau buat akun) dan menonaktifkannya. Admin mengelola produk, stok, transaksi, dan laporan; pengaturan toko dan anggota tetap khusus pemilik.
Selesai jika: admin dan kasir yang dibuat hanya punya akses sesuai peran, terikat ke satu toko, serta status aksesnya mengikuti status pemilik.

**T1.6 — Pengaturan tampilan toko**
Pemilik memilih preset atau memasukkan warna utama dan aksen hex, memilih radius komponen (`md`, `lg`, `xl`), dan melihat preview langsung. Tema disimpan di `stores.theme`, berlaku bagi semua anggota toko, dan hanya mode terang.
Selesai jika: hanya pemilik dapat mengubah tema; tema valid tersimpan dan tampil bagi pemilik, admin, serta kasir di toko yang sama; preview langsung bekerja; warna teks di atas primary lolos rasio kontras WCAG AA minimal 4.5:1; tema kosong memakai primary default emerald; ada test otorisasi dan lintas toko.

## Tahap 2 — Produk dan Stok

**T2.1 — Kategori**
CRUD kategori per toko.
Selesai jika: ada test lintas toko dan nama kategori unik per toko.

**T2.2 — Produk**
CRUD produk: nama, deskripsi, SKU/barcode, kategori, harga (integer), foto, satuan (default `pcs`), stok, ambang stok, dan `track_stock` (default true).
Selesai jika: semua field produk tersimpan; kasir hanya bisa melihat produk, tidak bisa mengubah harga; produk `track_stock=false` tidak divalidasi/diperingatkan berdasarkan stok; test lintas toko lulus.

**T2.3 — Pergerakan stok dan log aktivitas toko**
Tabel `stock_movements`, penyesuaian stok manual oleh pemilik/admin/kasir dengan alasan, riwayat per produk dan pelaku; log aktivitas toko untuk penambahan produk, penyesuaian stok, dan transaksi.
Selesai jika: setiap perubahan stok produk yang dilacak tercatat dan stoknya tidak bisa menjadi minus; produk tanpa pelacakan stok tidak menghasilkan pergerakan; kasir hanya dapat menyesuaikan stok di tokonya dan tidak bisa mengubah produk/harga; owner dapat melihat akun pelaku dan aktivitas terbaru di tokonya; aktivitas antar toko terisolasi dan log append-only.

**T2.4 — Pencarian dan indikator stok**
Pencarian, filter kategori, dan penanda stok menipis.
Selesai jika: produk yang melacak stok dan berada di bawah ambang ditandai jelas di daftar dan di layar kasir.

**T2.5 — Opsi produk**
Kelola grup opsi per produk (`single` atau `multiple`) beserta opsi, selisih harga bertanda, status aktif, urutan, dan batas jumlah pilihan.
Selesai jika: aturan required/min/max divalidasi; opsi dan grup selalu terisolasi per toko; opsi nonaktif tidak dapat dipilih pada transaksi baru; ada test lintas toko.

## Tahap 3 — Transaksi Tunai

**T3.1 — Order dan item order**
Migrasi dan model `orders`, `order_items`, dan `order_item_options` (snapshot nama produk, opsi, selisih harga, dan harga satuan final), nomor order unik per toko, enum status.
Selesai jika: perubahan harga produk atau opsi tidak mengubah riwayat order lama (ada test).

**T3.2 — Layar kasir**
Grid produk, pencarian, keranjang (tambah, ubah jumlah, hapus), pilih opsi produk, catatan per item, dan diskon nominal atau persen.
Selesai jika: satu transaksi bisa disusun dalam kurang dari 30 detik di HP, jumlah opsi sesuai aturan grup, catatan tersimpan, dan total memakai harga dasar ditambah selisih harga opsi terpilih.

**T3.3 — Checkout tunai**
Input nama pelanggan dan nomor meja opsional, uang diterima, kembalian otomatis, snapshot item/opsi/catatan, dan checkout atomik. Stok produk `track_stock=true` diproses dalam satu transaksi database dengan `lockForUpdate`; produk `track_stock=false` tidak mengurangi stok dan tidak membuat `stock_movements`.
Selesai jika: dua checkout bersamaan untuk stok terakhir tidak membuat stok minus; produk tanpa pelacakan stok dapat dibeli tanpa mutasi stok; total, snapshot opsi, dan catatan benar (ada test).

**T3.4 — Struk digital**
Halaman struk lewat signed URL, menampilkan snapshot item beserta opsi/catatan dan metadata pelanggan/meja, dengan tombol bagikan ke WhatsApp dan cetak dari browser. Checkout tunai membuka struk setelah pembayaran berhasil.
Selesai jika: struk bisa dibuka tanpa login lewat tautan bertanda tangan dan tidak bisa ditebak; snapshot, metadata pelanggan/meja, dan catatan tampil sesuai transaksi.

**T3.5 — Pembatalan transaksi**
Pemilik membatalkan order dengan alasan; stok kembali dan tercatat di `stock_movements`.
Selesai jika: kasir tidak bisa membatalkan dan pembatalan ganda ditolak; stok hanya kembali dan dicatat untuk item yang produknya melacak stok.

## Tahap 4 — Pembayaran Nontunai

**T4.1 — Konfigurasi Midtrans dan tabel pembayaran**
Tabel `payments`, service wrapper Midtrans, konfigurasi dari `.env`.
Selesai jika: service bisa dibuat tanpa memanggil jaringan di test (di-mock).

**T4.2 — Checkout nontunai**
Buat order `pending`, minta token Snap atau QRIS, tampilkan pembayaran di layar kasir.
Selesai jika: stok belum berkurang saat order masih `pending`.

**T4.3 — Webhook**
`POST /webhooks/midtrans` dengan verifikasi `signature_key`, idempoten, mengubah status dan mengurangi stok saat `paid`.
Selesai jika: signature salah ditolak, notifikasi ganda tidak mengurangi stok dua kali, hanya produk yang melacak stok yang berubah, dan semua kasus status (paid, expired, failed) punya test.

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

**T6.2 — Penyempurnaan panel developer**
Penyempurnaan alur dan antarmuka Panel Developer setelah T1.2b berdasarkan hasil uji dan kebutuhan operasional; tidak menggantikan kontrol persetujuan maupun audit log di T1.2b.
Selesai jika: temuan penyempurnaan yang disepakati selesai tanpa mengurangi proteksi rute developer atau sifat append-only audit log.

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
