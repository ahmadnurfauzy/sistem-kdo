<script setup>
import { onMounted, reactive, ref } from 'vue'
import perangkatDaerahApi from '../api/perangkatDaerah'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'

const auth = useAuthStore()

const items = ref([])
const loading = ref(true)
const saving = ref(false)
const editingId = ref(null)
const showForm = ref(false)
const errors = ref({})
const toDelete = ref(null)
const deleting = ref(false)
const infoPesan = ref('')

const form = reactive({
  kode: '',
  nama: '',
})

async function load() {
  loading.value = true
  try {
    const res = await perangkatDaerahApi.list({ per_page: 100 })
    items.value = res.data.data
  } finally {
    loading.value = false
  }
}

onMounted(load)

function bukaTambah() {
  editingId.value = null
  form.kode = ''
  form.nama = ''
  errors.value = {}
  showForm.value = true
}

function bukaEdit(pd) {
  editingId.value = pd.id
  form.kode = pd.kode
  form.nama = pd.nama
  errors.value = {}
  showForm.value = true
}

async function submit() {
  saving.value = true
  errors.value = {}
  infoPesan.value = ''

  try {
    if (editingId.value) {
      await perangkatDaerahApi.update(editingId.value, { ...form })
    } else {
      await perangkatDaerahApi.create({ ...form })
    }
    showForm.value = false
    await load()
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else if (err.response?.status === 409) {
      infoPesan.value = err.response.data.message
    }
  } finally {
    saving.value = false
  }
}

async function confirmDelete() {
  deleting.value = true
  infoPesan.value = ''
  try {
    await perangkatDaerahApi.remove(toDelete.value.id)
    toDelete.value = null
    await load()
  } catch (err) {
    if (err.response?.status === 409) {
      infoPesan.value = err.response.data.message
      toDelete.value = null
    }
  } finally {
    deleting.value = false
  }
}

function fieldError(field) {
  return errors.value[field]?.[0]
}
</script>

<template>
  <div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div>
        <h4 class="mb-1">Perangkat Daerah</h4>
        <p class="text-secondary mb-0">Instansi pengguna / pemilik kendaraan dinas</p>
      </div>
      <button v-if="auth.isAdmin" class="btn btn-primary" @click="bukaTambah">
        + Tambah Perangkat Daerah
      </button>
    </div>

    <div v-if="infoPesan" class="alert alert-warning">{{ infoPesan }}</div>

    <!-- form tambah/ubah -->
    <div v-if="showForm" class="card border-0 shadow-sm mb-3 border-start border-primary border-4">
      <div class="card-body">
        <h6 class="mb-3">{{ editingId ? 'Ubah' : 'Tambah' }} Perangkat Daerah</h6>
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-md-3">
            <label class="form-label">Kode <span class="text-danger">*</span></label>
            <input
              v-model.trim="form.kode"
              class="form-control"
              :class="{ 'is-invalid': fieldError('kode') }"
              placeholder="DISDIK"
            />
            <div class="invalid-feedback">{{ fieldError('kode') }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nama Instansi <span class="text-danger">*</span></label>
            <input
              v-model.trim="form.nama"
              class="form-control"
              :class="{ 'is-invalid': fieldError('nama') }"
              placeholder="Dinas Pendidikan"
            />
            <div class="invalid-feedback">{{ fieldError('nama') }}</div>
          </div>
          <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
              Simpan
            </button>
            <button type="button" class="btn btn-secondary" @click="showForm = false">Batal</button>
          </div>
        </form>
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
              <th style="width: 140px">Kode</th>
              <th>Nama Instansi</th>
              <th class="text-center">Pegawai</th>
              <th class="text-center">Kendaraan</th>
              <th v-if="auth.isAdmin" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pd in items" :key="pd.id">
              <td><span class="badge bg-secondary">{{ pd.kode }}</span></td>
              <td class="fw-semibold">{{ pd.nama }}</td>
              <td class="text-center">{{ pd.jumlah_pegawai ?? 0 }}</td>
              <td class="text-center">{{ pd.jumlah_kendaraan ?? 0 }}</td>
              <td v-if="auth.isAdmin" class="text-end">
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-primary" @click="bukaEdit(pd)">Edit</button>
                  <button class="btn btn-outline-danger" @click="toDelete = pd">Hapus</button>
                </div>
              </td>
            </tr>
            <tr v-if="items.length === 0">
              <td :colspan="auth.isAdmin ? 5 : 4" class="text-center text-secondary py-5">
                Belum ada data perangkat daerah.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ConfirmModal
      :visible="!!toDelete"
      title="Hapus Perangkat Daerah"
      :message="`Hapus ${toDelete?.nama}? PD yang masih memiliki pegawai atau kendaraan tidak dapat dihapus.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>
