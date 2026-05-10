<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Welcome, {{ auth.user?.name }}</h1>
        <p class="opacity-90">{{ employeeInfo.job_title }} • {{ employeeInfo.department_name }}</p>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Pending Tasks -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Pending Tasks</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.pendingTasks }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Leave Balance -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Leave Balance</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.leaveBalance }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Pending Leaves -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Pending Leaves</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.pendingLeaves }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Notifications</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.unreadNotifications }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <router-link
            to="/employee/tasks"
            class="px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 font-medium transition text-center"
          >
            📋 My Tasks
          </router-link>
          <router-link
            to="/employee/attendance"
            class="px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg text-green-700 font-medium transition text-center"
          >
            ⏰ Attendance
          </router-link>
          <router-link
            to="/employee/leaves/apply"
            class="px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg text-yellow-700 font-medium transition text-center"
          >
            🗓️ Apply Leave
          </router-link>
          <router-link
            to="/employee/profile"
            class="px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg text-purple-700 font-medium transition text-center"
          >
            👤 My Profile
          </router-link>
        </div>
      </div>

      <!-- Recent Tasks -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-900">Recent Tasks</h2>
          <router-link to="/employee/tasks" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            View All →
          </router-link>
        </div>
        <div v-if="recentTasks.length > 0" class="space-y-3">
          <div
            v-for="task in recentTasks.slice(0, 3)"
            :key="task.id"
            class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition"
          >
            <div class="flex-1">
              <h3 class="font-medium text-gray-900">{{ task.title }}</h3>
              <p class="text-sm text-gray-600 mt-1">Manager: {{ task.manager?.user?.name }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium',
                  {
                    'bg-red-100 text-red-700': task.priority === 'high',
                    'bg-yellow-100 text-yellow-700': task.priority === 'medium',
                    'bg-green-100 text-green-700': task.priority === 'low'
                  }
                ]"
              >
                {{ task.priority }}
              </span>
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
                {{ task.status }}
              </span>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          <p>No pending tasks</p>
        </div>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'
import AppLayout from '@/layouts/AppLayout.vue'

const auth = useAuthStore()

const employeeInfo = ref({
  job_title: 'Loading...',
  department_name: 'Loading...'
})

const stats = ref({
  pendingTasks: 0,
  leaveBalance: 0,
  pendingLeaves: 0,
  unreadNotifications: 0
})

const recentTasks = ref<any[]>([])

onMounted(async () => {
  try {
    // Load employee info from auth store
    if (auth.user) {
      const employee = (auth as any).employee
      if (employee) {
        employeeInfo.value.job_title = employee.job_title || 'Employee'
        employeeInfo.value.department_name = employee.department?.name || 'Department'
      }
    }

    // Load tasks
    const tasksRes = await api.get('/employee/tasks')
    if (tasksRes.data.data) {
      const allTasks = Array.isArray(tasksRes.data.data) ? tasksRes.data.data : []
      recentTasks.value = allTasks
      stats.value.pendingTasks = allTasks.filter((t: any) => t.status !== 'completed').length
    }
  } catch (error) {
    console.error('[Employee Dashboard] Error loading data:', error)
  }
})
</script>
