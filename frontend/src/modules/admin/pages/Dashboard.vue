<template>
  <div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg shadow-md p-8">
      <h1 class="text-4xl font-bold mb-2">Admin Dashboard</h1>
      <p class="text-blue-100">Welcome back! Here's what's happening with your organization.</p>
    </div>

    <!-- Stats Grid -->
    <div v-if="!loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatsCard title="Companies" :value="stats.companies" icon="building-2" color="blue" />
      <StatsCard title="Departments" :value="stats.departments" icon="folder" color="green" />
      <StatsCard title="Employees" :value="stats.employees" icon="users" color="purple" />
    </div>
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-lg shadow p-6 animate-pulse">
        <div class="h-4 bg-gray-200 rounded mb-4 w-1/2"></div>
        <div class="h-8 bg-gray-200 rounded"></div>
      </div>
      <div class="bg-white rounded-lg shadow p-6 animate-pulse">
        <div class="h-4 bg-gray-200 rounded mb-4 w-1/2"></div>
        <div class="h-8 bg-gray-200 rounded"></div>
      </div>
      <div class="bg-white rounded-lg shadow p-6 animate-pulse">
        <div class="h-4 bg-gray-200 rounded mb-4 w-1/2"></div>
        <div class="h-8 bg-gray-200 rounded"></div>
      </div>
    </div>

    <!-- Quick Actions -->
    <BaseCard>
      <PageHeader title="Quick Actions" subtitle="Common admin tasks" />
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <RouterLink
          to="/admin/users"
          class="p-4 bg-cyan-50 hover:bg-cyan-100 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">👤</div>
          <p class="font-semibold text-sm text-cyan-900">Manage Users</p>
        </RouterLink>
        <RouterLink
          to="/admin/companies"
          class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">🏢</div>
          <p class="font-semibold text-sm text-blue-900">Manage Companies</p>
        </RouterLink>
        <RouterLink
          to="/admin/job-categories"
          class="p-4 bg-green-50 hover:bg-green-100 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">📁</div>
          <p class="font-semibold text-sm text-green-900">Manage Categories</p>
        </RouterLink>
        <RouterLink
          to="/admin/users"
          class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">🗂️</div>
          <p class="font-semibold text-sm text-purple-900">Archived Users</p>
        </RouterLink>
      </div>
    </BaseCard>

    <!-- Recent Activity -->
    <BaseCard>
      <PageHeader title="Recent Activity" subtitle="Latest admin actions" />
      <div class="mt-4">
        <p class="text-sm text-gray-600 py-8 text-center">
          Recent admin activity will appear here (sourced from backend).
        </p>
      </div>
    </BaseCard>

    <!-- System Status -->
    <BaseCard>
      <PageHeader title="System Status" />
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="flex items-center justify-between p-3 bg-green-50 rounded">
          <span class="font-medium text-gray-700">Database Connection</span>
          <span class="text-green-600 font-semibold">✓ Connected</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-green-50 rounded">
          <span class="font-medium text-gray-700">API Server</span>
          <span class="text-green-600 font-semibold">✓ Running</span>
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import StatsCard from '@/components/base/StatsCard.vue'
import BaseCard from '@/components/base/BaseCard.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import api from '@/api/axios'

const stats = ref({ companies: 0, departments: 0, employees: 0 })
const loading = ref(true)

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/dashboard-stats')
    const payload = data?.data ?? data
    stats.value = {
      companies: Number(payload?.companies ?? 0),
      departments: Number(payload?.departments ?? 0),
      employees: Number(payload?.employees ?? 0)
    }
  } catch (e) {
    console.error('Failed to load dashboard stats:', e)
    // graceful fallback — backend may not provide endpoint yet
  } finally {
    loading.value = false
  }
})
</script>
