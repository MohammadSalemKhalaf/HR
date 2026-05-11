<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-3">
          <span class="text-sm font-semibold text-blue-400">Applications</span>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">My Applications</h1>
        <p class="text-slate-400">Track the status of your job applications</p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-xl p-4">
          <p class="text-slate-400 text-sm">Total Applications</p>
          <p class="text-2xl font-bold text-white mt-1">{{ applications.length }}</p>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-xl p-4">
          <p class="text-slate-400 text-sm">Pending</p>
          <p class="text-2xl font-bold text-yellow-400 mt-1">{{ stats.pending }}</p>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-xl p-4">
          <p class="text-slate-400 text-sm">Accepted</p>
          <p class="text-2xl font-bold text-green-400 mt-1">{{ stats.accepted }}</p>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-xl p-4">
          <p class="text-slate-400 text-sm">Rejected</p>
          <p class="text-2xl font-bold text-red-400 mt-1">{{ stats.rejected }}</p>
        </div>
      </div>

      <!-- Applications List -->
      <div class="space-y-4">
        <div
          v-for="app in applications"
          :key="app.id"
          class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-xl p-6 hover:border-blue-500/50 transition"
        >
          <!-- Header Row -->
          <div class="flex items-start justify-between gap-4 mb-4">
            <div>
              <h3 class="text-lg font-bold text-white">{{ app.jobvacancy?.title }}</h3>
              <p class="text-sm text-slate-400 mt-1">{{ app.jobvacancy?.company?.name }}</p>
            </div>

            <!-- Status Badge -->
            <span
              :class="[
                'px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap',
                {
                  'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30': app.status === 'pending',
                  'bg-green-500/20 text-green-300 border border-green-500/30': app.status === 'accepted',
                  'bg-red-500/20 text-red-300 border border-red-500/30': app.status === 'rejected',
                }
              ]"
            >
              {{ formatStatus(app.status) }}
            </span>
          </div>

          <!-- Details Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-slate-700">
            <div>
              <p class="text-xs text-slate-500 uppercase tracking-wide">Location</p>
              <p class="text-white font-medium mt-1">{{ app.jobvacancy?.location }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500 uppercase tracking-wide">Salary</p>
              <p class="text-green-400 font-medium mt-1">${{ formatSalary(app.jobvacancy?.salary) }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500 uppercase tracking-wide">Applied Date</p>
              <p class="text-white font-medium mt-1">{{ formatDate(app.created_at) }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-500 uppercase tracking-wide">AI Match Score</p>
              <p class="text-blue-400 font-medium mt-1">{{ app.aiGeneratedScore || 'N/A' }}/10</p>
            </div>
          </div>

          <!-- AI Feedback & Actions -->
          <div class="space-y-3">
            <div v-if="app.aiGeneratedFeedback" class="p-3 bg-indigo-500/10 border border-indigo-500/30 rounded-lg">
              <p class="text-xs text-indigo-300 uppercase tracking-wide mb-1">AI Feedback</p>
              <p class="text-sm text-indigo-100">{{ app.aiGeneratedFeedback }}</p>
            </div>

            <div class="flex gap-2">
              <button
                @click="viewJobDetails(app.jobvacancy?.id)"
                class="flex-1 px-3 py-2 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-300 text-sm font-medium transition"
              >
                View Job
              </button>
              <button
                v-if="app.status === 'accepted'"
                class="flex-1 px-3 py-2 bg-green-500/20 border border-green-500/30 rounded-lg text-green-300 text-sm font-medium"
              >
                ✓ Accepted
              </button>
              <button
                v-else-if="app.status === 'rejected'"
                class="flex-1 px-3 py-2 bg-red-500/20 border border-red-500/30 rounded-lg text-red-300 text-sm font-medium"
              >
                ✗ Rejected
              </button>
              <button
                v-else
                class="flex-1 px-3 py-2 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-300 text-sm font-medium transition cursor-default"
              >
                Pending Review
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="applications.length === 0 && !loading" class="text-center py-12">
          <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="text-lg font-semibold text-slate-300 mb-2">No applications yet</h3>
          <p class="text-slate-500 mb-4">Start applying for jobs to see them here</p>
          <router-link
            to="/jobs"
            class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-lg transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Browse Jobs
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0c4.418 0 8 3.582 8 8h-8z" />
          </svg>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const router = useRouter()

const applications = ref<any[]>([])
const loading = ref(false)

const stats = computed(() => ({
  pending: applications.value.filter(a => a.status === 'pending').length,
  accepted: applications.value.filter(a => a.status === 'accepted').length,
  rejected: applications.value.filter(a => a.status === 'rejected').length,
}))

const formatStatus = (status: string): string => {
  const map: Record<string, string> = {
    pending: 'Pending Review',
    accepted: 'Accepted',
    rejected: 'Rejected',
  }
  return map[status] || status
}

const formatSalary = (salary: string): string => {
  const num = parseInt(salary.replace(/[^\d]/g, '')) || 0
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(0) + 'K'
  return num.toString()
}

const formatDate = (date: string): string => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const loadApplications = async () => {
  loading.value = true
  try {
    const response = await api.get('/applications')
    const data = response.data?.data
    applications.value = Array.isArray(data) ? data : data?.data || []
  } catch (error) {
    console.error('Error loading applications:', error)
    applications.value = []
  } finally {
    loading.value = false
  }
}

const viewJobDetails = (jobId: string) => {
  router.push(`/jobs/${jobId}`)
}

onMounted(() => {
  loadApplications()
})
</script>
