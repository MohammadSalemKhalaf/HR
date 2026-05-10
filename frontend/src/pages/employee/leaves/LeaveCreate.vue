<template>
  <AppLayout>
    <div class="space-y-6">
      <section class="overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 text-white shadow-xl">
        <div class="flex flex-col gap-6 p-8 lg:flex-row lg:items-end lg:justify-between">
          <div class="space-y-3">
            <router-link to="/employee/leaves" class="inline-flex items-center text-sm font-medium text-cyan-100 hover:text-white">
              ← Back to Leaves
            </router-link>
            <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100">
              Apply Leave
            </div>
            <div>
              <h1 class="text-3xl font-bold md:text-4xl">Request Time Off</h1>
              <p class="mt-2 max-w-2xl text-sm text-slate-200">
                Submit a leave request with clear dates and a reason so your manager can review it fast.
              </p>
            </div>
          </div>

          <div class="grid gap-3 rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur sm:grid-cols-3 lg:min-w-[30rem]">
            <div class="rounded-2xl bg-white/10 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-cyan-100">Days</p>
              <p class="mt-2 text-2xl font-bold">{{ daysCount }}</p>
            </div>
            <div class="rounded-2xl bg-white/10 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-cyan-100">Status</p>
              <p class="mt-2 text-2xl font-bold">Pending</p>
            </div>
            <div class="rounded-2xl bg-white/10 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-cyan-100">Type</p>
              <p class="mt-2 text-2xl font-bold">{{ leaveTypeLabel || 'Leave' }}</p>
            </div>
          </div>
        </div>
      </section>

      <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
          <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900">Leave Details</h2>
            <p class="mt-1 text-sm text-slate-500">Fill in the dates and reason below.</p>
          </div>

          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">From Date *</label>
                <input
                  v-model="form.from_date"
                  type="date"
                  required
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
                <p v-if="errors.from_date" class="mt-1 text-sm text-rose-600">{{ errors.from_date }}</p>
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">To Date *</label>
                <input
                  v-model="form.to_date"
                  type="date"
                  required
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
                <p v-if="errors.to_date" class="mt-1 text-sm text-rose-600">{{ errors.to_date }}</p>
              </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Reason *</label>
                <select
                  v-model="form.reason"
                  required
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                >
                  <option value="">Select a reason</option>
                  <option value="vacation">Vacation</option>
                  <option value="sick_leave">Sick Leave</option>
                  <option value="personal">Personal</option>
                  <option value="family_emergency">Family Emergency</option>
                  <option value="maternity">Maternity Leave</option>
                  <option value="other">Other</option>
                </select>
                <p v-if="errors.reason" class="mt-1 text-sm text-rose-600">{{ errors.reason }}</p>
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Number of Days</label>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900">
                  {{ daysCount }} days
                </div>
              </div>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Remarks</label>
              <textarea
                v-model="form.remarks"
                rows="5"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                placeholder="Additional details or notes (optional)"
              ></textarea>
            </div>

            <div v-if="errors.general" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
              {{ errors.general }}
            </div>

            <div v-if="successMessage" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
              {{ successMessage }}
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row">
              <button
                type="submit"
                :disabled="loading"
                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-500"
              >
                {{ loading ? 'Submitting...' : 'Submit Leave Request' }}
              </button>
              <router-link
                to="/employee/leaves"
                class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
              >
                Cancel
              </router-link>
            </div>
          </form>
        </section>

        <aside class="space-y-6">
          <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Quick Tips</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
              <li class="rounded-2xl bg-slate-50 px-4 py-3">Pick dates in the correct order. The request will stay pending until reviewed.</li>
              <li class="rounded-2xl bg-slate-50 px-4 py-3">Use a clear reason so your manager can approve faster.</li>
              <li class="rounded-2xl bg-slate-50 px-4 py-3">Add remarks if there are handover notes or urgent context.</li>
            </ul>
          </section>

          <section class="rounded-3xl border border-cyan-100 bg-cyan-50 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-cyan-900">Leave Summary</h3>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
              <div class="rounded-2xl bg-white/70 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-700">Selected Range</p>
                <p class="mt-1 text-sm font-semibold text-cyan-900">{{ summaryRange }}</p>
              </div>
              <div class="rounded-2xl bg-white/70 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-700">Requested Type</p>
                <p class="mt-1 text-sm font-semibold text-cyan-900">{{ leaveTypeLabel || 'Choose a reason' }}</p>
              </div>
            </div>
          </section>
        </aside>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'

const router = useRouter()
const loading = ref(false)
const successMessage = ref('')
const errors = ref<any>({})

const auth = useAuthStore()

const form = ref({
  from_date: '',
  to_date: '',
  reason: '',
  remarks: ''
})

const daysCount = computed(() => {
  if (!form.value.from_date || !form.value.to_date) return 0
  const from = new Date(form.value.from_date)
  const to = new Date(form.value.to_date)
  const diff = Math.ceil((to.getTime() - from.getTime()) / (1000 * 60 * 60 * 24)) + 1
  return diff > 0 ? diff : 0
})

const leaveTypeLabel = computed(() => {
  const labels: Record<string, string> = {
    vacation: 'Annual',
    sick_leave: 'Sick',
    personal: 'Other',
    family_emergency: 'Other',
    maternity: 'Other',
    other: 'Other'
  }

  return labels[form.value.reason] || ''
})

const summaryRange = computed(() => {
  if (!form.value.from_date && !form.value.to_date) return 'No dates selected yet'
  if (!form.value.from_date || !form.value.to_date) return 'Select both dates'
  return `${form.value.from_date} → ${form.value.to_date}`
})

const submitForm = async () => {
  errors.value = {}
  successMessage.value = ''
  loading.value = true

  // client-side validation
  if (!form.value.from_date || !form.value.to_date) {
    errors.value.from_date = errors.value.from_date || 'Please select both dates.'
    loading.value = false
    return
  }

  const from = new Date(form.value.from_date)
  const to = new Date(form.value.to_date)
  if (to < from) {
    errors.value.to_date = 'To date must be same or after From date.'
    loading.value = false
    return
  }

  try {
    // map frontend reason -> backend leave_type
    const reasonMap: Record<string, string> = {
      vacation: 'annual',
      sick_leave: 'sick',
      personal: 'other',
      family_emergency: 'other',
      maternity: 'other',
      other: 'other'
    }

    const payload = {
      employee_id: (auth as any).employee?.id,
      leave_type: reasonMap[form.value.reason] || 'other',
      start_date: form.value.from_date,
      end_date: form.value.to_date,
      days_count: daysCount.value,
      notes: form.value.remarks
    }

    await api.post('/leave/apply', payload)

    successMessage.value = 'Leave request submitted successfully!'
    setTimeout(() => {
      router.push('/employee/leaves')
    }, 2000)
  } catch (error: any) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value.general = error.response?.data?.message || 'An error occurred'
    }
  } finally {
    loading.value = false
  }
}
</script>
