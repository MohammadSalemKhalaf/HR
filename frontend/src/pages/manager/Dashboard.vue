<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-slate-500">Manager Area</p>
          <h1 class="text-3xl font-bold text-slate-900">Manager Dashboard</h1>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
          <p class="text-sm text-slate-500">Departments</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.departmentsCount || 0 }}</p>
        </div>
        <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
          <p class="text-sm text-slate-500">Employees</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.employeesCount || 0 }}</p>
        </div>
        <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
          <p class="text-sm text-slate-500">Pending Leaves</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.pendingLeaves || 0 }}</p>
        </div>
        <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
          <p class="text-sm text-slate-500">Today Attendance</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.todayAttendance || 0 }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <router-link to="/manager/tasks" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
          <p class="text-xs uppercase tracking-wide text-slate-500">Quick Access</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">📋 Tasks</p>
        </router-link>
        <router-link to="/manager/employees" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
          <p class="text-xs uppercase tracking-wide text-slate-500">Quick Access</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">👥 Employees</p>
        </router-link>
        <router-link to="/manager/leaves" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
          <p class="text-xs uppercase tracking-wide text-slate-500">Quick Access</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">📅 Leaves</p>
        </router-link>
        <router-link to="/manager/attendance" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
          <p class="text-xs uppercase tracking-wide text-slate-500">Quick Access</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">⏱ Attendance</p>
        </router-link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'

const stats = ref({ departmentsCount: 0, employeesCount: 0, pendingLeaves: 0, todayAttendance: 0 })

async function fetchStats() {
  try {
    const res = await fetch('/api/manager/stats')
    if (!res.ok) throw new Error('Fetch failed')
    const data = await res.json()
    Object.assign(stats.value, data)
  } catch (err) { console.error(err) }
}

onMounted(() => fetchStats())
</script>
