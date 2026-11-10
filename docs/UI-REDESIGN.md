# UI-REDESIGN.md — Redesign tampilan Sistem KDO

Dokumen ini **menggantikan** aturan warna/gaya di `UI-TABLE.md` dan menjadi acuan utama tampilan seluruh `frontend/`. Perilaku tabel (tabel di desktop, card di mobile, state loading/kosong/error) tetap mengikuti `UI-TABLE.md`, dengan perubahan yang tertulis di bagian 7.

## 1. Masalah di tampilan sekarang

Hasil audit `frontend/src`:

- Tampilan masih Bootstrap bawaan: navbar biru solid, `table-light`, `btn-group`, `btn-outline-*`
- Emoji dipakai sebagai ikon (🚗 ✅ ⚠️ ❌ 🏛️) sehingga terlihat tidak profesional dan beda di tiap OS
- Warna hanya biru `#0d6efd` bawaan + gradient login yang generik
- Semua halaman daftar masih `<table>` penuh dengan 8 kolom, di mobile hanya bisa scroll samping
- Navigasi mobile hanya hamburger
- Tidak ada font khusus, tidak ada hirarki visual (judul, label, angka terlihat sama beratnya)

## 2. Arah desain

Nuansa mengikuti situs resmi BPAD/DKI Jakarta: **latar pastel lembut, biru royal sebagai aksen utama, ungu sebagai aksen kedua, bagian gelap navy untuk penekanan**, card putih berradius besar, tombol utama bergradient biru ke ungu, font Plus Jakarta Sans.

Prinsip:

1. **Lembut, bukan kaku**: radius besar (12 sampai 24px), bayangan tipis berwarna biru-navy, latar bergradient halus
2. **Satu bahasa visual** di semua halaman lewat komponen bersama (`PageHeader`, `StatCard`, `DataTable`, `KondisiBadge`, `ConfirmModal`)
3. **Mobile first**: navigasi bawah (bottom tab bar) di mobile, topbar di desktop, tabel jadi card, tombol dan input nyaman disentuh
4. **Tanpa emoji**. Semua ikon pakai Bootstrap Icons
5. Jangan menyalin logo BPAD/DKI. Warna dan nuansa saja yang dipakai. Logo aplikasi cukup kotak gradient dengan ikon mobil

## 3. Palet warna (token)

Diambil dari screenshot situs resmi BPAD (login dan beranda).

| Token | Hex | Dipakai untuk |
|---|---|---|
| `--kdo-blue` | `#2F54FA` | aksen utama, tombol, link aktif |
| `--kdo-blue-700` | `#274CC4` | teks link, judul aksen, menu aktif |
| `--kdo-violet` | `#824DFF` | ujung gradient tombol, aksen kedua |
| `--kdo-navy` | `#3F4B6E` | hero/penekanan gelap |
| `--kdo-navy-900` | `#101727` | footer atau area paling gelap (opsional) |
| `--kdo-ink` | `#1B2447` | judul |
| teks isi | `#363D4A` | body text |
| `--kdo-muted` | `#6B7494` | teks sekunder |
| `--kdo-bg` | `#F3F8FE` | latar halaman |
| `--kdo-blush` | `#E8E1F6` | semburat ungu di latar |
| `--kdo-border` | `#E6EAF3` | garis tipis |
| `--kdo-teal` | `#0A8E8E` | aksen pendukung (ikon perangkat daerah) |
| `--kdo-orange` | `#FF9E01` | aksen hangat kecil (sorotan hero) |

Warna kondisi kendaraan (pastel, teks gelap):

| Kondisi | Latar | Teks |
|---|---|---|
| Baik | `#E3F6EC` | `#157347` |
| Rusak ringan | `#FFF1D6` | `#9A5B00` |
| Rusak berat | `#FDE3E3` | `#B42318` |

Gradient tombol utama: `linear-gradient(135deg, #2F54FA, #824DFF)`.

## 4. Dependensi

```bash
cd frontend
npm i bootstrap-icons @fontsource-variable/plus-jakarta-sans
```

Font dibundel lewat npm (bukan CDN) supaya tetap jalan di jaringan instansi yang membatasi akses luar. Bootstrap tetap versi 5.3 (sudah terpasang).

### `src/main.js`

```js
import '@fontsource-variable/plus-jakarta-sans'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
```

### `index.html` (tambahkan di `<head>`)

```html
<meta name="theme-color" content="#F3F8FE" />
```

Ubah juga meta viewport menjadi:

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
```

## 5. Kode inti

### `src/style.css` (ganti seluruh isi)

```css
/* ===== Token ===== */
:root {
  --kdo-blue: #2f54fa;
  --kdo-blue-700: #274cc4;
  --kdo-violet: #824dff;
  --kdo-navy: #3f4b6e;
  --kdo-ink: #1b2447;
  --kdo-muted: #6b7494;
  --kdo-bg: #f3f8fe;
  --kdo-border: #e6eaf3;
  --kdo-teal: #0a8e8e;
  --kdo-orange: #ff9e01;
  --kdo-grad: linear-gradient(135deg, #2f54fa 0%, #824dff 100%);
  --kdo-shadow: 0 8px 28px rgba(31, 41, 90, 0.07);
  --kdo-radius: 16px;

  /* remap variabel Bootstrap supaya seluruh komponen ikut berubah */
  --bs-primary: #2f54fa;
  --bs-primary-rgb: 47, 84, 250;
  --bs-body-font-family: 'Plus Jakarta Sans Variable', 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
  --bs-body-color: #363d4a;
  --bs-body-color-rgb: 54, 61, 74;
  --bs-heading-color: #1b2447;
  --bs-secondary-color: #6b7494;
  --bs-border-color: #e6eaf3;
  --bs-border-radius: 0.75rem;
  --bs-link-color: #274cc4;
  --bs-link-color-rgb: 39, 76, 196;
  --bs-link-hover-color: #1d3a9c;
  --bs-link-hover-color-rgb: 29, 58, 156;
}

/* ===== Dasar ===== */
body {
  min-height: 100dvh;
  background: var(--kdo-bg);
  -webkit-font-smoothing: antialiased;
}
body::before {
  content: '';
  position: fixed;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  background:
    radial-gradient(640px 420px at 88% -6%, rgba(130, 77, 255, 0.14), transparent 62%),
    radial-gradient(720px 520px at -6% 106%, rgba(47, 84, 250, 0.12), transparent 62%);
}
h1, h2, h3, h4, h5, h6 { font-weight: 700; letter-spacing: -0.01em; }

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation: none !important; transition: none !important; }
}

