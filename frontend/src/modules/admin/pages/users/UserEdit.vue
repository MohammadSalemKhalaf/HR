<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit User</h2>
        <p class="mt-2 text-sm text-slate-600">Update the user's password while keeping identity and role intact.</p>
      </div>

      <router-link
        to="/admin/users"
        class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
      >
        Back
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
      <div class="text-center text-slate-500">Loading user...</div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
      <div class="text-center text-rose-600">{{ error }}</div>
    </div>

    <!-- Form -->
    <div v-else class="mx-auto max-w-3xl">
      <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
        <form @submit.prevent="updatePassword" class="space-y-6">
          <div class="grid gap-4 md:grid-cols-3">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
              <input
                type="text"
                :value="user.name"
                class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500"
                readonly
              >
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
              <input
                type="text"
                :value="user.email"
                class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500"
                readonly
              >
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
              <input
                type="text"
                :value="user.role_name || user.role"
                class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500"
                readonly
              >
            </div>
          </div>

          <div class="grid gap-6 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">New Password</label>
              <input
                v-model="form.password"
                type="password"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                :class="{ 'border-rose-300 bg-rose-50': errors.password }"
              >
              <p v-if="errors.password" class="mt-2 text-sm text-rose-600">{{ errors.password }}</p>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Confirm Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
              >
            </div>
          </div>

          <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
            <router-link
              to="/admin/users"
              class="rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="submitting"
              class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500 disabled:opacity-50"
            >
              {{ submitting ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'

interface User {
  id: string
  name: string
  email: string
  role: string
  role_name: string
}

const route = useRoute()
const router = useRouter()
const user = ref<User>({
  id: '',
  name: '',
  email: '',
  role: '',
  role_name: ''
})
const loading = ref(false)
const error = ref<string | null>(null)
const submitting = ref(false)
const form = ref({
  password: '',
  password_confirmation: ''
})
const errors = ref<Record<string, string>>({})

const fetchUser = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get(`/admin/users/${route.params.id}`)
    user.value = response.data.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load user'
    console.error('Error fetching user:', err)
  } finally {
    loading.value = false
  }
}

const updatePassword = async () => {
  errors.value = {}
  if (!form.value.password) {
    errors.value.password = 'Password is required'
    return
  }
  if (form.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
    return
  }
  if (form.value.password !== form.value.password_confirmation) {
    errors.value.password = 'Passwords do not match'
    return
  }

  submitting.value = true
  try {
    await api.put(`/admin/users/${route.params.id}`, {
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    })
    router.push('/admin/users')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to update password'
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    }
    console.error('Error updating password:', err)
  } finally {
    submitting.value = false
  }
}

onMounted(fetchUser)
</script>
