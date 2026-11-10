# AGENTS.md — Instruksi untuk Agen AI

Dokumen ini berisi instruksi permanen bagi agen AI (LLM) yang bekerja di repositori ini. **Baca dan patuhi seluruh aturan di bawah sebelum melakukan perubahan apa pun.** Aturan di sini berlaku untuk seluruh sesi kerja AI, bukan hanya sesi pembuatnya.

## 1. Konteks Proyek

- **Nama aplikasi**: Sistem Pengelolaan Kendaraan Dinas Operasional (KDO) Perangkat Daerah
- **Identitas peserta**: JP-1 — nomor peserta 20270311
- **Tujuan**: membantu proses pendataan kendaraan dinas operasional milik perangkat daerah, mengetahui kondisi kendaraan, serta mencatat pegawai (PNS) yang bertanggung jawab atas penggunaannya.
- **Tiga entitas utama (WAJIB ada)**:
  1. **KDO (Kendaraan)** — nomor polisi, merek, tipe/model, tahun produksi, warna, kondisi (`baik` | `rusak_ringan` | `rusak_berat`)
  2. **PNS (Pegawai)** — nama, NIP (18 digit), jabatan, golongan
  3. **PD (Perangkat Daerah)** — kode, nama instansi (mis. Dinas Pendidikan, Dinas Kesehatan)
- Relasi: PD memiliki banyak pegawai dan banyak kendaraan; satu kendaraan memiliki satu pegawai sebagai penanggung jawab (nullable = belum ditunjuk).

## 2. Stack & Struktur Repositori

Monorepo dua aplikasi:

```
sistem-kdo/
├── backend/    Laravel 13 — REST API (PHP 8.5, MySQL/MariaDB, Sanctum)
├── frontend/   Vue 3 SPA (Vite, Vue Router, Pinia, Axios, Bootstrap 5)
├── docs/       dump database (.sql), dokumentasi API (Scribe), Postman collection
├── AGENTS.md   (file ini)
└── README.md
```

- Backend adalah **REST API murni** — tidak memakai Blade untuk UI; semua respons endpoint API berupa JSON.
- Frontend berkomunikasi dengan backend **hanya melalui REST API** (`/api/v1/...`), autentikasi bearer token Sanctum.

## 3. Perintah Penting

