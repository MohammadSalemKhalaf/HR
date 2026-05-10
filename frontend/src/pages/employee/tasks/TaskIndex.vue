<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">My Tasks</h1>
          <p class="text-gray-600 mt-1">Tasks assigned to you by your manager</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 flex flex-wrap gap-3">
        <button
          @click="filterStatus = null"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition',
            filterStatus === null
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          All ({{ totalTasks }})
        </button>
        <button
          @click="filterStatus = 'pending'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition',
            filterStatus === 'pending'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          Pending ({{ pendingCount }})
        </button>
        <button
          @click="filterStatus = 'in_progress'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition',
            filterStatus === 'in_progress'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          In Progress ({{ inProgressCount }})
        </button>
        <button
          @click="filterStatus = 'completed'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition',
            filterStatus === 'completed'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          Completed ({{ completedCount }})
        </button>
      </div>

      <!-- Tasks List -->
      <div v-if="filteredTasks.length > 0" class="space-y-4">
        <router-link
          v-for="task in filteredTasks"
          :key="task.id"
          :to="`/employee/tasks/${task.id}`"
          class="block bg-white rounded-lg shadow hover:shadow-md transition p-6 cursor-pointer border-l-4"
          :class="{
            'border-red-500': task.priority === 'high',
            'border-yellow-500': task.priority === 'medium',
            'border-green-500': task.priority === 'low'
          }"
        >
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900">{{ task.title }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ task.description }}</p>
            </div>
            <div class="ml-4 text-right">
              <span
                :class="[
                  'inline-block px-3 py-1 rounded-full text-xs font-medium',
                  {
                    'bg-red-100 text-red-700': task.priority === 'high',
                    'bg-yellow-100 text-yellow-700': task.priority === 'medium',
                    'bg-green-100 text-green-700': task.priority === 'low'
                  }
                ]"
              >
                {{ task.priority }}
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
            <span>📌 {{ task.manager?.user?.name || 'Unknown Manager' }}</span>
            <span>📅 Due: {{ formatDate(task.due_date) }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div v-if="task.repository_url" class="text-blue-600">
              <a :href="task.repository_url" target="_blank" class="text-sm hover:underline">
                🔗 View Repository
              </a>
            </div>
            <span
              :class="[
                'px-3 py-1 rounded-full text-xs font-medium',
                {
                  'bg-blue-100 text-blue-700': task.status === 'pending',
                  'bg-purple-100 text-purple-700': task.status === 'in_progress',
                  'bg-green-100 text-green-700': task.status === 'completed'
                }
              ]"
            >
              {{ formatStatus(task.status) }}
            </span>
          </div>
        </router-link>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <div class="text-gray-400 text-5xl mb-4">📋</div>
        <p class="text-gray-600 text-lg">No tasks found</p>
        <p class="text-gray-500 text-sm mt-2">
          {{
            filterStatus
              ? 'No tasks with this status'
              : 'You have no assigned tasks yet'
          }}
        </p>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'
import AppLayout from '@/layouts/AppLayout.vue'

const tasks = ref<any[]>([])
const filterStatus = ref<string | null>(null)
const loading = ref(false)

const filteredTasks = computed(() => {
  if (!filterStatus.value) return tasks.value
  return tasks.value.filter((t) => t.status === filterStatus.value)
})

const totalTasks = computed(() => tasks.value.length)
const pendingCount = computed(() => tasks.value.filter((t) => t.status === 'pending').length)
const inProgressCount = computed(() => tasks.value.filter((t) => t.status === 'in_progress').length)
const completedCount = computed(() => tasks.value.filter((t) => t.status === 'completed').length)

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatStatus = (status: string) => {
  return status.replace('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

onMounted(async () => {
  loading.value = true
  try {
    // First, try to get tasks assigned to the employee
    const response = await api.get('/employee/tasks')
    if (response.data.data) {
      tasks.value = Array.isArray(response.data.data) ? response.data.data : []
    }
  } catch (error) {
    console.error('[Employee Tasks] Error loading tasks:', error)
  } finally {
    loading.value = false
  }
})
</script>
