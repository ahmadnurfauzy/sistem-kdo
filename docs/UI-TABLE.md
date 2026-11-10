# UI-TABLE.md — Panduan UI/UX Tabel & Daftar Data

Dokumen ini adalah **spesifikasi wajib** untuk semua tampilan daftar data (KDO, PNS, Perangkat Daerah) di `frontend/`. Baca bersama `AGENTS.md`. Jika ada konflik, `AGENTS.md` bagian arsitektur menang, dokumen ini menang untuk urusan tampilan.

## 1. Arah Desain

Gaya: **Clean and minimal**. Terasa seperti dashboard SaaS modern: putih, lega, tenang, tidak ramai.

Prinsip utama:

1. **Satu komponen untuk semua daftar**: `src/components/DataTable.vue`. Halaman KDO, PNS, dan PD hanya mengisi kolom dan slot. Dilarang membuat `<table>` mentah di halaman.
2. **Desktop/tablet (>= 768px) tampil sebagai tabel. Mobile (< 768px) tampil sebagai card list.** Tidak boleh ada scroll horizontal di mobile.
3. **Warna hanya untuk makna**, terutama badge kondisi. Selebihnya netral.
4. **Hirarki jelas**: info utama tebal, info pendukung abu-abu dan lebih kecil.
5. **Selalu tangani 4 state**: loading, kosong, error, berisi data.

Yang DILARANG:

- Tabel Bootstrap default berborder penuh (`table-bordered`, `table-striped`)
- Warna mencolok, gradient, bayangan tebal, animasi berlebihan
- `alert()` / `confirm()` bawaan browser (pakai modal)
- Spinner polos sebagai satu-satunya loading
- Library UI tambahan (Vuetify, PrimeVue, Element Plus, dll). Cukup Bootstrap 5.3 + CSS custom + Bootstrap Icons

## 2. Spesifikasi Desktop (>= 768px)

**Wrapper**: card dengan `border: 1px solid var(--bs-border-color)`, `border-radius: 12px`, `overflow: hidden`, latar `var(--bs-body-bg)`. Tanpa shadow.

**Toolbar** (di atas tabel, di dalam halaman, bukan di dalam komponen):

- Kiri: input search dengan ikon `bi-search`, placeholder contoh nyata ("Cari nopol, merek, atau PD"), debounce 400 ms
- Tengah/kanan: filter (select kondisi, select PD)
- Paling kanan: tombol **Tambah** (primary), tampil hanya untuk role `admin`

**Header tabel**:

- Latar transparan, teks `var(--bs-secondary-color)`, 12px, `font-weight: 500`
- Sticky (`position: sticky; top: 0`), garis bawah 1px
- Tidak huruf kapital semua, pakai sentence case ("Nomor polisi", bukan "NOMOR POLISI")

**Baris**:

- Padding vertikal `0.85rem`, tinggi nyaman (min ~52px)
- Pemisah antar baris hanya garis tipis horizontal (`border-top: 1px solid var(--bs-border-color)`), tanpa garis vertikal
- Hover: latar `var(--bs-tertiary-bg)` lembut
- Kolom utama (nomor polisi / nama) `font-weight: 500`
- Kolom sekunder (PD, tahun, warna) pakai teks abu-abu
- Baris boleh diklik untuk buka detail (`cursor: pointer`)

**Kolom aksi** (paling kanan):

- Satu tombol ikon `bi-three-dots-vertical` yang membuka dropdown: Lihat, Ubah, Hapus (Hapus berwarna danger)
- Untuk role `viewer`, hanya "Lihat" yang ada, atau kolom aksi disembunyikan sama sekali

**Responsif tabel**: kolom sekunder boleh disembunyikan di layar menengah lewat properti `hideBelow: 'lg'` pada definisi kolom.

**Footer / paginasi**:

- Kiri: teks "Menampilkan 1 sampai 10 dari 15"
- Kanan: tombol prev, nomor halaman (jendela maksimal 5), next
- Style tombol: outline kecil, halaman aktif terisi `primary`

## 3. Spesifikasi Mobile (< 768px)

Tiap baris data menjadi **satu card**:

```
┌──────────────────────────────┐
│ B 1234 XYZ          [ Baik ] │   ← baris 1: judul (tebal) + badge
│ Toyota Avanza                │   ← baris 2: info utama
│ Dinas Pendidikan · 2021      │   ← baris 3: info pendukung (abu-abu, kecil)
│                           ⋮  │   ← aksi (titik tiga), hanya admin
└──────────────────────────────┘
```

