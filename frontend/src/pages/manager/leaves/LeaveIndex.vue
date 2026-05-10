<template>
  <AppLayout>
    <div class="leaves-root">

      <!-- Header -->
      <div class="page-header">
        <div class="header-eyebrow">
          <span class="eyebrow-dot" />
          Management
        </div>
        <h1 class="page-title">Leave Requests</h1>
        <div class="title-bar" />
      </div>

      <!-- Table Card -->
      <div v-if="leaves.length > 0" class="table-card">

        <div class="table-card__header">
          <div class="table-card__title-row">
            <div class="table-title-icon">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="table-card__title">All Leave Requests</span>
          </div>
          <div class="header-badges">
            <span class="count-badge count-badge--pending">
              {{ leaves.filter(l => l.status === 'pending').length }} pending
            </span>
            <span class="count-badge">{{ leaves.length }} total</span>
          </div>
        </div>

        <div class="table-wrapper">
          <table class="records-table">
            <thead>
              <tr>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Employee
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Type
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Period
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Days
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Status
                  </span>
                </th>
                <th class="th-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(l, i) in leaves"
                :key="l.id"
                class="record-row"
                :style="{ animationDelay: `${i * 50}ms` }"
              >
                <!-- Employee -->
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">
                      {{ (l.employee?.user?.name || '?')[0].toUpperCase() }}
                    </div>
                    <div class="employee-cell__info">
                      <span class="employee-name">{{ l.employee?.user?.name || '—' }}</span>
                      <span v-if="l.employee?.job_title" class="employee-title">{{ l.employee.job_title }}</span>
                    </div>
                  </div>
                </td>

                <!-- Type -->
                <td>
                  <span class="leave-type-badge" :class="`leave-type-badge--${l.leave_type}`">
                    <component :is="leaveTypeIcon(l.leave_type).icon" class="leave-type-svg" />
                    {{ leaveTypeIcon(l.leave_type).label }}
                  </span>
                </td>

                <!-- Period -->
                <td>
                  <span class="period-cell">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formatDate(l.start_date) }}
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="period-arrow">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    {{ formatDate(l.end_date) }}
                  </span>
                </td>

                <!-- Days -->
                <td>
                  <span class="days-badge">{{ l.days_count }}d</span>
                </td>

                <!-- Status -->
                <td>
                  <span v-if="l.status === 'pending'" class="status-badge status-badge--pending">
                    <span class="status-dot status-dot--pending status-dot--pulse" />
                    Pending
                  </span>
                  <span v-else-if="l.status === 'approved'" class="status-badge status-badge--approved">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Approved
                  </span>
                  <span v-else-if="l.status === 'rejected'" class="status-badge status-badge--rejected">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Rejected
                  </span>
                </td>

                <!-- Actions -->
                <td class="td-right">
                  <div v-if="l.status === 'pending'" class="action-btns">
                    <button
                      class="action-btn action-btn--approve"
                      :disabled="loadingId === l.id"
                      @click="approveLeave(l.id)"
                    >
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      Approve
                    </button>
                    <button
                      class="action-btn action-btn--reject"
                      :disabled="loadingId === l.id"
                      @click="rejectLeave(l.id)"
                    >
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Reject
                    </button>
                  </div>
                  <span v-else class="no-action">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-state__ring">
          <div class="empty-state__icon">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <p class="empty-state__title">No Leave Requests</p>
        <p class="empty-state__sub">No leave requests from your departments at the moment.</p>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, h } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const leaves = ref<any[]>([])
const loadingId = ref<number | null>(null)

/* ── Icons as render functions ─────────────────────── */
const AnnualIcon = () => h('svg', { width: 13, height: 13, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
])
const SickIcon = () => h('svg', { width: 13, height: 13, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })
])
const UnpaidIcon = () => h('svg', { width: 13, height: 13, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
])
const DefaultIcon = () => h('svg', { width: 13, height: 13, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
])

function leaveTypeIcon(type: string): { icon: any; label: string } {
  const map: Record<string, { icon: any; label: string }> = {
    annual: { icon: AnnualIcon, label: 'Annual' },
    sick:   { icon: SickIcon,   label: 'Sick' },
    unpaid: { icon: UnpaidIcon, label: 'Unpaid' },
  }
  return map[type] ?? { icon: DefaultIcon, label: type ? type.charAt(0).toUpperCase() + type.slice(1) : 'Other' }
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

async function approveLeave(id: number) {
  loadingId.value = id
  try {
    await api.post(`/manager/leaves/${id}/approve`)
  } catch {
    try { await api.post('/leave/approve', { leave_id: id }) }
    catch (fallbackErr) { console.error('Error approving leave:', fallbackErr); loadingId.value = null; return }
  }
  await fetchLeaves()
  loadingId.value = null
}

async function rejectLeave(id: number) {
  loadingId.value = id
  try {
    await api.post(`/manager/leaves/${id}/reject`)
  } catch {
    try { await api.post('/leave/reject', { leave_id: id }) }
    catch (fallbackErr) { console.error('Error rejecting leave:', fallbackErr); loadingId.value = null; return }
  }
  await fetchLeaves()
  loadingId.value = null
}

async function fetchLeaves() {
  try {
    const res = await api.get('/manager/leaves')
    leaves.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading leaves:', err) }
}

onMounted(() => fetchLeaves())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.leaves-root {
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
  animation: fadeSlideDown 0.4s ease both;
}

.header-eyebrow {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.13em;
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
  margin-top: 0.45rem;
  width: 2.8rem;
  height: 3px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

/* ─── Table Card ─────────────────────────────────────── */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.48s ease 0.08s both;
}

.table-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  padding: 0.95rem 1.4rem;
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
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
  background: #fff7ed;
  color: #ea580c;
  display: flex;
  align-items: center;
  justify-content: center;
}

.table-card__title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
}

