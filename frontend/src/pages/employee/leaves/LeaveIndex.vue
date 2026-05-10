<template>
  <AppLayout>
    <div class="space-y-6">
      <section class="overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 text-white shadow-xl">
        <div class="flex flex-col gap-6 p-8 lg:flex-row lg:items-end lg:justify-between">
          <div class="space-y-3">
            <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100">
              My Leave
            </div>
            <div>
              <h1 class="text-3xl font-bold md:text-4xl">Leave Requests</h1>
              <p class="mt-2 max-w-2xl text-sm text-slate-200">Track your applications, balances, and request history from one place.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-sm font-medium text-slate-500">Total Leave Balance</p>
          <p class="mt-3 text-3xl font-bold text-slate-900">{{ leaveBalance.total }}</p>
          <p class="mt-1 text-xs text-slate-500">Annual quota for the current cycle</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-sm font-medium text-slate-500">Used Leaves</p>
          <p class="mt-3 text-3xl font-bold text-slate-900">{{ leaveBalance.used }}</p>
          <p class="mt-1 text-xs text-slate-500">Approved days already consumed</p>
        </div>
        <div class="rounded-3xl border border-cyan-100 bg-cyan-50 p-6 shadow-sm">
          <p class="text-sm font-medium text-cyan-700">Remaining</p>
          <p class="mt-3 text-3xl font-bold text-cyan-900">{{ leaveBalance.remaining }}</p>
          <p class="mt-1 text-xs text-cyan-700/80">Days still available to request</p>
        </div>
      </section>

      <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
          <div class="mb-6 flex items-start justify-between gap-4">
            <div>
              <h2 class="text-xl font-bold text-slate-900">Apply for Leave</h2>
              <p class="mt-1 text-sm text-slate-500">Create a new request without leaving this page.</p>
            </div>
            <router-link
              to="/employee/leaves"
              class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
            >
              View List
            </router-link>
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

            <div v-if="errors.leave_id" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
              {{ errors.leave_id }}
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
        </div>

        <aside class="space-y-6">
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
              <div class="rounded-2xl bg-white/70 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-700">Status</p>
                <p class="mt-1 text-sm font-semibold text-cyan-900">Pending</p>
              </div>
            </div>
          </section>

          <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Quick Tips</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
              <li class="rounded-2xl bg-slate-50 px-4 py-3">Choose a date range in order. The request stays pending until approved.</li>
              <li class="rounded-2xl bg-slate-50 px-4 py-3">Use a clear reason so your manager can review faster.</li>
              <li class="rounded-2xl bg-slate-50 px-4 py-3">Add remarks for handover notes or urgent context.</li>
            </ul>
          </section>
        </aside>
      </section>

      <div class="flex flex-wrap gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
        <button
          @click="filterStatus = null"
          :class="[
            'px-4 py-2 rounded-full font-medium transition',
            filterStatus === null
              ? 'bg-slate-900 text-white'
              : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
          ]"
        >
          All ({{ leaves.length }})
        </button>
        <button
          @click="filterStatus = 'pending'"
          :class="[
            'px-4 py-2 rounded-full font-medium transition',
            filterStatus === 'pending'
              ? 'bg-amber-500 text-white'
              : 'bg-amber-50 text-amber-700 hover:bg-amber-100'
          ]"
        >
          Pending ({{ pendingCount }})
        </button>
        <button
          @click="filterStatus = 'approved'"
          :class="[
            'px-4 py-2 rounded-full font-medium transition',
            filterStatus === 'approved'
              ? 'bg-emerald-600 text-white'
              : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
          ]"
        >
          Approved ({{ approvedCount }})
        </button>
        <button
          @click="filterStatus = 'rejected'"
          :class="[
            'px-4 py-2 rounded-full font-medium transition',
            filterStatus === 'rejected'
              ? 'bg-rose-600 text-white'
              : 'bg-rose-50 text-rose-700 hover:bg-rose-100'
          ]"
        >
          Rejected ({{ rejectedCount }})
        </button>
      </div>

      <!-- Leaves List -->
      <div v-if="filteredLeaves.length > 0" class="grid gap-4">
        <div
          v-for="leave in filteredLeaves"
          :key="leave.id"
          class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
          <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <h3 class="text-lg font-bold text-slate-900">{{ leave.reason || 'Leave Request' }}</h3>
                <span
                  :class="[
                    'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap',
                    getStatusBg(leave.status)
                  ]"
                >
                  {{ formatStatus(leave.status) }}
                </span>
                <button
                  v-if="leave.status === 'pending'"
                  type="button"
                  class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                  @click="cancelLeave(leave)"
                >
                  Cancel
                </button>
              </div>
              <p class="text-sm text-slate-600">
                {{ formatDate(getStartDate(leave)) }} - {{ formatDate(getEndDate(leave)) }} · {{ calculateDays(getStartDate(leave), getEndDate(leave)) }} days
              </p>
            </div>
          </div>
          <div v-if="leave.remarks || leave.notes" class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
            <p><strong class="text-slate-900">Remarks:</strong> {{ leave.remarks || leave.notes }}</p>
          </div>
          <div class="mt-4 text-xs text-slate-500">
            Applied on {{ formatDate(leave.created_at) }}
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-3xl">📋</div>
        <p class="mt-4 text-lg font-semibold text-slate-900">No leave requests found</p>
        <p class="mt-1 text-sm text-slate-500">Start by submitting your first leave request.</p>
        <router-link
          to="/employee/leaves/apply"
          class="mt-6 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
        >
          Apply for Leave
        </router-link>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'
