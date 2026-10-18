# PRD: CuanBozz, Aplikasi Kasir Multi-Toko (SaaS POS)

Versi 0.2 · Status: Draft · Jenis: Proyek portfolio

Dokumen terkait: `AGENTS.md` (aturan agent), `docs/ERD.md` (skema database), `docs/TASKS.md` (daftar task).

## 1. Ringkasan

CuanBozz adalah aplikasi kasir berbasis web yang dapat dipakai oleh banyak pemilik usaha sekaligus. Setiap pengguna mendaftar (email atau akun Google), membuat toko sendiri (nama, alamat, logo), lalu mengelola produk, stok, dan transaksi. Data tiap toko terisolasi penuh dari toko lain (multi-tenant). Pembayaran nontunai diterima melalui payment gateway (QRIS, e-wallet, virtual account), dan seluruh antarmuka dirancang mobile first agar nyaman dipakai kasir lewat HP.

## 2. Latar Belakang dan Masalah

Banyak UMKM masih mencatat penjualan manual atau memakai aplikasi kasir yang mahal dan rumit. Kebutuhan mereka sederhana: mencatat penjualan dengan cepat, tahu sisa stok, menerima pembayaran QRIS tanpa ribet, dan melihat omzet harian. CuanBozz menjawab kebutuhan itu dengan aplikasi ringan yang bisa langsung dipakai dari HP.

## 3. Tujuan dan Non-Tujuan

### Tujuan

- Pengguna dapat mendaftar dan mulai berjualan dalam waktu kurang dari 5 menit.
- Kasir dapat menyelesaikan satu transaksi dalam kurang dari 30 detik.
- Data antar toko terisolasi: toko A tidak pernah dapat mengakses data toko B.
- Pembayaran nontunai tercatat otomatis tanpa input manual.
- Aplikasi nyaman dipakai di layar HP (lebar 360 px ke atas).

### Non-Tujuan (versi 1)

- Multi-cabang dalam satu toko
- Manajemen supplier dan pembelian barang
- Pajak kompleks dan akuntansi penuh
- Integrasi printer thermal langsung
- Aplikasi native Android/iOS
- Mode offline penuh

## 4. Target Pengguna

| Persona | Deskripsi | Kebutuhan utama |
|---|---|---|
| Pemilik toko | Pemilik warung, kafe kecil, atau toko online | Melihat omzet, mengelola produk, mengatur kasir |
| Kasir | Karyawan yang melayani pembeli | Input transaksi cepat, terima pembayaran |
| Admin platform | Pengelola aplikasi (pembuat proyek) | Memantau pengguna dan toko |

## 5. Ruang Lingkup Fitur

### 5.1 Autentikasi dan Onboarding

- Registrasi dengan email dan kata sandi (verifikasi email) atau dengan akun Google.
- Login (email atau Google), logout, dan lupa kata sandi. Akun yang dibuat lewat Google tidak wajib punya kata sandi, dan email Google yang sudah terdaftar dihubungkan ke akun yang sama.
- Setelah registrasi, pengguna diarahkan membuat toko: nama toko, alamat, nomor telepon, logo (opsional).
- Satu pemilik memiliki satu toko pada versi 1.

### 5.2 Manajemen Toko dan Anggota

- Pemilik dapat mengubah profil toko.
- Pemilik dapat menambah kasir (undang lewat email atau buat akun langsung) dan menonaktifkannya.
- Role: **Pemilik** (akses penuh) dan **Kasir** (hanya transaksi dan lihat produk). Satu kasir terikat ke satu toko.

### 5.3 Produk dan Stok

- CRUD produk: nama, SKU/barcode, kategori, harga jual, foto, stok, ambang stok menipis.
- CRUD kategori.
- Stok berkurang otomatis saat transaksi selesai dan kembali saat transaksi dibatalkan.
- Penyesuaian stok manual disertai alasan dan tercatat di riwayat pergerakan stok.
- Peringatan stok menipis berdasarkan ambang batas per produk.
- Pencarian produk dan filter kategori.

