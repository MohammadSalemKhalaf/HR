<template>
  <div>
    <div class="grid grid-cols-3 gap-4 mb-6">
      <StatsCard title="Companies" :value="stats.companies" />
      <StatsCard title="Departments" :value="stats.departments" />
      <StatsCard title="Employees" :value="stats.employees" />
    </div>

    <div class="space-y-4">
      <BaseCard>
        <PageHeader title="Recent Activity" />
        <p class="text-sm text-gray-600">Recent admin activity will appear here (sourced from backend).</p>
      </BaseCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import StatsCard from '@/components/base/StatsCard.vue'
import BaseCard from '@/components/base/BaseCard.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import api from '@/api/axios'

const stats = ref({ companies: 0, departments: 0, employees: 0 })

onMounted(async () => {
  try {
    const { data } = await api.get('/admin/dashboard-stats')
    stats.value = data
  } catch (e) {
    // graceful fallback — backend may not provide endpoint yet
  }
})
</script>
