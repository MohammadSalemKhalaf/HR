<template>
  <AppLayout>
    <div class="container mx-auto p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">{{ task.title || 'Task' }}</h2>
        <div class="flex items-center gap-3">
          <router-link :to="`/manager/tasks/${task.id}/edit`" class="btn">Edit</router-link>
          <form @submit.prevent="destroy">
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </div>

      <div class="p-4 border rounded bg-white">
        <div class="mb-2"><strong>Assigned to:</strong> {{ task.employee?.user?.name || '-' }}</div>
        <div class="mb-2"><strong>Department:</strong> {{ task.department?.name || '-' }}</div>
        <div class="mb-2"><strong>Priority:</strong> {{ capital(task.priority) }}</div>
        <div class="mb-2"><strong>Status:</strong> {{ formatStatus(task.status) }}</div>
        <div class="mb-2"><strong>Due:</strong> {{ dueDate }}</div>
        <div class="mt-4">
          <strong>Description</strong>
          <div class="mt-2">{{ task.description || '-' }}</div>
        </div>
        <div v-if="task.repository_url" class="mt-4">
          <a :href="task.repository_url" target="_blank" rel="noreferrer">Repository</a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()

const task = reactive<any>({ id: route.params.id })

const dueDate = computed(() => {
  if (!task.due_date) return '-'

  const date = new Date(task.due_date)
  if (Number.isNaN(date.getTime())) {
    return String(task.due_date).split('T')[0] || String(task.due_date)
  }

  return date.toISOString().split('T')[0]
})

function capital(val: string) {
  if (!val) return '-'
  return val.charAt(0).toUpperCase() + val.slice(1)
}

function formatStatus(s: string) {
  if (!s) return '-'
  return s.replaceAll('_', ' ').replace(/(^|\s)\S/g, t => t.toUpperCase())
}

async function fetchTask() {
  // Prefer server-injected data if present
  // window.__INITIAL_DATA__ can be populated from Blade during server-side render
  // Fallback to API fetch
  const id = route.params.id
  // @ts-ignore
  const initial = (window as any).__INITIAL_DATA__?.task
  if (initial && String(initial.id) === String(id)) {
    Object.assign(task, initial)
    return
  }

  try {
    const res = await api.get(`/manager/tasks/${id}`)
    const data = res.data
    Object.assign(task, data)
  } catch (err) {
    console.error(err)
  }
}

async function destroy() {
  if (!confirm('Delete this task?')) return
  try {
    await api.delete(`/manager/tasks/${task.id}`)
    router.push({ name: 'ManagerTaskIndex' })
  } catch (err) {
    console.error(err)
  }
}

onMounted(() => {
  fetchTask()
})
</script>

<style scoped>
.btn { @apply px-3 py-1 bg-indigo-600 text-white rounded }
.btn-danger { @apply px-3 py-1 bg-red-600 text-white rounded }
</style>
