<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import pegawaiApi from '../api/pegawai'
import perangkatDaerahApi from '../api/perangkatDaerah'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => Boolean(route.params.id))
const pegawaiId = route.params.id

const daftarPD = ref([])
const loading = ref(false)
const saving = ref(false)
const errors = ref({})

const form = reactive({
  nama: '',
  nip: '',
  jabatan: '',
  golongan: '',
  perangkat_daerah_id: '',
})

onMounted(async () => {
  loading.value = true
  try {
    const pdRes = await perangkatDaerahApi.list({ per_page: 100 })
    daftarPD.value = pdRes.data.data

    if (isEdit.value) {
      const res = await pegawaiApi.get(pegawaiId)
      const p = res.data.data
      Object.assign(form, {
        nama: p.nama,
        nip: p.nip,
        jabatan: p.jabatan,
        golongan: p.golongan,
        perangkat_daerah_id: p.perangkat_daerah?.id ?? '',
      })
    }
  } finally {
    loading.value = false
  }
})

async function submit() {
  saving.value = true
  errors.value = {}

  const payload = {
    ...form,
    perangkat_daerah_id: Number(form.perangkat_daerah_id),
  }

  try {
    if (isEdit.value) {
      await pegawaiApi.update(pegawaiId, payload)
    } else {
      await pegawaiApi.create(payload)
    }
    router.push({ name: 'pegawai.index' })
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    }
  } finally {
    saving.value = false
  }
}

function fieldError(field) {
  return errors.value[field]?.[0]
}
</script>

<template>
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="d-flex align-items-center mb-3">
        <router-link class="btn btn-outline-secondary btn-sm me-3" :to="{ name: 'pegawai.index' }">
          ← Kembali
        </router-link>
        <h4 class="mb-0">{{ isEdit ? 'Ubah Pegawai' : 'Tambah Pegawai' }}</h4>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <form v-else class="card border-0 shadow-sm" @submit.prevent="submit">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.nama"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nama') }"
                placeholder="Budi Santoso"
              />
              <div class="invalid-feedback">{{ fieldError('nama') }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">NIP (18 digit) <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.nip"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nip') }"
                placeholder="197504121998031001"
                maxlength="18"
              />
              <div class="invalid-feedback">{{ fieldError('nip') }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Golongan <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.golongan"
                class="form-control"
                :class="{ 'is-invalid': fieldError('golongan') }"
                placeholder="III/a"
              />
              <div class="invalid-feedback">{{ fieldError('golongan') }}</div>
              <div class="form-text">Format: I/a sampai IV/e (contoh: III/a)</div>
            </div>

            <div class="col-12">
              <label class="form-label">Jabatan <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.jabatan"
                class="form-control"
                :class="{ 'is-invalid': fieldError('jabatan') }"
                placeholder="Kepala Sub Bagian Umum"
              />
              <div class="invalid-feedback">{{ fieldError('jabatan') }}</div>
            </div>

            <div class="col-12">
              <label class="form-label">Perangkat Daerah <span class="text-danger">*</span></label>
              <select
                v-model="form.perangkat_daerah_id"
                class="form-select"
                :class="{ 'is-invalid': fieldError('perangkat_daerah_id') }"
                required
              >
                <option value="" disabled>Pilih perangkat daerah…</option>
                <option v-for="pd in daftarPD" :key="pd.id" :value="pd.id">
                  {{ pd.nama }} ({{ pd.kode }})
                </option>
              </select>
              <div class="invalid-feedback">{{ fieldError('perangkat_daerah_id') }}</div>
            </div>
          </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-end gap-2">
          <router-link class="btn btn-secondary" :to="{ name: 'pegawai.index' }">Batal</router-link>
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
