<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Create Job Category</h2>
        <p class="mt-2 text-sm text-slate-600">Add a new hiring category.</p>
      </div>

      <router-link
        to="/admin/job-categories"
        class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
      >
        Back
      </router-link>
    </div>

    <div class="mx-auto max-w-2xl rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
      <form @submit.prevent="createCategory" class="space-y-6">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Category Name</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            :class="{ 'border-rose-300 bg-rose-50': errors.name }"
          >
          <p v-if="errors.name" class="mt-2 text-sm text-rose-600">{{ errors.name }}</p>
        </div>

        <div v-if="formError" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ formError }}
        </div>

        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
          <router-link
            to="/admin/job-categories"
            class="rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="submitting"
            class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500 disabled:opacity-50"
          >
            {{ submitting ? 'Creating...' : 'Create Category' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'

const router = useRouter()
const submitting = ref(false)
const formError = ref<string | null>(null)
const errors = ref<Record<string, string>>({})

const form = ref({
  name: ''
})

const firstError = (value: unknown): string => {
  if (Array.isArray(value)) {
    return String(value[0] ?? '')
  }
  return String(value ?? '')
}

const createCategory = async () => {
  errors.value = {}
  formError.value = null
  submitting.value = true

  try {
    await api.post('/job-categories', {
      name: form.value.name
    })

    router.push('/admin/job-categories')
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Failed to create category'
    const backendErrors = err.response?.data?.errors || {}

    Object.keys(backendErrors).forEach((key) => {
      errors.value[key] = firstError(backendErrors[key])
    })
  } finally {
    submitting.value = false
  }
}
</script>
