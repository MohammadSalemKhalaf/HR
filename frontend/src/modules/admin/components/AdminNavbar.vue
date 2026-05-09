<template>
  <header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <h2 class="text-lg font-semibold text-indigo-600">EMS Admin</h2>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">{{ auth.user?.name }}</span>
        <button
          @click="handleLogout"
          :disabled="auth.loading"
          class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded transition disabled:opacity-50"
        >
          {{ auth.loading ? 'Logging out...' : 'Logout' }}
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

async function handleLogout() {
  await auth.logout()
}
</script>

