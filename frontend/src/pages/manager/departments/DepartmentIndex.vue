<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-slate-500">Management</p>
          <h2 class="text-3xl font-bold text-slate-900">My Departments</h2>
        </div>
      </div>

      <div v-if="departments.length > 0" class="grid gap-4 md:grid-cols-2">
        <div v-for="d in departments" :key="d.id" class="rounded-lg border border-slate-200 bg-white p-6 hover:shadow-lg transition">
          <div class="flex items-start justify-between">
            <div>
              <router-link :to="`/manager/departments/${d.id}`" class="text-lg font-bold text-slate-900 hover:text-cyan-600">
                {{ d.name }}
              </router-link>
              <p v-if="d.code" class="mt-1 text-sm text-slate-500">Code: {{ d.code }}</p>
            </div>
          </div>
          <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-slate-600">
              <span class="font-semibold text-slate-900">{{ d.employees_count || 0 }}</span> Employees
            </p>
            <router-link :to="`/manager/departments/${d.id}`" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
              View →
            </router-link>
          </div>
        </div>
      </div>
      <div v-else class="rounded-lg border border-slate-200 bg-white p-12 text-center">
        <p class="text-slate-500 text-sm">You don't manage any departments yet.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const departments = ref<any[]>([])

async function fetchDepartments() {
  try {
    const res = await api.get('/manager/departments')
    departments.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading departments:', err) }
}

onMounted(() => fetchDepartments())
</script>