- Card: border 1px, radius 12px, padding `0.9rem`, jarak antar card `0.6rem`, tanpa shadow
- Seluruh card bisa di-tap untuk buka detail
- Area sentuh minimal **44x44px** untuk tombol aksi
- Filter dilipat di balik tombol **Filter** yang membuka `offcanvas` (dari bawah), bukan baris panjang di atas
- Search tetap tampil penuh di atas daftar
- Paginasi cukup tombol **Sebelumnya / Berikutnya** dan teks "Halaman 2 dari 5"
- Detail data dibuka di `offcanvas` samping/bawah, bukan pindah halaman

## 4. Badge Kondisi (KDO)

Gunakan class subtle Bootstrap 5.3 supaya otomatis aman untuk dark mode:

| Nilai API | Label | Class |
|---|---|---|
| `baik` | Baik | `bg-success-subtle text-success-emphasis` |
| `rusak_ringan` | Rusak ringan | `bg-warning-subtle text-warning-emphasis` |
| `rusak_berat` | Rusak berat | `bg-danger-subtle text-danger-emphasis` |

Bentuk: pill (`rounded-pill`), `font-size: 0.75rem`, padding `0.25rem 0.6rem`, `white-space: nowrap`. Di mobile boleh label singkat ("Ringan", "Berat"). Buat sebagai komponen `KondisiBadge.vue` (lihat bagian 8) dan pakai di mana pun kondisi ditampilkan.

Warna **tidak boleh jadi satu-satunya penanda**: label teks selalu ada.

## 5. State Wajib

| State | Desktop | Mobile |
|---|---|---|
| **Loading** | 5 baris skeleton shimmer (bukan spinner) | 4 card skeleton |
| **Kosong** | Ikon besar abu-abu + judul ("Belum ada data KDO") + 1 kalimat + tombol "Tambah KDO" (admin saja). Jika kosong karena filter: "Tidak ada hasil" + tombol "Reset filter" | sama |
| **Error** | Banner ringan di atas daftar: pesan jelas + tombol "Coba lagi" | sama |
| **Berisi** | Tabel normal | Card list |

## 6. Interaksi

- **Konfirmasi hapus**: modal Bootstrap berisi nama/identitas data yang dihapus ("Hapus KDO B 1234 XYZ?"), tombol "Batal" dan "Hapus" (danger). Dilarang `confirm()`.
- **Setelah aksi sukses**: toast kecil ("KDO ditambahkan", "Data diperbarui"), tanpa tanda seru, tanpa kata "berhasil".
- **Tombol saat submit**: nonaktif + spinner kecil di dalam tombol, cegah klik ganda.
- **Error validasi 422**: tampilkan di bawah field terkait (`invalid-feedback`), bukan alert global.
- **Search + filter**: nilai disinkronkan ke query string URL supaya refresh/bagikan link tidak kehilangan filter.
- **Role**: tombol Tambah, Ubah, Hapus hanya muncul untuk `admin`. Ambil dari Pinia store, jangan hardcode.

## 7. Gaya & Token

Gunakan variabel CSS Bootstrap 5.3 (bukan hex mentah) supaya dark mode `data-bs-theme="dark"` tinggal nyala:

| Kebutuhan | Pakai |
|---|---|
| Latar | `var(--bs-body-bg)`, `var(--bs-tertiary-bg)` |
| Border | `var(--bs-border-color)` |
| Teks sekunder | `var(--bs-secondary-color)` |
| Radius card | `12px`; kontrol `8px` |
| Font | bawaan Bootstrap, ukuran isi tabel `0.875rem`, header `0.75rem` |

Satu warna aksen saja (`primary` Bootstrap) untuk tombol utama dan halaman aktif. Ikon: **Bootstrap Icons** (`bootstrap-icons`).

## 8. Implementasi Acuan

Gunakan sebagai titik awal, boleh disesuaikan selama spesifikasi di atas tetap terpenuhi.

### 8.1 `src/components/KondisiBadge.vue`