### 5.4 Transaksi Kasir (POS)

- Layar kasir berisi daftar produk dan keranjang.
- Tambah, ubah jumlah, dan hapus item di keranjang.
- Diskon per transaksi (nominal atau persen).
- Metode pembayaran: **Tunai** dan **Nontunai (payment gateway)**.
- Pembayaran tunai: input uang diterima, kembalian dihitung otomatis.
- Struk digital: halaman struk lewat tautan bertanda tangan, bagikan via WhatsApp, atau cetak dari browser.
- Pembatalan transaksi oleh pemilik dengan alasan.

### 5.5 Integrasi Payment Gateway

- Penyedia: **Midtrans Snap** (mode sandbox untuk pengembangan dan demo).
- Metode yang didukung: QRIS, e-wallet, virtual account bank.
- Alur pembayaran:
  1. Kasir memilih pembayaran nontunai, sistem membuat order berstatus `pending`.
  2. Sistem meminta tagihan ke Midtrans dan menampilkan pembayaran (QRIS atau nomor VA).
  3. Pembeli membayar.
  4. Midtrans mengirim notifikasi (webhook) ke server.
  5. Server memverifikasi `signature_key`, mengubah status menjadi `paid`, dan mengurangi stok.
  6. Layar kasir memperbarui status secara otomatis (polling).
- Status order: `pending`, `paid`, `expired`, `failed`, `cancelled`.
- Tagihan kedaluwarsa otomatis setelah batas waktu (15 menit).
- Webhook bersifat idempoten: notifikasi ganda tidak membuat pencatatan atau pengurangan stok ganda.

### 5.6 Laporan dan Dashboard

- Ringkasan hari ini: omzet, jumlah transaksi, rata-rata nilai transaksi (hanya order `paid`).
- Grafik penjualan 7 hari dan 30 hari.
- Produk terlaris.
- Riwayat transaksi dengan filter tanggal, metode bayar, dan status. Kasir hanya melihat transaksi miliknya.
- Export laporan ke CSV/Excel (opsional, versi 1.1).

### 5.7 Panel Admin Platform (sederhana)

- Daftar pengguna dan toko terdaftar.
- Menonaktifkan toko atau akun bermasalah.

## 6. Kebutuhan Mobile Friendly

- Desain **mobile first**: semua layar utama dapat dipakai penuh di lebar 360 px.
- Navigasi bawah (bottom navigation) untuk kasir: Kasir, Produk, Riwayat, Laporan.
- Target sentuh minimal 44 px, tombol bayar mudah dijangkau ibu jari.
- Layar kasir menampilkan produk dalam grid dan keranjang sebagai panel yang dapat ditarik ke atas.
- Dapat dipasang ke layar utama HP (PWA): manifest, ikon, dan tampilan layar penuh.
- Pemindaian barcode memakai kamera HP (opsional, versi 1.1).
- Performa: halaman utama termuat di bawah 3 detik pada jaringan 4G.
- Saat koneksi putus, tampilkan pesan jelas dan cegah data hilang.

## 7. Kebutuhan Non-Fungsional

### Keamanan

- Isolasi data per toko: setiap tabel bisnis memiliki `store_id`, semua query difilter otomatis (global scope), dan akses diperiksa lewat policy.
- Kata sandi di-hash, proteksi CSRF, validasi input, dan rate limiting pada login.
- Verifikasi tanda tangan webhook payment gateway.
- Kunci API (Midtrans, Google) disimpan di environment variable, tidak masuk repositori.
- Seluruh akses melalui HTTPS.

### Konsistensi data

- Pembuatan order, pengurangan stok, dan pencatatan pergerakan stok dilakukan dalam satu transaksi database.
- Penguncian baris (`lockForUpdate`) agar stok tidak minus saat dua kasir menjual barang yang sama.
- Uang disimpan sebagai integer rupiah.

### Ketersediaan dan performa

- Respons rata-rata di bawah 500 ms untuk operasi umum.
- Backup database harian.

