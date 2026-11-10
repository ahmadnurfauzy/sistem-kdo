<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import akunApi from '../api/akun'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'
import DataTable from '../components/DataTable.vue'
import PageHeader from '../components/PageHeader.vue'

const auth = useAuthStore()

const items = ref([])
const meta = ref(null)
const loading = ref(true)
const error = ref('')

const filters = reactive({ search: '', role: '', page: 1 })

const saving = ref(false)
const editingId = ref(null)
const showForm = ref(false)
const errors = ref({})
const toDelete = ref(null)
const deleting = ref(false)
const infoPesan = ref('')

const form = reactive({ name: '', email: '', password: '', role: 'pengguna' })

const ROLE_LABEL = {
  admin: 'Admin',
  pengelola: 'Pengelola',
  pengguna: 'Pengguna',
}

const columns = [
  { key: 'name', label: 'Nama' },
  { key: 'email', label: 'Email' },
  { key: 'role', label: 'Role' },
  { key: 'dibuat_pada', label: 'Terdaftar', hideBelow: 'lg' },
]

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await akunApi.list({
      search: filters.search || undefined,
      role: filters.role || undefined,
      page: filters.page,
    })
    items.value = res.data.data
    meta.value = res.data.meta
  } catch {
    error.value = 'Data akun gagal dimuat. Periksa koneksi Anda lalu coba lagi.'
  } finally {
    loading.value = false
  }
}

onMounted(load)

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
  () => filters.role,
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
  filters.role = ''
}

function bukaTambah() {
  editingId.value = null
  form.name = ''
  form.email = ''
  form.password = ''
  form.role = 'pengguna'
  errors.value = {}
  showForm.value = true
}

function bukaEdit(akun) {
  editingId.value = akun.id
  form.name = akun.name
  form.email = akun.email
  form.password = ''
  form.role = akun.role
  errors.value = {}
  showForm.value = true
}

