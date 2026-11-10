<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const nav = [
  { name: 'dashboard', label: 'Dashboard', short: 'Beranda', icon: 'bi-grid-1x2', match: '/' },
  { name: 'kendaraan.index', label: 'Kendaraan', short: 'Kendaraan', icon: 'bi-car-front', match: '/kendaraan' },
  { name: 'pegawai.index', label: 'Pegawai', short: 'Pegawai', icon: 'bi-people', match: '/pegawai' },
  { name: 'perangkat-daerah.index', label: 'Perangkat daerah', short: 'PD', icon: 'bi-buildings', match: '/perangkat-daerah' },
  { name: 'laporan.index', label: 'Laporan', short: 'Laporan', icon: 'bi-bar-chart-line', match: '/laporan' },
]

/** Menu topnav: admin mendapat menu "Akun" tambahan (tidak masuk bottom nav). */
const navTop = computed(() =>
  auth.isAdmin
    ? [...nav, { name: 'akun.index', label: 'Akun', icon: 'bi-person-gear', match: '/akun' }]
    : nav,
)

function isActive(item) {
  return item.match === '/' ? route.path === '/' : route.path.startsWith(item.match)
}

const initials = computed(() =>
  (auth.user?.name || '?')
    .split(' ')
    .map((s) => s[0])
    .slice(0, 2)
    .join('')
    .toUpperCase(),
)

/**
 * Tutup dropdown pengguna (mobile): dropdown hanya menutup saat klik
 * di luar menu — simulasikan klik pada body.
 */
function closeUserDropdown() {
  document.body.click()
}

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div v-if="auth.isAuthenticated">
    <header class="topbar">
      <div class="container topbar-inner">
        <router-link :to="{ name: 'dashboard' }" class="brand">
          <span class="brand-mark"><i class="bi bi-car-front-fill"></i></span>
          <span class="brand-text">
            <strong>Sistem KDO</strong>
            <small class="d-none d-sm-block">Kendaraan Dinas Operasional</small>
          </span>
        </router-link>

        <nav class="topnav d-none d-lg-flex">
          <router-link
            v-for="item in navTop"
            :key="item.name"
            :to="{ name: item.name }"
            class="topnav-link"
            :class="{ active: isActive(item) }"
          >
            <i :class="['bi', item.icon]"></i>{{ item.label }}
          </router-link>
        </nav>

        <div class="dropdown topbar-user">
          <button class="user-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="user-avatar">{{ initials }}</span>
            <span class="d-none d-sm-inline">{{ auth.user?.name }}</span>
            <span class="role-tag d-none d-sm-inline">{{ auth.user?.role }}</span>
            <i class="bi bi-chevron-down small text-secondary"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2 d-sm-none">
              <div class="fw-semibold">{{ auth.user?.name }}</div>
              <span class="role-tag">{{ auth.user?.role }}</span>
            </li>
            <li v-if="auth.isAdmin" class="d-lg-none">
              <router-link class="dropdown-item" :to="{ name: 'akun.index' }" @click="closeUserDropdown">
                <i class="bi bi-person-gear me-2"></i>Akun
              </router-link>
            </li>
            <li class="d-lg-none"><hr class="dropdown-divider" /></li>
            <li>
              <button class="dropdown-item" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar
              </button>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main class="container app-main">
      <router-view />
    </main>

    <nav class="bottomnav d-lg-none" aria-label="Navigasi utama">
      <router-link
        v-for="item in nav"
        :key="item.name"
        :to="{ name: item.name }"
        :class="{ active: isActive(item) }"
      >
        <i :class="['bi', item.icon]"></i>
        <span>{{ item.short }}</span>
      </router-link>
    </nav>
  </div>

  <router-view v-else />
</template>
