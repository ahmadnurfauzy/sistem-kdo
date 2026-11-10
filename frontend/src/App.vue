<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div v-if="auth.isAuthenticated">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
      <div class="container">
        <router-link :to="{ name: 'dashboard' }" class="navbar-brand fw-semibold">
          🚗 Sistem KDO
        </router-link>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarUtama"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div id="navbarUtama" class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'dashboard' }">Dashboard</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'kendaraan.index' }">Kendaraan</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'pegawai.index' }">Pegawai</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'perangkat-daerah.index' }">Perangkat Daerah</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'laporan.index' }">Laporan</router-link>
            </li>
          </ul>

          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle text-white"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
              >
                {{ auth.user?.name }}
                <span class="badge bg-light text-primary ms-1">{{ auth.user?.role }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <button class="dropdown-item" @click="handleLogout">Logout</button>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container pb-5">
      <router-view />
    </main>
  </div>

  <router-view v-else />
</template>
