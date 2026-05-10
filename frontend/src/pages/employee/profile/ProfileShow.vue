<template>
  <AppLayout>
    <div class="space-y-6 max-w-5xl">
      <div class="overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 text-white shadow-xl">
        <div class="flex flex-col gap-6 p-8 md:flex-row md:items-center md:justify-between">
          <div class="space-y-4">
            <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100">
              Employee Profile
            </div>
            <div>
              <h1 class="text-3xl font-bold md:text-4xl">My Profile</h1>
              <p class="mt-2 max-w-2xl text-sm text-slate-200">
                Your employment details, company info, and account summary in one clean view.
              </p>
            </div>
          </div>

          <div v-if="profile" class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
            <div class="h-16 w-16 rounded-2xl bg-white/15 flex items-center justify-center text-xl font-bold text-white ring-1 ring-white/10">
              {{ initials }}
            </div>
            <div>
              <h2 class="text-lg font-semibold">{{ profile.user?.name || profile.name || 'Employee' }}</h2>
              <p class="text-sm text-slate-200">{{ profile.user?.email || '' }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="profile" class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
          <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
              <h2 class="text-lg font-bold text-slate-900">Personal Information</h2>
              <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">Identity</span>
            </div>
            <dl class="grid gap-4 sm:grid-cols-2">
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Full Name</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.user?.name }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900 break-all">{{ profile.user?.email }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Employee ID</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.employee_number }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</dt>
                <dd class="mt-2">
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                      profile.status === 'active'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-rose-100 text-rose-700'
                    ]"
                  >
                    {{ profile.status }}
                  </span>
                </dd>
              </div>
            </dl>
          </section>

          <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
              <h2 class="text-lg font-bold text-slate-900">Employment Information</h2>
              <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-medium text-cyan-700">Work</span>
            </div>
            <dl class="grid gap-4 sm:grid-cols-2">
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Job Title</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.job_title || '-' }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Department</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.department?.name || '-' }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Manager</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.manager?.user?.name || 'Not Assigned' }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Hired Date</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ formatDate(profile.hired_at) }}</dd>
              </div>
              <div v-if="profile.salary" class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Salary</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">${{ parseFloat(profile.salary).toLocaleString() }}</dd>
              </div>
            </dl>
          </section>
        </div>

        <aside class="space-y-6">
          <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Company Information</h2>
            <p class="mt-1 text-sm text-slate-500">Your workplace details and contact points.</p>
            <dl class="mt-5 space-y-3">
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Company Name</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.company?.name || '-' }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Industry</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.company?.industry || '-' }}</dd>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Address</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ profile.company?.address || '-' }}</dd>
              </div>
              <div v-if="profile.company?.website" class="rounded-2xl bg-slate-50 p-4">
                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Website</dt>
                <dd class="mt-2 text-sm font-semibold text-slate-900 break-all">
                  <a :href="profile.company.website" target="_blank" class="text-cyan-700 hover:text-cyan-800 underline underline-offset-4">
                    {{ profile.company.website }}
                  </a>
                </dd>
              </div>
            </dl>
          </section>

          <section class="rounded-3xl border border-cyan-100 bg-cyan-50 p-6 shadow-sm">
            <h3 class="text-base font-bold text-cyan-900">Profile tip</h3>
            <p class="mt-2 text-sm leading-6 text-cyan-800">
              Keep your profile details accurate so your manager and HR can reach you easily.
            </p>
          </section>
        </aside>
      </div>

      <div v-else class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">⌛</div>
        <p class="mt-4 text-slate-600">Loading profile...</p>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'

const auth = useAuthStore()
const profile = ref<any>(null)

const initials = computed(() => {
  const source = profile.value?.user?.name || profile.value?.name || 'Employee'
  return source
    .split(' ')
    .map((word: string) => word[0] || '')
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(async () => {
  // Get profile from auth store
  const employee = (auth as any).employee
  if (employee) {
    profile.value = employee
  }

  // If profile is missing (e.g. page refresh), rehydrate from /auth/me via store.
  if (!profile.value) {
    try {
      await auth.fetchUser()
      profile.value = (auth as any).employee || null
    } catch (error) {
      console.error('[Profile] Error loading profile:', error)
    }
  }
})
</script>
