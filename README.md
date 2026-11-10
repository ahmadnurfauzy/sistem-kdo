# Sistem Pengelolaan Kendaraan Dinas Operasional (KDO) Perangkat Daerah

> **JP-1 · Nomor Peserta 20270311**

Sistem informasi untuk mengelola data Kendaraan Dinas Operasional (KDO) milik perangkat daerah: pendataan kendaraan, pemantauan kondisi kendaraan, serta pencatatan Pegawai Negeri Sipil (PNS) yang bertanggung jawab atas penggunaannya.

## Fitur Utama

- 🔐 Autentikasi login (token) dengan role **admin** (CRUD penuh) dan **viewer** (hanya baca)
- 🚗 **Pengelolaan KDO** — data nomor polisi, merek, tipe/model, tahun produksi, warna, kondisi (`baik` / `rusak ringan` / `rusak berat`), penanggung jawab
- 👷 **Pengelolaan PNS** — data nama, NIP, jabatan, golongan, perangkat daerah
- 🏛️ **Pengelolaan Perangkat Daerah** — data instansi pengguna/pemilik kendaraan
- 📊 Laporan rekap kondisi kendaraan per perangkat daerah
- 📚 REST API terdokumentasi (Scribe + Postman collection)

*(Daftar fitur akan diperbarui seiring fase pengembangan.)*

## Tech Stack

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 13 (PHP 8.5) — REST API |
| Frontend | Vue 3 + Vite + Vue Router + Pinia + Axios, Bootstrap 5 |
| Database | MySQL / MariaDB (dikelola via phpMyAdmin) |
| Autentikasi | Laravel Sanctum (bearer token) |

## Struktur Repositori

```
sistem-kdo/
├── backend/    # REST API (Laravel)
├── frontend/   # SPA (Vue 3)
├── docs/       # dump database, dokumentasi API, Postman collection
├── AGENTS.md   # instruksi AI selama pembangunan aplikasi
└── README.md   # (file ini)
```

## Persyaratan

- PHP ≥ 8.2 (ekstensi umum XAMPP memenuhi)
- Composer
- Node.js ≥ 18
- MySQL / MariaDB (mis. XAMPP)
- phpMyAdmin (opsional, untuk import dump)

## Instalasi & Menjalankan

### 1. Siapkan database

Buat database kosong (atau import dump):

```sql
CREATE DATABASE kdo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Untuk memakai dump yang disertakan, import `docs/kdo-db-dump.sql` lewat phpMyAdmin (tab **Import**) atau CLI:

```bash
mysql -u root kdo_db < docs/kdo-db-dump.sql
```

### 2. Backend (Laravel API)

```bash
cd backend
composer install
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
# sesuaikan .env: DB_DATABASE=kdo_db (user/password MySQL lokal)
php artisan migrate --seed
php artisan serve           # http://localhost:8000
```

### 3. Frontend (Vue 3)

```bash
cd frontend
npm install
npm run dev                 # http://localhost:5173
```

## Dokumentasi API

- Dokumen interaktif (Scribe): tersedia di `docs/api/` — *(akan di-generate pada fase dokumentasi)*
- Postman collection: `docs/postman/` — *(tersedia pada fase dokumentasi)*

Base URL API: `http://localhost:8000/api/v1`

## Penggunaan AI dalam Pembangunan Aplikasi

> Aplikasi dibangun menggunakan **OpenCode** dengan model AI **Big Pickle**.

AI digunakan sebagai asisten pemrograman di seluruh proses pembangunan: scaffolding proyek, penulisan kode backend/frontend, perancangan skema database dan endpoint, pengujian, serta penulisan dokumentasi. Seluruh arah dan keputusan teknis dilakukan oleh pemilik repositori, dan seluruh hasil ditinjau ulang sebelum di-commit. Instruksi yang diberikan kepada AI selama pembangunan terdokumentasi dalam file [AGENTS.md](AGENTS.md).

---

© 2026 — JP-1 (peserta 20270311)