.header-badges {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.count-badge {
  font-size: 0.72rem;
  font-weight: 600;
  color: #64748b;
  background: #f1f5f9;
  padding: 0.25rem 0.7rem;
  border-radius: 9999px;
}

.count-badge--pending {
  color: #92400e;
  background: #fef3c7;
}

/* ─── Table ──────────────────────────────────────────── */
.table-wrapper { overflow-x: auto; }

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
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #94a3b8;
  white-space: nowrap;
}

.th-inner {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}

.th-right { text-align: right; }

.record-row {
  border-bottom: 1px solid #f1f5f9;
  opacity: 0;
  animation: rowIn 0.35s ease forwards;
  transition: background 0.15s;
}

.record-row:last-child { border-bottom: none; }
.record-row:hover { background: #fafbff; }

.records-table td {
  padding: 0.9rem 1.2rem;
  vertical-align: middle;
}

.td-right { text-align: right; }

/* ─── Employee Cell ──────────────────────────────────── */
.employee-cell {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.employee-avatar {
  flex-shrink: 0;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #818cf8, #6366f1);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.employee-cell__info {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.employee-name {
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
}

.employee-title {
  font-size: 0.72rem;
  color: #94a3b8;
}

/* ─── Leave Type Badge ───────────────────────────────── */
.leave-type-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.28rem 0.65rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
  background: #f1f5f9;
  color: #475569;
}

.leave-type-badge--annual { background: #eff6ff; color: #2563eb; }
.leave-type-badge--sick   { background: #fdf4ff; color: #9333ea; }
.leave-type-badge--unpaid { background: #f8fafc; color: #64748b; }

/* ─── Period Cell ────────────────────────────────────── */
.period-cell {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: #475569;
  font-weight: 500;
  white-space: nowrap;
}

.period-arrow { color: #cbd5e1; }

/* ─── Days Badge ─────────────────────────────────────── */
.days-badge {
  display: inline-block;
  padding: 0.22rem 0.6rem;
  background: #f0fdf4;
  color: #16a34a;
  font-size: 0.75rem;
  font-weight: 700;
  border-radius: 0.45rem;
}

/* ─── Status Badges ──────────────────────────────────── */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.3rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.74rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--pending  { background: #fef3c7; color: #92400e; }
.status-badge--approved { background: #ecfdf5; color: #065f46; }
.status-badge--rejected { background: #fff1f2; color: #9f1239; }

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-dot--pending { background: #f59e0b; }

.status-dot--pulse {
  animation: pulse 1.6s ease-in-out infinite;
}

/* ─── Action Buttons ─────────────────────────────────── */
.action-btns {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: flex-end;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.38rem 0.85rem;
  border-radius: 0.6rem;
  font-size: 0.76rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: opacity 0.18s, transform 0.18s;
  font-family: inherit;
  white-space: nowrap;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

.action-btn:not(:disabled):hover { opacity: 0.82; transform: translateY(-1px); }
.action-btn:not(:disabled):active { transform: translateY(0); }

.action-btn--approve {
  background: #ecfdf5;
  color: #065f46;
}

.action-btn--approve:not(:disabled):hover {
  background: #d1fae5;
}

.action-btn--reject {
  background: #fff1f2;
  color: #9f1239;
}

.action-btn--reject:not(:disabled):hover {
  background: #ffe4e6;
}

.no-action {
  font-size: 0.8rem;
  color: #cbd5e1;
}

/* ─── Empty State ─────────────────────────────────────── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.65rem;
  padding: 4rem 1rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  animation: fadeSlideDown 0.5s ease 0.08s both;
}

.empty-state__ring {
  width: 5rem;
  height: 5rem;
  border-radius: 50%;
  border: 2px dashed #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.4rem;
}

.empty-state__icon {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 50%;
  background: #f8fafc;
  color: #cbd5e1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-state__title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #334155;
}

.empty-state__sub {
  font-size: 0.82rem;
  color: #94a3b8;
  text-align: center;
  max-width: 22rem;
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
  50%       { opacity: 0.35; transform: scale(1.4); }
}
</style>