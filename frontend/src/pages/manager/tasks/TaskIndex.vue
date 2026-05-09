<template>
  <AppLayout>
    <div class="container mx-auto p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Tasks</h2>
        <router-link to="/manager/tasks/create" class="btn btn-primary">Create Task</router-link>
      </div>

      <div class="space-y-4">
        <div v-for="task in tasks" :key="task.id" class="p-4 border rounded-lg flex items-center justify-between">
          <div>
            <div class="font-semibold">{{ task.title }}</div>
            <div class="text-sm text-slate-600">{{ task.employee?.user?.name }} · {{ task.department?.name || '-' }}</div>
          </div>
          <div class="flex items-center gap-4">
            <div :class="priorityClass(task.priority)" class="px-2 py-1 rounded">{{ capital(task.priority) }}</div>
            <div :class="statusClass(task.status)" class="px-2 py-1 rounded">{{ formatStatus(task.status) }}</div>
            <router-link :to="`/manager/tasks/${task.id}`" class="text-sm text-blue-600">View</router-link>
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-between" v-if="pagination">
        <button @click="fetchTasks(pagination.prev)" :disabled="!pagination.prev" class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Prev</button>
        <button @click="fetchTasks(pagination.next)" :disabled="!pagination.next" class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const tasks = ref<any[]>([])
const pagination = ref<any | null>(null)

function capital(val: string) {
  if (!val) return '-'
  return val.charAt(0).toUpperCase() + val.slice(1)
}

function formatStatus(s: string) {
  if (!s) return '-'
  return s.replaceAll('_', ' ').replace(/(^|\s)\S/g, t => t.toUpperCase())
}

function priorityClass(p: string) {
  if (p === 'high') return 'bg-red-200'
  if (p === 'medium') return 'bg-yellow-200'
  return 'bg-green-200'
}

function statusClass(s: string) {
  if (s === 'completed') return 'bg-green-200'
  if (s === 'in_progress') return 'bg-blue-200'
  return 'bg-gray-200'
}

async function fetchTasks(url = '/api/manager/tasks') {
  try {
    const endpoint = url.startsWith('http') ? url : url.replace('/api', '')
    const res = await api.get(endpoint)
    const data = res.data
    tasks.value = data.data || data
    pagination.value = data.meta ? { prev: data.links?.prev, next: data.links?.next } : null
  } catch (err) {
    console.error(err)
  }
}

onMounted(() => fetchTasks())
</script>

<style scoped>
.btn-primary { @apply px-3 py-1 bg-indigo-600 text-white rounded }
</style>
