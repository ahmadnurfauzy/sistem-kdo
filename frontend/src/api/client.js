import axios from 'axios'
import router from '../router'

/**
 * Instance Axios terpusat untuk seluruh SPA.
 * - Menyisipkan bearer token otomatis dari sesi tersimpan
 * - Menangani 401 dengan auto-logout dan redirect ke halaman login
 */
const client = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    Accept: 'application/json',
  },
})

client.interceptors.request.use((config) => {
  const token = localStorage.getItem('kdo_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

client.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // token invalid/kedaluwarsa → akhiri sesi lokal & kembali ke login
      localStorage.removeItem('kdo_token')
      localStorage.removeItem('kdo_user')
      if (router.currentRoute.value.name !== 'login') {
        router.push({ name: 'login' })
      }
    }
    return Promise.reject(error)
  },
)

export default client
