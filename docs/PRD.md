# PRD: CuanBozz, Aplikasi Kasir Multi-Toko (SaaS POS)

Versi 0.3 · Status: Draft · Jenis: Proyek portfolio

Dokumen terkait: `AGENTS.md` (aturan agent), `docs/ERD.md` (skema database), `docs/TASKS.md` (daftar task).

## 1. Ringkasan

CuanBozz adalah aplikasi kasir berbasis web yang dapat dipakai oleh banyak pemilik usaha sekaligus. Setiap pengguna mendaftar (email atau akun Google), membuat toko sendiri (nama, alamat, logo), mengatur tema toko, lalu mengelola produk, stok, opsi produk, dan transaksi. Data tiap toko terisolasi penuh dari toko lain (multi-tenant). Pembayaran nontunai diterima melalui payment gateway (QRIS, e-wallet, virtual account), dan seluruh antarmuka dirancang mobile first agar nyaman dipakai kasir lewat HP.

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
| Admin toko | Staf kepercayaan pemilik | Mengelola produk, stok, transaksi, dan melihat laporan keuangan |
| Kasir | Karyawan yang melayani pembeli | Input transaksi cepat, terima pembayaran |
| Developer | Pengelola platform | Meninjau pendaftaran, mengelola toko/pengguna, dan melihat log keputusan |

## 5. Ruang Lingkup Fitur

### 5.1 Autentikasi dan Onboarding

- Registrasi dengan email dan kata sandi (wajib verifikasi email) atau dengan akun Google. Akun Google baru juga memiliki status persetujuan `pending`; akun dari jalur pendaftaran maupun Google tidak pernah menjadi developer.
- Login (email atau Google), logout, dan lupa kata sandi. Akun yang dibuat lewat Google tidak wajib punya kata sandi, dan email Google yang sudah terdaftar dihubungkan ke akun yang sama.
- Setelah registrasi dan verifikasi email (untuk jalur email), pengguna diarahkan membuat toko: nama toko, alamat, nomor telepon, logo (opsional). Setelah toko dibuat, status akun tetap `pending` dan pengguna masuk ke halaman **Menunggu persetujuan**.
- Status persetujuan akun: `pending`, `approved`, `rejected`, `suspended`. Developer menyetujui atau menolak pendaftaran dari Panel Developer. Penolakan menyertakan alasan yang terlihat di halaman status. Pemilik baru dapat menggunakan aplikasi penuh setelah `approved`.
- Selama status pemilik bukan `approved`, ia hanya dapat membuka form onboarding toko (bila belum membuat toko), halaman status akun, dan logout; akses lain diarahkan ke halaman status. Status `suspended` memblokir pemilik, admin, dan kasir tokonya.
- Admin dan kasir yang dibuat pemilik tidak memerlukan persetujuan developer. Akses mereka mengikuti status persetujuan/pemblokiran pemilik toko.
- Pendaftaran atau login Google tidak dapat menetapkan `is_developer`. Akun developer tidak memiliki toko dan hanya dibuat operator secara manual melalui perintah Artisan terlindungi.
- Satu pemilik memiliki satu toko pada versi 1.

### 5.2 Manajemen Toko dan Anggota

- Pemilik dapat mengubah profil toko.
- Pemilik dapat memilih preset tema atau mengatur warna utama dan aksen dengan nilai hex, melihat preview langsung, serta memilih radius komponen (`md`, `lg`, `xl`). Tema berlaku bagi pemilik dan semua anggota toko.
- Teks normal di atas warna utama harus memiliki rasio kontras WCAG AA minimal 4.5:1. Tema v1 hanya mendukung mode terang; nilai tema kosong menggunakan default design system dengan warna utama emerald (`#10B981`).
- Pemilik dapat menambah admin toko atau kasir (undang lewat email atau buat akun langsung), mengatur status aktifnya, dan menonaktifkan aksesnya.
- Role anggota toko: **Pemilik** (akses penuh, termasuk pengaturan toko dan anggota), **Admin toko** (mengelola produk, kategori, stok, transaksi, dan melihat laporan; tidak dapat mengubah profil, tema, pembayaran, atau anggota), dan **Kasir** (melayani transaksi, melihat produk dan riwayat transaksi miliknya, serta melakukan penyesuaian stok dengan alasan). Admin dan kasir terikat ke satu toko.

### 5.3 Produk dan Stok

