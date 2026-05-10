<template>
  <AppLayout>
    <div class="attendance-root">

      <!-- Header -->
      <div class="page-header">
        <div class="header-eyebrow">
          <span class="eyebrow-dot" />
          Management
        </div>
        <h1 class="page-title">Attendance Records</h1>
        <div class="title-bar" />
      </div>

      <!-- Filter Card -->
      <div class="filter-card">
        <div class="filter-card__icon">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
          </svg>
        </div>
        <div class="filter-card__label">Filter by Date</div>
        <div class="filter-card__controls">
          <div class="date-input-wrap">
            <svg class="date-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <input v-model="selectedDate" type="date" class="date-input" @change="filterByDate" />
          </div>
          <button class="filter-btn" @click="filterByDate">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
            </svg>
            Search
          </button>
        </div>
      </div>

      <!-- Table Card -->
      <div class="table-card">

        <!-- Table Header -->
        <div class="table-card__header">
          <div class="table-card__title-row">
            <div class="table-title-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span class="table-title">
              {{ formattedDate }}
            </span>
          </div>
          <div class="table-card__badge">
            {{ records.length }} record{{ records.length !== 1 ? 's' : '' }}
          </div>
        </div>

        <!-- Records Table -->
        <div v-if="records.length > 0" class="table-wrapper">
          <table class="records-table">
            <thead>
              <tr>
                <th>
                  <span class="th-inner">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Employee
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                    </svg>
                    Department
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                    </svg>
                    Check In
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    Check Out
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Hours
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Status
                  </span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(r, i) in records"
                :key="r.id"
                class="record-row"
                :style="{ animationDelay: `${i * 45}ms` }"
              >
                <td>
                  <router-link :to="`/manager/employees/${r.employee?.id}`" class="employee-link">
                    <div class="employee-avatar">
                      {{ (r.employee?.user?.name || '?')[0].toUpperCase() }}
                    </div>
                    <span>{{ r.employee?.user?.name || '—' }}</span>
                  </router-link>
                </td>
                <td>
                  <span class="dept-tag">{{ r.employee?.department?.name || '—' }}</span>
                </td>
                <td>
                  <span class="time-cell time-cell--in">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                    </svg>
                    {{ r.check_in_at ? formatTime(r.check_in_at) : '—' }}
                  </span>
                </td>
                <td>
                  <span class="time-cell time-cell--out">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    {{ r.check_out_at ? formatTime(r.check_out_at) : '—' }}
                  </span>
                </td>
                <td>
                  <span class="hours-cell">{{ r.hours || '—' }}</span>
                </td>
                <td>
                  <span v-if="r.check_in_at && r.check_out_at" class="status-badge status-badge--complete">
                    <span class="status-dot status-dot--complete" />
                    Complete
                  </span>
                  <span v-else-if="r.check_in_at" class="status-badge status-badge--progress">
                    <span class="status-dot status-dot--progress status-dot--pulse" />
                    In Progress
                  </span>
                  <span v-else class="status-badge status-badge--absent">
                    <span class="status-dot status-dot--absent" />
                    Not Started
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-state__icon">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <p class="empty-state__title">No Records Found</p>
          <p class="empty-state__sub">No attendance records for the selected date.</p>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const selectedDate = ref(new Date().toISOString().split('T')[0])
const records = ref<any[]>([])

const formattedDate = computed(() =>
  new Date(selectedDate.value).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric'
  })
)