/* ===== Tombol ===== */
.btn {
  font-weight: 600;
  border-radius: 12px;
  padding: 0.55rem 1.1rem;
}
.btn-primary {
  --bs-btn-color: #fff;
  --bs-btn-hover-color: #fff;
  --bs-btn-active-color: #fff;
  --bs-btn-disabled-color: #fff;
  --bs-btn-bg: transparent;
  --bs-btn-hover-bg: transparent;
  --bs-btn-active-bg: transparent;
  --bs-btn-disabled-bg: transparent;
  --bs-btn-border-color: transparent;
  --bs-btn-hover-border-color: transparent;
  --bs-btn-active-border-color: transparent;
  --bs-btn-disabled-border-color: transparent;
  background-image: var(--kdo-grad);
  box-shadow: 0 6px 16px rgba(47, 84, 250, 0.28);
  transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
}
.btn-primary:hover { filter: brightness(1.07); transform: translateY(-1px); }
.btn-primary:active { transform: translateY(0); }
.btn-primary:disabled { opacity: 0.55; box-shadow: none; }

.btn-soft {
  background: rgba(47, 84, 250, 0.08);
  color: var(--kdo-blue-700);
  border: 0;
}
.btn-soft:hover { background: rgba(47, 84, 250, 0.14); color: var(--kdo-blue-700); }

.icon-btn {
  width: 36px; height: 36px;
  display: inline-grid; place-items: center;
  border: 0; border-radius: 10px;
  background: transparent; color: var(--kdo-muted);
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
}
.icon-btn:hover { background: rgba(47, 84, 250, 0.08); color: var(--kdo-blue-700); }
.icon-btn.danger:hover { background: #fde3e3; color: #c62828; }
.row-actions { display: inline-flex; gap: 0.15rem; }

/* ===== Form ===== */
.form-control, .form-select {
  border-radius: 12px;
  border-color: var(--kdo-border);
  padding: 0.6rem 0.9rem;
}
.form-control:focus, .form-select:focus {
  border-color: var(--kdo-blue);
  box-shadow: 0 0 0 4px rgba(47, 84, 250, 0.12);
}
.form-label { font-size: 0.82rem; font-weight: 600; color: var(--kdo-ink); margin-bottom: 0.35rem; }

.field { position: relative; }
.field-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--kdo-muted); pointer-events: none; }
.field-input { padding-left: 2.7rem; }
.field-toggle { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); }

.search-field { position: relative; min-width: 220px; }
.search-field > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--kdo-muted); pointer-events: none; }
.search-field .form-control { padding-left: 2.5rem; }

/* ===== Card ===== */
.card, .card.border-0 {
  border: 1px solid var(--kdo-border) !important;
  border-radius: var(--kdo-radius);
}
.card, .card.shadow-sm { box-shadow: var(--kdo-shadow) !important; }
.card-header { background: transparent !important; border-bottom: 1px solid var(--kdo-border); padding: 1rem 1.25rem; }
.card-footer { background: transparent !important; border-top: 1px solid var(--kdo-border); }

/* ===== Tabel (berlaku juga untuk halaman yang belum dimigrasi) ===== */
.table { --bs-table-hover-bg: rgba(47, 84, 250, 0.035); --bs-table-bg: transparent; font-size: 0.9rem; }
.table > thead.table-light { --bs-table-bg: transparent; --bs-table-color: var(--kdo-muted); }
.table > thead th {
  font-size: 0.75rem; font-weight: 600; color: var(--kdo-muted);
  border-bottom: 1px solid var(--kdo-border); padding: 0.8rem 1rem; white-space: nowrap;
}
.table > tbody td { padding: 0.9rem 1rem; border-color: var(--kdo-border); }

