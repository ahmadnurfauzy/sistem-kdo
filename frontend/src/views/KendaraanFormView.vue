<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import kendaraanApi from '../api/kendaraan'
import pegawaiApi from '../api/pegawai'
import perangkatDaerahApi from '../api/perangkatDaerah'
import PageHeader from '../components/PageHeader.vue'

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
      <PageHeader
        eyebrow="Data utama"
        :title="isEdit ? 'Ubah kendaraan' : 'Tambah kendaraan'"
        :subtitle="isEdit ? 'Perbarui data kendaraan dinas operasional' : 'Lengkapi data kendaraan dinas operasional'"
      >
        <router-link class="btn btn-soft" :to="{ name: 'kendaraan.index' }">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </router-link>
      </PageHeader>

      <div v-if="loading" class="card">
        <div class="card-body">
          <span class="skel mb-3" style="width: 30%"></span>
          <span class="skel mb-2"></span>
          <span class="skel mb-2" style="width: 80%"></span>
          <span class="skel mb-2" style="width: 60%"></span>
          <span class="skel" style="width: 40%"></span>
        </div>
      </div>

      <form v-else class="card" @submit.prevent="submit">
        <div class="card-body">
          <div class="form-section-title">Identitas</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="nomor-polisi">Nomor polisi <span class="text-danger">*</span></label>
              <input
                id="nomor-polisi"
                v-model.trim="form.nomor_polisi"
                class="form-control"
                :class="{ 'is-invalid': fieldError('nomor_polisi') }"
                placeholder="Masukkan nomor polisi"
              />
              <div class="invalid-feedback">{{ fieldError('nomor_polisi') }}</div>
              <div class="form-text">Contoh: DK 1234 AB</div>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="pd-kendaraan">Perangkat daerah (pemilik) <span class="text-danger">*</span></label>
              <select
                id="pd-kendaraan"
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

          <div class="form-section-title">Spesifikasi</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="merek">Merek <span class="text-danger">*</span></label>
              <input
                id="merek"
                v-model.trim="form.merek"
                class="form-control"
                :class="{ 'is-invalid': fieldError('merek') }"
                placeholder="Masukkan merek"
              />
              <div class="invalid-feedback">{{ fieldError('merek') }}</div>
              <div class="form-text">Contoh: Toyota</div>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="tipe">Tipe / model <span class="text-danger">*</span></label>
              <input
                id="tipe"
                v-model.trim="form.tipe"
                class="form-control"
                :class="{ 'is-invalid': fieldError('tipe') }"
                placeholder="Masukkan tipe atau model"
              />
              <div class="invalid-feedback">{{ fieldError('tipe') }}</div>
              <div class="form-text">Contoh: Avanza 1.5 G</div>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="tahun-produksi">Tahun produksi <span class="text-danger">*</span></label>
              <input
                id="tahun-produksi"
                v-model.number="form.tahun_produksi"
                type="number"
                min="1980"
                class="form-control"
                :class="{ 'is-invalid': fieldError('tahun_produksi') }"
                placeholder="Masukkan tahun produksi"
              />
              <div class="invalid-feedback">{{ fieldError('tahun_produksi') }}</div>
              <div class="form-text">Contoh: 2021</div>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="warna">Warna <span class="text-danger">*</span></label>
              <input
                id="warna"
                v-model.trim="form.warna"
                class="form-control"
                :class="{ 'is-invalid': fieldError('warna') }"
                placeholder="Masukkan warna"
              />
              <div class="invalid-feedback">{{ fieldError('warna') }}</div>
              <div class="form-text">Contoh: Putih</div>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="kondisi">Kondisi <span class="text-danger">*</span></label>
              <select
                id="kondisi"
                v-model="form.kondisi"
                class="form-select"
                :class="{ 'is-invalid': fieldError('kondisi') }"
              >
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak ringan</option>
                <option value="rusak_berat">Rusak berat</option>
              </select>
              <div class="invalid-feedback">{{ fieldError('kondisi') }}</div>
            </div>
          </div>

          <div class="form-section-title">Penugasan</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="pegawai-pj">Penanggung jawab (opsional)</label>
              <select id="pegawai-pj" v-model="form.pegawai_id" class="form-select">
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

          <div class="form-actions">
            <router-link class="btn btn-soft" :to="{ name: 'kendaraan.index' }">Batal</router-link>
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
