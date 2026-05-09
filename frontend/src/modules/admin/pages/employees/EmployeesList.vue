<template>
  <div class="space-y-6">
    <PageHeader title="Employees" subtitle="Manage all employees in the system">
      <template #actions>
        <button @click="openCreate" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
          + New Employee
        </button>
      </template>
    </PageHeader>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 flex gap-4">
      <input 
        v-model="searchQuery"
        type="text"
        placeholder="Search by name or email..."
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
      />
      <select 
        v-model="filterDept"
        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
      >
        <option value="">All Departments</option>
        <option value="it">IT</option>
        <option value="hr">HR</option>
        <option value="sales">Sales</option>
      </select>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow p-8">
      <div class="flex items-center justify-center space-x-4">
        <LoadingSpinner />
        <span class="text-gray-600">Loading employees...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
      <p class="font-semibold">Error loading employees</p>
      <p class="text-sm mt-1">{{ error }}</p>
      <button @click="load" class="mt-3 px-3 py-1 bg-red-600 text-white rounded text-sm">Retry</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredEmployees.length === 0" class="bg-white rounded-lg shadow">
      <EmptyState
        title="No employees found"
        :description="searchQuery ? 'Try adjusting your search criteria' : 'Create your first employee to get started'"
        @action="openCreate"
      />
    </div>

    <!-- Employees Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Department</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="emp in filteredEmployees" :key="emp.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-semibold">
                    {{ emp.name?.charAt(0).toUpperCase() }}
                  </div>
                  <span class="font-medium text-gray-900">{{ emp.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ emp.email }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ emp.department_name || '—' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ emp.position || '—' }}</td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 text-xs font-semibold rounded-full" :class="emp.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                  {{ emp.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <button @click="editEmployee(emp)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                  <button @click="deleteEmployee(emp)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Summary -->
    <div v-if="employees.length > 0" class="bg-blue-50 rounded-lg p-4 text-sm text-blue-800">
      <p>Showing <span class="font-semibold">{{ filteredEmployees.length }}</span> of <span class="font-semibold">{{ employees.length }}</span> employees</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import PageHeader from '@/components/base/PageHeader.vue'
import LoadingSpinner from '@/components/base/LoadingSpinner.vue'
import EmptyState from '@/components/base/EmptyState.vue'
import api from '@/api/axios'

const employees = ref([] as any[])
const loading = ref(false)
const error = ref('')
const searchQuery = ref('')
const filterDept = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/employees')
    employees.value = data.data || data
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Failed to load employees'
    console.error('[Employees] Load error:', e)
  } finally {
    loading.value = false
  }
}

const filteredEmployees = computed(() => {
  return employees.value.filter(emp => {
    const matchesSearch = !searchQuery.value || 
      emp.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      emp.email?.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    const matchesDept = !filterDept.value || emp.department_id?.toString() === filterDept.value
    
    return matchesSearch && matchesDept
  })
})

function openCreate() {
  // implement modal later
}

function editEmployee(emp: any) {
  // implement later
}

async function deleteEmployee(emp: any) {
  if (confirm(`Are you sure you want to delete ${emp.name}?`)) {
    try {
      await api.delete(`/employees/${emp.id}`)
      await load()
    } catch (e: any) {
      alert('Failed to delete employee: ' + (e.response?.data?.message || e.message))
    }
  }
}

onMounted(load)
</script>
