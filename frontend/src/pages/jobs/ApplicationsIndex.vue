<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-950 via-black to-gray-950 py-12 px-4">
    <div class="max-w-5xl mx-auto">
      <div class="mb-8">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Applications</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">My Applications</h1>
        <p class="mt-2 text-sm text-gray-400">Track your job applications and their status</p>
      </div>

      <div v-if="loading" class="text-gray-400 text-center py-12">Loading…</div>

      <div v-else>
        <div v-if="applications.length === 0" class="text-center py-12">
          <p class="text-gray-400 mb-4">You haven't applied for any jobs yet.</p>
          <router-link to="/jobs" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition">
            Browse Jobs
          </router-link>
        </div>

        <div v-else class="space-y-4">
          <div v-for="app in applications" :key="app.id" class="bg-gray-900/80 backdrop-blur border border-gray-800 rounded-2xl p-6 hover:border-gray-700 transition">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <h3 class="text-lg font-semibold text-white">{{ app.jobvacancy?.title || 'Unknown Job' }}</h3>
                <p class="text-sm text-gray-400 mt-1">{{ app.jobvacancy?.company?.name || 'Unknown Company' }}</p>
                <p class="text-xs text-gray-500 mt-2">Applied {{ formatDate(app.created_at) }}</p>
              </div>

              <div>
                <div class="mb-3">
                  <span :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                    app.status === 'accepted' ? 'bg-green-100 text-green-800' : 
                    app.status === 'rejected' ? 'bg-red-100 text-red-800' :
                    app.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-blue-100 text-blue-800'
                  ]">
                    {{ ucfirst(app.status) }}
                  </span>
                </div>
                <p class="text-sm text-gray-300"><strong>Resume:</strong> {{ app.resume?.filename || 'N/A' }}</p>
              </div>

              <div class="md:text-right">
                <p class="text-sm text-gray-300 mb-2">
                  <strong>AI Match Score:</strong>
                  <span class="text-lg font-bold text-indigo-400">{{ app.aiGeneratedScore || 0 }}/10</span>
                </p>
                <router-link :to="{ name: 'JobShow', params: { id: app.jobvacancy?.id } }" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition">
                  View Job →
                </router-link>
              </div>
            </div>

            <div v-if="app.aiGeneratedFeedback" class="mt-4 pt-4 border-t border-gray-700">
              <p class="text-sm text-gray-300 italic bg-gray-800/50 p-3 rounded">{{ app.aiGeneratedFeedback }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import api from '@/api/axios'

export default defineComponent({
  name: 'ApplicationsIndex',
  setup() {
    const applications = ref<any[]>([])
    const loading = ref(true)

    async function fetchApplications() {
      loading.value = true
      try {
        const res = await api.get('/applications')
        applications.value = res.data.data?.data || res.data.data || []
      } catch (e) {
        console.error('Failed to fetch applications', e)
      } finally {
        loading.value = false
      }
    }

    function formatDate(date: string) {
      return new Date(date).toLocaleDateString()
    }

    function ucfirst(str: string) {
      return str?.charAt(0).toUpperCase() + str?.slice(1).toLowerCase() || ''
    }

    onMounted(() => fetchApplications())

    return { applications, loading, formatDate, ucfirst }
  }
})
</script>

<style scoped></style>
