<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white border-b px-6 py-4 flex items-center justify-between">
      <h1 class="text-lg font-bold text-slate-900">Company Portal</h1>
      <button
        @click="logout"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
      >
        Logout
      </button>
    </header>

    <div class="flex">
      <CompanySidebar />
      <main class="flex-1 p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import CompanySidebar from './CompanySidebar.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const logout = async () => {
  await auth.logout()
}
</script>
