<template>
  <AppLayout>
    <div class="container mx-auto p-6">
      <h2 class="text-xl font-semibold mb-4">Edit Task</h2>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block mb-1">Title</label>
          <input v-model="form.title" class="w-full border p-2 rounded" required />
        </div>

        <div class="mb-3">
          <label class="block mb-1">Assign to</label>
          <select v-model="form.employee_id" class="w-full border p-2 rounded" required>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.user?.name }} ({{ employee.department?.name }})</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="block mb-1">Priority</label>
          <select v-model="form.priority" class="w-full border p-2 rounded">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="block mb-1">Status</label>
          <select v-model="form.status" class="w-full border p-2 rounded">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="block mb-1">Repository URL</label>
          <input v-model="form.repository_url" class="w-full border p-2 rounded" />
        </div>

        <div class="mb-3">
          <label class="block mb-1">Due Date</label>
          <input type="date" v-model="form.due_date" class="w-full border p-2 rounded" />
        </div>

        <div class="mb-3">
          <label class="block mb-1">Description</label>
          <textarea v-model="form.description" class="w-full border p-2 rounded"></textarea>
        </div>

        <button class="btn btn-primary">Save</button>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()
const employees = ref<any[]>([])
const form = ref<any>({ title: '', employee_id: '', priority: 'medium', status: 'pending', repository_url: '', due_date: '', description: '' })

async function fetchEmployees() {
  try {
    const res = await api.get('/manager/tasks/employees')
    employees.value = res.data.data || res.data
  } catch (err) { console.error(err) }
}

async function fetchTask() {
  try {
    const id = route.params.id
    const res = await api.get(`/manager/tasks/${id}`)
    const data = res.data
    Object.assign(form.value, data)
  } catch (err) { console.error(err) }
}

async function submit() {
  try {
    const id = route.params.id
    await api.put(`/manager/tasks/${id}`, form.value)
    router.push({ name: 'ManagerTaskShow', params: { id } })
  } catch (err) { console.error(err) }
}

onMounted(() => { fetchEmployees(); fetchTask() })
</script>

<style scoped>
.btn-primary { @apply px-3 py-1 bg-indigo-600 text-white rounded }
</style>
