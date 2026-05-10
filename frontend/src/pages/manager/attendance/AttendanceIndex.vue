<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-slate-500">Management</p>
          <h2 class="text-3xl font-bold text-slate-900">Attendance Records</h2>
        </div>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white p-6">
        <form @submit.prevent="filterByDate" class="flex flex-wrap gap-4 items-end">
          <div class="flex-1 min-w-[250px]">
            <label class="block text-sm font-medium text-slate-700 mb-2">Filter by Date</label>
            <input v-model="selectedDate" type="date" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500" />
          </div>
          <button type="submit" class="rounded-lg bg-cyan-600 px-6 py-2 text-sm font-semibold text-white hover:bg-cyan-500 transition">
            Filter
          </button>
        </form>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
          <h3 class="font-semibold text-slate-900">
            Attendance for {{ new Date(selectedDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
          </h3>
        </div>

        <div v-if="records.length > 0" class="overflow-hidden">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
              <tr>
                <th class="px-6 py-3 text-left">Employee</th>
                <th class="px-6 py-3 text-left">Department</th>
                <th class="px-6 py-3 text-left">Check In</th>
                <th class="px-6 py-3 text-left">Check Out</th>
                <th class="px-6 py-3 text-left">Hours</th>
                <th class="px-6 py-3 text-left">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="r in records" :key="r.id" class="hover:bg-slate-50 transition">
                <td class="px-6 py-3">
                  <router-link :to="`/manager/employees/${r.employee?.id}`" class="font-semibold text-cyan-600 hover:text-cyan-700">
                    {{ r.employee?.user?.name || '-' }}
                  </router-link>
                </td>
                <td class="px-6 py-3 text-slate-600">{{ r.employee?.department?.name || '-' }}</td>
                <td class="px-6 py-3 text-slate-600">{{ r.check_in_at ? formatTime(r.check_in_at) : '—' }}</td>
                <td class="px-6 py-3 text-slate-600">{{ r.check_out_at ? formatTime(r.check_out_at) : '—' }}</td>
                <td class="px-6 py-3 text-slate-600">{{ r.hours || '—' }}</td>
                <td class="px-6 py-3">
                  <span v-if="r.check_in_at && r.check_out_at" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                    ✓ Complete
                  </span>
                  <span v-else-if="r.check_in_at" class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                    ⏱ In Progress
                  </span>
                  <span v-else class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                    — Not Started
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="px-6 py-12 text-center">
          <p class="text-slate-500 text-sm">No attendance records for the selected date.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const selectedDate = ref(new Date().toISOString().split('T')[0])
const records = ref<any[]>([])

function formatTime(time: string) {
  return new Date(time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}

async function filterByDate() {
  await fetchRecords()
}

async function fetchRecords() {
  try {
    const res = await api.get('/manager/attendance', { params: { date: selectedDate.value } })
    records.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading attendance records:', err) }
}

onMounted(() => fetchRecords())
</script>
