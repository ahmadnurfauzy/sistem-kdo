<script setup>
import { onMounted, ref } from 'vue'
import laporanApi from '../api/laporan'

const data = ref([])
const ringkasan = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await laporanApi.kondisi()
    data.value = res.data.data
    ringkasan.value = res.data.ringkasan
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <div class="mb-3">
      <h4 class="mb-1">Laporan Kondisi Kendaraan</h4>
      <p class="text-secondary mb-0">
        Rekap jumlah kendaraan dinas operasional tiap perangkat daerah menurut kondisi
      </p>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <template v-else>
      <!-- ringkasan -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
              <div class="fs-2">🚗</div>
              <div class="fs-4 fw-bold">{{ ringkasan.total_kendaraan }}</div>
              <div class="text-secondary small">Total Kendaraan</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
              <div class="fs-2">✅</div>
              <div class="fs-4 fw-bold text-success">{{ ringkasan.total_baik }}</div>
              <div class="text-secondary small">Baik</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
              <div class="fs-2">⚠️</div>
              <div class="fs-4 fw-bold text-warning">{{ ringkasan.total_rusak_ringan }}</div>
              <div class="text-secondary small">Rusak Ringan</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
              <div class="fs-2">❌</div>
              <div class="fs-4 fw-bold text-danger">{{ ringkasan.total_rusak_berat }}</div>
              <div class="text-secondary small">Rusak Berat</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
              <div class="fs-2">🏛️</div>
              <div class="fs-4 fw-bold text-info">{{ ringkasan.total_perangkat_daerah }}</div>
              <div class="text-secondary small">Perangkat Daerah</div>
            </div>
          </div>
        </div>
      </div>

      <!-- tabel rekap -->
      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Perangkat Daerah</th>
                <th class="text-center">Total</th>
                <th class="text-center">Baik</th>
                <th class="text-center">Rusak Ringan</th>
                <th class="text-center">Rusak Berat</th>
                <th class="text-center">Tanpa PJ</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in data" :key="r.perangkat_daerah.id">
                <td>
                  <span class="badge bg-secondary me-2">{{ r.perangkat_daerah.kode }}</span>
                  {{ r.perangkat_daerah.nama }}
                </td>
                <td class="text-center fw-semibold">{{ r.total_kendaraan }}</td>
                <td class="text-center text-success">{{ r.kondisi.baik }}</td>
                <td class="text-center text-warning-emphasis">{{ r.kondisi.rusak_ringan }}</td>
                <td class="text-center text-danger">{{ r.kondisi.rusak_berat }}</td>
                <td class="text-center text-secondary">{{ r.tanpa_penanggung_jawab }}</td>
              </tr>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <th>Total</th>
                <th class="text-center">{{ ringkasan.total_kendaraan }}</th>
                <th class="text-center">{{ ringkasan.total_baik }}</th>
                <th class="text-center">{{ ringkasan.total_rusak_ringan }}</th>
                <th class="text-center">{{ ringkasan.total_rusak_berat }}</th>
                <th class="text-center">—</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>
