<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')

function isiAkunUji(role) {
  form.email = `${role}@kdo.test`
  form.password = 'password123'
}

async function submit() {
  loading.value = true
  errorMessage.value = ''

  try {
    await auth.login(form.email, form.password)
    router.push({ name: 'dashboard' })
  } catch (err) {
    const res = err.response?.data
    errorMessage.value =
      res?.errors?.email?.[0] || res?.errors?.password?.[0] || res?.message || 'Login gagal. Periksa email dan kata sandi Anda.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <aside class="auth-aside">
        <div class="brand-mark brand-mark-lg"><i class="bi bi-car-front-fill"></i></div>
        <div>
          <h2>Sistem KDO</h2>
          <p>Pengelolaan Kendaraan Dinas Operasional Perangkat Daerah</p>
          <ul class="auth-points">
            <li><i class="bi bi-check-circle-fill"></i>Pendataan kendaraan dan kondisinya</li>
            <li><i class="bi bi-check-circle-fill"></i>Penanggung jawab tiap kendaraan</li>
            <li><i class="bi bi-check-circle-fill"></i>Rekap per perangkat daerah</li>
          </ul>
        </div>
      </aside>

      <section class="auth-form">
        <span class="eyebrow">Selamat datang</span>
        <h1>Masuk ke akun Anda</h1>
        <p class="text-secondary small mb-4">Gunakan email dan kata sandi yang terdaftar.</p>

        <div v-if="errorMessage" class="alert alert-danger py-2 small d-flex gap-2 align-items-start" role="alert">
          <i class="bi bi-exclamation-circle mt-1"></i><span>{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="field">
              <i class="bi bi-envelope field-icon"></i>
              <input
                id="email"
                v-model.trim="form.email"
                type="email"
                class="form-control field-input"
                placeholder="nama@instansi.go.id"
                autocomplete="username"
                required
                autofocus
              />
            </div>
          </div>

          <div class="mb-4">
            <label for="password" class="form-label">Kata sandi</label>
            <div class="field">
              <i class="bi bi-lock field-icon"></i>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control field-input pe-5"
                placeholder="Masukkan kata sandi"
                autocomplete="current-password"
                required
              />
              <button
                type="button"
                class="icon-btn field-toggle"
                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                @click="showPassword = !showPassword"
              >
                <i :class="['bi', showPassword ? 'bi-eye-slash' : 'bi-eye']"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Masuk
          </button>
        </form>

        <div class="mt-4 pt-3 border-top">
          <div class="small text-secondary mb-2">Akun uji, ketuk untuk mengisi otomatis</div>
          <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-soft btn-sm" @click="isiAkunUji('admin')">
              <i class="bi bi-shield-check me-1"></i>Admin
            </button>
            <button type="button" class="btn btn-soft btn-sm" @click="isiAkunUji('pengelola')">
              <i class="bi bi-clipboard-data me-1"></i>Pengelola
            </button>
            <button type="button" class="btn btn-soft btn-sm" @click="isiAkunUji('pengguna')">
              <i class="bi bi-eye me-1"></i>Pengguna
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
