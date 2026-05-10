<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-slate-500">Management</p>
          <h2 class="text-3xl font-bold text-slate-900">Leave Requests</h2>
        </div>
      </div>

      <div v-if="leaves.length > 0" class="rounded-lg border border-slate-200 bg-white overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left">Employee</th>
              <th class="px-6 py-3 text-left">Type</th>
              <th class="px-6 py-3 text-left">Period</th>
              <th class="px-6 py-3 text-left">Days</th>
              <th class="px-6 py-3 text-left">Status</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr v-for="l in leaves" :key="l.id" class="hover:bg-slate-50 transition">
              <td class="px-6 py-3">
                <span class="font-medium text-slate-900">{{ l.employee?.user?.name || '-' }}</span>
                <p class="text-xs text-slate-500 mt-1">{{ l.employee?.job_title }}</p>
              </td>
              <td class="px-6 py-3">
                <span class="font-medium text-slate-900">{{ leaveTypeIcon(l.leave_type) }}</span>
              </td>
              <td class="px-6 py-3 text-slate-600">{{ formatDate(l.start_date) }} – {{ formatDate(l.end_date) }}</td>
              <td class="px-6 py-3 text-slate-600">{{ l.days_count }} days</td>
              <td class="px-6 py-3">
                <span v-if="l.status === 'pending'" class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                  ⏳ Pending
                </span>
                <span v-else-if="l.status === 'approved'" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                  ✓ Approved
                </span>
                <span v-else-if="l.status === 'rejected'" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-700">
                  ✗ Rejected
                </span>
              </td>
              <td class="px-6 py-3 text-right">
                <div v-if="l.status === 'pending'" class="flex items-center justify-end gap-2">
                  <button @click="approveLeave(l.id)" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-200 transition">
                    ✓ Approve
                  </button>
                  <button @click="rejectLeave(l.id)" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-700 hover:bg-rose-200 transition">
                    ✗ Reject
                  </button>
                </div>
                <span v-else class="text-xs text-slate-500">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="rounded-lg border border-slate-200 bg-white p-12 text-center">
        <p class="text-slate-500 text-sm">No leave requests from your departments.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const leaves = ref<any[]>([])

function leaveTypeIcon(type: string) {
  const icons: any = { annual: '🏖 Annual', sick: '🏥 Sick', unpaid: '💼 Unpaid' }
  return icons[type] || `📋 ${type}`
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

async function approveLeave(id: number) {
  try {
    await api.post(`/manager/leaves/${id}/approve`)
    await fetchLeaves()
  } catch (err) { console.error('Error approving leave:', err) }
}

async function rejectLeave(id: number) {
  try {
    await api.post(`/manager/leaves/${id}/reject`)
    await fetchLeaves()
  } catch (err) { console.error('Error rejecting leave:', err) }
}

async function fetchLeaves() {
  try {
    const res = await api.get('/manager/leaves')
    leaves.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading leaves:', err) }
}

onMounted(() => fetchLeaves())
</script>
