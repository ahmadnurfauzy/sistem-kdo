<script setup>
import { onMounted, ref } from 'vue'
import laporanApi from '../api/laporan'
import kendaraanApi from '../api/kendaraan'
import { useAuthStore } from '../stores/auth'
import StatCard from '../components/StatCard.vue'
import KondisiBadge from '../components/KondisiBadge.vue'

const auth = useAuthStore()
const ringkasan = ref(null)
const rusakBerat = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const [laporanRes, rusakRes] = await Promise.all([
      laporanApi.kondisi(),
      kendaraanApi.list({ kondisi: 'rusak_berat', per_page: 5 }),
    ])
    ringkasan.value = laporanRes.data.ringkasan
    rusakBerat.value = rusakRes.data.data
  } finally {
    loading.value = false
  }
})

const stats = [
  { label: 'Total kendaraan', key: 'total_kendaraan', icon: 'bi-car-front', tone: 'blue' },
  { label: 'Kondisi baik', key: 'total_baik', icon: 'bi-check-circle', tone: 'green' },
  { label: 'Rusak ringan', key: 'total_rusak_ringan', icon: 'bi-exclamation-triangle', tone: 'amber' },
  { label: 'Rusak berat', key: 'total_rusak_berat', icon: 'bi-x-octagon', tone: 'red' },
  { label: 'Perangkat daerah', key: 'total_perangkat_daerah', icon: 'bi-buildings', tone: 'teal' },
]

const shortcuts = [
  { to: 'kendaraan.index', label: 'Data kendaraan', desc: 'Kelola KDO', icon: 'bi-car-front', tone: 'blue' },
  { to: 'pegawai.index', label: 'Data pegawai', desc: 'PNS penanggung jawab', icon: 'bi-people', tone: 'violet' },
  { to: 'perangkat-daerah.index', label: 'Perangkat daerah', desc: 'Instansi pemilik', icon: 'bi-buildings', tone: 'teal' },
  { to: 'laporan.index', label: 'Laporan', desc: 'Rekap kondisi', icon: 'bi-bar-chart-line', tone: 'amber' },
]
</script>

<template>
  <div>
    <section class="hero mb-4">
      <div>
        <span class="eyebrow eyebrow-light">Selamat datang</span>
        <h1>Halo, {{ auth.user?.name }}</h1>
        <p>Pantau kondisi kendaraan dinas operasional dan penanggung jawabnya dalam satu tempat.</p>
        <router-link :to="{ name: 'kendaraan.index' }" class="btn btn-light">
          Lihat kendaraan <i class="bi bi-arrow-right ms-1"></i>
        </router-link>
      </div>
      <div class="hero-art" aria-hidden="true"><i class="bi bi-car-front-fill"></i></div>
    </section>

    <div v-if="loading" class="stat-grid mb-4">
      <div v-for="n in 5" :key="n" class="stat"><span class="skel" style="height: 44px; width: 100%"></span></div>
    </div>

    <template v-else-if="ringkasan">
      <div class="stat-grid mb-4">
        <StatCard
          v-for="s in stats"
          :key="s.key"
          :label="s.label"
          :value="ringkasan[s.key]"
          :icon="s.icon"
          :tone="s.tone"
        />
      </div>

      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span class="fw-bold">Prioritas perbaikan</span>
              <span class="badge-kondisi badge-kondisi-rusak_berat badge rounded-pill">Rusak berat</span>
            </div>

            <router-link
              v-for="k in rusakBerat"
              :key="k.id"
              :to="{ name: 'kendaraan.show', params: { id: k.id } }"
              class="list-row"
            >
              <div class="stat-icon tone-red"><i class="bi bi-car-front"></i></div>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold">{{ k.nomor_polisi }}</div>
                <div class="small text-secondary text-truncate">
                  {{ k.merek }} {{ k.tipe }} · {{ k.perangkat_daerah?.nama }}
                </div>
              </div>
              <KondisiBadge :value="k.kondisi" short />
            </router-link>

            <div v-if="rusakBerat.length === 0" class="text-center py-5 px-3">
              <i class="bi bi-patch-check fs-1 text-success opacity-50"></i>
              <div class="fw-semibold mt-2">Semua kendaraan layak pakai</div>
              <div class="small text-secondary">Tidak ada kendaraan dengan kondisi rusak berat.</div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-header fw-bold">Akses cepat</div>
            <div class="card-body">
              <div class="row g-2">
                <div v-for="s in shortcuts" :key="s.to" class="col-6">
                  <router-link :to="{ name: s.to }" class="tile h-100">
                    <div class="stat-icon" :class="`tone-${s.tone}`"><i :class="['bi', s.icon]"></i></div>
                    <div class="fw-semibold small lh-sm">{{ s.label }}<small>{{ s.desc }}</small></div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
