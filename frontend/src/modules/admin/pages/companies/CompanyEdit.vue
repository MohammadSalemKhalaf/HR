<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit Company</h2>
        <p class="mt-2 text-sm text-slate-600">Update company profile details.</p>
      </div>

      <router-link
        to="/admin/companies"
        class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
      >
        Back
      </router-link>
    </div>

    <div v-if="loading" class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8 text-center text-slate-500">
      Loading company...
    </div>

    <div v-else class="mx-auto max-w-3xl rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
      <form @submit.prevent="updateCompany" class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Company Name</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
              :class="{ 'border-rose-300 bg-rose-50': errors.name }"
            >
            <p v-if="errors.name" class="mt-2 text-sm text-rose-600">{{ errors.name }}</p>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Industry</label>
            <input
              v-model="form.industry"
              type="text"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
              :class="{ 'border-rose-300 bg-rose-50': errors.industry }"
            >
            <p v-if="errors.industry" class="mt-2 text-sm text-rose-600">{{ errors.industry }}</p>
          </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Owner Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
              :class="{ 'border-rose-300 bg-rose-50': errors.email }"
            >
            <p v-if="errors.email" class="mt-2 text-sm text-rose-600">{{ errors.email }}</p>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">(Leave blank to keep current owner)</label>
          </div>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Address</label>
          <input
            v-model="form.address"
            type="text"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            :class="{ 'border-rose-300 bg-rose-50': errors.address }"
          >
          <p v-if="errors.address" class="mt-2 text-sm text-rose-600">{{ errors.address }}</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Website (optional)</label>
          <input
            v-model="form.website"
            type="text"
            placeholder="https://example.com"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            :class="{ 'border-rose-300 bg-rose-50': errors.website }"
          >
          <p v-if="errors.website" class="mt-2 text-sm text-rose-600">{{ errors.website }}</p>
        </div>

        <div v-if="formError" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ formError }}
        </div>

        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
          <router-link
            to="/admin/companies"
            class="rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="submitting"
            class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500 disabled:opacity-50"
          >
            {{ submitting ? 'Updating...' : 'Update Company' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const submitting = ref(false)
const formError = ref<string | null>(null)
const errors = ref<Record<string, string>>({})

const form = ref({
  name: '',
  address: '',
  industry: '',
  website: ''
})

// owner email editable
form.value.email = ''

const firstError = (value: unknown): string => {
  if (Array.isArray(value)) {
    return String(value[0] ?? '')
  }
  return String(value ?? '')
}

const fetchCompany = async () => {
  loading.value = true
  formError.value = null

  try {
    const response = await api.get(`/companies/${route.params.id}`)
    const company = response.data.data

    form.value = {
      name: company?.name || '',
      address: company?.address || '',
      industry: company?.industry || '',
      website: company?.website || '',
      email: company?.owner?.email || ''
    }
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Failed to load company'
  } finally {
    loading.value = false
  }
}

const updateCompany = async () => {
  errors.value = {}
  formError.value = null
  submitting.value = true

  try {
    await api.put(`/companies/${route.params.id}`, {
      name: form.value.name,
      address: form.value.address,
      industry: form.value.industry,
        website: form.value.website || null,
        email: form.value.email || null,
    })

    router.push('/admin/companies')
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Failed to update company'
    const backendErrors = err.response?.data?.errors || {}

    Object.keys(backendErrors).forEach((key) => {
      errors.value[key] = firstError(backendErrors[key])
    })
  } finally {
    submitting.value = false
  }
}

onMounted(fetchCompany)
</script>
