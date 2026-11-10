<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import pegawaiApi from '../api/pegawai'
import perangkatDaerahApi from '../api/perangkatDaerah'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'
import DataTable from '../components/DataTable.vue'
import PageHeader from '../components/PageHeader.vue'

const auth = useAuthStore()

const items = ref([])
const meta = ref(null)
const daftarPD = ref([])
const loading = ref(true)
const error = ref('')

const filters = reactive({ search: '', perangkat_daerah_id: '', page: 1 })

const toDelete = ref(null)
const deleting = ref(false)

const columns = [
  { key: 'nama', label: 'Nama' },
  { key: 'nip', label: 'NIP' },
  { key: 'jabatan', label: 'Jabatan' },
  { key: 'golongan', label: 'Golongan', hideBelow: 'lg' },
  { key: 'perangkat_daerah', label: 'Perangkat daerah', hideBelow: 'lg' },
  { key: 'jumlah_kendaraan_ditangani', label: 'Kendaraan ditangani', class: 'text-center' },
]

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await pegawaiApi.list({
      search: filters.search || undefined,
      perangkat_daerah_id: filters.perangkat_daerah_id || undefined,
      page: filters.page,
    })
    items.value = res.data.data
    meta.value = res.data.meta
  } catch {
    error.value = 'Data pegawai gagal dimuat. Periksa koneksi Anda lalu coba lagi.'
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
  () => filters.perangkat_daerah_id,
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
  filters.perangkat_daerah_id = ''
}

async function confirmDelete() {
  deleting.value = true
  try {
    await pegawaiApi.remove(toDelete.value.id)
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
      title="Pegawai Negeri Sipil"
      :subtitle="meta ? `${meta.total} pegawai terdata` : 'Memuat data…'"
    >
      <router-link v-if="auth.isAdmin" :to="{ name: 'pegawai.create' }" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah pegawai
      </router-link>
    </PageHeader>

    <div class="toolbar">
      <div class="search-field flex-grow-1">
        <i class="bi bi-search"></i>
        <input v-model.trim="filters.search" type="search" class="form-control" placeholder="Cari nama, NIP, atau jabatan" />
      </div>

      <button class="btn btn-soft d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterPegawai">
        <i class="bi bi-sliders2 me-1"></i>Filter
      </button>

      <div id="filterPegawai" class="collapse d-md-flex toolbar-filters">
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
      empty-icon="bi-people"
      empty-title="Tidak ada pegawai"
      empty-text="Belum ada data, atau tidak ada yang cocok dengan pencarian dan filter."
      @page-change="changePage"
    >
      <template #cell-nama="{ row }"><span class="fw-semibold text-body">{{ row.nama }}</span></template>
      <template #cell-nip="{ row }"><span class="font-monospace small text-secondary">{{ row.nip }}</span></template>
      <template #cell-perangkat_daerah="{ row }"><span class="text-secondary">{{ row.perangkat_daerah?.nama }}</span></template>
      <template #cell-jumlah_kendaraan_ditangani="{ row }">
        <span class="fw-semibold">{{ row.jumlah_kendaraan_ditangani ?? 0 }}</span>
      </template>

      <template #card="{ row }">
        <div class="fw-bold">{{ row.nama }}</div>
        <div class="font-monospace small text-secondary">{{ row.nip }}</div>
        <div class="small mt-1">{{ row.jabatan }} · {{ row.golongan }}</div>
        <div class="small text-secondary">
          <i class="bi bi-buildings me-1"></i>{{ row.perangkat_daerah?.nama }}
        </div>
        <div class="small text-secondary">
          <i class="bi bi-car-front me-1"></i>{{ row.jumlah_kendaraan_ditangani ?? 0 }} kendaraan ditangani
        </div>
      </template>

      <template #actions="{ row }">
        <div class="row-actions">
          <router-link
            v-if="auth.isAdmin"
            class="icon-btn"
            :to="{ name: 'pegawai.edit', params: { id: row.id } }"
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
        <router-link v-if="auth.isAdmin" :to="{ name: 'pegawai.create' }" class="btn btn-primary btn-sm">Tambah pegawai</router-link>
      </template>
    </DataTable>

    <ConfirmModal
      :visible="!!toDelete"
      title="Hapus pegawai"
      :message="`Hapus pegawai ${toDelete?.nama}? Kendaraan yang ditangani akan menjadi tanpa penanggung jawab.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>