```vue
<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: { type: String, required: true },
  short: { type: Boolean, default: false },
})

const MAP = {
  baik: { label: 'Baik', short: 'Baik', cls: 'bg-success-subtle text-success-emphasis' },
  rusak_ringan: { label: 'Rusak ringan', short: 'Ringan', cls: 'bg-warning-subtle text-warning-emphasis' },
  rusak_berat: { label: 'Rusak berat', short: 'Berat', cls: 'bg-danger-subtle text-danger-emphasis' },
}

const item = computed(() => MAP[props.value] ?? { label: props.value, short: props.value, cls: 'bg-secondary-subtle text-secondary-emphasis' })
</script>

<template>
  <span class="badge rounded-pill fw-medium" :class="item.cls" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; white-space: nowrap">
    {{ short ? item.short : item.label }}
  </span>
</template>
```

### 8.2 `src/components/DataTable.vue`

Kontrak komponen:

- **Props**: `columns` (`[{ key, label, hideBelow?, class? }]`), `rows`, `loading`, `meta` (objek paginasi dari API: `current_page`, `last_page`, `total`, `from`, `to`), `rowKey` (default `id`), `emptyTitle`, `emptyText`, `clickable`
- **Slot**: `cell-{key}` (scope: `row`), `actions` (scope: `row`), `card` (scope: `row`, isi card mobile), `empty` (aksi saat kosong, mis. tombol Tambah)
- **Emit**: `page-change(page)`, `row-click(row)`

```vue
<script setup>
import { computed } from 'vue'

const props = defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, default: () => [] },
  loading: Boolean,
  meta: { type: Object, default: null },
  rowKey: { type: String, default: 'id' },
  clickable: Boolean,
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
      <div class="table-responsive dt-scroll">
        <table class="table dt-table align-middle mb-0">
          <thead>
            <tr>
              <th v-for="col in columns" :key="col.key" :class="[hideClass(col), col.class]">{{ col.label }}</th>
              <th v-if="$slots.actions" style="width: 56px"></th>
            </tr>
          </thead>
          <tbody>
            <template v-if="loading">
              <tr v-for="n in 5" :key="'sk' + n">
                <td v-for="col in columns" :key="col.key" :class="hideClass(col)"><span class="dt-skel"></span></td>
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
        <i class="bi bi-inbox"></i>
        <h6>{{ emptyTitle }}</h6>
        <p>{{ emptyText }}</p>
        <slot name="empty" />
      </div>

      <div v-if="meta && rows.length" class="dt-footer">
        <span class="small text-body-secondary">Menampilkan {{ meta.from }} sampai {{ meta.to }} dari {{ meta.total }}</span>
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
          <span class="dt-skel mb-2" style="width: 55%"></span>
          <span class="dt-skel mb-2" style="width: 80%"></span>
          <span class="dt-skel" style="width: 40%"></span>
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
          <div class="flex-grow-1 min-w-0">
            <slot name="card" :row="row" />
          </div>
          <div v-if="$slots.actions" class="ms-2" @click.stop>
            <slot name="actions" :row="row" />
          </div>
        </div>
      </template>
      <div v-else class="dt-card dt-empty">
        <i class="bi bi-inbox"></i>
        <h6>{{ emptyTitle }}</h6>
        <p>{{ emptyText }}</p>
        <slot name="empty" />
      </div>

      <div v-if="meta && rows.length" class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-outline-secondary btn-sm" :disabled="meta.current_page === 1" @click="go(meta.current_page - 1)">Sebelumnya</button>
        <span class="small text-body-secondary">Halaman {{ meta.current_page }} dari {{ meta.last_page }}</span>
        <button class="btn btn-outline-secondary btn-sm" :disabled="meta.current_page === meta.last_page" @click="go(meta.current_page + 1)">Berikutnya</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dt-card { background: var(--bs-body-bg); border: 1px solid var(--bs-border-color); border-radius: 12px; overflow: hidden; }
.dt-scroll { max-height: 70vh; }
.dt-table { --bs-table-bg: transparent; font-size: 0.875rem; }
.dt-table thead th {
  position: sticky; top: 0; z-index: 1;
  background: var(--bs-body-bg);
  color: var(--bs-secondary-color);
  font-size: 0.75rem; font-weight: 500;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--bs-border-color);
}
.dt-table tbody td { padding: 0.85rem 1rem; border-top: 1px solid var(--bs-border-color); border-bottom: 0; }
.dt-table tbody tr:first-child td { border-top: 0; }
.dt-table tbody tr:hover td { background: var(--bs-tertiary-bg); }
.dt-click { cursor: pointer; }

.dt-footer { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-top: 1px solid var(--bs-border-color); }

.dt-empty { text-align: center; padding: 3rem 1rem; }
.dt-empty i { font-size: 2.5rem; color: var(--bs-secondary-color); opacity: 0.6; }
.dt-empty h6 { margin: 0.75rem 0 0.25rem; font-weight: 500; }
.dt-empty p { color: var(--bs-secondary-color); font-size: 0.875rem; margin-bottom: 1rem; }

.dt-mcard {
  display: flex; align-items: flex-start;
  background: var(--bs-body-bg); border: 1px solid var(--bs-border-color);
  border-radius: 12px; padding: 0.9rem; margin-bottom: 0.6rem;
}
.min-w-0 { min-width: 0; }

.dt-skel {
  display: block; height: 14px; width: 70%; border-radius: 6px;
  background: linear-gradient(90deg, var(--bs-tertiary-bg) 25%, var(--bs-secondary-bg) 50%, var(--bs-tertiary-bg) 75%);
  background-size: 200% 100%;
  animation: dt-shimmer 1.2s infinite linear;
}
@keyframes dt-shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }
</style>
```

