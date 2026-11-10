<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const loading = ref(false)
const errorMessage = ref('')

async function submit() {
  loading.value = true
  errorMessage.value = ''

  try {
    await auth.login(form.email, form.password)
    router.push({ name: 'dashboard' })
  } catch (err) {
    const res = err.response?.data
    errorMessage.value =
      res?.errors?.email?.[0] || res?.errors?.password?.[0] || res?.message || 'Login gagal.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page d-flex align-items-center justify-content-center min-vh-100 bg-primary bg-gradient">
    <div class="col-11 col-sm-8 col-md-5 col-lg-4">
      <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
          <div class="text-center mb-4">
            <div class="fs-1">🚗</div>
            <h4 class="fw-bold mb-1">Sistem KDO</h4>
            <p class="text-secondary small mb-0">
              Pengelolaan Kendaraan Dinas Operasional Perangkat Daerah
            </p>
          </div>

          <div v-if="errorMessage" class="alert alert-danger py-2 small">
            {{ errorMessage }}
          </div>

          <form @submit.prevent="submit">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="form-control"
                placeholder="admin@kdo.test"
                required
                autofocus
              />
            </div>

            <div class="mb-4">
              <label for="password" class="form-label">Password</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="••••••••"
                required
              />
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              Masuk
            </button>
          </form>

          <hr class="my-4" />
          <p class="text-secondary small text-center mb-0">
            Akun uji: <code>admin@kdo.test</code> / <code>viewer@kdo.test</code>
            <br />password: <code>password123</code>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  background-image: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}
</style>
