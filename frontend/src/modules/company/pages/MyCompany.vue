<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">My Company</h2>
      </div>
      <div class="flex gap-2">
        <button @click="fetchCompany" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Refresh</button>
        <button v-if="!editing" @click="editing = true" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">Edit</button>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
      <div v-if="loading" class="text-slate-500">Loading...</div>
      <div v-else class="space-y-4">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Name</label>
            <input v-model="form.name" :disabled="!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Industry</label>
            <input v-model="form.industry" :disabled="!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Address</label>
          <input v-model="form.address" :disabled="!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Website</label>
          <input v-model="form.website" :disabled="!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Owner Email</label>
          <input v-model="form.email" :disabled="!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
        </div>

        <div v-if="error" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ error }}</div>

        <div v-if="editing" class="flex justify-end gap-2">
          <button @click="cancelEdit" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="save" :disabled="saving" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-50">{{ saving ? 'Saving...' : 'Save' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

const loading = ref(false)
const saving = ref(false)
const editing = ref(false)
const error = ref<string | null>(null)

const form = ref({ name: '', industry: '', address: '', website: '', email: '' })
const original = ref({ name: '', industry: '', address: '', website: '', email: '' })

const fetchCompany = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get('/company/profile')
    const c = res.data?.data || {}
    form.value = {
      name: c.name || '',
      industry: c.industry || '',
      address: c.address || '',
      website: c.website || '',
      email: c.owner?.email || '',
    }
    original.value = { ...form.value }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load company'
  } finally {
    loading.value = false
  }
}

const cancelEdit = () => {
  editing.value = false
  form.value = { ...original.value }
}

const save = async () => {
  saving.value = true
  error.value = null
  try {
    await api.put('/company/profile', {
      name: form.value.name,
      industry: form.value.industry,
      address: form.value.address,
      website: form.value.website || null,
      email: form.value.email || null,
    })
    editing.value = false
    await fetchCompany()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to update company'
  } finally {
    saving.value = false
  }
}

onMounted(fetchCompany)
</script>
