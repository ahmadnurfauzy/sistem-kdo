import { defineStore } from 'pinia'
import authApi from '../api/auth'

/**
 * Store sesi pengguna: token + profil, dipertahankan di localStorage.
 */
export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('kdo_token') || '',
    user: JSON.parse(localStorage.getItem('kdo_user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => Boolean(state.token),
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: {
    setSession(token, user) {
      this.token = token
      this.user = user
      localStorage.setItem('kdo_token', token)
      localStorage.setItem('kdo_user', JSON.stringify(user))
    },

    clearSession() {
      this.token = ''
      this.user = null
      localStorage.removeItem('kdo_token')
      localStorage.removeItem('kdo_user')
    },

    async login(email, password) {
      const res = await authApi.login({ email, password })
      this.setSession(res.data.data.token, res.data.data.user)
    },

    async logout() {
      try {
        await authApi.logout()
      } catch {
        // token mungkin sudah tidak valid — tetap akhiri sesi lokal
      }
      this.clearSession()
    },
  },
})
