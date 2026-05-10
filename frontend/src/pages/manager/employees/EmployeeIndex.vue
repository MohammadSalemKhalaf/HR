<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-slate-500">Management</p>
          <h2 class="text-3xl font-bold text-slate-900">Employees</h2>
        </div>
      </div>

      <div v-if="employees.length > 0" class="rounded-lg border border-slate-200 bg-white overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left">Name</th>
              <th class="px-6 py-3 text-left">Email</th>
              <th class="px-6 py-3 text-left">Department</th>
              <th class="px-6 py-3 text-left">Title</th>
              <th class="px-6 py-3 text-left">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr v-for="e in employees" :key="e.id" class="hover:bg-slate-50 transition">
              <td class="px-6 py-3">
                <router-link :to="`/manager/employees/${e.id}`" class="font-semibold text-cyan-600 hover:text-cyan-700">
                  {{ e.user?.name || '-' }}
                </router-link>
              </td>
              <td class="px-6 py-3 text-slate-600">{{ e.user?.email || '-' }}</td>
              <td class="px-6 py-3">
                <router-link v-if="e.department" :to="`/manager/departments/${e.department.id}`" class="text-slate-600 hover:text-slate-900 underline">
                  {{ e.department.name }}
                </router-link>
                <span v-else>-</span>
              </td>
              <td class="px-6 py-3 text-slate-600">{{ e.job_title || '-' }}</td>
              <td class="px-6 py-3">
                <span v-if="e.status === 'active'" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                  ✓ Active
                </span>
                <span v-else class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                  {{ capital(e.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="rounded-lg border border-slate-200 bg-white p-12 text-center">
        <p class="text-slate-500 text-sm">No employees in your departments yet.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const employees = ref<any[]>([])

function capital(val: string) {
  if (!val) return '-'
  return val.charAt(0).toUpperCase() + val.slice(1)
}

async function fetchEmployees() {
  try {
    const res = await api.get('/manager/employees')
    employees.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading employees:', err) }
}

onMounted(() => fetchEmployees())
</script>
