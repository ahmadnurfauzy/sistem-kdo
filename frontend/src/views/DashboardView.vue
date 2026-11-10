<script setup>
import { onMounted, ref } from 'vue'
import laporanApi from '../api/laporan'
import kendaraanApi from '../api/kendaraan'
import { useAuthStore } from '../stores/auth'

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
  { label: 'Total Kendaraan', key: 'total_kendaraan', icon: '🚗', color: 'primary' },
  { label: 'Kondisi Baik', key: 'total_baik', icon: '✅', color: 'success' },
  { label: 'Rusak Ringan', key: 'total_rusak_ringan', icon: '⚠️', color: 'warning' },
  { label: 'Rusak Berat', key: 'total_rusak_berat', icon: '❌', color: 'danger' },
  { label: 'Perangkat Daerah', key: 'total_perangkat_daerah', icon: '🏛️', color: 'info' },
]
</script>

<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1">Dashboard</h4>
        <p class="text-secondary mb-0">Selamat datang, {{ auth.user?.name }} 👋</p>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <template v-else-if="ringkasan">
      <!-- kartu statistik -->
      <div class="row g-3 mb-4">
        <div v-for="stat in stats" :key="stat.key" class="col-6 col-md">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
              <div class="fs-3">{{ stat.icon }}</div>
              <div class="fs-4 fw-bold">{{ ringkasan[stat.key] }}</div>
              <div class="text-secondary small">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <!-- kendaraan rusak berat: prioritas perbaikan -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
              ⚠️ Prioritas Perbaikan — Kendaraan Rusak Berat
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Nomor Polisi</th>
                    <th>Merek / Tipe</th>
                    <th>Perangkat Daerah</th>
                    <th>Penanggung Jawab</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="k in rusakBerat" :key="k.id">
                    <td>
                      <router-link
                        :to="{ name: 'kendaraan.show', params: { id: k.id } }"
                        class="fw-semibold text-decoration-none"
                      >
                        {{ k.nomor_polisi }}
                      </router-link>
                    </td>
                    <td>{{ k.merek }} {{ k.tipe }}</td>
                    <td>{{ k.perangkat_daerah?.nama }}</td>
                    <td>{{ k.penanggung_jawab?.nama ?? '—' }}</td>
                  </tr>
                  <tr v-if="rusakBerat.length === 0">
                    <td colspan="4" class="text-center text-secondary py-4">
                      Tidak ada kendaraan rusak berat 🎉
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- akses cepat -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Akses Cepat</div>
            <div class="card-body d-grid gap-2">
              <router-link
                :to="{ name: 'kendaraan.index' }"
                class="btn btn-outline-primary text-start"
              >
                🚗 Kelola Data Kendaraan (KDO)
              </router-link>
              <router-link :to="{ name: 'pegawai.index' }" class="btn btn-outline-primary text-start">
                👷 Kelola Data Pegawai (PNS)
              </router-link>
              <router-link
                :to="{ name: 'perangkat-daerah.index' }"
                class="btn btn-outline-primary text-start"
              >
                🏛️ Kelola Perangkat Daerah
              </router-link>
              <router-link :to="{ name: 'laporan.index' }" class="btn btn-outline-primary text-start">
                📊 Laporan Kondisi Kendaraan
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
