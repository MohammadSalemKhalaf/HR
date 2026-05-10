<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Job Applications</h2>
      </div>
      <div class="flex gap-2">
        <button v-if="showArchived" @click="showArchived = false" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Active</button>
        <button v-else @click="showArchived = true" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Archived</button>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
          <tr>
            <th class="px-4 py-3">Applicant</th>
            <th class="px-4 py-3">Vacancy</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="app in applications" :key="app.id">
            <td class="px-4 py-3">{{ app.user?.name || '-' }}</td>
            <td class="px-4 py-3">{{ app.jobvacancy?.title || '-' }}</td>
            <td class="px-4 py-3">{{ app.status || '-' }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex flex-wrap justify-end gap-x-3 gap-y-1">
                <button @click="viewApplication(app)" class="font-semibold text-slate-700">View</button>
                <button v-if="!showArchived" @click="openEditStatusModal(app)" class="font-semibold text-cyan-700">Edit</button>
                <button v-if="!showArchived" @click="acceptApplication(app)" class="font-semibold text-emerald-700">Accept</button>
                <button v-if="!showArchived" @click="rejectApplication(app)" class="font-semibold text-amber-700">Reject</button>
                <button v-if="!showArchived" @click="archiveApplication(app)" class="font-semibold text-rose-700">Archive</button>
                <button v-else @click="restoreApplication(app)" class="font-semibold text-emerald-700">Restore</button>
              </div>
            </td>
          </tr>
          <tr v-if="applications.length === 0">
            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No applications found.</td>
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

    <div v-if="selectedApplication" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="selectedApplication = null">
      <div class="mx-auto mt-12 max-w-2xl rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Application Details</h3>
        <p class="mt-3 text-sm"><span class="font-semibold">Applicant:</span> {{ selectedApplication.user?.name || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Email:</span> {{ selectedApplication.user?.email || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Vacancy:</span> {{ selectedApplication.jobvacancy?.title || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Status:</span> {{ selectedApplication.status || '-' }}</p>
        <div class="mt-5 flex justify-end">
          <button @click="selectedApplication = null" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">Close</button>
        </div>
      </div>
    </div>

    <div v-if="showEditStatusModal" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="closeEditStatusModal">
      <div class="mx-auto mt-16 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Edit Application Status</h3>
        <p class="mt-1 text-sm text-slate-600">Applicant: {{ statusTargetApplication?.user?.name || '-' }}</p>

        <div class="mt-4">
          <label class="block text-sm font-semibold text-slate-700">Status</label>
          <select v-model="selectedStatus" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="pending">Pending</option>
            <option value="accepted">Accepted</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeEditStatusModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="submitStatusUpdate" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">Update</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import api from '@/api/axios'

const applications = ref<any[]>([])
const selectedApplication = ref<any | null>(null)
const showArchived = ref(false)
const showEditStatusModal = ref(false)
const statusTargetApplication = ref<any | null>(null)
const selectedStatus = ref('pending')
const currentPage = ref(1)
const pagination = ref({ current_page: 1, last_page: 1 })

const load = async () => {
  const res = await api.get('/applications', {
    params: {
      archived: showArchived.value ? 'true' : 'false',
      page: currentPage.value,
    },
  })
  const payload = res.data?.data || {}
  applications.value = payload.data || payload || []
  pagination.value = {
    current_page: payload.current_page || 1,
    last_page: payload.last_page || 1,
  }
}

const viewApplication = async (application: any) => {
  const res = await api.get(`/applications/${application.id}`)
  selectedApplication.value = res.data?.data || application
}

const openEditStatusModal = (application: any) => {
  statusTargetApplication.value = application
  selectedStatus.value = application.status || 'pending'
  showEditStatusModal.value = true
}

const closeEditStatusModal = () => {
  showEditStatusModal.value = false
  statusTargetApplication.value = null
  selectedStatus.value = 'pending'
}

const submitStatusUpdate = async () => {
  if (!statusTargetApplication.value) return

  await api.put(`/applications/${statusTargetApplication.value.id}`, { status: selectedStatus.value })
  closeEditStatusModal()
  await load()
}

const setStatus = async (application: any) => {
  const status = window.prompt('Enter status: pending, accepted, rejected', application.status || 'pending')
  if (!status) return
  await api.put(`/applications/${application.id}`, { status })
  await load()
}

const acceptApplication = async (application: any) => {
  await api.post(`/applications/${application.id}/accept`)
  await load()
}

const rejectApplication = async (application: any) => {
  await api.post(`/applications/${application.id}/reject`)
  await load()
}

const archiveApplication = async (application: any) => {
  if (!confirm('Archive this application?')) return
  await api.delete(`/applications/${application.id}`)
  await load()
}

const restoreApplication = async (application: any) => {
  if (!confirm('Restore this application?')) return
  await api.post(`/applications/${application.id}/restore`)
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
