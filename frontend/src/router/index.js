import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

/**
 * Rute SPA. Guard:
 * - halaman selain /login wajib memiliki token
 * - pengguna yang sudah login tidak bisa kembali ke /login
 * - halaman ber-meta requiresAdmin (kelola akun) hanya untuk role admin
 */
const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/',
      name: 'dashboard',
      component: () => import('../views/DashboardView.vue'),
    },
    {
      path: '/kendaraan',
      name: 'kendaraan.index',
      component: () => import('../views/KendaraanView.vue'),
    },
    {
      path: '/kendaraan/create',
      name: 'kendaraan.create',
      component: () => import('../views/KendaraanFormView.vue'),
    },
    {
      path: '/kendaraan/:id',
      name: 'kendaraan.show',
      component: () => import('../views/KendaraanDetailView.vue'),
    },
    {
      path: '/kendaraan/:id/edit',
      name: 'kendaraan.edit',
      component: () => import('../views/KendaraanFormView.vue'),
    },
    {
      path: '/pegawai',
      name: 'pegawai.index',
      component: () => import('../views/PegawaiView.vue'),
    },
    {
      path: '/pegawai/create',
      name: 'pegawai.create',
      component: () => import('../views/PegawaiFormView.vue'),
    },
    {
      path: '/pegawai/:id/edit',
      name: 'pegawai.edit',
      component: () => import('../views/PegawaiFormView.vue'),
    },
    {
      path: '/perangkat-daerah',
      name: 'perangkat-daerah.index',
      component: () => import('../views/PerangkatDaerahView.vue'),
    },
    {
      path: '/laporan',
      name: 'laporan.index',
      component: () => import('../views/LaporanView.vue'),
    },
    {
      path: '/akun',
      name: 'akun.index',
      component: () => import('../views/AkunView.vue'),
      meta: { requiresAdmin: true },
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: { name: 'dashboard' },
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'dashboard' }
  }

  if (!to.meta.guest && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  // halaman khusus admin (mis. /akun) — role lain dialihkan ke dashboard
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'dashboard' }
  }
})

export default router
