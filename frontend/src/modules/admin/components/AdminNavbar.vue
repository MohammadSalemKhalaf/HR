<template>
  <header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <h2 class="text-lg font-semibold text-indigo-600">EMS Admin</h2>
      </div>
      <div class="flex items-center gap-3">
        <button @click="onLogout" class="text-sm text-gray-600 hover:text-gray-800">Logout</button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
function onLogout() {
  auth.logout()
}
</script>