- CRUD produk: nama, deskripsi, SKU/barcode, kategori, harga jual, foto, satuan (`unit`, default `pcs`), stok, ambang stok menipis, dan pilihan apakah stok dilacak (`track_stock`). Produk dengan stok tidak terbatas selalu tersedia dan tidak mengurangi stok saat transaksi.
- Pemilik dapat mengatur grup opsi produk dengan tipe pilihan tunggal atau banyak, aturan required/min/max, urutan, dan opsi aktif dengan selisih harga rupiah positif atau negatif.
- CRUD kategori.
- Untuk produk dengan `track_stock=true`, stok berkurang saat transaksi selesai dan kembali saat transaksi dibatalkan. Untuk produk dengan `track_stock=false`, stok diabaikan dan transaksi tidak membuat pergerakan stok.
- Penyesuaian stok manual oleh pemilik, admin, atau kasir disertai alasan dan tercatat di riwayat pergerakan stok dengan identitas pelaku. Penyesuaian tidak boleh membuat stok negatif; produk tanpa pelacakan stok tidak dapat disesuaikan.
- Peringatan stok menipis berdasarkan ambang batas per produk.
- Pencarian produk dan filter kategori.

### 5.4 Transaksi Kasir (POS)

- Layar kasir berisi daftar produk dan keranjang.
- Tambah, ubah jumlah, dan hapus item di keranjang. Setiap item dapat menyimpan opsi yang dipilih dan catatan.
- Harga satuan pada transaksi adalah harga dasar produk ditambah selisih harga semua opsi terpilih. Nama grup, nama opsi, dan selisih harganya disimpan sebagai snapshot agar riwayat tidak berubah saat opsi produk diedit.
- Diskon per transaksi (nominal atau persen).
- Metode pembayaran: **Tunai** dan **Nontunai (payment gateway)**.
- Pembayaran tunai: input uang diterima, kembalian dihitung otomatis.
- Nama pelanggan dan nomor meja opsional dicatat pada transaksi dan ditampilkan pada struk.
- Setelah pembayaran tunai berhasil, kasir diarahkan ke struk digital. Halaman struk lewat tautan bertanda tangan, bagikan via WhatsApp, atau cetak dari browser.
- Pembatalan transaksi oleh pemilik dengan alasan.

### 5.5 Integrasi Payment Gateway

- Penyedia: **Midtrans Snap** (mode sandbox untuk pengembangan dan demo).
- Metode yang didukung: QRIS, e-wallet, virtual account bank.
- Alur pembayaran:
  1. Kasir memilih pembayaran nontunai, sistem membuat order berstatus `pending`.
  2. Sistem meminta tagihan ke Midtrans dan menampilkan pembayaran (QRIS atau nomor VA).
  3. Pembeli membayar.
  4. Midtrans mengirim notifikasi (webhook) ke server.
  5. Server memverifikasi `signature_key`, mengubah status menjadi `paid`, dan mengurangi stok hanya untuk produk `track_stock=true`.
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

### 5.7 Panel Developer

- **Beranda**: jumlah pendaftar menunggu, aktif, ditolak, ditangguhkan, dan total toko.
- **Pendaftaran**: daftar pendaftar dengan filter status. Detail menampilkan nama, email, metode login, tanggal daftar, nama toko, alamat, dan telepon. Developer dapat memilih **Setujui** atau **Tolak**; penolakan wajib menyertakan alasan.
- **Toko**: daftar dan pencarian toko, dengan aksi tangguhkan atau aktifkan kembali.
- **Pengguna**: daftar dan pencarian pengguna.
- **Log Aktivitas**: riwayat keputusan developer (setujui, tolak, tangguhkan, aktifkan kembali), pelaku, target, catatan, dan waktu; log tidak dapat diedit atau dihapus.
- **Akun Saya**: ubah kata sandi developer.
- Hanya akun `is_developer=true` yang dapat mengakses `/developer/*`; permintaan oleh non-developer mendapat 404. Developer tidak memiliki toko dan bukan anggota `store_members`.

### 5.8 Log Aktivitas Toko

- Owner dapat melihat maksimal 100 aktivitas toko terbaru beserta nama dan email akun pelaku.
- Catat penambahan produk, perubahan stok manual beserta alasannya, dan transaksi tunai yang selesai.
- Log menggunakan snapshot target dan detail seperlunya, terisolasi per toko, serta append-only.

## 6. Kebutuhan Mobile Friendly

- Desain **mobile first**: semua layar utama dapat dipakai penuh di lebar 360 px.
- Navigasi bawah (bottom navigation) untuk kasir: Kasir, Produk, Riwayat, Laporan.
- Target sentuh minimal 44 px, tombol bayar mudah dijangkau ibu jari.
- Semua antarmuka memakai token semantik (`primary-50` sampai `primary-900`, `accent`, `surface`, `text`, `border`, `success`, `warning`, `danger`) dari CSS variables. Komponen tidak memakai warna hard-coded atau kelas warna Tailwind langsung.
- Tema toko diterapkan konsisten ke seluruh halaman anggota dalam mode terang.
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

