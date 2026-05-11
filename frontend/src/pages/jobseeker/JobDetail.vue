<template>
  <AppLayout>
    <div class="p-6">
      <!-- Back Button -->
      <router-link
        to="/jobs"
        class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 mb-6 transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Jobs
      </router-link>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0c4.418 0 8 3.582 8 8h-8z" />
        </svg>
      </div>

      <div v-else-if="job" class="space-y-6">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-8 text-white">
          <div class="mb-6">
            <h1 class="text-4xl font-bold mb-2">{{ job.title }}</h1>
            <p class="text-lg opacity-90">{{ job.company?.name }}</p>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/10 backdrop-blur rounded-lg p-4">
              <p class="text-sm opacity-80">Location</p>
              <p class="font-semibold">{{ job.location }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4">
              <p class="text-sm opacity-80">Type</p>
              <p class="font-semibold">{{ formatType(job.type) }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4">
              <p class="text-sm opacity-80">Salary</p>
              <p class="font-semibold text-green-300">${{ formatSalary(job.salary) }}/year</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4">
              <p class="text-sm opacity-80">Category</p>
              <p class="font-semibold">{{ job.jobcategory?.name || 'General' }}</p>
            </div>
          </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4.5 3a2.5 2.5 0 015 0v.006h3.992v-.006a2.5 2.5 0 015 0v2a2.5 2.5 0 01-2.5 2.5H7a2.5 2.5 0 01-2.5-2.5v-2zm0 5a2.5 2.5 0 015 0v.006h3.992v-.006a2.5 2.5 0 015 0v2a2.5 2.5 0 01-2.5 2.5H7a2.5 2.5 0 01-2.5-2.5v-2zm0 5a2.5 2.5 0 015 0v.006h3.992v-.006a2.5 2.5 0 015 0v2a2.5 2.5 0 01-2.5 2.5H7a2.5 2.5 0 01-2.5-2.5v-2z" />
                </svg>
                Job Description
              </h2>
              <div class="text-slate-300 leading-relaxed whitespace-pre-wrap">
                {{ job.description }}
              </div>
            </div>

            <!-- Requirements -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.002v2.947c0 1.993-1.679 3.615-3.712 3.92-1.231.228-2.367.455-3.312.455-1.945 0-3.03-1.114-3.743-2.251C2.874 10.487 2 9.306 2 8.404v-2.947a3.066 3.066 0 012.267-3.002z" clip-rule="evenodd" />
                </svg>
                Requirements
              </h2>
              <div class="text-slate-300 leading-relaxed whitespace-pre-wrap">
                {{ job.requirements || 'No specific requirements listed' }}
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Info Card -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
              <h3 class="text-lg font-bold text-white mb-4">Job Details</h3>
              <div class="space-y-3 text-sm">
                <div>
                  <p class="text-slate-400">Position</p>
                  <p class="text-white font-medium">{{ job.title }}</p>
                </div>
                <div>
                  <p class="text-slate-400">Company</p>
                  <p class="text-white font-medium">{{ job.company?.name }}</p>
                </div>
                <div>
                  <p class="text-slate-400">Location</p>
                  <p class="text-white font-medium">{{ job.location }}</p>
                </div>
                <div>
                  <p class="text-slate-400">Employment Type</p>
                  <p class="text-white font-medium">{{ formatType(job.type) }}</p>
                </div>
              </div>
            </div>

            <!-- Apply Button -->
            <button
              v-if="!hasApplied"
              @click="goToApply"
              class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold rounded-lg transition duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
              Apply Now
            </button>

            <div v-else class="w-full py-3 bg-slate-700/50 border border-green-500/50 text-green-300 font-semibold rounded-lg text-center flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Already Applied
            </div>

            <!-- Similar Jobs -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
              <h3 class="text-lg font-bold text-white mb-4">Quick Links</h3>
              <div class="space-y-2">
                <router-link
                  to="/my-applications"
                  class="block p-2 rounded-lg hover:bg-slate-700/50 text-blue-400 hover:text-blue-300 transition text-sm"
                >
                  View my applications
                </router-link>
                <router-link
                  to="/jobs"
                  class="block p-2 rounded-lg hover:bg-slate-700/50 text-blue-400 hover:text-blue-300 transition text-sm"
                >
                  Browse more jobs
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Not Found -->
      <div v-else class="text-center py-12">
        <p class="text-slate-400">Job not found</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const router = useRouter()
const route = useRoute()

const job = ref<any>(null)
const loading = ref(false)
const hasApplied = ref(false)

const formatType = (type: string): string => {
  const map: Record<string, string> = {
    'full-time': 'Full-time',
    'contract': 'Contract',
    'hybrid': 'Hybrid',
    'remote': 'Remote',
  }
  return map[type] || type
}

const formatSalary = (salary: string): string => {
  const num = parseInt(salary.replace(/[^\d]/g, '')) || 0
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(0) + 'K'
  return num.toString()
}

const loadJob = async () => {
  loading.value = true
  try {
    const response = await api.get(`/job-vacancies/${route.params.id}`)
    job.value = response.data?.data || null

    // Check if already applied
    if (job.value) {
      checkIfApplied()
    }
  } catch (error) {
    console.error('Error loading job:', error)
    job.value = null
  } finally {
    loading.value = false
  }
}

const checkIfApplied = async () => {
  try {
    const response = await api.get('/applications')
    const applications = response.data?.data?.data || []
    hasApplied.value = applications.some((app: any) => app.jobVacancyId === job.value.id)
  } catch (error) {
    console.error('Error checking applications:', error)
  }
}

const goToApply = () => {
  router.push(`/jobs/${route.params.id}/apply`)
}

onMounted(() => {
  loadJob()
})
</script>
