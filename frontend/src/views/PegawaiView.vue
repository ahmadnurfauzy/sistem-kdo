<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import pegawaiApi from '../api/pegawai'
import perangkatDaerahApi from '../api/perangkatDaerah'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'

const auth = useAuthStore()

const items = ref([])
const meta = ref(null)
const daftarPD = ref([])
const loading = ref(true)

const filters = reactive({
  search: '',
  perangkat_daerah_id: '',
  page: 1,
})

const toDelete = ref(null)
const deleting = ref(false)

async function load() {
  loading.value = true
  try {
    const res = await pegawaiApi.list({
      search: filters.search || undefined,
      perangkat_daerah_id: filters.perangkat_daerah_id || undefined,
      page: filters.page,
    })
    items.value = res.data.data
    meta.value = res.data.meta
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const pdRes = await perangkatDaerahApi.list({ per_page: 100 })
  daftarPD.value = pdRes.data.data
  await load()
})

watch(
  () => ({ ...filters }),
  () => {
    filters.page = 1
    load()
  },
)

function changePage(p) {
  filters.page = p
  load()
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
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div>
        <h4 class="mb-1">Pegawai Negeri Sipil</h4>
        <p class="text-secondary mb-0">{{ meta ? `${meta.total} pegawai terdata` : 'Memuat…' }}</p>
      </div>
      <router-link v-if="auth.isAdmin" :to="{ name: 'pegawai.create' }" class="btn btn-primary">
        + Tambah Pegawai
      </router-link>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-5">
            <input
              v-model.trim="filters.search"
              type="search"
              class="form-control"
              placeholder="Cari nama / NIP / jabatan…"
            />
          </div>
          <div class="col-6 col-md-4">
            <select v-model="filters.perangkat_daerah_id" class="form-select">
              <option value="">Semua perangkat daerah</option>
              <option v-for="pd in daftarPD" :key="pd.id" :value="pd.id">{{ pd.nama }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama</th>
              <th>NIP</th>
              <th>Jabatan</th>
              <th>Golongan</th>
              <th>Perangkat Daerah</th>
              <th class="text-center">Kendaraan Ditangani</th>
              <th v-if="auth.isAdmin" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in items" :key="p.id">
              <td class="fw-semibold">{{ p.nama }}</td>
              <td><code>{{ p.nip }}</code></td>
              <td>{{ p.jabatan }}</td>
              <td>{{ p.golongan }}</td>
              <td>{{ p.perangkat_daerah?.nama }}</td>
              <td class="text-center">{{ p.jumlah_kendaraan_ditangani ?? 0 }}</td>
              <td v-if="auth.isAdmin" class="text-end">
                <div class="btn-group btn-group-sm">
                  <router-link
                    class="btn btn-outline-primary"
                    :to="{ name: 'pegawai.edit', params: { id: p.id } }"
                  >
                    Edit
                  </router-link>
                  <button class="btn btn-outline-danger" @click="toDelete = p">Hapus</button>
                </div>
              </td>
            </tr>
            <tr v-if="items.length === 0">
              <td :colspan="auth.isAdmin ? 7 : 6" class="text-center text-secondary py-5">
                Tidak ada pegawai yang cocok dengan filter.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="meta && meta.last_page > 1" class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span class="text-secondary small">Halaman {{ meta.current_page }} dari {{ meta.last_page }}</span>
        <div class="btn-group">
          <button
            class="btn btn-sm btn-outline-secondary"
            :disabled="meta.current_page <= 1"
            @click="changePage(meta.current_page - 1)"
          >
            ‹ Sebelumnya
          </button>
          <button
            class="btn btn-sm btn-outline-secondary"
            :disabled="meta.current_page >= meta.last_page"
            @click="changePage(meta.current_page + 1)"
          >
            Berikutnya ›
          </button>
        </div>
      </div>
    </div>

    <ConfirmModal
      :visible="!!toDelete"
      title="Hapus Pegawai"
      :message="`Hapus pegawai ${toDelete?.nama}? Kendaraan yang ditangani akan menjadi tanpa penanggung jawab.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>