## 8. Alur Pengguna Utama

1. **Onboarding**: daftar (email atau Google), buat toko, tambah produk pertama.
2. **Penjualan tunai**: pilih produk, cek keranjang, pilih tunai, input uang diterima, selesai, kirim struk.
3. **Penjualan QRIS**: pilih produk, pilih nontunai, tampilkan QR, pembeli membayar, status otomatis lunas, struk terbit.
4. **Pemantauan pemilik**: buka dashboard, lihat omzet hari ini, cek stok menipis.

## 9. Model Data

Skema lengkap ada di `docs/ERD.md`. Ringkasan tabel: `users`, `stores`, `store_members`, `categories`, `products`, `stock_movements`, `orders`, `order_items`, `payments`.

## 10. Tech Stack

| Lapisan | Pilihan |
|---|---|
| Framework | Laravel |
| Auth | Laravel Breeze (Blade) + Laravel Socialite (Google) |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Database | MySQL (dev dan produksi), SQLite in-memory (test) |
| Test | Pest |
| Payment gateway | Midtrans Snap (sandbox) |
| Grafik | Chart.js |

## 11. Metrik Keberhasilan

Karena ini proyek portfolio, keberhasilan diukur dari kelengkapan dan kualitas demo:

- Aplikasi ter-deploy dan dapat dicoba publik, lengkap dengan akun demo.
- Login email dan Google berjalan.
- Alur pembayaran QRIS sandbox berjalan dari awal hingga status lunas otomatis.
- Pengujian membuktikan toko A tidak dapat mengakses data toko B.
- README memuat latar masalah, keputusan arsitektur, diagram, dan tangkapan layar atau video demo.
- Skor Lighthouse mobile minimal 80.

## 12. Rencana Rilis

Rincian task per tahap ada di `docs/TASKS.md`.

| Tahap | Cakupan | Hasil |
|---|---|---|
| 0 | Fondasi proyek dan layout mobile | Kerangka siap dikembangkan |
| 1 | Auth (email dan Google), toko, isolasi data | Pengguna bisa daftar dan punya toko |
| 2 | Produk, kategori, stok | Katalog siap dipakai |
| 3 | Transaksi tunai dan struk | Versi pertama dapat didemokan |
| 4 | Payment gateway dan webhook | Pembayaran QRIS/e-wallet berjalan |
| 5 | Dashboard dan laporan | Pemilik melihat omzet dan produk terlaris |
| 6 | PWA, panel admin, audit mobile dan keamanan | Siap dipamerkan |
| 7 | Data demo, deploy, dokumentasi | Siap masuk portfolio |

## 13. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Cakupan membengkak | Proyek tidak selesai | Patuhi daftar non-tujuan, selesaikan tahap berurutan |
| Kebocoran data antar toko | Cacat keamanan serius | Global scope, policy, dan test lintas toko di setiap fitur |
| Webhook terlambat atau ganda | Status atau stok keliru | Idempotensi, verifikasi tanda tangan, cek status ke gateway |
| Aktivasi gateway produksi butuh dokumen usaha | Tidak bisa transaksi nyata | Gunakan sandbox untuk demo dan jelaskan di README |
| Stok minus karena transaksi bersamaan | Data tidak akurat | Row locking dan transaksi database |
| Kunci API bocor di repo publik | Penyalahgunaan akun | `.env` di `.gitignore`, kunci dibuat ulang bila sempat ter-commit |

## 14. Keputusan dan Pertanyaan Terbuka

**Sudah diputuskan**

- Nama produk: CuanBozz
- Payment gateway: Midtrans (sandbox)
- Satu pemilik satu toko pada versi 1
- Login Google termasuk versi 1

**Masih terbuka**

- Apakah perlu paket langganan (gratis vs premium) sebagai simulasi SaaS?
- Apakah struk perlu bisa dicetak ke printer thermal Bluetooth (saat ini non-tujuan)?
- Domain yang dipakai untuk deploy.
