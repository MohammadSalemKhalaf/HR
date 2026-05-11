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

    <template v-else-if="isJobSeekerArea">
      <div class="min-h-screen bg-slate-950 text-white">
        <header class="sticky top-0 z-30 border-b border-slate-800/80 bg-slate-950/85 backdrop-blur-xl">
          <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 lg:px-6">
            <div>
              <p class="text-[11px] uppercase tracking-[0.22em] text-cyan-300">Job Portal</p>
              <h1 class="text-xl font-black text-white">Find your next opportunity</h1>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
              <router-link
                to="/jobs"
                class="rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-cyan-400 hover:text-cyan-200"
              >
                Jobs
              </router-link>
              <router-link
                to="/my-applications"
                class="rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-cyan-400 hover:text-cyan-200"
              >
                My Applications
              </router-link>
              <button
                v-if="authStore.isAuthenticated"
                type="button"
                class="rounded-full border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/20"
                @click="authStore.logout()"
              >
                Log Out
              </button>
            </div>
          </div>
        </header>

        <main class="relative mx-auto max-w-7xl px-4 py-6 lg:px-6">
          <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.18),_transparent_58%)]" />
          <slot />
        </main>
      </div>
    </template>

    <template v-else>
      <header class="bg-white shadow">
        <div class="container mx-auto flex items-center justify-between p-4">
          <h1 class="text-lg font-bold">Backoffice</h1>
          <div class="flex items-center gap-3">
            <nav class="space-x-4 text-sm text-gray-600">
              <router-link :to="dashboardRoute">Dashboard</router-link>
            </nav>
            <button
              v-if="isEmployeeArea"
              type="button"
              class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
              @click="authStore.logout()"
            >
              Log Out
            </button>
          </div>
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
const isEmployeeArea = computed(() => route.path.startsWith('/employee'))
const isJobSeekerArea = computed(() => route.path.startsWith('/jobs') || route.path.startsWith('/register') || route.path.startsWith('/my-applications'))

const dashboardRoute = computed(() => {
  if (isEmployeeArea.value) return '/employee'
  if (isManagerArea.value) return '/manager'
  return '/admin'
})

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
