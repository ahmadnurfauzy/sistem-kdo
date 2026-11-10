<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import kendaraanApi from '../api/kendaraan'
import pegawaiApi from '../api/pegawai'
import perangkatDaerahApi from '../api/perangkatDaerah'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => Boolean(route.params.id))
const kendaraanId = route.params.id

const daftarPD = ref([])
const daftarPegawai = ref([])
const loading = ref(false)
const saving = ref(false)
const errors = ref({})

const form = reactive({
  nomor_polisi: '',
  merek: '',
  tipe: '',
  tahun_produksi: '',
  warna: '',
  kondisi: 'baik',
  perangkat_daerah_id: '',
  pegawai_id: null,
})

onMounted(async () => {
  loading.value = true
  try {
    const [pdRes, pegawaiRes] = await Promise.all([
      perangkatDaerahApi.list({ per_page: 100 }),
      pegawaiApi.list({ per_page: 100 }),
    ])
    daftarPD.value = pdRes.data.data
    daftarPegawai.value = pegawaiRes.data.data

    if (isEdit.value) {
      const res = await kendaraanApi.get(kendaraanId)
      const k = res.data.data
      Object.assign(form, {
        nomor_polisi: k.nomor_polisi,
        merek: k.merek,
        tipe: k.tipe,
        tahun_produksi: k.tahun_produksi,
        warna: k.warna,
        kondisi: k.kondisi,
        perangkat_daerah_id: k.perangkat_daerah?.id ?? '',
        pegawai_id: k.penanggung_jawab?.id ?? null,
      })
    }
  } finally {
    loading.value = false
  }
})

/** PJ hanya boleh pegawai dari PD yang dipilih (aturan bisnis API) */
const pilihanPJ = computed(() =>
  daftarPegawai.value.filter(
    (p) => p.perangkat_daerah?.id === Number(form.perangkat_daerah_id),
  ),
)

watch(
  () => form.perangkat_daerah_id,
  () => {
    // ganti PD → reset PJ agar tidak tersisa pegawai lintas PD
    form.pegawai_id = null
  },
)

async function submit() {
  saving.value = true
  errors.value = {}

  const payload = {
    ...form,
    tahun_produksi: Number(form.tahun_produksi),
    perangkat_daerah_id: Number(form.perangkat_daerah_id),
    pegawai_id: form.pegawai_id ? Number(form.pegawai_id) : null,
  }

  try {
    if (isEdit.value) {
      await kendaraanApi.update(kendaraanId, payload)
    } else {
      await kendaraanApi.create(payload)
    }
    router.push({ name: 'kendaraan.index' })
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
    <div class="col-lg-8">
      <div class="d-flex align-items-center mb-3">
        <router-link class="btn btn-outline-secondary btn-sm me-3" :to="{ name: 'kendaraan.index' }">
          ← Kembali
        </router-link>
        <h4 class="mb-0">{{ isEdit ? 'Ubah Kendaraan' : 'Tambah Kendaraan' }}</h4>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <form v-else class="card border-0 shadow-sm" @submit.prevent="submit">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nomor Polisi <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.nomor_polisi"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nomor_polisi') }"
                placeholder="DK 1234 AB"
              />
              <div class="invalid-feedback">{{ fieldError('nomor_polisi') }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Perangkat Daerah (pemilik) <span class="text-danger">*</span></label>
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

            <div class="col-md-6">
              <label class="form-label">Merek <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.merek"
                class="form-control"
                :class="{ 'is-invalid': fieldError('merek') }"
                placeholder="Toyota"
              />
              <div class="invalid-feedback">{{ fieldError('merek') }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tipe / Model <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.tipe"
                class="form-control"
                :class="{ 'is-invalid': fieldError('tipe') }"
                placeholder="Avanza 1.5 G"
              />
              <div class="invalid-feedback">{{ fieldError('tipe') }}</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Tahun Produksi <span class="text-danger">*</span></label>
              <input
                v-model.number="form.tahun_produksi"
                type="number"
                min="1980"
                class="form-control"
                :class="{ 'is-invalid': fieldError('tahun_produksi') }"
                placeholder="2021"
              />
              <div class="invalid-feedback">{{ fieldError('tahun_produksi') }}</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Warna <span class="text-danger">*</span></label>
              <input
                v-model.trim="form.warna"
                class="form-control"
                :class="{ 'is-invalid': fieldError('warna') }"
                placeholder="Putih"
              />
              <div class="invalid-feedback">{{ fieldError('warna') }}</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Kondisi <span class="text-danger">*</span></label>
              <select
                v-model="form.kondisi"
                class="form-select"
                :class="{ 'is-invalid': fieldError('kondisi') }"
              >
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
              </select>
              <div class="invalid-feedback">{{ fieldError('kondisi') }}</div>
            </div>

            <div class="col-12">
              <label class="form-label">Penanggung Jawab (opsional)</label>
              <select v-model="form.pegawai_id" class="form-select">
                <option :value="null">— belum ditunjuk —</option>
                <option v-for="p in pilihanPJ" :key="p.id" :value="p.id">
                  {{ p.nama }} · {{ p.jabatan }} ({{ p.golongan }})
                </option>
              </select>
              <div class="form-text">
                Hanya pegawai dari perangkat daerah terpilih yang ditampilkan.
              </div>
              <div class="invalid-feedback">{{ fieldError('pegawai_id') }}</div>
            </div>
          </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-end gap-2">
          <router-link class="btn btn-secondary" :to="{ name: 'kendaraan.index' }">Batal</router-link>
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
