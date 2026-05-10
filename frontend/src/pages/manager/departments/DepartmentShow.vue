<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-slate-500">Management</p>
          <h2 class="text-3xl font-bold text-slate-900">{{ department.name || 'Department' }}</h2>
        </div>
        <router-link to="/manager/departments" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">← Back to Departments</router-link>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
          <h3 class="font-semibold text-slate-900">Department Information</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
          <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Department Name</p>
            <p class="mt-2 font-semibold text-slate-900">{{ department.name || '-' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Department Code</p>
            <p class="mt-2 font-semibold text-slate-900">{{ department.code || '-' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Manager</p>
            <p class="mt-2 font-semibold text-slate-900">{{ department.manager?.user?.name || '-' }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold text-slate-900">Employees</h3>
            <span class="text-sm text-slate-500">{{ employees.length }} employee(s)</span>
          </div>
        </div>

        <div v-if="employees.length > 0" class="overflow-hidden">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
              <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Email</th>
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
        <div v-else class="px-6 py-12 text-center">
          <p class="text-slate-500 text-sm">No employees in this department yet.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const route = useRoute()
const department = ref<any>({})
const employees = ref<any[]>([])

function capital(val: string) {
  if (!val) return '-'
  return val.charAt(0).toUpperCase() + val.slice(1)
}

async function fetchDepartment() {
  try {
    const id = route.params.id
    const res = await api.get(`/manager/departments/${id}`)
    const data = res.data
    department.value = data.department || data
    employees.value = data.employees || []
  } catch (err) { console.error(err) }
}

onMounted(() => fetchDepartment())
</script>