async function submit() {
  saving.value = true
  errors.value = {}
  infoPesan.value = ''

  const payload = { name: form.name, email: form.email, role: form.role }
  if (form.password) {
    payload.password = form.password
  }

  try {
    if (editingId.value) {
      await akunApi.update(editingId.value, payload)
    } else {
      await akunApi.create(payload)
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
    await akunApi.remove(toDelete.value.id)
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
      eyebrow="Administrasi"
      title="Akun"
      :subtitle="meta ? `${meta.total} akun terdaftar` : 'Kelola pengguna dan role akses sistem'"
    >
      <button class="btn btn-primary" @click="bukaTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah akun
      </button>
    </PageHeader>

    <div v-if="infoPesan" class="alert alert-warning d-flex gap-2 align-items-start" role="alert">
      <i class="bi bi-exclamation-triangle mt-1"></i><span>{{ infoPesan }}</span>
    </div>

    <!-- form tambah/ubah -->
    <div v-if="showForm" class="card mb-3">
      <div class="card-header fw-bold">{{ editingId ? 'Ubah' : 'Tambah' }} akun</div>
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label" for="nama-akun">Nama <span class="text-danger">*</span></label>
              <input
                id="nama-akun"
                v-model.trim="form.name"
                class="form-control"
                :class="{ 'is-invalid': fieldError('name') }"
                placeholder="Masukkan nama"
              />
              <div class="invalid-feedback">{{ fieldError('name') }}</div>
              <div class="form-text">Contoh: Budi Santoso</div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="email-akun">Email <span class="text-danger">*</span></label>
              <input
                id="email-akun"
                v-model.trim="form.email"
                type="email"
                class="form-control"
                :class="{ 'is-invalid': fieldError('email') }"
                placeholder="Masukkan email"
              />
              <div class="invalid-feedback">{{ fieldError('email') }}</div>
              <div class="form-text">Contoh: budi@kdo.test — dipakai untuk masuk</div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="role-akun">Role <span class="text-danger">*</span></label>
              <select
                id="role-akun"
                v-model="form.role"
                class="form-select"
                :class="{ 'is-invalid': fieldError('role') }"
              >
                <option value="admin">Admin — akses penuh + kelola akun</option>
                <option value="pengelola">Pengelola — ubah data KDO/PNS/PD</option>
                <option value="pengguna">Pengguna — hanya baca</option>
              </select>
              <div class="invalid-feedback">{{ fieldError('role') }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="password-akun">
                Kata sandi <span v-if="!editingId" class="text-danger">*</span>
              </label>
              <input
                id="password-akun"
                v-model="form.password"
                type="password"
                class="form-control"
                :class="{ 'is-invalid': fieldError('password') }"
                :placeholder="editingId ? 'Masukkan kata sandi baru (opsional)' : 'Masukkan kata sandi'"
                autocomplete="new-password"
              />
              <div class="invalid-feedback">{{ fieldError('password') }}</div>
              <div class="form-text">{{ editingId ? 'Minimal 8 karakter. Kosongkan bila tidak diubah.' : 'Minimal 8 karakter.' }}</div>
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

    <div class="toolbar">
      <div class="search-field flex-grow-1">
        <i class="bi bi-search"></i>
        <input v-model.trim="filters.search" type="search" class="form-control" placeholder="Cari nama atau email" />
      </div>

      <button class="btn btn-soft d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterAkun">
        <i class="bi bi-sliders2 me-1"></i>Filter
      </button>

      <div id="filterAkun" class="collapse d-md-flex toolbar-filters">
        <select v-model="filters.role" class="form-select" aria-label="Filter role">
          <option value="">Semua role</option>
          <option value="admin">Admin</option>
          <option value="pengelola">Pengelola</option>
          <option value="pengguna">Pengguna</option>
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
      empty-icon="bi-person-gear"
      empty-title="Tidak ada akun"
      empty-text="Belum ada akun, atau tidak ada yang cocok dengan pencarian dan filter."
      @page-change="changePage"
    >
      <template #cell-name="{ row }">
        <span class="fw-semibold text-body">
          {{ row.name }}
          <span v-if="row.id === auth.user?.id" class="badge rounded-pill bg-secondary-subtle text-secondary ms-1">Anda</span>
        </span>
      </template>
      <template #cell-email="{ row }"><span class="text-secondary">{{ row.email }}</span></template>
      <template #cell-role="{ row }"><span class="chip" :class="`chip-${row.role}`">{{ ROLE_LABEL[row.role] ?? row.role }}</span></template>
      <template #cell-dibuat_pada="{ row }"><span class="text-secondary">{{ row.dibuat_pada }}</span></template>

      <template #card="{ row }">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <div class="min-w-0">
            <div class="fw-bold text-truncate">
              {{ row.name }}
              <span v-if="row.id === auth.user?.id" class="badge rounded-pill bg-secondary-subtle text-secondary ms-1">Anda</span>
            </div>
            <div class="small text-secondary text-truncate">{{ row.email }}</div>
          </div>
          <span class="chip" :class="`chip-${row.role}`">{{ ROLE_LABEL[row.role] ?? row.role }}</span>
        </div>
        <div class="small text-secondary mt-1">
          <i class="bi bi-calendar3 me-1"></i>{{ row.dibuat_pada }}
        </div>
      </template>

      <template #actions="{ row }">
        <div class="row-actions">
          <button class="icon-btn" aria-label="Ubah" title="Ubah" @click="bukaEdit(row)">
            <i class="bi bi-pencil"></i>
          </button>
          <button
            v-if="row.id !== auth.user?.id"
            class="icon-btn danger"
            aria-label="Hapus" title="Hapus"
            @click="toDelete = row"
          >
            <i class="bi bi-trash3"></i>
          </button>
        </div>
      </template>

      <template #empty>
        <button class="btn btn-soft btn-sm me-2" @click="resetFilter">Reset filter</button>
        <button class="btn btn-primary btn-sm" @click="bukaTambah">Tambah akun</button>
      </template>
    </DataTable>

    <ConfirmModal
      :visible="!!toDelete"
      title="Hapus akun"
      :message="`Hapus akun ${toDelete?.name}? Tindakan ini tidak dapat dibatalkan.`"
      confirm-label="Ya, hapus"
      :busy="deleting"
      @confirm="confirmDelete"
      @cancel="toDelete = null"
    />
  </div>
</template>

<style scoped>
.chip {
  display: inline-flex;
  align-items: center;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
  white-space: nowrap;
}
.chip-admin { background: rgba(130, 77, 255, 0.12); color: #6a2fe0; }
.chip-pengelola { background: rgba(47, 84, 250, 0.1); color: var(--kdo-blue-700); }
.chip-pengguna { background: #eef1f8; color: var(--kdo-muted); }
</style>
