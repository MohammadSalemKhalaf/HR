<template>
  <div class="space-y-6">
    <div>
      <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company Area</p>
      <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Dashboard</h2>
      <p class="mt-2 text-sm text-slate-600">Overview for company workflows.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-sm text-slate-500">Departments</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.departments }}</p>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-sm text-slate-500">Employees</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.employees }}</p>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-sm text-slate-500">Open Vacancies</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.jobs }}</p>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-sm text-slate-500">Applications</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.applications }}</p>
      </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
      <router-link to="/company/my-company" class="rounded-xl border border-slate-200 bg-white p-5 hover:border-cyan-300">My Company</router-link>
      <router-link to="/company/departments" class="rounded-xl border border-slate-200 bg-white p-5 hover:border-cyan-300">Departments</router-link>
      <router-link to="/company/employees" class="rounded-xl border border-slate-200 bg-white p-5 hover:border-cyan-300">Employees</router-link>
      <router-link to="/company/vacancies" class="rounded-xl border border-slate-200 bg-white p-5 hover:border-cyan-300">Job Vacancies</router-link>
      <router-link to="/company/applications" class="rounded-xl border border-slate-200 bg-white p-5 hover:border-cyan-300">Job Applications</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

const stats = ref({ departments: 0, employees: 0, jobs: 0, applications: 0 })

const fetchStats = async () => {
  try {
    const res = await api.get('/company/dashboard-stats')
    const payload = res.data?.data || {}
    stats.value = {
      departments: payload.departments || 0,
      employees: payload.employees || 0,
      jobs: payload.jobs || 0,
      applications: payload.applications || 0,
    }
  } catch (err) {
    console.error('Failed to fetch company stats', err)
  }
}

onMounted(fetchStats)
</script>