### 8.3 Contoh pemakaian di halaman KDO

```vue
<DataTable
  :columns="[
    { key: 'nomor_polisi', label: 'Nomor polisi' },
    { key: 'kendaraan', label: 'Kendaraan' },
    { key: 'perangkat_daerah', label: 'Perangkat daerah', hideBelow: 'lg' },
    { key: 'kondisi', label: 'Kondisi' },
  ]"
  :rows="kdo.items"
  :loading="kdo.loading"
  :meta="kdo.meta"
  clickable
  empty-title="Belum ada data KDO"
  empty-text="Tambahkan kendaraan dinas pertama untuk mulai mendata."
  @page-change="kdo.fetch"
  @row-click="openDetail"
>
  <template #cell-nomor_polisi="{ row }"><span class="fw-medium">{{ row.nomor_polisi }}</span></template>
  <template #cell-kendaraan="{ row }">{{ row.merek }} {{ row.tipe }}</template>
  <template #cell-perangkat_daerah="{ row }"><span class="text-body-secondary">{{ row.perangkat_daerah?.nama }}</span></template>
  <template #cell-kondisi="{ row }"><KondisiBadge :value="row.kondisi" /></template>

  <template #card="{ row }">
    <div class="d-flex justify-content-between align-items-center">
      <span class="fw-medium">{{ row.nomor_polisi }}</span>
      <KondisiBadge :value="row.kondisi" short />
    </div>
    <div class="small mt-1">{{ row.merek }} {{ row.tipe }}</div>
    <div class="small text-body-secondary">{{ row.perangkat_daerah?.nama }} · {{ row.tahun_produksi }}</div>
  </template>

  <template v-if="auth.isAdmin" #actions="{ row }">
    <!-- dropdown titik tiga: Lihat, Ubah, Hapus -->
  </template>

  <template v-if="auth.isAdmin" #empty>
    <button class="btn btn-primary btn-sm" @click="openCreate">Tambah KDO</button>
  </template>
</DataTable>
```

## 9. Checklist Sebelum Menyelesaikan Halaman Daftar

- [ ] Memakai `DataTable.vue`, bukan `<table>` buatan sendiri
- [ ] Desktop berupa tabel, mobile berupa card (cek di lebar 375px dan 768px)
- [ ] Tidak ada scroll horizontal di mobile
- [ ] Badge kondisi memakai `KondisiBadge.vue`
- [ ] Skeleton loading, empty state, dan error state tampil benar
- [ ] Hapus lewat modal konfirmasi, hasil aksi lewat toast
- [ ] Tombol tulis hanya tampil untuk `admin`
- [ ] Search dan filter tersinkron ke query string, paginasi memakai `meta` dari API
- [ ] Area sentuh tombol di mobile minimal 44px
- [ ] Tampilan tetap terbaca di dark mode (`data-bs-theme="dark"`)

## 10. Catatan untuk Agen AI

- Jika menambah halaman daftar baru (PNS, PD, laporan), **pakai ulang** komponen di atas dan hanya definisikan kolom, slot sel, dan slot card.
- Jika spesifikasi di sini terasa kurang untuk kasus tertentu, pilih opsi yang **lebih sederhana dan lebih tenang secara visual**, bukan menambah dekorasi.
- Jika menambah konvensi UI baru, perbarui dokumen ini dan catat di `AGENTS.md`.
