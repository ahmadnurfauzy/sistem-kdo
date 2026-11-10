<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import kendaraanApi from '../api/kendaraan'
import pegawaiApi from '../api/pegawai'
import { useAuthStore } from '../stores/auth'
import ConfirmModal from '../components/ConfirmModal.vue'
import KondisiBadge from '../components/KondisiBadge.vue'
import PageHeader from '../components/PageHeader.vue'

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

const spesifikasi = computed(() =>
  kendaraan.value
    ? `${kendaraan.value.merek} ${kendaraan.value.tipe} · ${kendaraan.value.tahun_produksi} · ${kendaraan.value.warna}`
    : 'Memuat data…',
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
      <PageHeader
        eyebrow="Detail kendaraan"
        :title="kendaraan?.nomor_polisi ?? 'Detail kendaraan'"
        :subtitle="spesifikasi"
      >
        <KondisiBadge v-if="kendaraan" :value="kendaraan.kondisi" class="mb-2" />
        <router-link class="btn btn-soft mb-2" :to="{ name: 'kendaraan.index' }">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </router-link>
        <template v-if="auth.canWrite && kendaraan">
          <router-link
            class="btn btn-primary mb-2"
            :to="{ name: 'kendaraan.edit', params: { id: kendaraan.id } }"
          >
            <i class="bi bi-pencil me-1"></i>Ubah
          </router-link>
          <button class="btn btn-danger mb-2" @click="showConfirm = true">
            <i class="bi bi-trash3 me-1"></i>Hapus
          </button>
        </template>
      </PageHeader>

      <div v-if="loading" class="card">
        <div class="card-body">
          <span class="skel mb-3" style="width: 35%"></span>
          <span class="skel mb-2"></span>
          <span class="skel mb-2" style="width: 80%"></span>
          <span class="skel" style="width: 50%"></span>
        </div>
      </div>

      <template v-else-if="kendaraan">
        <!-- data kendaraan -->
        <div class="card mb-3">
          <div class="card-header fw-bold"><i class="bi bi-card-list me-2"></i>Data kendaraan</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-6 col-md-3">
                <div class="detail-label">Nomor polisi</div>
                <div class="detail-value">{{ kendaraan.nomor_polisi }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Merek</div>
                <div class="detail-value">{{ kendaraan.merek }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Tipe / model</div>
                <div class="detail-value">{{ kendaraan.tipe }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Tahun produksi</div>
                <div class="detail-value">{{ kendaraan.tahun_produksi }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Warna</div>
                <div class="detail-value">{{ kendaraan.warna }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Kondisi</div>
                <div class="detail-value"><KondisiBadge :value="kendaraan.kondisi" /></div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Perangkat daerah</div>
                <div class="detail-value">{{ kendaraan.perangkat_daerah?.nama }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Terdaftar pada</div>
                <div class="detail-value">{{ kendaraan.dibuat_pada }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="detail-label">Terakhir diubah</div>
                <div class="detail-value">{{ kendaraan.diubah_pada }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- penanggung jawab -->
        <div class="card">
          <div class="card-header fw-bold"><i class="bi bi-person-check me-2"></i>Penanggung jawab penggunaan</div>
          <div class="card-body">
            <div v-if="notifikasi" class="alert alert-info py-2 small d-flex gap-2 align-items-start" role="status">
              <i class="bi bi-info-circle mt-1"></i><span>{{ notifikasi }}</span>
            </div>

            <template v-if="auth.canWrite">
              <div class="row g-2 align-items-end">
                <div class="col-md-8">
                  <label class="form-label" for="pilih-pj">Pegawai (PNS) penanggung jawab</label>
                  <select id="pilih-pj" v-model="pilihPJ" class="form-select">
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
                    <span v-if="savingPJ" class="spinner-border spinner-border-sm me-1"></span>
                    Simpan penanggung jawab
                  </button>
                </div>
              </div>
            </template>

            <div v-if="kendaraan.penanggung_jawab" class="pj-box mt-3">
              <div class="stat-icon tone-blue"><i class="bi bi-person"></i></div>
              <div class="min-w-0">
                <div class="fw-semibold">{{ kendaraan.penanggung_jawab.nama }}</div>
                <div class="small text-secondary">
                  NIP {{ kendaraan.penanggung_jawab.nip }} ·
                  {{ kendaraan.penanggung_jawab.jabatan }} ·
                  Gol. {{ kendaraan.penanggung_jawab.golongan }}
                </div>
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
    title="Hapus kendaraan"
    :message="`Hapus kendaraan ${kendaraan?.nomor_polisi}? Tindakan ini tidak dapat dibatalkan.`"
    confirm-label="Ya, hapus"
    :busy="deleting"
    @confirm="confirmDelete"
    @cancel="showConfirm = false"
  />
</template>

<style scoped>
.detail-label {
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--kdo-muted);
  margin-bottom: 0.25rem;
}
.detail-value {
  font-weight: 600;
  color: var(--kdo-ink);
  line-height: 1.4;
}

.pj-box {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  padding: 0.9rem 1rem;
  background: var(--kdo-bg);
  border: 1px solid var(--kdo-border);
  border-radius: 14px;
}
</style>
