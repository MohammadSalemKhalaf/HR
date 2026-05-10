<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-950 via-black to-gray-950 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="mb-8 flex items-center justify-between gap-4">
        <div>
          <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Career</p>
          <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Available Positions</h1>
          <p class="mt-2 text-sm text-gray-400">Find your next opportunity</p>
        </div>
        <router-link :to="{ name: 'MyApplications' }" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800/60 border border-gray-700 rounded-full text-sm font-semibold text-white hover:bg-indigo-600 hover:border-indigo-500 transition">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a2 2 0 00-2 2v1H6a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-8a2 2 0 00-2-2h-2V4a2 2 0 00-2-2zM9 6V4a1 1 0 112 0v2H9z"/></svg>
          My Applications
        </router-link>
      </div>

      <div v-if="loading" class="text-gray-400 text-center py-12">Loading jobs…</div>

      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <router-link v-for="job in jobs" :key="job.id" :to="{ name: 'JobShow', params: { id: job.id } }" class="group bg-gray-900/80 backdrop-blur border border-gray-800 rounded-2xl p-6 hover:border-indigo-500 transition">
          <div class="space-y-3">
            <h2 class="text-lg font-bold text-white group-hover:text-indigo-400 transition">{{ job.title }}</h2>
            <div class="space-y-2">
              <div class="flex items-center gap-2 text-sm text-gray-400">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                {{ job.company?.name || 'Company' }}
              </div>
              <div class="flex items-center gap-2 text-sm text-gray-400">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                {{ job.location }}
              </div>
            </div>
            <div class="pt-3 border-t border-gray-700">
              <p class="text-green-400 font-semibold">${{ (job.salary || 0).toLocaleString() }} /year</p>
            </div>
          </div>
        </router-link>
      </div>

      <div v-if="!loading && jobs.length === 0" class="text-center py-12 text-gray-400">
        <p>No jobs available at the moment. Check back soon!</p>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import api from '@/api/axios'

export default defineComponent({
  name: 'JobsIndex',
  setup() {
    const jobs = ref<any[]>([])
    const loading = ref(true)

    async function fetchJobs() {
      loading.value = true
      try {
        const res = await api.get('/vacancies')
        jobs.value = res.data.data?.data || res.data.data || []
      } catch (e) {
        console.error('Failed to fetch jobs', e)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      fetchJobs()
    })

    return { jobs, loading }
  }
})
</script>

<style scoped>
</style>
