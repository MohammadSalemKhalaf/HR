<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Back Button -->
      <router-link to="/employee/tasks" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
        ← Back to Tasks
      </router-link>

      <div v-if="task" class="bg-white rounded-lg shadow p-8">
        <!-- Header -->
        <div class="mb-6 pb-6 border-b">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-gray-900">{{ task.title }}</h1>
              <p class="text-gray-600 mt-2">{{ task.description }}</p>
            </div>
            <div class="text-right">
              <span
                :class="[
                  'inline-block px-4 py-2 rounded-full font-medium',
                  {
                    'bg-red-100 text-red-700': task.priority === 'high',
                    'bg-yellow-100 text-yellow-700': task.priority === 'medium',
                    'bg-green-100 text-green-700': task.priority === 'low'
                  }
                ]"
              >
                {{ task.priority }} Priority
              </span>
            </div>
          </div>

          <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>👤 Manager: {{ task.manager?.user?.name }}</span>
            <span>📅 Due: {{ formatDate(task.due_date) }}</span>
            <span>⏱️ {{ getDaysUntilDue(task.due_date) }}</span>
          </div>
        </div>

        <!-- Status Section -->
        <div class="mb-8 pb-8 border-b">
          <h2 class="text-lg font-bold text-gray-900 mb-4">Status</h2>
          <div class="flex items-center gap-4">
            <span
              :class="[
                'px-4 py-2 rounded-full font-medium',
                {
                  'bg-blue-100 text-blue-700': task.status === 'pending',
                  'bg-purple-100 text-purple-700': task.status === 'in_progress',
                  'bg-green-100 text-green-700': task.status === 'completed'
                }
              ]"
            >
              {{ formatStatus(task.status) }}
            </span>
            <div class="flex gap-2">
              <button
                v-if="task.status !== 'in_progress' && task.status !== 'completed'"
                @click="updateStatus('in_progress')"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition"
                :disabled="updating"
              >
                Start Work
              </button>
              <button
                v-if="task.status !== 'completed'"
                @click="updateStatus('completed')"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition"
                :disabled="updating"
              >
                Mark Complete
              </button>
            </div>
          </div>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h3 class="font-bold text-gray-900 mb-3">Task Details</h3>
            <dl class="space-y-3">
              <div>
                <dt class="text-sm font-medium text-gray-500">Manager</dt>
                <dd class="text-gray-900">{{ task.manager?.user?.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Department</dt>
                <dd class="text-gray-900">{{ task.department?.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                <dd class="text-gray-900 capitalize">{{ task.priority }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="text-gray-900 capitalize">{{ task.status.replace('_', ' ') }}</dd>
              </div>
            </dl>
          </div>

          <div>
            <h3 class="font-bold text-gray-900 mb-3">Timeline</h3>
            <dl class="space-y-3">
              <div>
                <dt class="text-sm font-medium text-gray-500">Assigned Date</dt>
                <dd class="text-gray-900">{{ formatDate(task.created_at) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                <dd class="text-gray-900">{{ formatDate(task.due_date) }}</dd>
              </div>
              <div v-if="task.completed_at">
                <dt class="text-sm font-medium text-gray-500">Completed Date</dt>
                <dd class="text-gray-900">{{ formatDate(task.completed_at) }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Repository Link -->
        <div v-if="task.repository_url" class="mt-8 pt-8 border-t">
          <a
            :href="task.repository_url"
            target="_blank"
            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
          >
            🔗 Open Repository
          </a>
        </div>
      </div>

      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-600">Loading task...</p>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'
import AppLayout from '@/layouts/AppLayout.vue'

const route = useRoute()
const task = ref<any>(null)
const updating = ref(false)

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

const getDaysUntilDue = (dueDate: string) => {
  const due = new Date(dueDate)
  const today = new Date()
  const diff = due.getTime() - today.getTime()
  const days = Math.ceil(diff / (1000 * 60 * 60 * 24))
  if (days < 0) return `${Math.abs(days)} days overdue`
  if (days === 0) return 'Due today'
  return `${days} days remaining`
}

const updateStatus = async (newStatus: string) => {
  updating.value = true
  try {
    await api.put(`/employee/tasks/${route.params.id}`, { status: newStatus })
    task.value.status = newStatus
  } catch (error) {
    console.error('[Task] Error updating status:', error)
  } finally {
    updating.value = false
  }
}

onMounted(async () => {
  try {
    const response = await api.get(`/employee/tasks/${route.params.id}`)
    task.value = response.data.data
  } catch (error) {
    console.error('[Task] Error loading task:', error)
  }
})
</script>
