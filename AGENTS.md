# AGENTS.md — CuanBozz

Aturan kerja untuk AI agent (Codex/Orca) di repo ini. Baca seluruh file ini sebelum mengubah kode.

## Ringkasan proyek

CuanBozz adalah aplikasi kasir web multi-toko (SaaS POS). Pengguna login (email atau Google), membuat toko sendiri, mengelola produk dan stok, mencatat transaksi, dan menerima pembayaran nontunai lewat payment gateway. Setiap toko datanya terisolasi penuh dari toko lain. Antarmuka **mobile first**.

Dokumen acuan (baca sebelum mengerjakan task):

- `docs/PRD.md`: kebutuhan produk dan ruang lingkup
- `docs/ERD.md`: skema database
- `docs/TASKS.md`: daftar task berurutan beserta kriteria selesai

Jika ada konflik antara file ini dan PRD, berhenti dan tanyakan. Jangan menebak.

## Tech stack (jangan diganti atau ditambah tanpa izin)

| Lapisan | Pilihan |
|---|---|
| Framework | Laravel (versi stabil terbaru, catat versi persisnya di `composer.json`) |
| Auth | Laravel Breeze (Blade) + Laravel Socialite (Google) |
| Frontend | Blade + Alpine.js + Tailwind CSS (tanpa SPA) |
| Database | MySQL (dev dan produksi), SQLite in-memory (test) |
| Test | Pest |
| Code style | Laravel Pint |
| Payment gateway | Midtrans Snap (sandbox untuk dev dan demo) |
| Grafik | Chart.js |
| Queue/scheduler | Database queue + Laravel scheduler |

## Perintah

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
composer run dev            # jalankan server dev
php artisan test            # semua test harus lulus
./vendor/bin/pint           # format kode
npm run build               # pastikan build frontend sukses
```

## Aturan multi-tenant (KRITIS)

1. Semua tabel bisnis (`categories`, `products`, `stock_movements`, `orders`, `order_items`, `payments`) punya kolom `store_id`.
2. Semua model bisnis memakai trait `BelongsToStore`: global scope yang memfilter `store_id` toko aktif, dan mengisi `store_id` otomatis saat `creating`.
3. Jangan pernah query model bisnis tanpa scope. `withoutGlobalScopes()` hanya boleh di area admin platform dan wajib diberi komentar alasan.
4. Route model binding harus lewat scope, sehingga ID milik toko lain menghasilkan 404.
5. Setiap controller memakai Policy. Cek peran dan kepemilikan toko di Policy, bukan di view.
6. **Setiap fitur baru wajib punya test lintas toko**: user toko A tidak bisa melihat, mengubah, atau menghapus data toko B.

## Peran

- `owner`: akses penuh ke tokonya (produk, laporan, kasir, pembatalan transaksi, profil toko)
- `cashier`: hanya layar kasir, melihat produk, dan riwayat transaksi miliknya; tidak boleh melihat laporan atau mengubah harga
- `admin platform`: flag `is_admin` di tabel `users`, hanya untuk panel admin sederhana

## Autentikasi

- Login email + kata sandi (Breeze) dan **login Google** (Socialite).
- Kolom `users.google_id` nullable; `users.password` nullable untuk akun yang hanya memakai Google.
- Alur Google: callback mencari user berdasarkan `google_id`, lalu berdasarkan email; jika belum ada, buat user baru dengan email terverifikasi. Jika email sudah terdaftar, hubungkan `google_id` ke akun itu.
- Setelah login, user yang belum punya toko diarahkan ke `/onboarding/store` (middleware).
- Environment variable: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`.
- Rate limiting pada login, dan jangan pernah mencatat token atau secret ke log.

## Pembayaran (Midtrans)

- Gunakan mode sandbox selama pengembangan. Kunci hanya dari `.env` (`MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION=false`).
- Webhook di `POST /webhooks/midtrans`: kecualikan dari CSRF, **verifikasi `signature_key`** (SHA512 dari `order_id + status_code + gross_amount + server_key`), dan tolak jika tidak cocok.
- Webhook harus **idempoten**: notifikasi ganda tidak boleh membuat pencatatan atau pengurangan stok ganda.
- Status order: `pending`, `paid`, `expired`, `failed`, `cancelled`. Transisi status didefinisikan di satu tempat (enum + method di model), bukan tersebar.
- Stok berkurang saat order menjadi `paid` (nontunai) atau saat checkout tunai selesai.

## Konsistensi data

- Pembuatan order + pengurangan stok + pencatatan `stock_movements` dalam satu `DB::transaction`.
- Gunakan `lockForUpdate()` pada baris produk agar stok tidak minus saat dua kasir menjual barang yang sama.
- Uang disimpan sebagai **integer rupiah** (tanpa desimal/float).
- `order_items` menyimpan snapshot nama dan harga produk saat transaksi.

## Konvensi kode

- Bahasa UI: Indonesia. Nama kode, tabel, kolom, dan variabel: Inggris.
- Logika bisnis di `app/Actions` atau `app/Services`, bukan di controller. Validasi pakai Form Request.
- Jangan mengubah migrasi yang sudah di-commit; buat migrasi baru.
- Mobile first: desain mulai dari lebar 360 px, target sentuh minimal 44 px, navigasi bawah (bottom nav) untuk kasir.
- Tanpa library tambahan (composer atau npm) tanpa izin. Jika perlu, jelaskan alasannya dan tunggu persetujuan.
- Komentar hanya untuk "kenapa", bukan "apa".

## Larangan

- Meng-commit `.env`, kunci API, atau data pribadi.
- Mengubah atau menghapus test supaya lulus. Perbaiki kodenya.
- Mengerjakan hal di luar PRD: multi-cabang, supplier/pembelian, pajak kompleks, printer thermal, aplikasi native.
- Mengerjakan lebih dari satu task per sesi atau branch.
- Menjalankan perintah destruktif (`migrate:fresh`, hapus data) pada database selain lokal.

## Cara kerja per task

1. Ambil satu task dari `docs/TASKS.md` dan buat branch `feat/<id-task>-<slug>`.
2. Kerjakan hanya cakupan task itu. Jika menemukan masalah di luar cakupan, catat di bagian akhir laporan, jangan diperbaiki diam-diam.
3. Tulis test bersamaan dengan kodenya.
4. Sebelum menyatakan selesai, jalankan dan pastikan lulus: `./vendor/bin/pint`, `php artisan test`, `npm run build`.
5. Laporan akhir berisi: file yang diubah, keputusan yang diambil, hasil menjalankan test, dan hal yang belum selesai atau meragukan. Jangan menyatakan "selesai" jika test belum dijalankan.
6. Ragu soal aturan bisnis atau keamanan: tanyakan dulu.

## Definition of Done (berlaku untuk semua task)

- Kriteria "Selesai jika" pada task terpenuhi.
- Test baru ada dan lulus, termasuk test lintas toko bila menyentuh data bisnis.
- Pint, test, dan build lulus.
- Tampilan dicek di lebar 360 px.
- Tidak ada secret atau data pribadi di diff.
