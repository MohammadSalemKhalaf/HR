<template>
  <div class="space-y-6">
    <PageHeader title="Departments" subtitle="Organize your departments and teams">
      <template #actions>
        <button @click="openCreate" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
          + New Department
        </button>
      </template>
    </PageHeader>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow p-8">
      <div class="flex items-center justify-center space-x-4">
        <LoadingSpinner />
        <span class="text-gray-600">Loading departments...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
      <p class="font-semibold">Error loading departments</p>
      <p class="text-sm mt-1">{{ error }}</p>
      <button @click="load" class="mt-3 px-3 py-1 bg-red-600 text-white rounded text-sm">Retry</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="departments.length === 0" class="bg-white rounded-lg shadow">
      <EmptyState
        title="No departments yet"
        description="Create your first department to get started"
        @action="openCreate"
      />
    </div>

    <!-- Departments Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="dept in departments" 
        :key="dept.id"
        class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 cursor-pointer"
        @click="editDepartment(dept)"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ dept.name }}</h3>
            <p class="text-sm text-gray-500">{{ dept.company_name }}</p>
          </div>
          <div class="text-2xl">📁</div>
        </div>
        <div class="text-sm text-gray-600">
          <p><span class="font-medium">Manager:</span> {{ dept.manager_name || 'Not assigned' }}</p>
          <p><span class="font-medium">Employees:</span> {{ dept.employees_count || 0 }}</p>
        </div>
        <div class="mt-4 flex gap-2">
          <button @click.stop="editDepartment(dept)" class="flex-1 px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">Edit</button>
          <button @click.stop="deleteDepartment(dept)" class="flex-1 px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
        </div>
      </div>
    </div>

    <!-- Department Modal (to be implemented) -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-xl font-bold mb-4">{{ editing ? 'Edit Department' : 'New Department' }}</h2>
        <p class="text-gray-600 text-sm">Department form coming soon...</p>
        <div class="mt-6 flex gap-2 justify-end">
          <button @click="closeModal" class="px-4 py-2 text-gray-700 border rounded hover:bg-gray-50">Cancel</button>
          <button @click="closeModal" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BaseTable from '@/components/base/BaseTable.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import LoadingSpinner from '@/components/base/LoadingSpinner.vue'
import EmptyState from '@/components/base/EmptyState.vue'
import api from '@/api/axios'

const departments = ref([] as any[])
const showModal = ref(false)
const editing = ref(null as any)
const loading = ref(false)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/departments')
    departments.value = data.data || data
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Failed to load departments'
    console.error('[Departments] Load error:', e)
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editing.value = null
  showModal.value = true
}

function editDepartment(dept: any) {
  editing.value = dept
  showModal.value = true
}

async function deleteDepartment(dept: any) {
  if (confirm(`Are you sure you want to delete "${dept.name}"?`)) {
    try {
      await api.delete(`/departments/${dept.id}`)
      await load()
    } catch (e: any) {
      alert('Failed to delete department: ' + (e.response?.data?.message || e.message))
    }
  }
}

function closeModal() {
  showModal.value = false
}

onMounted(load)
</script>
