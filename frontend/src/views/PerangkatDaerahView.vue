<script setup>
import { onMounted, reactive, ref } from 'vue'
import perangkatDaerahApi from '../api/perangkatDaerah'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'
import DataTable from '../components/DataTable.vue'
import PageHeader from '../components/PageHeader.vue'

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

const columns = [
  { key: 'kode', label: 'Kode' },
  { key: 'nama', label: 'Nama instansi' },
  { key: 'jumlah_pegawai', label: 'Pegawai', class: 'text-center' },
  { key: 'jumlah_kendaraan', label: 'Kendaraan', class: 'text-center' },
]

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
    <PageHeader
      eyebrow="Data utama"
      title="Perangkat daerah"
      subtitle="Instansi pengguna / pemilik kendaraan dinas"
    >
      <button v-if="auth.canWrite" class="btn btn-primary" @click="bukaTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah perangkat daerah
      </button>
    </PageHeader>

    <div v-if="infoPesan" class="alert alert-warning d-flex gap-2 align-items-start" role="alert">
      <i class="bi bi-exclamation-triangle mt-1"></i><span>{{ infoPesan }}</span>
    </div>

    <!-- form tambah/ubah -->
    <div v-if="showForm" class="card mb-3">
      <div class="card-header fw-bold">{{ editingId ? 'Ubah' : 'Tambah' }} perangkat daerah</div>
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label" for="kode-pd">Kode <span class="text-danger">*</span></label>
              <input
                id="kode-pd"
                v-model.trim="form.kode"
                class="form-control"
                :class="{ 'is-invalid': fieldError('kode') }"
                placeholder="Masukkan kode PD"
              />
              <div class="invalid-feedback">{{ fieldError('kode') }}</div>
              <div class="form-text">Contoh: DISDIK, DISKES</div>
            </div>
            <div class="col-md-9">
              <label class="form-label" for="nama-pd">Nama instansi <span class="text-danger">*</span></label>
              <input
                id="nama-pd"
                v-model.trim="form.nama"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nama') }"
                placeholder="Masukkan nama instansi"
              />
              <div class="invalid-feedback">{{ fieldError('nama') }}</div>
              <div class="form-text">Contoh: Dinas Pendidikan</div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
              Simpan
            </button>
            <button type="button" class="btn btn-soft" @click="showForm = false">Batal</button>
          </div>
        </form>
      </div>
    </div>

    <DataTable
      :columns="columns"
      :rows="items"
      :loading="loading"
      empty-icon="bi-buildings"
      empty-title="Belum ada perangkat daerah"
      empty-text="Perangkat daerah akan tampil di sini setelah ditambahkan."
    >
      <template #cell-kode="{ row }"><span class="role-tag">{{ row.kode }}</span></template>
      <template #cell-nama="{ row }"><span class="fw-semibold text-body">{{ row.nama }}</span></template>
      <template #cell-jumlah_pegawai="{ row }"><span class="fw-semibold">{{ row.jumlah_pegawai ?? 0 }}</span></template>
      <template #cell-jumlah_kendaraan="{ row }"><span class="fw-semibold">{{ row.jumlah_kendaraan ?? 0 }}</span></template>

      <template #card="{ row }">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <span class="fw-bold">{{ row.nama }}</span>
          <span class="role-tag">{{ row.kode }}</span>
        </div>
        <div class="small text-secondary mt-1">
          <i class="bi bi-people me-1"></i>{{ row.jumlah_pegawai ?? 0 }} pegawai ·
          <i class="bi bi-car-front me-1"></i>{{ row.jumlah_kendaraan ?? 0 }} kendaraan
        </div>
      </template>

      <template #actions="{ row }">
        <div class="row-actions">
          <button
            v-if="auth.canWrite"
            class="icon-btn"
            aria-label="Ubah" title="Ubah"
            @click="bukaEdit(row)"
          >
            <i class="bi bi-pencil"></i>
          </button>
          <button v-if="auth.canWrite" class="icon-btn danger" aria-label="Hapus" title="Hapus" @click="toDelete = row">
            <i class="bi bi-trash3"></i>
          </button>
        </div>
      </template>

      <template #empty>
        <button v-if="auth.canWrite" class="btn btn-primary btn-sm" @click="bukaTambah">Tambah perangkat daerah</button>
      </template>
    </DataTable>

    <ConfirmModal
      :visible="!!toDelete"
      title="Hapus perangkat daerah"
      :message="`Hapus ${toDelete?.nama}? PD yang masih memiliki pegawai atau kendaraan tidak dapat dihapus.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>
