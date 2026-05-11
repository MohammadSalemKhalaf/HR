<template>
  <AuthLayout>
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
          <!-- Header -->
          <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">RAG EMS</h1>
            <p class="text-sm text-gray-600 mt-1">Employee Management System</p>
          </div>

          <!-- Alerts -->
          <div v-if="auth.error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
            <p class="font-semibold">{{ auth.error }}</p>
            <p v-if="debugMode" class="mt-2 text-xs opacity-75">{{ errorDetails }}</p>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleLogin" class="space-y-5">
            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="your@email.com"
                :disabled="auth.loading"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-500"
              />
              <p v-if="validationErrors.email" class="mt-1 text-sm text-red-600">
                {{ Array.isArray(validationErrors.email) ? validationErrors.email[0] : validationErrors.email }}
              </p>
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                :disabled="auth.loading"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-500"
              />
              <p v-if="validationErrors.password" class="mt-1 text-sm text-red-600">
                {{ Array.isArray(validationErrors.password) ? validationErrors.password[0] : validationErrors.password }}
              </p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="auth.loading"
              class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-medium py-2 rounded-lg transition flex items-center justify-center gap-2"
            >
              <svg v-if="auth.loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              {{ auth.loading ? 'Logging in...' : 'Sign In' }}
            </button>
          </form>

          <!-- Job Seeker Registration -->
          <div class="mt-4">
            <router-link
              to="/register"
              class="flex w-full items-center justify-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 transition hover:border-indigo-300 hover:bg-indigo-100"
            >
              Create Job Seeker Account
            </router-link>
            <p class="mt-2 text-center text-xs text-gray-500">
              Job seekers register here, then sign in to browse jobs and apply.
            </p>
          </div>

          <!-- Test Credentials -->
          <div class="mt-6 pt-6 border-t">
            <p class="text-xs text-gray-500 text-center mb-2">Demo credentials:</p>
            <div class="bg-gray-50 p-3 rounded text-xs text-gray-600 space-y-1">
              <p><strong>Admin:</strong> admin@gmail.com</p>
              <p><strong>Password:</strong> 12345678</p>
            </div>
          </div>

          <!-- Debug Info (Development Only) -->
          <div v-if="debugMode && auth.error" class="mt-4 pt-4 border-t">
            <p class="text-xs text-gray-500 mb-2">Debug Info:</p>
            <div class="bg-gray-900 text-gray-100 p-2 rounded text-xs overflow-auto max-h-32">
              <pre>{{ debugInfo }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })
const validationErrors = ref<Record<string, string | string[]>>({})
const lastError = ref<any>(null)
const debugMode = ref(false)

const errorDetails = computed(() => {
  if (!lastError.value) return ''
  if (typeof lastError.value === 'string') return lastError.value
  return JSON.stringify(lastError.value, null, 2)
})

const debugInfo = computed(() => {
  return JSON.stringify({
    apiUrl: import.meta.env.VITE_API_URL,
    hasToken: !!auth.token,
    tokenLength: auth.token?.length,
    userRole: auth.user?.role,
    error: auth.error,
    lastError: lastError.value?.message || lastError.value
  }, null, 2)
})

async function handleLogin() {
  validationErrors.value = {}
  auth.clearError()
  lastError.value = null

  try {
    console.log('[LoginPage] Form submit with:', form.email)
    await auth.login(form)
    
    // Login successful, redirect based on role
    console.log('[LoginPage] Login successful, user role:', auth.user?.role)
    const redirectPath = auth.getRedirectPath()
    console.log('[LoginPage] Redirecting to:', redirectPath)
    await router.push(redirectPath)
  } catch (e: any) {
    console.error('[LoginPage] Login error:', e)
    lastError.value = e
    
    if (e.type === 'validation') {
      validationErrors.value = e.validation || {}
      console.error('[LoginPage] Validation errors:', validationErrors.value)
    } else {
      // Enable debug mode on error for 10 seconds
      debugMode.value = true
      setTimeout(() => {
        debugMode.value = false
      }, 10000)
    }
  }
}

// Allow manual debug mode toggle with keyboard shortcut
if (typeof window !== 'undefined') {
  window.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
      debugMode.value = !debugMode.value
      console.log('[LoginPage] Debug mode:', debugMode.value ? 'ON' : 'OFF')
    }
  })
}
</script>


