<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Job Vacancies</h2>
      </div>
      <div class="flex gap-2">
        <button v-if="showArchived" @click="showArchived = false" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Active</button>
        <template v-else>
          <button @click="openCreate" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">+ New Vacancy</button>
          <button @click="showArchived = true" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Archived</button>
        </template>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
          <tr>
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">Location</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Salary</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="job in vacancies" :key="job.id">
            <td class="px-4 py-3">{{ job.title }}</td>
            <td class="px-4 py-3">{{ job.location }}</td>
            <td class="px-4 py-3">{{ job.type }}</td>
            <td class="px-4 py-3">{{ job.salary }}</td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex gap-3">
                <button @click="viewVacancy(job)" class="font-semibold text-slate-700">View</button>
                <button v-if="!showArchived" @click="openEdit(job)" class="font-semibold text-cyan-700">Edit</button>
                <button v-if="!showArchived" @click="archiveVacancy(job)" class="font-semibold text-rose-700">Archive</button>
                <button v-else @click="restoreVacancy(job)" class="font-semibold text-emerald-700">Restore</button>
              </div>
            </td>
          </tr>
          <tr v-if="vacancies.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">No vacancies found.</td>
          </tr>
        </tbody>
      </table>

      <div class="flex items-center justify-between border-t border-slate-200 px-4 py-3 text-sm">
        <span>Page {{ pagination.current_page }} / {{ pagination.last_page }}</span>
        <div class="flex gap-2">
          <button :disabled="pagination.current_page <= 1" @click="prevPage" class="rounded border px-2 py-1 disabled:opacity-50">Prev</button>
          <button :disabled="pagination.current_page >= pagination.last_page" @click="nextPage" class="rounded border px-2 py-1 disabled:opacity-50">Next</button>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-40 bg-black/40 p-4">
      <div class="mx-auto mt-10 max-w-2xl rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">{{ editing ? 'Edit Vacancy' : 'Create Vacancy' }}</h3>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Title</label>
            <input v-model="form.title" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Location</label>
            <input v-model="form.location" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Type</label>
            <select v-model="form.type" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="full-time">full-time</option>
              <option value="contract">contract</option>
              <option value="hybrid">hybrid</option>
              <option value="remote">remote</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Salary</label>
            <input v-model="form.salary" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700">Description</label>
            <textarea v-model="form.description" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="saveVacancy" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">Save</button>
        </div>
      </div>
    </div>

    <div v-if="selectedVacancy" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="selectedVacancy = null">
      <div class="mx-auto mt-12 max-w-2xl rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">{{ selectedVacancy.title }}</h3>
        <p class="mt-3 text-sm text-slate-700">{{ selectedVacancy.description }}</p>
        <div class="mt-5 flex justify-end">
          <button @click="selectedVacancy = null" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import api from '@/api/axios'

const vacancies = ref<any[]>([])
const showArchived = ref(false)
const showModal = ref(false)
const editing = ref<any | null>(null)
const selectedVacancy = ref<any | null>(null)
const currentPage = ref(1)
const pagination = ref({ current_page: 1, last_page: 1 })

const form = ref({
  title: '',
  location: '',
  type: 'full-time',
  salary: '',
  description: '',
})

const load = async () => {
  const res = await api.get('/vacancies', {
    params: {
      archived: showArchived.value ? 'true' : 'false',
      page: currentPage.value,
    },
  })

  const payload = res.data?.data || {}
  vacancies.value = payload.data || payload || []
  pagination.value = {
    current_page: payload.current_page || 1,
    last_page: payload.last_page || 1,
  }
}

const openCreate = () => {
  editing.value = null
  form.value = { title: '', location: '', type: 'full-time', salary: '', description: '' }
  showModal.value = true
}

const openEdit = (job: any) => {
  editing.value = job
  form.value = {
    title: job.title || '',
    location: job.location || '',
    type: job.type || 'full-time',
    salary: job.salary || '',
    description: job.description || '',
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveVacancy = async () => {
  if (editing.value) {
    await api.put(`/job-vacancies/${editing.value.id}`, {
      title: form.value.title,
      location: form.value.location,
      type: form.value.type,
      salary: form.value.salary,
      description: form.value.description,
    })
  } else {
    await api.post('/job-vacancies', {
      title: form.value.title,
      location: form.value.location,
      type: form.value.type,
      salary: form.value.salary,
      description: form.value.description,
    })
  }
  closeModal()
  await load()
}

const viewVacancy = async (job: any) => {
  const res = await api.get(`/job-vacancies/${job.id}`)
  selectedVacancy.value = res.data?.data || job
}

const archiveVacancy = async (job: any) => {
  if (!confirm(`Archive \"${job.title}\"?`)) return
  await api.delete(`/job-vacancies/${job.id}`)
  await load()
}

const restoreVacancy = async (job: any) => {
  if (!confirm(`Restore \"${job.title}\"?`)) return
  await api.post(`/job-vacancies/${job.id}/restore`)
  await load()
}

const nextPage = () => {
  if (currentPage.value < pagination.value.last_page) currentPage.value += 1
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value -= 1
}

watch([showArchived, currentPage], load)
onMounted(load)
</script>
