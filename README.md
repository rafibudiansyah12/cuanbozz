# CuanBozz

CuanBozz adalah aplikasi kasir web multi-toko (SaaS POS) untuk UMKM. Pemilik toko dapat mengelola produk, stok, dan transaksi; setiap toko memiliki data yang terisolasi. Antarmuka dirancang mobile first.

Fondasi proyek menggunakan Laravel 12.70.0, Laravel Breeze (Blade), Alpine.js, Tailwind CSS, Pest, dan Laravel Pint. Integrasi Google Socialite dan pembayaran Midtrans akan ditambahkan pada task berikutnya.

## Persyaratan

- PHP 8.2 atau lebih baru beserta ekstensi PDO MySQL dan SQLite
- Composer
- Node.js dan npm
- MySQL dengan database `cuanbozz`

## Menjalankan aplikasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer run dev
```

Di Windows PowerShell, gunakan `Copy-Item .env.example .env` untuk menyalin file environment. Sesuaikan koneksi MySQL di `.env` sebelum menjalankan migrasi.

## Pemeriksaan

```bash
./vendor/bin/pint
php artisan test
npm run build
```

Pengujian menggunakan SQLite in-memory. Kredensial Google dan Midtrans pada `.env.example` dibiarkan kosong; isi hanya pada `.env` lokal saat integrasi terkait tersedia.
