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

1. Semua tabel bisnis (`store_members`, `categories`, `products`, `product_option_groups`, `product_options`, `stock_movements`, `orders`, `order_items`, `order_item_options`, `payments`) punya kolom `store_id`.
2. Semua model bisnis memakai trait `BelongsToStore`: global scope yang memfilter `store_id` toko aktif, dan mengisi `store_id` otomatis saat `creating`.
3. Jangan pernah query model bisnis tanpa scope. `withoutGlobalScopes()` hanya boleh di area developer platform untuk kebutuhan pengelolaan lintas toko dan wajib diberi komentar alasan.
4. Route model binding harus lewat scope, sehingga ID milik toko lain menghasilkan 404.
5. Setiap controller memakai Policy. Cek peran dan kepemilikan toko di Policy, bukan di view.
6. **Setiap fitur baru wajib punya test lintas toko**: user toko A tidak bisa melihat, mengubah, atau menghapus data toko B.

## Peran

- `owner`: akses penuh ke tokonya (produk, laporan, kasir, pembatalan transaksi, profil toko)
- `admin`: mengelola produk, kategori, stok, transaksi, dan laporan; tidak dapat mengubah pengaturan toko atau anggota
- `cashier`: layar kasir, melihat produk dan riwayat transaksi miliknya, serta menyesuaikan stok dengan alasan; tidak boleh melihat laporan atau mengubah harga/produk
- `developer`: flag `is_developer` di tabel `users`, pengelola platform yang tidak memiliki toko dan tidak menjadi anggota toko.

## Autentikasi

- Login email + kata sandi (Breeze) dan **login Google** (Socialite).
- Kolom `users.google_id` nullable; `users.password` nullable untuk akun yang hanya memakai Google.
- Alur Google: callback mencari user berdasarkan `google_id`, lalu berdasarkan email; jika belum ada, buat user baru dengan email terverifikasi. Jika email sudah terdaftar, hubungkan `google_id` ke akun itu.
- Setelah pendaftaran dan verifikasi email (untuk pendaftaran email), calon pemilik diarahkan membuat toko di `/onboarding/store`, lalu melihat halaman status persetujuan. Pemilik hanya mendapat akses penuh setelah disetujui developer.
- Akun Google yang baru dibuat juga berstatus `pending`; akun yang dibuat lewat pendaftaran atau Google tidak pernah otomatis atau dapat menjadi developer.
- Environment variable: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`.
- Rate limiting pada login, dan jangan pernah mencatat token atau secret ke log.

## Persetujuan akun dan developer

- Middleware `EnsureAccountApproved` membatasi pemilik yang statusnya bukan `approved` ke form onboarding toko (jika belum memiliki toko), halaman status akun, dan logout. Akses ke halaman lain diarahkan ke halaman status akun. Status `rejected` menampilkan alasan penolakan.
- Jika pemilik berstatus `suspended`, pemilik dan seluruh admin serta kasir tokonya tidak dapat memakai aplikasi. Akun admin dan kasir yang dibuat pemilik tidak memerlukan persetujuan developer; status akses mereka mengikuti status pemilik tokonya.
- Middleware `EnsureDeveloper` melindungi panel `/developer/*`. Rute tersebut harus menjawab 404, bukan 403, untuk pengguna non-developer.
- Setiap keputusan developer (approve, reject, suspend, reactivate) wajib dicatat ke `developer_audit_logs`. Log bersifat append-only: baris tidak boleh diubah atau dihapus.
- Developer tidak pernah diwakili di `store_members`, tidak memiliki toko, dan tidak tunduk pada global scope toko. Pengecualian lintas toko hanya dilakukan untuk fungsi panel developer yang memang memerlukannya.
- Akun developer hanya boleh dibuat lewat perintah Artisan `developer:create`, dengan kata sandi diminta secara interaktif. Dilarang menaruh kredensial developer di seeder, README, atau file lain di repo karena repo ini publik. Akun developer selalu berstatus `approved`.

## Pembayaran (Midtrans)

- Gunakan mode sandbox selama pengembangan. Kunci hanya dari `.env` (`MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION=false`).
- Webhook di `POST /webhooks/midtrans`: kecualikan dari CSRF, **verifikasi `signature_key`** (SHA512 dari `order_id + status_code + gross_amount + server_key`), dan tolak jika tidak cocok.
- Webhook harus **idempoten**: notifikasi ganda tidak boleh membuat pencatatan atau pengurangan stok ganda.
- Status order: `pending`, `paid`, `expired`, `failed`, `cancelled`. Transisi status didefinisikan di satu tempat (enum + method di model), bukan tersebar.
- Stok hanya berkurang untuk produk `track_stock=true`, saat order menjadi `paid` (nontunai) atau saat checkout tunai selesai. Produk `track_stock=false` tidak menghasilkan pergerakan stok.

## Konsistensi data

- Pembuatan order, perubahan stok produk yang dilacak, dan pencatatan `stock_movements` dilakukan dalam satu `DB::transaction`. Produk `track_stock=false` tidak mengubah stok dan tidak membuat pergerakan stok.
- Gunakan `lockForUpdate()` pada baris produk yang stoknya dilacak agar stok tidak minus saat dua kasir menjual barang yang sama.
- Uang disimpan sebagai **integer rupiah** (tanpa desimal/float); selisih harga opsi boleh bertanda positif atau negatif.
- `order_items` menyimpan snapshot nama produk, harga satuan final, dan catatan; `order_item_options` menyimpan snapshot nama grup, nama opsi, dan selisih harga tanpa relasi ke `product_options`.

## Konvensi kode

- Bahasa UI: Indonesia. Nama kode, tabel, kolom, dan variabel: Inggris.
- Logika bisnis di `app/Actions` atau `app/Services`, bukan di controller. Validasi pakai Form Request.
- Jangan mengubah migrasi yang sudah di-commit; buat migrasi baru.
- Mobile first: desain mulai dari lebar 360 px, target sentuh minimal 44 px, navigasi bawah (bottom nav) untuk kasir.
- Tanpa library tambahan (composer atau npm) tanpa izin. Jika perlu, jelaskan alasannya dan tunggu persetujuan.
- Komentar hanya untuk "kenapa", bukan "apa".

## Konvensi UI

- Jangan hard-code warna langsung di Blade atau CSS, termasuk nilai hex, kelas warna Tailwind seperti `bg-green-500`, maupun arbitrary color classes.
- Gunakan hanya token semantik `primary-50` sampai `primary-900`, `accent`, `surface`, `text`, `border`, `success`, `warning`, dan `danger`. Token dipetakan melalui CSS variables dari tema toko atau default design system.
- Jangan hard-code nilai warna token di komponen. Nilai dari tema tersimpan, preset, atau default design system hanya diterapkan ke CSS variables token di root aplikasi; komponen memakai token tersebut.
- Tema v1 hanya mode terang. Teks normal di atas warna `primary` harus memenuhi rasio kontras WCAG AA minimal 4.5:1.

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
