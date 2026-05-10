<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Attendance</h1>
          <p class="text-gray-600 mt-1">Check in and out to track your attendance</p>
        </div>
      </div>

      <!-- Check-in/out Section -->
      <div class="bg-white rounded-lg shadow p-8">
        <div class="text-center mb-8">
          <div class="text-5xl font-bold text-gray-900 mb-2">{{ currentTime }}</div>
          <div class="text-gray-600">{{ currentDate }}</div>
        </div>

        <div class="flex gap-4 justify-center mb-8">
          <button
            v-if="!checkedIn"
            @click="checkIn"
            :disabled="loading"
            class="px-8 py-4 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white text-lg font-bold rounded-lg transition flex items-center gap-2"
          >
            <span v-if="loading" class="animate-spin">⏳</span>
            <span v-else>✓</span>
            Check In
          </button>
          <button
            v-else
            @click="checkOut"
            :disabled="loading"
            class="px-8 py-4 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white text-lg font-bold rounded-lg transition flex items-center gap-2"
          >
            <span v-if="loading" class="animate-spin">⏳</span>
            <span v-else>✗</span>
            Check Out
          </button>
        </div>

        <div v-if="todayRecord" class="text-center text-gray-600">
          <p v-if="todayRecord.check_in">
            ✓ Checked in at {{ formatTime(todayRecord.check_in) }}
          </p>
          <p v-if="todayRecord.check_out">
            ✗ Checked out at {{ formatTime(todayRecord.check_out) }}
          </p>
        </div>
      </div>

      <!-- Records Table -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Attendance Records</h2>

        <div v-if="records.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Check In</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Check Out</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Hours</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50">
                <td class="px-6 py-3 text-sm text-gray-900">{{ formatDate(record.date) }}</td>
                <td class="px-6 py-3 text-sm text-gray-900">
                  {{ record.check_in ? formatTime(record.check_in) : '-' }}
                </td>
                <td class="px-6 py-3 text-sm text-gray-900">
                  {{ record.check_out ? formatTime(record.check_out) : '-' }}
                </td>
                <td class="px-6 py-3 text-sm text-gray-900">
                  {{ calculateHours(record.check_in, record.check_out) }}h
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="text-center py-8 text-gray-500">
          <p>No attendance records yet</p>
        </div>
      </div>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/api/axios'
import AppLayout from '@/layouts/AppLayout.vue'

const records = ref<any[]>([])
const todayRecord = ref<any>(null)
const checkedIn = ref(false)
const loading = ref(false)

const currentTime = computed(() => {
  const now = new Date()
  return now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
})

const currentDate = computed(() => {
  const now = new Date()
  return now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
})

const safeDate = (input: string | null | undefined) => {
  if (!input) return null
  // If already ISO or contains space, try direct parse
  const cleaned = input.toString()
  const parsed = new Date(cleaned)
  if (!isNaN(parsed.getTime())) return parsed

  // Try common fallback: time-only string like "08:30:00"
  if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(cleaned)) {
    return new Date(`2000-01-01T${cleaned}`)
  }

  return null
}

const formatDate = (date: string | null | undefined) => {
  const d = safeDate(date)
  if (!d) return 'Invalid Date'
  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (value: string | null | undefined) => {
  const d = safeDate(value)
  if (!d) return '-'
  return d.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const calculateHours = (checkIn: string | null | undefined, checkOut: string | null | undefined) => {
  const start = safeDate(checkIn)
  const end = safeDate(checkOut)
  if (!start || !end) return '-'
  const hours = (end.getTime() - start.getTime()) / (1000 * 60 * 60)
  return hours < 0 ? '-' : hours.toFixed(1)
}

const checkIn = async () => {
  loading.value = true
  try {
    await api.post('/attendance/check-in')
    checkedIn.value = true
    await loadRecords()
  } catch (error) {
    console.error('[Attendance] Error checking in:', error)
  } finally {
    loading.value = false
  }
}

const checkOut = async () => {
  loading.value = true
  try {
    await api.post('/attendance/check-out')
    checkedIn.value = false
    await loadRecords()
  } catch (error) {
    console.error('[Attendance] Error checking out:', error)
  } finally {
    loading.value = false
  }
}

const loadRecords = async () => {
  try {
    const response = await api.get('/attendance')
    const data = response.data.data || []
    // normalize records: map backend fields to frontend names
    records.value = data.map((r: any) => ({
      ...r,
      date: r.attendance_date || r.date || r.created_at || null,
      check_in: r.check_in_at || r.check_in || null,
      check_out: r.check_out_at || r.check_out || null
    }))

    // Check today's record by comparing date (yyyy-mm-dd)
    const todayIso = new Date().toISOString().split('T')[0]
    todayRecord.value = records.value.find((r) => {
      const d = safeDate(r.date)
      if (!d) return false
      return d.toISOString().split('T')[0] === todayIso
    }) || null

    checkedIn.value = !!todayRecord.value?.check_in && !todayRecord.value?.check_out
  } catch (error) {
    console.error('[Attendance] Error loading records:', error)
  }
}

// Update time every second
setInterval(() => {
  // Component will reactively update due to computed properties
}, 1000)

onMounted(async () => {
  await loadRecords()
})
</script>
