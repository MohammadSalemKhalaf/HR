<template>
  <div class="min-h-screen bg-slate-100">
    <template v-if="isManagerArea">
      <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 flex-col border-r border-slate-200 bg-white lg:flex">
        <div class="border-b border-slate-200 px-6 py-6">
          <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Backoffice</p>
          <h1 class="mt-2 text-2xl font-black text-slate-900">Manager Panel</h1>
          <p class="mt-1 text-sm text-slate-500">{{ authStore.user?.name || 'Manager' }}</p>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
          <router-link
            v-for="item in managerNav"
            :key="item.to"
            :to="item.to"
            class="block rounded-xl px-4 py-3 text-sm font-semibold transition"
            :class="isActive(item.to)
              ? 'bg-cyan-600 text-white shadow'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            {{ item.label }}
          </router-link>
        </nav>

        <div class="border-t border-slate-200 p-4">
          <button
            type="button"
            class="w-full rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
            @click="authStore.logout()"
          >
            Log Out
          </button>
        </div>
      </aside>

      <header class="sticky top-0 z-30 border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Backoffice</p>
            <p class="text-lg font-bold text-slate-900">Manager Panel</p>
          </div>
          <button
            type="button"
            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700"
            @click="authStore.logout()"
          >
            Log Out
          </button>
        </div>
        <nav class="mt-3 flex gap-2 overflow-x-auto pb-1">
          <router-link
            v-for="item in managerNav"
            :key="`mobile-${item.to}`"
            :to="item.to"
            class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold transition"
            :class="isActive(item.to)
              ? 'bg-cyan-600 text-white'
              : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
          >
            {{ item.label }}
          </router-link>
        </nav>
      </header>

      <main class="p-4 lg:ml-72 lg:p-8">
        <slot />
      </main>
    </template>

    <template v-else>
      <header class="bg-white shadow">
        <div class="container mx-auto flex items-center justify-between p-4">
          <h1 class="text-lg font-bold">Backoffice</h1>
          <nav class="space-x-4 text-sm text-gray-600">
            <router-link to="/admin">Dashboard</router-link>
          </nav>
        </div>
      </header>
      <main class="container mx-auto p-6">
        <slot />
      </main>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()

const isManagerArea = computed(() => route.path.startsWith('/manager'))

const managerNav = [
  { to: '/manager', label: 'Dashboard' },
  { to: '/manager/tasks', label: 'Tasks' },
  { to: '/manager/employees', label: 'Employees' },
  { to: '/manager/departments', label: 'Departments' },
  { to: '/manager/leaves', label: 'Leaves' },
  { to: '/manager/attendance', label: 'Attendance' },
  { to: '/manager/department-notifications', label: 'Notifications' }
]

function isActive(path: string) {
  if (path === '/manager') return route.path === path
  return route.path.startsWith(path)
}
</script>
