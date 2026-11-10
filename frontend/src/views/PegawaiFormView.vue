<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import pegawaiApi from '../api/pegawai'
import perangkatDaerahApi from '../api/perangkatDaerah'
import PageHeader from '../components/PageHeader.vue'

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
      <PageHeader
        eyebrow="Data utama"
        :title="isEdit ? 'Ubah pegawai' : 'Tambah pegawai'"
        :subtitle="isEdit ? 'Perbarui data Pegawai Negeri Sipil' : 'Lengkapi data Pegawai Negeri Sipil'"
      >
        <router-link class="btn btn-soft" :to="{ name: 'pegawai.index' }">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </router-link>
      </PageHeader>

      <div v-if="loading" class="card">
        <div class="card-body">
          <span class="skel mb-3" style="width: 30%"></span>
          <span class="skel mb-2"></span>
          <span class="skel mb-2" style="width: 80%"></span>
          <span class="skel" style="width: 60%"></span>
        </div>
      </div>

      <form v-else class="card" @submit.prevent="submit">
        <div class="card-body">
          <div class="form-section-title">Identitas</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="nama">Nama lengkap <span class="text-danger">*</span></label>
              <input
                id="nama"
                v-model.trim="form.nama"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nama') }"
                placeholder="Masukkan nama lengkap"
              />
              <div class="invalid-feedback">{{ fieldError('nama') }}</div>
              <div class="form-text">Contoh: Budi Santoso</div>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="nip">NIP <span class="text-danger">*</span></label>
              <input
                id="nip"
                v-model.trim="form.nip"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nip') }"
                placeholder="Masukkan NIP 18 digit"
                maxlength="18"
                inputmode="numeric"
              />
              <div class="invalid-feedback">{{ fieldError('nip') }}</div>
              <div class="form-text">18 digit angka. Contoh: 197504121998031001</div>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="golongan">Golongan <span class="text-danger">*</span></label>
              <input
                id="golongan"
                v-model.trim="form.golongan"
                class="form-control"
                :class="{ 'is-invalid': fieldError('golongan') }"
                placeholder="Masukkan golongan"
              />
              <div class="invalid-feedback">{{ fieldError('golongan') }}</div>
              <div class="form-text">Contoh: III/a</div>
            </div>
          </div>

          <div class="form-section-title">Kepegawaian</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="jabatan">Jabatan <span class="text-danger">*</span></label>
              <input
                id="jabatan"
                v-model.trim="form.jabatan"
                class="form-control"
                :class="{ 'is-invalid': fieldError('jabatan') }"
                placeholder="Masukkan jabatan"
              />
              <div class="invalid-feedback">{{ fieldError('jabatan') }}</div>
              <div class="form-text">Contoh: Kepala Sub Bagian Umum</div>
            </div>
          </div>

          <div class="form-section-title">Penempatan</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="pd-pegawai">Perangkat daerah <span class="text-danger">*</span></label>
              <select
                id="pd-pegawai"
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

          <div class="form-actions">
            <router-link class="btn btn-soft" :to="{ name: 'pegawai.index' }">Batal</router-link>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
              Simpan
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.form-section-title {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--kdo-muted);
  margin: 1.5rem 0 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--kdo-border);
}
.form-section-title:first-child {
  margin-top: 0;
}
</style>