/* ===== Badge kondisi ===== */
.badge-kondisi {
  display: inline-flex; align-items: center;
  font-size: 0.75rem; font-weight: 600;
  padding: 0.32rem 0.7rem; white-space: nowrap;
}
.badge-kondisi::before {
  content: ''; width: 6px; height: 6px; border-radius: 50%;
  background: currentColor; margin-right: 0.4rem;
}
.badge-kondisi-baik { background: #e3f6ec; color: #157347; }
.badge-kondisi-rusak_ringan { background: #fff1d6; color: #9a5b00; }
.badge-kondisi-rusak_berat { background: #fde3e3; color: #b42318; }

/* ===== Eyebrow, judul halaman ===== */
.eyebrow {
  display: inline-block;
  font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
  color: var(--kdo-blue-700); background: rgba(47, 84, 250, 0.1);
  padding: 0.28rem 0.65rem; border-radius: 999px;
}
.eyebrow-light { background: rgba(255, 255, 255, 0.14); color: #fff; }

.page-head {
  display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
  gap: 1rem; margin-bottom: 1.25rem;
}
.page-head h1 { font-size: 1.6rem; font-weight: 800; color: var(--kdo-ink); margin: 0.5rem 0 0.15rem; }
.page-head p { margin: 0; color: var(--kdo-muted); font-size: 0.9rem; }
@media (max-width: 575.98px) {
  .page-head h1 { font-size: 1.35rem; }
  .page-head-actions, .page-head-actions .btn { width: 100%; }
}

.toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem; margin-bottom: 1rem; }
.toolbar-filters { gap: 0.6rem; width: 100%; flex-direction: column; }
.toolbar-filters.show { display: flex; }
.toolbar-filters .form-select { min-width: 180px; }
@media (min-width: 768px) {
  .toolbar-filters { width: auto; flex-direction: row; }
  .toolbar-filters .form-select { width: auto; }
}

/* ===== Skeleton ===== */
.skel {
  display: block; height: 14px; width: 70%; border-radius: 6px;
  background: linear-gradient(90deg, #eef1f8 25%, #e2e7f3 50%, #eef1f8 75%);
  background-size: 200% 100%;
  animation: skel 1.2s infinite linear;
}
@keyframes skel { from { background-position: 200% 0; } to { background-position: -200% 0; } }

/* ===== Shell: topbar + bottom nav ===== */
.topbar {
  position: sticky; top: 0; z-index: 1030;
  padding-top: env(safe-area-inset-top);
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: saturate(180%) blur(14px);
  -webkit-backdrop-filter: saturate(180%) blur(14px);
  border-bottom: 1px solid var(--kdo-border);
}
.topbar-inner { display: flex; align-items: center; gap: 1rem; height: 64px; }
.brand { display: flex; align-items: center; gap: 0.7rem; text-decoration: none; color: var(--kdo-ink); }
.brand-mark {
  width: 38px; height: 38px; flex: none;
  display: grid; place-items: center;
  border-radius: 12px; background: var(--kdo-grad); color: #fff; font-size: 1.15rem;
  box-shadow: 0 6px 14px rgba(47, 84, 250, 0.3);
}
.brand-mark-lg { width: 56px; height: 56px; border-radius: 18px; font-size: 1.7rem; }
.brand-text { display: flex; flex-direction: column; line-height: 1.15; }
.brand-text strong { font-size: 0.98rem; font-weight: 800; }
.brand-text small { font-size: 0.7rem; color: var(--kdo-muted); }

.topnav { margin-left: 1rem; gap: 0.2rem; padding: 0.3rem; background: rgba(47, 84, 250, 0.05); border-radius: 14px; }
.topnav-link {
  display: flex; align-items: center; gap: 0.45rem;
  padding: 0.5rem 0.95rem; border-radius: 11px;
  color: var(--kdo-muted); font-weight: 600; font-size: 0.88rem; text-decoration: none;
  transition: color 0.15s, background 0.15s;
}
.topnav-link:hover { color: var(--kdo-ink); }
.topnav-link.active { background: #fff; color: var(--kdo-blue-700); box-shadow: 0 2px 8px rgba(31, 41, 90, 0.08); }

.topbar-user { margin-left: auto; }
.user-chip {
  display: flex; align-items: center; gap: 0.6rem;
  border: 1px solid var(--kdo-border); background: #fff; color: var(--kdo-ink);
  border-radius: 999px; padding: 0.25rem 0.8rem 0.25rem 0.3rem; font-size: 0.85rem; font-weight: 600;
}
.user-avatar {
  width: 32px; height: 32px; border-radius: 50%;
  display: grid; place-items: center;
  background: var(--kdo-grad); color: #fff; font-size: 0.75rem; font-weight: 700;
}
.role-tag {
  font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
  color: var(--kdo-blue-700); background: rgba(47, 84, 250, 0.1);
  padding: 0.12rem 0.45rem; border-radius: 999px;
}
.dropdown-menu { border: 1px solid var(--kdo-border); border-radius: 14px; box-shadow: var(--kdo-shadow); padding: 0.4rem; }
.dropdown-item { border-radius: 9px; font-weight: 500; font-size: 0.9rem; }

.app-main { padding-top: 1.5rem; padding-bottom: 2rem; }

.bottomnav {
  position: fixed; left: 0; right: 0; bottom: 0; z-index: 1030;
  display: grid; grid-template-columns: repeat(5, 1fr);
  padding: 0.35rem 0.25rem calc(0.35rem + env(safe-area-inset-bottom));
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
  border-top: 1px solid var(--kdo-border);
}
.bottomnav a {
  display: flex; flex-direction: column; align-items: center; gap: 2px;
  padding: 0.3rem 0; font-size: 0.68rem; font-weight: 600;
  color: var(--kdo-muted); text-decoration: none;
}
.bottomnav a i { font-size: 1.2rem; padding: 0.1rem 1rem; border-radius: 999px; transition: background 0.15s; }
.bottomnav a.active { color: var(--kdo-blue-700); }
.bottomnav a.active i { background: rgba(47, 84, 250, 0.12); }
@media (max-width: 991.98px) {
  .app-main { padding-bottom: calc(5.5rem + env(safe-area-inset-bottom)); }
}

/* ===== Stat card & dashboard ===== */
.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.stat-grid > :last-child:nth-child(odd) { grid-column: 1 / -1; }
@media (min-width: 992px) {
  .stat-grid { grid-template-columns: repeat(5, 1fr); gap: 1rem; }
  .stat-grid > :last-child:nth-child(odd) { grid-column: auto; }
}
.stat {
  display: flex; align-items: center; gap: 0.9rem;
  background: #fff; border: 1px solid var(--kdo-border); border-radius: var(--kdo-radius);
  padding: 1rem 1.1rem; box-shadow: var(--kdo-shadow);
}
.stat-icon {
  width: 44px; height: 44px; flex: none; display: grid; place-items: center;
  border-radius: 14px; font-size: 1.25rem; background: var(--tone-bg); color: var(--tone);
}
.stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; color: var(--kdo-ink); }
.stat-label { font-size: 0.78rem; color: var(--kdo-muted); margin-top: 0.3rem; }

.tone-blue { --tone: #2f54fa; --tone-bg: rgba(47, 84, 250, 0.1); }
.tone-green { --tone: #157347; --tone-bg: #e3f6ec; }
.tone-amber { --tone: #b26b00; --tone-bg: #fff1d6; }
.tone-red { --tone: #c62828; --tone-bg: #fde3e3; }
.tone-teal { --tone: #0a8e8e; --tone-bg: #ddf4f3; }
.tone-violet { --tone: #824dff; --tone-bg: rgba(130, 77, 255, 0.12); }

.hero {
  position: relative; overflow: hidden;
  display: flex; align-items: center; justify-content: space-between; gap: 1rem;
  padding: 1.75rem; border-radius: 20px; color: #fff;
  background:
    radial-gradient(420px 220px at 90% 10%, rgba(130, 77, 255, 0.55), transparent 65%),
    radial-gradient(360px 220px at 70% 120%, rgba(255, 158, 1, 0.28), transparent 65%),
    linear-gradient(135deg, #3f4b6e, #2a3358);
}
.hero h1 { color: #fff; font-size: 1.6rem; font-weight: 800; margin: 0.6rem 0 0.35rem; }
.hero p { color: rgba(255, 255, 255, 0.78); max-width: 34rem; margin-bottom: 1.1rem; }
.hero-art { font-size: 6rem; line-height: 1; color: rgba(255, 255, 255, 0.16); margin-right: 0.5rem; }
@media (max-width: 575.98px) {
  .hero { padding: 1.25rem; }
  .hero h1 { font-size: 1.35rem; }
  .hero-art { display: none; }
}

.list-row {
  display: flex; align-items: center; gap: 0.85rem;
  padding: 0.8rem 1.25rem; text-decoration: none; color: inherit;
  border-top: 1px solid var(--kdo-border); transition: background 0.15s;
}
.list-row:first-child { border-top: 0; }
.list-row:hover { background: rgba(47, 84, 250, 0.035); color: inherit; }

.tile {
  display: flex; align-items: center; gap: 0.8rem;
  padding: 0.9rem; border: 1px solid var(--kdo-border); border-radius: 14px;
  text-decoration: none; color: var(--kdo-ink); background: #fff;
  transition: transform 0.15s, box-shadow 0.15s;
}
.tile:hover { transform: translateY(-2px); box-shadow: var(--kdo-shadow); color: var(--kdo-ink); }
.tile small { display: block; color: var(--kdo-muted); font-size: 0.75rem; }

/* ===== Login ===== */
.auth-page {
  min-height: 100dvh; display: grid; place-items: center;
  padding: 1.25rem; padding-bottom: calc(1.25rem + env(safe-area-inset-bottom));
}
.auth-card {
  width: 100%; max-width: 880px;
  display: grid; grid-template-columns: 1fr 1.05fr;
  background: #fff; border: 1px solid var(--kdo-border); border-radius: 24px; overflow: hidden;
  box-shadow: 0 24px 60px rgba(31, 41, 90, 0.14);
}
.auth-aside {
  display: flex; flex-direction: column; justify-content: center; align-items: flex-start;
  padding: 2.75rem; background: #f3f4f8;
}
.auth-aside h2 { font-size: 1.5rem; font-weight: 800; margin: 1.1rem 0 0.4rem; color: var(--kdo-ink); }
.auth-aside p { color: var(--kdo-muted); font-size: 0.9rem; margin: 0 0 1.25rem; }
.auth-points { list-style: none; padding: 0; margin: 0; display: grid; gap: 0.6rem; font-size: 0.86rem; }
.auth-points i { color: var(--kdo-teal); margin-right: 0.5rem; }
.auth-form { padding: 2.75rem; }
.auth-form h1 { font-size: 1.5rem; font-weight: 800; color: var(--kdo-ink); margin: 0.7rem 0 0.3rem; }
@media (max-width: 767.98px) {
  .auth-card { grid-template-columns: 1fr; border-radius: 20px; }
  .auth-aside { flex-direction: row; align-items: center; gap: 0.9rem; padding: 1.1rem 1.25rem; }
  .auth-aside .brand-mark-lg { width: 44px; height: 44px; font-size: 1.3rem; border-radius: 14px; }
  .auth-aside h2 { margin: 0; font-size: 1.1rem; }
  .auth-aside p, .auth-points { display: none; }
  .auth-form { padding: 1.5rem 1.25rem; }
}

/* ===== Modal konfirmasi ===== */
.modal-backdrop-custom {
  position: fixed; inset: 0; z-index: 1040;
  background: rgba(16, 23, 39, 0.5);
  backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);
}
.modal { z-index: 1055; }
.modal-content { border: 0; border-radius: 20px; box-shadow: 0 24px 60px rgba(16, 23, 39, 0.25); }
.confirm-icon {
  width: 56px; height: 56px; margin: 0 auto 1rem; display: grid; place-items: center;
  border-radius: 50%; background: #fde3e3; color: #c62828; font-size: 1.5rem;
}

/* ===== Form: tombol aksi menempel di mobile ===== */
@media (max-width: 767.98px) {
  .form-actions {
    position: sticky; bottom: calc(4.6rem + env(safe-area-inset-bottom));
    display: flex; gap: 0.6rem; padding: 0.75rem; margin-top: 1rem;
    background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(10px);
    border: 1px solid var(--kdo-border); border-radius: 16px;
  }
  .form-actions .btn { flex: 1; }
}

/* ===== Sentuhan mobile ===== */
@media (max-width: 767.98px) {
  .btn, .form-control, .form-select { min-height: 44px; }
  .form-control, .form-select { font-size: 16px; } /* cegah auto-zoom di iOS */
  .icon-btn { width: 40px; height: 40px; }
}
```

### `src/App.vue` (ganti seluruh isi)

```vue
<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const nav = [
  { name: 'dashboard', label: 'Dashboard', short: 'Beranda', icon: 'bi-grid-1x2', match: '/' },
  { name: 'kendaraan.index', label: 'Kendaraan', short: 'Kendaraan', icon: 'bi-car-front', match: '/kendaraan' },
  { name: 'pegawai.index', label: 'Pegawai', short: 'Pegawai', icon: 'bi-people', match: '/pegawai' },
  { name: 'perangkat-daerah.index', label: 'Perangkat daerah', short: 'PD', icon: 'bi-buildings', match: '/perangkat-daerah' },
  { name: 'laporan.index', label: 'Laporan', short: 'Laporan', icon: 'bi-bar-chart-line', match: '/laporan' },
]

function isActive(item) {
  return item.match === '/' ? route.path === '/' : route.path.startsWith(item.match)
}

const initials = computed(() =>
  (auth.user?.name || '?')
    .split(' ')
    .map((s) => s[0])
    .slice(0, 2)
    .join('')
    .toUpperCase(),
)

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div v-if="auth.isAuthenticated">
    <header class="topbar">
      <div class="container topbar-inner">
        <router-link :to="{ name: 'dashboard' }" class="brand">
          <span class="brand-mark"><i class="bi bi-car-front-fill"></i></span>
          <span class="brand-text">
            <strong>Sistem KDO</strong>
            <small class="d-none d-sm-block">Kendaraan Dinas Operasional</small>
          </span>
        </router-link>

        <nav class="topnav d-none d-lg-flex">
          <router-link
            v-for="item in nav"
            :key="item.name"
            :to="{ name: item.name }"
            class="topnav-link"
            :class="{ active: isActive(item) }"
          >
            <i :class="['bi', item.icon]"></i>{{ item.label }}
          </router-link>
        </nav>

        <div class="dropdown topbar-user">
          <button class="user-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="user-avatar">{{ initials }}</span>
            <span class="d-none d-sm-inline">{{ auth.user?.name }}</span>
            <span class="role-tag d-none d-sm-inline">{{ auth.user?.role }}</span>
            <i class="bi bi-chevron-down small text-secondary"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2 d-sm-none">
              <div class="fw-semibold">{{ auth.user?.name }}</div>
              <span class="role-tag">{{ auth.user?.role }}</span>
            </li>
            <li>
              <button class="dropdown-item" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar
              </button>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main class="container app-main">
      <router-view />
    </main>

    <nav class="bottomnav d-lg-none" aria-label="Navigasi utama">
      <router-link
        v-for="item in nav"
        :key="item.name"
        :to="{ name: item.name }"
        :class="{ active: isActive(item) }"
      >
        <i :class="['bi', item.icon]"></i>
        <span>{{ item.short }}</span>
      </router-link>
    </nav>
  </div>

  <router-view v-else />
</template>
```

### `src/views/LoginView.vue` (ganti seluruh isi)

```vue
<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')

function isiAkunUji(role) {
  form.email = `${role}@kdo.test`
  form.password = 'password123'
}

async function submit() {
  loading.value = true
  errorMessage.value = ''

  try {
    await auth.login(form.email, form.password)
    router.push({ name: 'dashboard' })
  } catch (err) {
    const res = err.response?.data
    errorMessage.value =
      res?.errors?.email?.[0] || res?.errors?.password?.[0] || res?.message || 'Login gagal. Periksa email dan kata sandi Anda.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <aside class="auth-aside">
        <div class="brand-mark brand-mark-lg"><i class="bi bi-car-front-fill"></i></div>
        <div>
          <h2>Sistem KDO</h2>
          <p>Pengelolaan Kendaraan Dinas Operasional Perangkat Daerah</p>
          <ul class="auth-points">
            <li><i class="bi bi-check-circle-fill"></i>Pendataan kendaraan dan kondisinya</li>
            <li><i class="bi bi-check-circle-fill"></i>Penanggung jawab tiap kendaraan</li>
            <li><i class="bi bi-check-circle-fill"></i>Rekap per perangkat daerah</li>
          </ul>
        </div>
      </aside>

      <section class="auth-form">
        <span class="eyebrow">Selamat datang</span>
        <h1>Masuk ke akun Anda</h1>
        <p class="text-secondary small mb-4">Gunakan email dan kata sandi yang terdaftar.</p>

        <div v-if="errorMessage" class="alert alert-danger py-2 small d-flex gap-2 align-items-start" role="alert">
          <i class="bi bi-exclamation-circle mt-1"></i><span>{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="field">
              <i class="bi bi-envelope field-icon"></i>
              <input
                id="email"
                v-model.trim="form.email"
                type="email"
                class="form-control field-input"
                placeholder="nama@instansi.go.id"
                autocomplete="username"
                required
                autofocus
              />
            </div>
          </div>

          <div class="mb-4">
            <label for="password" class="form-label">Kata sandi</label>
            <div class="field">
              <i class="bi bi-lock field-icon"></i>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control field-input pe-5"
                placeholder="Masukkan kata sandi"
                autocomplete="current-password"
                required
              />
              <button
                type="button"
                class="icon-btn field-toggle"
                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                @click="showPassword = !showPassword"
              >
                <i :class="['bi', showPassword ? 'bi-eye-slash' : 'bi-eye']"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Masuk
          </button>
        </form>

        <div class="mt-4 pt-3 border-top">
          <div class="small text-secondary mb-2">Akun uji, ketuk untuk mengisi otomatis</div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-soft btn-sm" @click="isiAkunUji('admin')">
              <i class="bi bi-shield-check me-1"></i>Admin
            </button>
            <button type="button" class="btn btn-soft btn-sm" @click="isiAkunUji('viewer')">
              <i class="bi bi-eye me-1"></i>Viewer
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
```

### `src/components/PageHeader.vue`

```vue
<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  eyebrow: { type: String, default: '' },
})
</script>

<template>
  <div class="page-head">
    <div>
      <span v-if="eyebrow" class="eyebrow">{{ eyebrow }}</span>
      <h1>{{ title }}</h1>
      <p v-if="subtitle">{{ subtitle }}</p>
    </div>
    <div class="page-head-actions"><slot /></div>
  </div>
</template>
```

### `src/components/StatCard.vue`

```vue
<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], default: 0 },
  icon: { type: String, default: 'bi-circle' },
  tone: { type: String, default: 'blue' }, // blue | green | amber | red | teal | violet
})
</script>

<template>
  <div class="stat">
    <div class="stat-icon" :class="`tone-${tone}`"><i :class="['bi', icon]"></i></div>
    <div>
      <div class="stat-value">{{ value }}</div>
      <div class="stat-label">{{ label }}</div>
    </div>
  </div>
</template>
```

### `src/components/KondisiBadge.vue`

```vue
<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: { type: String, required: true },
  short: { type: Boolean, default: false },
})

const MAP = {
  baik: { label: 'Baik', short: 'Baik' },
  rusak_ringan: { label: 'Rusak ringan', short: 'Ringan' },
  rusak_berat: { label: 'Rusak berat', short: 'Berat' },
}

const item = computed(() => MAP[props.value] ?? { label: props.value, short: props.value })
</script>

<template>
  <span class="badge rounded-pill badge-kondisi" :class="`badge-kondisi-${value}`">
    {{ short ? item.short : item.label }}
  </span>
</template>
```

### `src/components/ConfirmModal.vue` (ganti seluruh isi)

```vue
<script setup>
defineProps({
  visible: { type: Boolean, default: false },
  title: { type: String, default: 'Konfirmasi' },
  message: { type: String, default: 'Yakin melanjutkan aksi ini?' },
  confirmLabel: { type: String, default: 'Ya, lanjutkan' },
  busy: { type: Boolean, default: false },
})

defineEmits(['confirm', 'cancel'])
</script>

<template>
  <div v-if="visible">
    <div class="modal-backdrop-custom" @click="$emit('cancel')"></div>
    <div class="modal d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog modal-dialog-centered px-2">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <div class="confirm-icon"><i class="bi bi-trash3"></i></div>
            <h5 class="mb-2">{{ title }}</h5>
            <p class="text-secondary mb-4">{{ message }}</p>
            <div class="d-flex gap-2">
              <button class="btn btn-soft flex-fill" :disabled="busy" @click="$emit('cancel')">Batal</button>
              <button class="btn btn-danger flex-fill" :disabled="busy" @click="$emit('confirm')">
                <span v-if="busy" class="spinner-border spinner-border-sm me-1"></span>
                {{ confirmLabel }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
```

### `src/components/DataTable.vue` (menggantikan versi di `UI-TABLE.md`)

Perubahan dari versi lama: radius 16px dan bayangan, footer aksi pada card mobile (bukan di samping, supaya tidak sempit), skeleton memakai class global `.skel`.

```vue
<script setup>
import { computed } from 'vue'

const props = defineProps({
  columns: { type: Array, required: true }, // [{ key, label, hideBelow?, class? }]
  rows: { type: Array, default: () => [] },
  loading: Boolean,
  meta: { type: Object, default: null },
  rowKey: { type: String, default: 'id' },
  clickable: Boolean,
  emptyIcon: { type: String, default: 'bi-inbox' },
  emptyTitle: { type: String, default: 'Belum ada data' },
  emptyText: { type: String, default: 'Data akan tampil di sini setelah ditambahkan.' },
})

const emit = defineEmits(['page-change', 'row-click'])

const pages = computed(() => {
  if (!props.meta) return []
  const { current_page: cur, last_page: last } = props.meta
  const start = Math.max(1, Math.min(cur - 2, last - 4))
  const end = Math.min(last, start + 4)
  const list = []
  for (let p = start; p <= end; p++) list.push(p)
  return list
})

const hideClass = (col) => (col.hideBelow ? `d-none d-${col.hideBelow}-table-cell` : '')

function go(page) {
  if (!props.meta || page < 1 || page > props.meta.last_page || page === props.meta.current_page) return
  emit('page-change', page)
}
</script>

<template>
  <div>
    <!-- DESKTOP: tabel -->
    <div class="dt-card d-none d-md-block">
      <div class="table-responsive">
        <table class="table dt-table align-middle mb-0">
          <thead>
            <tr>
              <th v-for="col in columns" :key="col.key" :class="[hideClass(col), col.class]">{{ col.label }}</th>
              <th v-if="$slots.actions" style="width: 130px"></th>
            </tr>
          </thead>
          <tbody>
            <template v-if="loading">
              <tr v-for="n in 5" :key="'sk' + n">
                <td v-for="col in columns" :key="col.key" :class="hideClass(col)"><span class="skel"></span></td>
                <td v-if="$slots.actions"></td>
              </tr>
            </template>
            <template v-else-if="rows.length">
              <tr
                v-for="row in rows"
                :key="row[rowKey]"
                :class="{ 'dt-click': clickable }"
                @click="clickable && emit('row-click', row)"
              >
                <td v-for="col in columns" :key="col.key" :class="[hideClass(col), col.class]">
                  <slot :name="`cell-${col.key}`" :row="row">{{ row[col.key] }}</slot>
                </td>
                <td v-if="$slots.actions" class="text-end" @click.stop>
                  <slot name="actions" :row="row" />
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div v-if="!loading && !rows.length" class="dt-empty">
        <i :class="['bi', emptyIcon]"></i>
        <h6>{{ emptyTitle }}</h6>
        <p>{{ emptyText }}</p>
        <slot name="empty" />
      </div>

      <div v-if="meta && rows.length && meta.last_page > 1" class="dt-footer">
        <span class="small text-secondary">
          Menampilkan {{ meta.from ?? '' }} sampai {{ meta.to ?? '' }} dari {{ meta.total }}
        </span>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary" :disabled="meta.current_page === 1" aria-label="Halaman sebelumnya" @click="go(meta.current_page - 1)">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button
            v-for="p in pages"
            :key="p"
            class="btn"
            :class="p === meta.current_page ? 'btn-primary' : 'btn-outline-secondary'"
            @click="go(p)"
          >{{ p }}</button>
          <button class="btn btn-outline-secondary" :disabled="meta.current_page === meta.last_page" aria-label="Halaman berikutnya" @click="go(meta.current_page + 1)">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- MOBILE: card list -->
    <div class="d-md-none">
      <template v-if="loading">
        <div v-for="n in 4" :key="'msk' + n" class="dt-mcard">
          <span class="skel mb-2" style="width: 55%"></span>
          <span class="skel mb-2" style="width: 80%"></span>
          <span class="skel" style="width: 40%"></span>
        </div>
      </template>

      <template v-else-if="rows.length">
        <div
          v-for="row in rows"
          :key="row[rowKey]"
          class="dt-mcard"
          :class="{ 'dt-click': clickable }"
          @click="clickable && emit('row-click', row)"
        >
          <slot name="card" :row="row" />
          <div v-if="$slots.actions" class="dt-mcard-actions" @click.stop>
            <slot name="actions" :row="row" />
          </div>
        </div>
      </template>

      <div v-else class="dt-card dt-empty">
        <i :class="['bi', emptyIcon]"></i>
        <h6>{{ emptyTitle }}</h6>
        <p>{{ emptyText }}</p>
        <slot name="empty" />
      </div>

      <div v-if="meta && rows.length && meta.last_page > 1" class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-soft btn-sm" :disabled="meta.current_page === 1" @click="go(meta.current_page - 1)">
          <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        <span class="small text-secondary">{{ meta.current_page }} / {{ meta.last_page }}</span>
        <button class="btn btn-soft btn-sm" :disabled="meta.current_page === meta.last_page" @click="go(meta.current_page + 1)">
          Berikutnya <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dt-card {
  background: #fff;
  border: 1px solid var(--kdo-border);
  border-radius: var(--kdo-radius);
  box-shadow: var(--kdo-shadow);
  overflow: hidden;
}
.dt-table { --bs-table-bg: transparent; }
.dt-table thead th { background: #fafbfe; }
.dt-table tbody tr:last-child td { border-bottom: 0; }
.dt-click { cursor: pointer; }

.dt-footer {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.75rem 1rem; border-top: 1px solid var(--kdo-border);
}

.dt-empty { text-align: center; padding: 3rem 1rem; }
.dt-empty i { font-size: 2.5rem; color: var(--kdo-blue); opacity: 0.35; }
.dt-empty h6 { margin: 0.75rem 0 0.25rem; font-weight: 700; }
.dt-empty p { color: var(--kdo-muted); font-size: 0.875rem; margin-bottom: 1rem; }

.dt-mcard {
  background: #fff;
  border: 1px solid var(--kdo-border);
  border-radius: var(--kdo-radius);
  box-shadow: var(--kdo-shadow);
  padding: 0.95rem;
  margin-bottom: 0.7rem;
}
.dt-mcard-actions {
  display: flex; justify-content: flex-end; gap: 0.25rem;
  margin: 0.75rem -0.35rem -0.35rem; padding-top: 0.5rem;
  border-top: 1px solid var(--kdo-border);
}
</style>
```

### `src/views/DashboardView.vue` (ganti seluruh isi)

```vue
<script setup>
import { onMounted, ref } from 'vue'
import laporanApi from '../api/laporan'
import kendaraanApi from '../api/kendaraan'
import { useAuthStore } from '../stores/auth'
import StatCard from '../components/StatCard.vue'
import KondisiBadge from '../components/KondisiBadge.vue'

const auth = useAuthStore()
const ringkasan = ref(null)
const rusakBerat = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const [laporanRes, rusakRes] = await Promise.all([
      laporanApi.kondisi(),
      kendaraanApi.list({ kondisi: 'rusak_berat', per_page: 5 }),
    ])
    ringkasan.value = laporanRes.data.ringkasan
    rusakBerat.value = rusakRes.data.data
  } finally {
    loading.value = false
  }
})

const stats = [
  { label: 'Total kendaraan', key: 'total_kendaraan', icon: 'bi-car-front', tone: 'blue' },
  { label: 'Kondisi baik', key: 'total_baik', icon: 'bi-check-circle', tone: 'green' },
  { label: 'Rusak ringan', key: 'total_rusak_ringan', icon: 'bi-exclamation-triangle', tone: 'amber' },
  { label: 'Rusak berat', key: 'total_rusak_berat', icon: 'bi-x-octagon', tone: 'red' },
  { label: 'Perangkat daerah', key: 'total_perangkat_daerah', icon: 'bi-buildings', tone: 'teal' },
]

const shortcuts = [
  { to: 'kendaraan.index', label: 'Data kendaraan', desc: 'Kelola KDO', icon: 'bi-car-front', tone: 'blue' },
  { to: 'pegawai.index', label: 'Data pegawai', desc: 'PNS penanggung jawab', icon: 'bi-people', tone: 'violet' },
  { to: 'perangkat-daerah.index', label: 'Perangkat daerah', desc: 'Instansi pemilik', icon: 'bi-buildings', tone: 'teal' },
  { to: 'laporan.index', label: 'Laporan', desc: 'Rekap kondisi', icon: 'bi-bar-chart-line', tone: 'amber' },
]
</script>

<template>
  <div>
    <section class="hero mb-4">
      <div>
        <span class="eyebrow eyebrow-light">Selamat datang</span>
        <h1>Halo, {{ auth.user?.name }}</h1>
        <p>Pantau kondisi kendaraan dinas operasional dan penanggung jawabnya dalam satu tempat.</p>
        <router-link :to="{ name: 'kendaraan.index' }" class="btn btn-light">
          Lihat kendaraan <i class="bi bi-arrow-right ms-1"></i>
        </router-link>
      </div>
      <div class="hero-art" aria-hidden="true"><i class="bi bi-car-front-fill"></i></div>
    </section>

    <div v-if="loading" class="stat-grid mb-4">
      <div v-for="n in 5" :key="n" class="stat"><span class="skel" style="height: 44px; width: 100%"></span></div>
    </div>

    <template v-else-if="ringkasan">
      <div class="stat-grid mb-4">
        <StatCard
          v-for="s in stats"
          :key="s.key"
          :label="s.label"
          :value="ringkasan[s.key]"
          :icon="s.icon"
          :tone="s.tone"
        />
      </div>

      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span class="fw-bold">Prioritas perbaikan</span>
              <span class="badge-kondisi badge-kondisi-rusak_berat badge rounded-pill">Rusak berat</span>
            </div>

            <router-link
              v-for="k in rusakBerat"
              :key="k.id"
              :to="{ name: 'kendaraan.show', params: { id: k.id } }"
              class="list-row"
            >
              <div class="stat-icon tone-red"><i class="bi bi-car-front"></i></div>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold">{{ k.nomor_polisi }}</div>
                <div class="small text-secondary text-truncate">
                  {{ k.merek }} {{ k.tipe }} · {{ k.perangkat_daerah?.nama }}
                </div>
              </div>
              <KondisiBadge :value="k.kondisi" short />
            </router-link>

            <div v-if="rusakBerat.length === 0" class="text-center py-5 px-3">
              <i class="bi bi-patch-check fs-1 text-success opacity-50"></i>
              <div class="fw-semibold mt-2">Semua kendaraan layak pakai</div>
              <div class="small text-secondary">Tidak ada kendaraan dengan kondisi rusak berat.</div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-header fw-bold">Akses cepat</div>
            <div class="card-body">
              <div class="row g-2">
                <div v-for="s in shortcuts" :key="s.to" class="col-6">
                  <router-link :to="{ name: s.to }" class="tile h-100">
                    <div class="stat-icon" :class="`tone-${s.tone}`"><i :class="['bi', s.icon]"></i></div>
                    <div class="fw-semibold small lh-sm">{{ s.label }}<small>{{ s.desc }}</small></div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
```

### `src/views/KendaraanView.vue` (ganti seluruh isi, jadi acuan halaman daftar lain)

```vue
<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import kendaraanApi from '../api/kendaraan'
import perangkatDaerahApi from '../api/perangkatDaerah'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'
import DataTable from '../components/DataTable.vue'
import KondisiBadge from '../components/KondisiBadge.vue'
import PageHeader from '../components/PageHeader.vue'

const auth = useAuthStore()
const router = useRouter()

const items = ref([])
const meta = ref(null)
const daftarPD = ref([])
const loading = ref(true)
const error = ref('')

const filters = reactive({ search: '', kondisi: '', perangkat_daerah_id: '', page: 1 })

const toDelete = ref(null)
const deleting = ref(false)

const columns = [
  { key: 'nomor_polisi', label: 'Nomor polisi' },
  { key: 'kendaraan', label: 'Kendaraan' },
  { key: 'tahun_produksi', label: 'Tahun', hideBelow: 'lg' },
  { key: 'perangkat_daerah', label: 'Perangkat daerah', hideBelow: 'lg' },
  { key: 'penanggung_jawab', label: 'Penanggung jawab', hideBelow: 'xl' },
  { key: 'kondisi', label: 'Kondisi' },
]

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await kendaraanApi.list({
      search: filters.search || undefined,
      kondisi: filters.kondisi || undefined,
      perangkat_daerah_id: filters.perangkat_daerah_id || undefined,
      page: filters.page,
    })
    items.value = res.data.data
    meta.value = res.data.meta
  } catch {
    error.value = 'Data kendaraan gagal dimuat. Periksa koneksi Anda lalu coba lagi.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const pdRes = await perangkatDaerahApi.list({ per_page: 100 })
  daftarPD.value = pdRes.data.data
  await load()
})

let timer
watch(
  () => filters.search,
  () => {
    clearTimeout(timer)
    timer = setTimeout(() => {
      filters.page = 1
      load()
    }, 400)
  },
)
watch(
  () => [filters.kondisi, filters.perangkat_daerah_id],
  () => {
    filters.page = 1
    load()
  },
)

function changePage(p) {
  filters.page = p
  load()
}

function resetFilter() {
  filters.search = ''
  filters.kondisi = ''
  filters.perangkat_daerah_id = ''
}

function openDetail(row) {
  router.push({ name: 'kendaraan.show', params: { id: row.id } })
}

async function confirmDelete() {
  deleting.value = true
  try {
    await kendaraanApi.remove(toDelete.value.id)
    toDelete.value = null
    await load()
  } finally {
    deleting.value = false
  }
}
</script>

<template>
  <div>
    <PageHeader
      eyebrow="Data utama"
      title="Kendaraan dinas"
      :subtitle="meta ? `${meta.total} kendaraan terdata` : 'Memuat data…'"
    >
      <router-link v-if="auth.isAdmin" :to="{ name: 'kendaraan.create' }" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah kendaraan
      </router-link>
    </PageHeader>

    <div class="toolbar">
      <div class="search-field flex-grow-1">
        <i class="bi bi-search"></i>
        <input v-model.trim="filters.search" type="search" class="form-control" placeholder="Cari nopol, merek, atau tipe" />
      </div>

      <button class="btn btn-soft d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterKdo">
        <i class="bi bi-sliders2 me-1"></i>Filter
      </button>

      <div id="filterKdo" class="collapse d-md-flex toolbar-filters">
        <select v-model="filters.kondisi" class="form-select" aria-label="Filter kondisi">
          <option value="">Semua kondisi</option>
          <option value="baik">Baik</option>
          <option value="rusak_ringan">Rusak ringan</option>
          <option value="rusak_berat">Rusak berat</option>
        </select>
        <select v-model="filters.perangkat_daerah_id" class="form-select" aria-label="Filter perangkat daerah">
          <option value="">Semua perangkat daerah</option>
          <option v-for="pd in daftarPD" :key="pd.id" :value="pd.id">{{ pd.nama }}</option>
        </select>
      </div>
    </div>

    <div v-if="error" class="alert alert-danger d-flex justify-content-between align-items-center" role="alert">
      <span>{{ error }}</span>
      <button class="btn btn-sm btn-outline-danger" @click="load">Coba lagi</button>
    </div>

    <DataTable
      :columns="columns"
      :rows="items"
      :loading="loading"
      :meta="meta"
      clickable
      empty-icon="bi-car-front"
      empty-title="Tidak ada kendaraan"
      empty-text="Belum ada data, atau tidak ada yang cocok dengan pencarian dan filter."
      @page-change="changePage"
      @row-click="openDetail"
    >
      <template #cell-nomor_polisi="{ row }"><span class="fw-semibold text-body">{{ row.nomor_polisi }}</span></template>
      <template #cell-kendaraan="{ row }">
        {{ row.merek }} <span class="text-secondary">{{ row.tipe }}</span>
      </template>
      <template #cell-perangkat_daerah="{ row }"><span class="text-secondary">{{ row.perangkat_daerah?.nama }}</span></template>
      <template #cell-penanggung_jawab="{ row }"><span class="text-secondary">{{ row.penanggung_jawab?.nama ?? '—' }}</span></template>
      <template #cell-kondisi="{ row }"><KondisiBadge :value="row.kondisi" /></template>

      <template #card="{ row }">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <span class="fw-bold">{{ row.nomor_polisi }}</span>
          <KondisiBadge :value="row.kondisi" short />
        </div>
        <div class="mt-1">{{ row.merek }} {{ row.tipe }}</div>
        <div class="small text-secondary">{{ row.perangkat_daerah?.nama }} · {{ row.tahun_produksi }}</div>
        <div class="small text-secondary mt-1">
          <i class="bi bi-person me-1"></i>{{ row.penanggung_jawab?.nama ?? 'Belum ada penanggung jawab' }}
        </div>
      </template>

      <template #actions="{ row }">
        <div class="row-actions">
          <router-link class="icon-btn" :to="{ name: 'kendaraan.show', params: { id: row.id } }" aria-label="Lihat detail" title="Lihat detail">
            <i class="bi bi-eye"></i>
          </router-link>
          <router-link
            v-if="auth.isAdmin"
            class="icon-btn"
            :to="{ name: 'kendaraan.edit', params: { id: row.id } }"
            aria-label="Ubah" title="Ubah"
          >
            <i class="bi bi-pencil"></i>
          </router-link>
          <button v-if="auth.isAdmin" class="icon-btn danger" aria-label="Hapus" title="Hapus" @click="toDelete = row">
            <i class="bi bi-trash3"></i>
          </button>
        </div>
      </template>

      <template #empty>
        <button class="btn btn-soft btn-sm me-2" @click="resetFilter">Reset filter</button>
        <router-link v-if="auth.isAdmin" :to="{ name: 'kendaraan.create' }" class="btn btn-primary btn-sm">Tambah kendaraan</router-link>
      </template>
    </DataTable>

    <ConfirmModal
      :visible="!!toDelete"
      title="Hapus kendaraan"
      :message="`Hapus kendaraan ${toDelete?.nomor_polisi}? Tindakan ini tidak dapat dibatalkan.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>
```

## 6. Halaman lain: porting mengikuti `KendaraanView.vue`

Halaman berikut belum ditulis ulang di dokumen ini. Kerjakan satu per satu dengan pola yang sama persis (PageHeader, toolbar, DataTable, ConfirmModal, icon-btn). Logika script (load, filter, hapus, validasi) **jangan diubah**, hanya tampilannya.

| Halaman | Kolom desktop | Isi card mobile | Catatan |
|---|---|---|---|
| `PegawaiView.vue` | Nama, NIP, Jabatan, Golongan (hideBelow lg), Perangkat daerah (hideBelow lg), Kendaraan ditangani | Nama (tebal), NIP kecil, jabatan, PD | NIP pakai font mono kecil, bukan `<code>` |
| `PerangkatDaerahView.vue` | Kode, Nama instansi, Pegawai, Kendaraan | Nama, kode, jumlah pegawai dan kendaraan | Form tambah/ubah tampil sebagai card di atas tabel, hilangkan `border-start border-4`, tombol Simpan/Batal pakai `.form-actions` |
| `LaporanView.vue` | PD, Total, Baik, Rusak ringan, Rusak berat, Tanpa PJ | Satu card per PD: kode + nama, lalu 3 angka kondisi sebagai mini badge | Ringkasan atas pakai `StatCard` di `.stat-grid`. Tambahkan bar horizontal proporsi kondisi per PD (div berlebar persen, hijau/kuning/merah pastel) |
| `KendaraanDetailView.vue` | — | — | Header: nopol besar + `KondisiBadge`. Info dalam dua card (Data kendaraan, Penanggung jawab) dengan baris label kecil abu-abu di atas nilai. Tombol Ubah/Hapus di `PageHeader`. Di mobile tombol memenuhi lebar |
| `KendaraanFormView.vue`, `PegawaiFormView.vue` | — | — | Form dalam card, grid dua kolom di desktop dan satu kolom di mobile. Kelompokkan field ("Identitas", "Spesifikasi", "Penugasan"). Tombol Simpan/Batal dibungkus `.form-actions` |

Ganti semua emoji yang tersisa (cari `🚗 ✅ ⚠️ ❌ 🏛️ 👷 📊 👋 🎉`) dengan Bootstrap Icons. Hapus semua class `table-light`, `btn-group` untuk aksi baris, dan `border-start border-4` dari halaman yang sudah dimigrasi.

## 7. Perubahan terhadap `UI-TABLE.md`

- Warna dan gaya: ikuti dokumen ini (bagian 3 dan 5)
- Aksi baris: **tombol ikon** (lihat, ubah, hapus), bukan dropdown titik tiga. Alasan: dropdown di dalam `.table-responsive` terpotong oleh `overflow`
- `DataTable.vue`: pakai versi di dokumen ini
- Selebihnya (state, konfirmasi hapus, role, checklist) tetap berlaku

## 8. Checklist responsif dan lintas platform

Uji di lebar 360px, 390px, 768px, 1024px, dan 1440px (DevTools device toolbar, plus satu perangkat sungguhan jika ada).

- [ ] Tidak ada scroll horizontal di semua halaman pada 360px
- [ ] Bottom nav tidak menutupi tombol atau konten terakhir (padding bawah sudah ada)
- [ ] Topbar dan bottom nav aman terhadap notch/home indicator (`env(safe-area-inset-*)`)
- [ ] Input 16px di mobile (tidak ada auto-zoom iOS), tinggi sentuh minimal 44px
- [ ] Tabel jadi card di bawah 768px, aksi ada di footer card
- [ ] Filter di mobile terlipat di balik tombol Filter
- [ ] Login: split layout di desktop, satu kolom di mobile, tombol akun uji berfungsi
- [ ] Modal hapus tidak terpotong di layar kecil
- [ ] Tidak ada emoji tersisa di seluruh `src/`
- [ ] Kontras teks cukup (teks abu `#6B7494` hanya untuk info sekunder, bukan isi penting)
- [ ] `npm run build` sukses tanpa error

## 9. Tambahan untuk `AGENTS.md`

Di bagian 5 (Frontend), tambahkan:

```markdown
- **Tampilan**: wajib mengikuti `UI-REDESIGN.md` (palet DKI/BPAD, Plus Jakarta Sans, Bootstrap Icons, tanpa emoji) dan `UI-TABLE.md` (tabel di desktop, card di mobile). Gunakan komponen bersama `PageHeader`, `StatCard`, `DataTable`, `KondisiBadge`, `ConfirmModal`. Jangan menulis `<table>` mentah atau warna hex langsung di halaman, pakai token `--kdo-*` di `style.css`.
```

## 10. Urutan kerja yang disarankan untuk agen AI

Satu commit per langkah (`feat:` atau `refactor:`):

1. Pasang dependensi, perbarui `main.js`, `index.html`, `style.css`
2. `App.vue` (topbar + bottom nav)
3. `LoginView.vue`
4. Komponen: `PageHeader`, `StatCard`, `KondisiBadge`, `ConfirmModal`, `DataTable`
5. `DashboardView.vue`
6. `KendaraanView.vue`
7. `PegawaiView.vue`, `PerangkatDaerahView.vue`
8. `LaporanView.vue`
9. `KendaraanDetailView.vue`, kedua form
10. Hapus sisa emoji, jalankan checklist bagian 8, `npm run build`

Jangan mengubah backend, route, store, atau nama field API selama redesign.
