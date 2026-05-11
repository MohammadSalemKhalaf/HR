<template>
  <AppLayout>
    <div class="space-y-6 p-6">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-3">
          <span class="text-sm font-semibold text-blue-400">Job Portal</span>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Explore Opportunities</h1>
        <p class="text-slate-400">Discover your next career move from {{ totalJobs }} available positions</p>
      </div>

      <!-- Search & Filters Section -->
      <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 space-y-4">
        <!-- Search Bar -->
        <div class="flex gap-3 flex-wrap">
          <div class="flex-1 min-w-64">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search by title or location..."
              @input="loadJobs"
              class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <button
            v-if="hasFilters"
            @click="clearFilters"
            class="px-4 py-2.5 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-slate-300 font-medium transition"
          >
            Clear Filters
          </button>
        </div>

        <!-- Filter Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <!-- Type Filter -->
          <select
            v-model="filters.type"
            @change="loadJobs"
            class="px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Types</option>
            <option value="full-time">Full-time</option>
            <option value="contract">Contract</option>
            <option value="hybrid">Hybrid</option>
            <option value="remote">Remote</option>
          </select>

          <!-- Category Filter -->
          <select
            v-model="filters.category"
            @change="loadJobs"
            class="px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>

          <!-- Salary Range Filter -->
          <select
            v-model="filters.salaryRange"
            @change="loadJobs"
            class="px-4 py-2.5 bg-slate-700/50 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Salaries</option>
            <option value="0-50000">$0 - $50,000</option>
            <option value="50000-100000">$50,000 - $100,000</option>
            <option value="100000-150000">$100,000 - $150,000</option>
            <option value="150000+">$150,000+</option>
          </select>
        </div>
      </div>

      <!-- Results Stats -->
      <div class="flex items-center justify-between">
        <p class="text-slate-300">
          Found <span class="font-bold text-blue-400">{{ filteredJobs.length }}</span> job{{ filteredJobs.length !== 1 ? 's' : '' }}
        </p>
        <p class="text-sm text-slate-500">
          <span v-if="currentPage > 1">Page {{ currentPage }} of {{ totalPages }}</span>
        </p>
      </div>

      <!-- Jobs Grid -->
      <div class="space-y-3">
        <div
          v-for="job in filteredJobs"
          :key="job.id"
          @click="goToJobDetail(job.id)"
          class="group bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 hover:border-blue-500/50 rounded-xl p-6 transition-all duration-300 cursor-pointer hover:shadow-lg hover:shadow-blue-500/10"
        >
          <div class="flex items-start justify-between gap-4">
            <!-- Job Info -->
            <div class="flex-1">
              <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition">
                {{ job.title }}
              </h3>
              <p class="text-sm text-slate-400 mt-1">{{ job.company?.name }}</p>

              <!-- Details Row -->
              <div class="flex flex-wrap items-center gap-3 mt-3 text-sm">
                <span class="flex items-center gap-1 text-slate-400">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                  {{ job.location }}
                </span>
                <span class="text-slate-600">•</span>
                <span v-if="job.type" class="px-2.5 py-1 bg-slate-700/50 rounded text-slate-300 font-medium text-xs">
                  {{ formatType(job.type) }}
                </span>
                <span v-if="job.jobcategory" class="px-2.5 py-1 bg-indigo-500/20 rounded text-indigo-300 font-medium text-xs">
                  {{ job.jobcategory.name }}
                </span>
              </div>
            </div>

            <!-- Salary & CTA -->
            <div class="text-right">
              <p class="text-lg font-bold text-green-400">
                ${{ formatSalary(job.salary) }}
              </p>
              <p class="text-xs text-slate-500 mt-1">/year</p>
              <button
                @click.stop="goToJobDetail(job.id)"
                class="mt-3 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white text-sm font-semibold rounded-lg transition"
              >
                View Job
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredJobs.length === 0 && !loading" class="text-center py-12">
          <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <h3 class="text-lg font-semibold text-slate-300 mb-1">No jobs found</h3>
          <p class="text-slate-500">Try adjusting your filters or search terms</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0c4.418 0 8 3.582 8 8h-8z" />
          </svg>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-between">
        <button
          :disabled="currentPage === 1 || loading"
          @click="previousPage"
          class="px-4 py-2 bg-slate-700/50 hover:bg-slate-700 disabled:opacity-50 border border-slate-600 rounded-lg text-slate-300 transition"
        >
          ← Previous
        </button>
        <span class="text-slate-400">Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          :disabled="currentPage >= totalPages || loading"
          @click="nextPage"
          class="px-4 py-2 bg-slate-700/50 hover:bg-slate-700 disabled:opacity-50 border border-slate-600 rounded-lg text-slate-300 transition"
        >
          Next →
        </button>
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

const jobs = ref<any[]>([])
const categories = ref<any[]>([])
const loading = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const totalJobs = ref(0)

const filters = ref({
  search: '',
  type: '',
  category: '',
  salaryRange: '',
})

const hasFilters = computed(() => {
  return filters.value.search || filters.value.type || filters.value.category || filters.value.salaryRange
})

const filteredJobs = computed(() => {
  return jobs.value.filter(job => {
    if (filters.value.salaryRange) {
      const salary = parseSalary(job.salary)
      const [min, max] = filters.value.salaryRange.split('-')
      const maxVal = max === '+' ? Infinity : parseInt(max)
      if (salary < parseInt(min) || salary > maxVal) return false
    }
    return true
  })
})

const parseSalary = (salaryStr: string): number => {
  const cleaned = salaryStr.replace(/[^\d]/g, '')
  return parseInt(cleaned) || 0
}

const formatSalary = (salary: string): string => {
  const num = parseSalary(salary)
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(0) + 'K'
  return num.toString()
}

const formatType = (type: string): string => {
  const map: Record<string, string> = {
    'full-time': 'Full-time',
    'contract': 'Contract',
    'hybrid': 'Hybrid',
    'remote': 'Remote',
  }
  return map[type] || type
}

const extractList = (payload: any): any[] => {
  if (Array.isArray(payload)) return payload
  if (Array.isArray(payload?.data)) return payload.data
  if (Array.isArray(payload?.data?.data)) return payload.data.data
  return []
}

const loadJobs = async () => {
  loading.value = true
  try {
    const params: any = {
      page: currentPage.value,
      archived: false,
    }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.type) params.type = filters.value.type
    if (filters.value.category) params.category = filters.value.category

    const response = await api.get('/vacancies', { params })
    const data = response.data?.data || {}
    jobs.value = extractList(data)
    totalPages.value = data.last_page || 1
    currentPage.value = data.current_page || 1
    totalJobs.value = data.total || jobs.value.length
  } catch (error) {
    console.error('Error loading jobs:', error)
    jobs.value = []
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await api.get('/job-categories')
    categories.value = extractList(response.data?.data)
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

const goToJobDetail = (jobId: string) => {
  router.push(`/jobs/${jobId}`)
}

const clearFilters = () => {
  filters.value = { search: '', type: '', category: '', salaryRange: '' }
  currentPage.value = 1
  loadJobs()
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    loadJobs()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    loadJobs()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

onMounted(() => {
  loadCategories()
  loadJobs()
})
</script>