- Pembuatan order, perubahan stok produk yang dilacak, dan pencatatan pergerakan stok dilakukan dalam satu transaksi database. Produk `track_stock=false` tidak mengubah stok dan tidak membuat pergerakan stok.
- Penguncian baris (`lockForUpdate`) pada produk yang stoknya dilacak agar stok tidak minus saat dua kasir menjual barang yang sama.
- Uang disimpan sebagai integer rupiah.
- Harga satuan order adalah harga dasar ditambah seluruh selisih harga opsi terpilih; snapshot opsi dan catatan item tidak berubah saat katalog diedit.

### Ketersediaan dan performa

- Respons rata-rata di bawah 500 ms untuk operasi umum.
- Backup database harian.

## 8. Alur Pengguna Utama

1. **Onboarding**: daftar (email atau Google), verifikasi email bila memakai email, buat toko, tunggu persetujuan developer, lalu mulai mengelola toko setelah disetujui.
2. **Penjualan tunai**: pilih produk, cek keranjang, pilih tunai, input uang diterima, selesai, kirim struk.
3. **Penjualan QRIS**: pilih produk, pilih nontunai, tampilkan QR, pembeli membayar, status otomatis lunas, struk terbit.
4. **Pemantauan pemilik**: buka dashboard, lihat omzet hari ini, cek stok menipis.

## 9. Model Data

Skema lengkap ada di `docs/ERD.md`. Ringkasan tabel: `users`, `stores`, `store_members`, `categories`, `products`, `product_option_groups`, `product_options`, `stock_movements`, `orders`, `order_items`, `order_item_options`, `payments`.

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
| 0 | Fondasi proyek, design system, layar UI statis, dan layout mobile | Kerangka serta layar contoh siap dikembangkan |
| 1 | Auth (email dan Google), toko, tema, isolasi data | Pengguna bisa daftar, punya toko, dan mengatur tampilannya |
| 2 | Produk, kategori, stok, dan opsi produk | Katalog serta kustomisasi produk siap dipakai |
| 3 | Transaksi tunai dan struk | Versi pertama dapat didemokan |
| 4 | Payment gateway dan webhook | Pembayaran QRIS/e-wallet berjalan |
| 5 | Dashboard dan laporan | Pemilik melihat omzet dan produk terlaris |
| 6 | PWA, penyempurnaan Panel Developer, audit mobile dan keamanan | Siap dipamerkan |
| 7 | Data demo, deploy, dokumentasi | Siap masuk portfolio |

## 13. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Cakupan membengkak | Proyek tidak selesai | Patuhi daftar non-tujuan, selesaikan tahap berurutan |
| Kebocoran data antar toko | Cacat keamanan serius | Global scope, policy, dan test lintas toko di setiap fitur |
| Tema dengan kontras rendah | Teks sulit dibaca | Validasi rasio kontras WCAG AA dan preview sebelum tema disimpan |
| Opsi atau stok produk salah dihitung | Total transaksi atau stok keliru | Snapshot harga opsi di item order; stok dan ledger hanya diproses untuk produk yang melacak stok |
| Webhook terlambat atau ganda | Status atau stok keliru | Idempotensi, verifikasi tanda tangan, cek status ke gateway |
| Aktivasi gateway produksi butuh dokumen usaha | Tidak bisa transaksi nyata | Gunakan sandbox untuk demo dan jelaskan di README |
| Stok minus karena transaksi bersamaan | Data tidak akurat | Row locking dan transaksi database |
| Kunci API bocor di repo publik | Penyalahgunaan akun | `.env` di `.gitignore`, kunci dibuat ulang bila sempat ter-commit |
| Pendaftar menunggu lama tanpa kabar | Kehilangan kepercayaan atau batal memakai aplikasi | Status pendaftaran selalu dapat dilihat di halaman status; keputusan terbuka mengenai notifikasi email |

## 14. Keputusan dan Pertanyaan Terbuka

**Sudah diputuskan**

- Nama produk: CuanBozz
- Payment gateway: Midtrans (sandbox)
- Satu pemilik satu toko pada versi 1
- Login Google termasuk versi 1
- Tema toko yang dapat dikustom pemilik hanya dalam mode terang pada versi 1
- Opsi produk dengan selisih harga per opsi termasuk versi 1
- Pendaftaran (termasuk melalui Google) berstatus `pending`; akses penuh pemilik menunggu persetujuan developer
- Akun developer hanya dibuat lewat `developer:create` dan tidak memiliki toko

**Masih terbuka**

- Apakah perlu paket langganan (gratis vs premium) sebagai simulasi SaaS?
- Apakah struk perlu bisa dicetak ke printer thermal Bluetooth (saat ini non-tujuan)?
- Domain yang dipakai untuk deploy.
- Apakah perlu notifikasi email ketika pendaftaran disetujui atau ditolak? Untuk v1, perubahan hanya diinformasikan melalui halaman status.