function formatTime(time: string) {
  return new Date(time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}

async function filterByDate() {
  await fetchRecords()
}

async function fetchRecords() {
  try {
    const res = await api.get('/manager/attendance', { params: { date: selectedDate.value } })
    records.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading attendance records:', err) }
}

async function findLatestDateWithRecords() {
  // Try to find the most recent date with attendance records
  let checkDate = new Date(selectedDate.value)
  for (let i = 0; i < 30; i++) {
    const dateStr = checkDate.toISOString().split('T')[0]
    try {
      const res = await api.get('/manager/attendance', { params: { date: dateStr } })
      const data = res.data?.data || res.data || []
      if (data.length > 0) {
        selectedDate.value = dateStr
        records.value = data
        return
      }
    } catch (err) {
      // Continue to previous date
    }
    checkDate.setDate(checkDate.getDate() - 1)
  }
  // If no records found in last 30 days, fall back to today
  await fetchRecords()
}

onMounted(() => findLatestDateWithRecords())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.attendance-root {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ─── Header ─────────────────────────────────────────── */
.page-header {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  animation: fadeSlideDown 0.45s ease both;
}

.header-eyebrow {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #94a3b8;
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #22d3ee);
}

.page-title {
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
  line-height: 1.1;
}

.title-bar {
  margin-top: 0.4rem;
  width: 2.8rem;
  height: 3px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

/* ─── Filter Card ────────────────────────────────────── */
.filter-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  padding: 1.1rem 1.4rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.1rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.5s ease 0.06s both;
}

.filter-card__icon {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 0.65rem;
  background: #f1f5f9;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.filter-card__label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #64748b;
  flex-shrink: 0;
}

.filter-card__controls {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
  flex-wrap: wrap;
}

.date-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
  flex: 1;
  min-width: 200px;
}

.date-icon {
  position: absolute;
  left: 0.85rem;
  color: #94a3b8;
  pointer-events: none;
}

.date-input {
  width: 100%;
  padding: 0.55rem 0.9rem 0.55rem 2.3rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.7rem;
  font-size: 0.875rem;
  color: #1e293b;
  background: #f8fafc;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: inherit;
}

.date-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
  background: #fff;
}

.filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.2rem;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  font-size: 0.82rem;
  font-weight: 600;
  border: none;
  border-radius: 0.7rem;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.18s;
  white-space: nowrap;
  font-family: inherit;
}

.filter-btn:hover {
  opacity: 0.88;
  transform: translateY(-1px);
}

.filter-btn:active {
  transform: translateY(0);
}

/* ─── Table Card ─────────────────────────────────────── */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.55s ease 0.12s both;
}

.table-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.4rem;
  border-bottom: 1px solid #f1f5f9;
  background: #fafbfc;
}

.table-card__title-row {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.table-title-icon {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 0.5rem;
  background: #eff6ff;
  color: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.table-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1e293b;
}

.table-card__badge {
  font-size: 0.72rem;
  font-weight: 600;
  color: #6366f1;
  background: #eef2ff;
  padding: 0.25rem 0.7rem;
  border-radius: 9999px;
}

/* ─── Table ──────────────────────────────────────────── */
.table-wrapper {
  overflow-x: auto;
}

.records-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.83rem;
}

.records-table thead tr {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.records-table th {
  padding: 0.75rem 1.2rem;
  text-align: left;
  font-weight: 600;
  color: #94a3b8;
  white-space: nowrap;
  font-size: 0.72rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.th-inner {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}

/* Row animation */
.record-row {
  border-bottom: 1px solid #f1f5f9;
  opacity: 0;
  animation: rowIn 0.35s ease forwards;
  transition: background 0.15s;
}

.record-row:last-child {
  border-bottom: none;
}

.record-row:hover {
  background: #fafbff;
}

.records-table td {
  padding: 0.85rem 1.2rem;
  vertical-align: middle;
}

/* Employee Link */
.employee-link {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  text-decoration: none;
  font-weight: 600;
  color: #4f46e5;
  transition: color 0.15s;
}

.employee-link:hover {
  color: #4338ca;
}

.employee-avatar {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #818cf8, #6366f1);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Department Tag */
.dept-tag {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 0.4rem;
}

/* Time Cells */
.time-cell {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.82rem;
  font-weight: 500;
  font-variant-numeric: tabular-nums;
}

.time-cell--in  { color: #059669; }
.time-cell--out { color: #dc2626; }

/* Hours */
.hours-cell {
  font-weight: 600;
  color: #334155;
  font-variant-numeric: tabular-nums;
}

/* Status Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.3rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.74rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--complete { background: #ecfdf5; color: #065f46; }
.status-badge--progress { background: #fffbeb; color: #92400e; }
.status-badge--absent   { background: #f8fafc; color: #64748b; }

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-dot--complete { background: #10b981; }
.status-dot--progress { background: #f59e0b; }
.status-dot--absent   { background: #cbd5e1; }

.status-dot--pulse {
  animation: pulse 1.6s ease-in-out infinite;
}

/* ─── Empty State ─────────────────────────────────────── */
.empty-state {
  padding: 3.5rem 1rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
}

.empty-state__icon {
  width: 4rem;
  height: 4rem;
  border-radius: 1rem;
  background: #f1f5f9;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
}

.empty-state__title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #334155;
}

.empty-state__sub {
  font-size: 0.82rem;
  color: #94a3b8;
}

/* ─── Animations ─────────────────────────────────────── */
@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes rowIn {
  from { opacity: 0; transform: translateX(-8px); }
  to   { opacity: 1; transform: translateX(0); }
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.4; transform: scale(1.35); }
}
</style>