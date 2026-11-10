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
      <router-link v-if="auth.canWrite" :to="{ name: 'kendaraan.create' }" class="btn btn-primary">
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
            v-if="auth.canWrite"
            class="icon-btn"
            :to="{ name: 'kendaraan.edit', params: { id: row.id } }"
            aria-label="Ubah" title="Ubah"
          >
            <i class="bi bi-pencil"></i>
          </router-link>
          <button v-if="auth.canWrite" class="icon-btn danger" aria-label="Hapus" title="Hapus" @click="toDelete = row">
            <i class="bi bi-trash3"></i>
          </button>
        </div>
      </template>

      <template #empty>
        <button class="btn btn-soft btn-sm me-2" @click="resetFilter">Reset filter</button>
        <router-link v-if="auth.canWrite" :to="{ name: 'kendaraan.create' }" class="btn btn-primary btn-sm">Tambah kendaraan</router-link>
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
