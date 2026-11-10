<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import kendaraanApi from '../api/kendaraan'
import pegawaiApi from '../api/pegawai'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const kendaraanId = route.params.id
const kendaraan = ref(null)
const daftarPegawai = ref([])
const pilihPJ = ref(null)
const savingPJ = ref(false)
const deleting = ref(false)
const showConfirm = ref(false)
const notifikasi = ref('')

const loading = ref(true)

onMounted(async () => {
  try {
    const [kRes, pegawaiRes] = await Promise.all([
      kendaraanApi.get(kendaraanId),
      pegawaiApi.list({ per_page: 100 }),
    ])
    kendaraan.value = kRes.data.data
    daftarPegawai.value = pegawaiRes.data.data
    pilihPJ.value = kendaraan.value.penanggung_jawab?.id ?? null
  } finally {
    loading.value = false
  }
})

/** PJ hanya pegawai dari PD pemilik kendaraan */
const pilihanPJ = computed(() =>
  daftarPegawai.value.filter(
    (p) => p.perangkat_daerah?.id === kendaraan.value?.perangkat_daerah?.id,
  ),
)

async function simpanPJ() {
  savingPJ.value = true
  notifikasi.value = ''
  try {
    const res = await kendaraanApi.assignPJ(kendaraanId, pilihPJ.value)
    kendaraan.value = res.data.data
    notifikasi.value = pilihPJ.value
      ? 'Penanggung jawab berhasil diperbarui.'
      : 'Penanggung jawab berhasil dilepas.'
  } catch (err) {
    notifikasi.value = err.response?.data?.errors?.pegawai_id?.[0] || 'Gagal menyimpan penanggung jawab.'
  } finally {
    savingPJ.value = false
  }
}

async function confirmDelete() {
  deleting.value = true
  try {
    await kendaraanApi.remove(kendaraanId)
    router.push({ name: 'kendaraan.index' })
  } finally {
    deleting.value = false
  }
}
</script>

<template>
  <div class="row justify-content-center">
    <div class="col-lg-9">
      <div class="d-flex align-items-center mb-3">
        <router-link class="btn btn-outline-secondary btn-sm me-3" :to="{ name: 'kendaraan.index' }">
          ← Kembali
        </router-link>
        <h4 class="mb-0">Detail Kendaraan</h4>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <template v-else-if="kendaraan">
        <!-- identitas kendaraan -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
              <div>
                <h5 class="mb-1 fw-bold">{{ kendaraan.nomor_polisi }}</h5>
                <div class="text-secondary">
                  {{ kendaraan.merek }} {{ kendaraan.tipe }} · {{ kendaraan.tahun_produksi }} ·
                  {{ kendaraan.warna }}
                </div>
              </div>
              <span class="badge rounded-pill badge-kondisi fs-6" :class="`badge-kondisi-${kendaraan.kondisi}`">
                {{ kendaraan.kondisi_label }}
              </span>
            </div>

            <dl class="row mb-0">
              <dt class="col-sm-4">Perangkat Daerah</dt>
              <dd class="col-sm-8">{{ kendaraan.perangkat_daerah?.nama }}</dd>
              <dt class="col-sm-4">Terdaftar pada</dt>
              <dd class="col-sm-8">{{ kendaraan.dibuat_pada }}</dd>
              <dt class="col-sm-4">Terakhir diubah</dt>
              <dd class="col-sm-8">{{ kendaraan.diubah_pada }}</dd>
            </dl>
          </div>

          <div v-if="auth.isAdmin" class="card-footer bg-white d-flex justify-content-end gap-2">
            <router-link
              class="btn btn-outline-primary"
              :to="{ name: 'kendaraan.edit', params: { id: kendaraan.id } }"
            >
              Ubah Data
            </router-link>
            <button class="btn btn-outline-danger" @click="showConfirm = true">Hapus</button>
          </div>
        </div>

        <!-- penanggung jawab -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-semibold">👷 Penanggung Jawab Penggunaan</div>
          <div class="card-body">
            <div v-if="notifikasi" class="alert alert-info py-2 small">{{ notifikasi }}</div>

            <template v-if="auth.isAdmin">
              <div class="row g-2 align-items-end">
                <div class="col-md-8">
                  <label class="form-label">Pegawai (PNS) penanggung jawab</label>
                  <select v-model="pilihPJ" class="form-select">
                    <option :value="null">— belum ditunjuk —</option>
                    <option v-for="p in pilihanPJ" :key="p.id" :value="p.id">
                      {{ p.nama }} · {{ p.jabatan }} ({{ p.golongan }})
                    </option>
                  </select>
                  <div class="form-text">
                    Hanya pegawai dari {{ kendaraan.perangkat_daerah?.nama }} yang dapat dipilih.
                  </div>
                </div>
                <div class="col-md-4">
                  <button class="btn btn-primary w-100" :disabled="savingPJ" @click="simpanPJ">
                    <span v-if="savingPJ" class="spinner-border spinner-border-sm me-2"></span>
                    Simpan Penanggung Jawab
                  </button>
                </div>
              </div>
            </template>

            <div v-if="kendaraan.penanggung_jawab" class="border rounded p-3 mt-3 bg-light">
              <div class="fw-semibold">{{ kendaraan.penanggung_jawab.nama }}</div>
              <div class="text-secondary small">
                NIP {{ kendaraan.penanggung_jawab.nip }} ·
                {{ kendaraan.penanggung_jawab.jabatan }} ·
                Gol. {{ kendaraan.penanggung_jawab.golongan }}
              </div>
            </div>
            <div v-else class="text-secondary fst-italic mt-3">
              Kendaraan ini belum memiliki penanggung jawab.
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>

  <ConfirmModal
    :visible="showConfirm"
    title="Hapus Kendaraan"
    :message="`Hapus kendaraan ${kendaraan?.nomor_polisi}? Tindakan ini tidak dapat dibatalkan.`"
    confirm-label="Ya, hapus"
    :busy="deleting"
    @confirm="confirmDelete"
    @cancel="showConfirm = false"
  />
</template>
