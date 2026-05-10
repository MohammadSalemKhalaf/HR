<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
          <p class="text-gray-600 mt-1">Task assignments, leave approvals, and other updates</p>
        </div>
        <button
          v-if="unreadCount > 0"
          @click="markAllAsRead"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
        >
          Mark All as Read
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 flex flex-wrap gap-3">
        <button
          @click="filterType = null"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition',
            filterType === null
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          All ({{ notifications.length }})
        </button>
        <button
          @click="filterType = 'unread'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition',
            filterType === 'unread'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          Unread ({{ unreadCount }})
        </button>
      </div>

      <!-- Notifications List -->
      <div v-if="filteredNotifications.length > 0" class="space-y-3">
        <div
          v-for="notification in filteredNotifications"
          :key="notification.id"
          @click="markAsRead(notification)"
          :class="[
            'bg-white rounded-lg shadow p-6 cursor-pointer border-l-4 transition',
            notification.read_at
              ? 'border-gray-200 opacity-75 hover:opacity-100'
              : 'border-blue-500 hover:bg-blue-50'
          ]"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">{{ notification.title || 'Notification' }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
              <div class="text-xs text-gray-500 mt-2">
                {{ formatTime(notification.created_at) }}
              </div>
            </div>
            <div v-if="!notification.read_at" class="ml-4 w-2 h-2 rounded-full bg-blue-500 mt-1 flex-shrink-0"></div>
          </div>

          <!-- Action Buttons for Task Notifications -->
          <div v-if="notification.type === 'task_assigned'" class="mt-4 flex gap-2">
            <router-link
              :to="`/employee/tasks/${notification.data?.task_id}`"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition"
            >
              View Task
            </router-link>
          </div>

          <!-- Action Buttons for Leave Notifications -->
          <div v-if="notification.type === 'leave_approved'" class="mt-4">
            <router-link
              to="/employee/leaves"
              class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition"
            >
              View Leave Request
            </router-link>
          </div>
          <div v-if="notification.type === 'leave_rejected'" class="mt-4">
            <router-link
              to="/employee/leaves"
              class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium transition"
            >
              View Leave Request
            </router-link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <div class="text-gray-400 text-5xl mb-4">🔔</div>
        <p class="text-gray-600 text-lg">No notifications</p>
        <p class="text-gray-500 text-sm mt-2">
          {{
            filterType === 'unread'
              ? 'You have no unread notifications'
              : 'You have no notifications yet'
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

const notifications = ref<any[]>([])
const filterType = ref<string | null>(null)

const unreadCount = computed(() => notifications.value.filter((n) => !n.read_at).length)

const filteredNotifications = computed(() => {
  if (filterType.value === 'unread') {
    return notifications.value.filter((n) => !n.read_at)
  }
  return notifications.value
})

const formatTime = (date: string) => {
  const now = new Date()
  const notificationDate = new Date(date)
  const diff = now.getTime() - notificationDate.getTime()
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (seconds < 60) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return notificationDate.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const markAsRead = async (notification: any) => {
  if (notification.read_at) return
  try {
    notification.read_at = new Date().toISOString()
  } catch (error) {
    console.error('[Notifications] Error marking as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    const unreadIds = notifications.value.filter((n) => !n.read_at).map((n) => n.id)
    for (const id of unreadIds) {
      notifications.value.find((n) => n.id === id)!.read_at = new Date().toISOString()
    }
  } catch (error) {
    console.error('[Notifications] Error marking all as read:', error)
  }
}

onMounted(async () => {
  try {
    // Fetch notifications from database
    const response = await api.get('/notifications')
    if (response.data.data) {
      notifications.value = Array.isArray(response.data.data) ? response.data.data : []
    }
  } catch (error) {
    console.error('[Notifications] Error loading notifications:', error)
  }
})
</script>
