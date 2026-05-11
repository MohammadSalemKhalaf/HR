<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo & Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl mb-4">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-white">Join as Job Seeker</h1>
        <p class="text-slate-400 mt-2">Create your account and explore job opportunities</p>
      </div>

      <!-- Form Card -->
      <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 shadow-2xl">
        <form @submit.prevent="handleRegister" class="space-y-5">
          <!-- Alert Messages -->
          <div v-if="errorMessage" class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
            <p class="text-sm text-red-300">{{ errorMessage }}</p>
          </div>
          <div v-if="successMessage" class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
            <p class="text-sm text-green-300">{{ successMessage }}</p>
          </div>

          <!-- Name Input -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="John Doe"
              class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
          </div>

          <!-- Email Input -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="your@email.com"
              class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
          </div>

          <!-- Password Input -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
            <input
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
            <p class="text-xs text-slate-400 mt-1">Minimum 8 characters</p>
          </div>

          <!-- Confirm Password Input -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Confirm Password</label>
            <input
              v-model="form.confirmPassword"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
          </div>

          <!-- Terms Checkbox -->
          <div class="flex items-start">
            <input
              v-model="form.agreeToTerms"
              type="checkbox"
              class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-blue-500 cursor-pointer mt-0.5"
            />
            <label class="ml-3 text-sm text-slate-300">
              I agree to the Terms of Service and Privacy Policy
            </label>
          </div>

          <!-- Register Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition duration-200"
          >
            <span v-if="!loading">Create Account</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              Creating account...
            </span>
          </button>

          <!-- Login Link -->
          <p class="text-center text-slate-400 text-sm">
            Already have an account?
            <router-link to="/login" class="text-blue-400 hover:text-blue-300 font-medium">Sign in</router-link>
          </p>
        </form>
      </div>

      <!-- Footer -->
      <p class="text-center text-slate-500 text-xs mt-6">
        By registering, you agree to join our growing community of job seekers and professionals.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'

const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
  agreeToTerms: false,
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleRegister = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  // Validation
  if (!form.value.name.trim()) {
    errorMessage.value = 'Please enter your name'
    return
  }

  if (!form.value.email.trim()) {
    errorMessage.value = 'Please enter your email'
    return
  }

  if (form.value.password.length < 8) {
    errorMessage.value = 'Password must be at least 8 characters'
    return
  }

  if (form.value.password !== form.value.confirmPassword) {
    errorMessage.value = 'Passwords do not match'
    return
  }

  if (!form.value.agreeToTerms) {
    errorMessage.value = 'You must agree to the terms of service'
    return
  }

  loading.value = true

  try {
    const response = await api.post('/auth/register', {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      role: 'job_seeker',
    })

    const token = response.data?.data?.token
    const user = response.data?.data?.user

    if (token && user) {
      localStorage.setItem('auth_token', token)
      localStorage.setItem('user', JSON.stringify(user))
      successMessage.value = 'Registration successful! Redirecting...'

      setTimeout(() => {
        router.push('/jobs')
      }, 1500)
    }
  } catch (error: any) {
    const errorData = error.response?.data
    errorMessage.value = errorData?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