| Aksi | Perintah |
|---|---|
| Jalankan API | `cd backend && php artisan serve` (http://localhost:8000) |
| Jalankan UI | `cd frontend && npm run dev` (http://localhost:5173) |
| Migration | `cd backend && php artisan migrate` (atau `migrate:fresh --seed`) |
| Seeder data uji | `cd backend && php artisan db:seed` |
| Generate docs API | `cd backend && php artisan scribe:generate` |
| Uji endpoint | gunakan Postman collection di `docs/postman/` |

- Database development: MySQL `kdo_db` (XAMPP, host `127.0.0.1`, user `root`, tanpa password).
- Kredensial sensitif hanya di `.env` (tidak pernah di-commit); contoh konfigurasi ada di `.env.example`.

## 4. Aturan Pengembangan Backend (Laravel)

1. **Skema database**: semua perubahan WAJIB melalui **migration** (`php artisan make:migration ...`). Jangan pernah mengubah struktur tabel langsung dari phpMyAdmin.
2. **Validasi**: semua request divalidasi lewat **FormRequest** — tidak pernah validasi inline di controller. Aturan penting: NIP `digits:18` + `unique`, nomor polisi `unique`, tahun produksi `integer` wajar, kondisi `in:baik,rusak_ringan,rusak_berat`, FK wajib `exists`.
3. **Respons**: gunakan **API Resource** untuk transformasi model. Bentuk respons harus konsisten:
   - Sukses item: `{ "data": { ... } }`
   - Sukses koleksi ter-paginasi: `{ "data": [...], "meta": { pagination } }`
   - Error validasi 422: `{ "message": "...", "errors": { field: [ ... ] } }`
   - Semua error (401/403/404/500) harus JSON dengan bentuk seragam.
4. **Status code harus tepat**: `200` sukses biasa, `201` create, `204` delete, `422` validasi, `401/403/404` sesuai konteks.
5. **Query**: selalu eager loading (`::with(...)`) saat respons memuat relasi — jangan sampai N+1. Daftar selalu ter-paginasi dan mendukung filter/search via query param.
6. **Penamaan**:
   - Endpoint: kebab-case, jamak, dalam versi `/api/v1` (contoh: `GET /api/v1/kendaraan`).
   - Tabel: bahasa Indonesia, snake_case (contoh: `perangkat_daerah`, `kendaraan`); jika bentuk jamak tidak wajar, set `protected $table` eksplisit di model.
   - Model: PascalCase Indonesia (contoh: `PerangkatDaerah`, `Kendaraan`).
   - Kolom/FK: snake_case dengan suffix `_id` (contoh: `perangkat_daerah_id`).
7. **Keamanan dasar** (kriteria penilaian):
   - Autentikasi via **Sanctum** bearer token; proteksi route dengan middleware `auth:sanctum`.
   - Role: `admin` (boleh CRUD) dan `viewer` (hanya read) — middleware role pada semua route mutasi.
   - Rate limiting `throttle:api` pada route API.
   - Model memakai `$fillable` (whelist), password tidak pernah boleh muncul di respons/resource.
   - Password disimpan ter-hash (bcrypt bawaan Laravel).

## 5. Aturan Pengembangan Frontend (Vue 3)

- Struktur: `src/router/` (route + guard), `src/stores/` (Pinia), `src/api/` (modul Axios per entitas), `src/views/` (halaman), `src/components/` (komponen umum).
- Semua panggilan API lewat instance Axios terpusat dengan **interceptor** token (`Authorization: Bearer ...`) dan penanganan 401 (auto-logout).
- State sesi (token, profil, role) dikelola Pinia dan dipertahankan di `localStorage`.
- Router guard: halaman selain `/login` wajib punya token; tombol/menu aksi tulis hanya tampil untuk role `admin`.
- Styling: Bootstrap 5 (class utilitas/komponen), konsisten tiap halaman (navbar, tabel, form, modal).

## 6. Aturan Git & Workflow

- Bekerja langsung di branch **`main`** (solo developer, history linear).
- **Commit granular per unit kerja** dengan pesan konventional: `feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:` (bahasa bebas, Indonesia diperbolehkan).
- Push setiap fase selesai (dan di tengah fase jika perubahan banyak).
- **Jangan pernah force-push ke `main`** dan jangan pernah commit: `.env`, `vendor/`, `node_modules/`, `storage/*.key`, dump besar selain `docs/`.

## 7. Kewajiban Saat Menambah/Mengubah Fitur

Setiap penambahan fitur baru wajib diikuti pembaruan:

1. Migration + model + seeder (jika ada tabel/kolom baru).
2. Endpoint di `routes/api.php` + FormRequest + API Resource.
3. Regenerasi dokumentasi API (`php artisan scribe:generate`) dan update Postman collection di `docs/postman/`.
4. Update README.md bila menyangkut fitur/cara pakai.
5. Update file ini (AGENTS.md) bila ada konvensi/keputusan arsitektur baru yang diadopsi.

## 8. Status Proyek (perbarui saat fase berubah)

- [x] Fase 0 — prasyarat & repo
- [x] Fase 1 — fondasi repo: scaffold backend + frontend, docs awal, commit pertama
- [x] Fase 2 — database: migration, model, seeder, dump awal (6 PD, 12 PNS, 15 KDO, 2 user)
- [x] Fase 3 — auth & keamanan dasar (Sanctum, role admin/viewer, throttle, CORS)
- [x] Fase 4 — REST API CRUD (inti): PD, PNS, KDO + assign PJ + laporan, teruji menyeluruh
- [x] Fase 5 — dokumentasi API (Scribe HTML + Postman collection + OpenAPI)
- [x] Fase 6 — frontend Vue 3: login, dashboard, CRUD 3 entitas, assign PJ, laporan
- [ ] Fase 7 — finalisasi & pengumpulan
- [ ] Fase 8 — bonus (riwayat kondisi/PJ, kolom tambahan, export, foto)

## 9. Deklarasi Penggunaan AI

Aplikasi dibangun menggunakan **OpenCode** dengan model AI **Big Pickle**. Seluruh proses development (scaffold, kode, dokumen) dibantu AI di bawah arahan dan review pemilik repo.
