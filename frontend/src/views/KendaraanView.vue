<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import kendaraanApi from '../api/kendaraan'
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
  kondisi: '',
  perangkat_daerah_id: '',
  page: 1,
})

const toDelete = ref(null)
const deleting = ref(false)

async function load() {
  loading.value = true
  try {
    const res = await kendaraanApi.list({
      search: filters.search || undefined,
      kondisi: filters.kondisi || undefined,
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
  const [pdRes] = await Promise.all([perangkatDaerahApi.list({ per_page: 100 })])
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
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div>
        <h4 class="mb-1">Kendaraan Dinas Operasional</h4>
        <p class="text-secondary mb-0">
          {{ meta ? `${meta.total} kendaraan terdata` : 'Memuat…' }}
        </p>
      </div>
      <router-link
        v-if="auth.isAdmin"
        :to="{ name: 'kendaraan.create' }"
        class="btn btn-primary"
      >
        + Tambah Kendaraan
      </router-link>
    </div>

    <!-- filter -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-4">
            <input
              v-model.trim="filters.search"
              type="search"
              class="form-control"
              placeholder="Cari nomor polisi / merek / tipe…"
            />
          </div>
          <div class="col-6 col-md-3">
            <select v-model="filters.kondisi" class="form-select">
              <option value="">Semua kondisi</option>
              <option value="baik">Baik</option>
              <option value="rusak_ringan">Rusak Ringan</option>
              <option value="rusak_berat">Rusak Berat</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <select v-model="filters.perangkat_daerah_id" class="form-select">
              <option value="">Semua perangkat daerah</option>
              <option v-for="pd in daftarPD" :key="pd.id" :value="pd.id">
                {{ pd.nama }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- tabel -->
    <div class="card border-0 shadow-sm">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nomor Polisi</th>
              <th>Merek / Tipe</th>
              <th class="text-center">Tahun</th>
              <th>Warna</th>
              <th>Kondisi</th>
              <th>Perangkat Daerah</th>
              <th>Penanggung Jawab</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="k in items" :key="k.id">
              <td>
                <router-link
                  :to="{ name: 'kendaraan.show', params: { id: k.id } }"
                  class="fw-semibold text-decoration-none"
                >
                  {{ k.nomor_polisi }}
                </router-link>
              </td>
              <td>{{ k.merek }} <span class="text-secondary">{{ k.tipe }}</span></td>
              <td class="text-center">{{ k.tahun_produksi }}</td>
              <td>{{ k.warna }}</td>
              <td>
                <span class="badge rounded-pill badge-kondisi" :class="`badge-kondisi-${k.kondisi}`">
                  {{ k.kondisi_label }}
                </span>
              </td>
              <td>{{ k.perangkat_daerah?.nama }}</td>
              <td>{{ k.penanggung_jawab?.nama ?? '—' }}</td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <router-link
                    class="btn btn-outline-secondary"
                    :to="{ name: 'kendaraan.show', params: { id: k.id } }"
                  >
                    Detail
                  </router-link>
                  <router-link
                    v-if="auth.isAdmin"
                    class="btn btn-outline-primary"
                    :to="{ name: 'kendaraan.edit', params: { id: k.id } }"
                  >
                    Edit
                  </router-link>
                  <button
                    v-if="auth.isAdmin"
                    class="btn btn-outline-danger"
                    @click="toDelete = k"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="items.length === 0">
              <td colspan="8" class="text-center text-secondary py-5">
                Tidak ada kendaraan yang cocok dengan filter.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- paginasi -->
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
      title="Hapus Kendaraan"
      :message="`Hapus kendaraan ${toDelete?.nomor_polisi}? Tindakan ini tidak dapat dibatalkan.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>
