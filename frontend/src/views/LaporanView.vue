<script setup>
import { onMounted, ref } from 'vue'
import laporanApi from '../api/laporan'
import DataTable from '../components/DataTable.vue'
import PageHeader from '../components/PageHeader.vue'
import StatCard from '../components/StatCard.vue'

const data = ref([])
const ringkasan = ref(null)
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await laporanApi.kondisi()
    // tambah id datar (id PD) agar bisa dipakai sebagai row key DataTable
    data.value = res.data.data.map((r) => ({ ...r, id: r.perangkat_daerah.id }))
    ringkasan.value = res.data.ringkasan
  } catch {
    error.value = 'Laporan gagal dimuat. Periksa koneksi Anda lalu coba lagi.'
  } finally {
    loading.value = false
  }
}

onMounted(load)

const stats = [
  { label: 'Total kendaraan', key: 'total_kendaraan', icon: 'bi-car-front', tone: 'blue' },
  { label: 'Kondisi baik', key: 'total_baik', icon: 'bi-check-circle', tone: 'green' },
  { label: 'Rusak ringan', key: 'total_rusak_ringan', icon: 'bi-exclamation-triangle', tone: 'amber' },
  { label: 'Rusak berat', key: 'total_rusak_berat', icon: 'bi-x-octagon', tone: 'red' },
  { label: 'Perangkat daerah', key: 'total_perangkat_daerah', icon: 'bi-buildings', tone: 'teal' },
]

const columns = [
  { key: 'perangkat_daerah', label: 'Perangkat daerah' },
  { key: 'total_kendaraan', label: 'Total', class: 'text-center' },
  { key: 'baik', label: 'Baik', class: 'text-center' },
  { key: 'rusak_ringan', label: 'Rusak ringan', class: 'text-center' },
  { key: 'rusak_berat', label: 'Rusak berat', class: 'text-center' },
  { key: 'tanpa_penanggung_jawab', label: 'Tanpa PJ', class: 'text-center' },
]

function pct(n, total) {
  return total ? Math.round((n / total) * 1000) / 10 : 0
}
</script>

<template>
  <div>
    <PageHeader
      eyebrow="Rekapitulasi"
      title="Laporan kondisi kendaraan"
      subtitle="Rekap jumlah kendaraan dinas operasional tiap perangkat daerah menurut kondisi"
    />

    <div v-if="error" class="alert alert-danger d-flex justify-content-between align-items-center" role="alert">
      <span>{{ error }}</span>
      <button class="btn btn-sm btn-outline-danger" @click="load">Coba lagi</button>
    </div>

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

      <DataTable
        :columns="columns"
        :rows="data"
        :loading="false"
        empty-icon="bi-bar-chart-line"
        empty-title="Belum ada data laporan"
        empty-text="Laporan akan tampil di sini setelah ada perangkat daerah dan kendaraan."
      >
        <template #cell-perangkat_daerah="{ row }">
          <div class="fw-semibold text-body">
            <span class="role-tag me-2">{{ row.perangkat_daerah.kode }}</span>{{ row.perangkat_daerah.nama }}
          </div>
          <div class="kondisi-bar mt-1" :title="`Proporsi kondisi ${row.perangkat_daerah.kode}`">
            <span v-if="row.kondisi.baik" class="kb kb-baik" :style="{ width: pct(row.kondisi.baik, row.total_kendaraan) + '%' }"></span>
            <span v-if="row.kondisi.rusak_ringan" class="kb kb-ringan" :style="{ width: pct(row.kondisi.rusak_ringan, row.total_kendaraan) + '%' }"></span>
            <span v-if="row.kondisi.rusak_berat" class="kb kb-berat" :style="{ width: pct(row.kondisi.rusak_berat, row.total_kendaraan) + '%' }"></span>
          </div>
        </template>
        <template #cell-total_kendaraan="{ row }"><span class="fw-semibold">{{ row.total_kendaraan }}</span></template>
        <template #cell-baik="{ row }"><span class="text-success fw-semibold">{{ row.kondisi.baik }}</span></template>
        <template #cell-rusak_ringan="{ row }"><span class="text-warning-emphasis fw-semibold">{{ row.kondisi.rusak_ringan }}</span></template>
        <template #cell-rusak_berat="{ row }"><span class="text-danger fw-semibold">{{ row.kondisi.rusak_berat }}</span></template>
        <template #cell-tanpa_penanggung_jawab="{ row }"><span class="text-secondary">{{ row.tanpa_penanggung_jawab }}</span></template>

        <template #card="{ row }">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="min-w-0">
              <div class="fw-bold text-truncate">{{ row.perangkat_daerah.nama }}</div>
              <span class="role-tag mt-1">{{ row.perangkat_daerah.kode }}</span>
            </div>
            <div class="text-end">
              <div class="fw-bold">{{ row.total_kendaraan }}</div>
              <div class="small text-secondary">kendaraan</div>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-1 mt-2">
            <span class="chip chip-baik">{{ row.kondisi.baik }} baik</span>
            <span class="chip chip-ringan">{{ row.kondisi.rusak_ringan }} ringan</span>
            <span class="chip chip-berat">{{ row.kondisi.rusak_berat }} berat</span>
          </div>

          <div class="kondisi-bar mt-2">
            <span v-if="row.kondisi.baik" class="kb kb-baik" :style="{ width: pct(row.kondisi.baik, row.total_kendaraan) + '%' }"></span>
            <span v-if="row.kondisi.rusak_ringan" class="kb kb-ringan" :style="{ width: pct(row.kondisi.rusak_ringan, row.total_kendaraan) + '%' }"></span>
            <span v-if="row.kondisi.rusak_berat" class="kb kb-berat" :style="{ width: pct(row.kondisi.rusak_berat, row.total_kendaraan) + '%' }"></span>
          </div>

          <div class="small text-secondary mt-2">
            <i class="bi bi-person-x me-1"></i>{{ row.tanpa_penanggung_jawab }} tanpa penanggung jawab
          </div>
        </template>
      </DataTable>
    </template>
  </div>
</template>

<style scoped>
.kondisi-bar {
  display: flex;
  height: 6px;
  width: 180px;
  border-radius: 999px;
  overflow: hidden;
  background: var(--kdo-border);
}
.kb { display: block; height: 100%; }
.kb-baik { background: #7fd6a4; }
.kb-ringan { background: #ffd07a; }
.kb-berat { background: #ff9d9d; }

.chip {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.18rem 0.6rem;
  border-radius: 999px;
}
.chip-baik { background: #e3f6ec; color: #157347; }
.chip-ringan { background: #fff1d6; color: #9a5b00; }
.chip-berat { background: #fde3e3; color: #b42318; }
</style>