import AppLayout from '@/layouts/AppLayout.vue'
import { useAuthStore } from '@/stores/auth'

const leaves = ref<any[]>([])
const filterStatus = ref<string | null>(null)
const leaveBalance = ref({ total: 0, used: 0, remaining: 0 })
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

const filteredLeaves = computed(() => {
  if (!filterStatus.value) return leaves.value
  return leaves.value.filter((l) => l.status === filterStatus.value)
})

const pendingCount = computed(() => leaves.value.filter((l) => l.status === 'pending').length)
const approvedCount = computed(() => leaves.value.filter((l) => l.status === 'approved').length)
const rejectedCount = computed(() => leaves.value.filter((l) => l.status === 'rejected').length)

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getStartDate = (leave: any) => leave.from_date || leave.start_date || leave.startDate
const getEndDate = (leave: any) => leave.to_date || leave.end_date || leave.endDate

const formatStatus = (status: string) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

function calculateDays(from: string, to: string) {
  if (!from || !to) return 0
  const start = new Date(from)
  const end = new Date(to)
  const diff = Math.ceil((end.getTime() - start.getTime()) / (1000 * 60 * 60 * 24)) + 1
  return diff > 0 ? diff : 0
}

const daysCount = computed(() => calculateDays(form.value.from_date, form.value.to_date))

const getStatusColor = (status: string) => {
  const colors: { [key: string]: string } = {
    pending: 'border-yellow-500',
    approved: 'border-green-500',
    rejected: 'border-red-500'
  }
  return colors[status] || 'border-gray-500'
}

const getStatusBg = (status: string) => {
  const colors: { [key: string]: string } = {
    pending: 'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700'
  }
  return colors[status] || 'bg-gray-100 text-gray-700'
}

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

const fetchLeaves = async () => {
  const response = await api.get('/leaves')
  leaves.value = response.data.data || []

  leaveBalance.value.total = 20
  leaveBalance.value.used = leaves.value.filter((l) => l.status === 'approved').reduce((sum, l) => {
    return sum + calculateDays(getStartDate(l), getEndDate(l))
  }, 0)
  leaveBalance.value.remaining = leaveBalance.value.total - leaveBalance.value.used
}

const submitForm = async () => {
  errors.value = {}
  successMessage.value = ''
  loading.value = true

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
    form.value = {
      from_date: '',
      to_date: '',
      reason: '',
      remarks: ''
    }

    await fetchLeaves()
  } catch (error: any) {
    if (error.type === 'validation' && error.validation) {
      errors.value = error.validation
    } else if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value.general = error.response?.data?.message || 'An error occurred'
    }
  } finally {
    loading.value = false
  }
}

const cancelLeave = async (leave: any) => {
  const leaveId = leave?.id || leave?.leave_id || ''
  if (!leaveId) {
    errors.value.general = 'Unable to cancel this leave request.'
    return
  }

  const confirmed = window.confirm('Are you sure you want to cancel this leave request?')
  if (!confirmed) return

  errors.value = {}
  successMessage.value = ''

  try {
    await api.post('/leave/cancel', { leave_id: leaveId })
    successMessage.value = 'Leave request canceled successfully.'
    await fetchLeaves()
  } catch (error: any) {
    if (error.type === 'validation' && error.validation) {
      errors.value = error.validation
    } else if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value.general = error.response?.data?.message || 'An error occurred'
    }
  }
}

onMounted(async () => {
  try {
    if (!(auth as any).employee) {
      await auth.fetchUser()
    }

    await fetchLeaves()
  } catch (error) {
    console.error('[Leaves] Error loading leaves:', error)
  }
})
</script>
