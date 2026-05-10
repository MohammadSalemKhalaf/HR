<template>
  <AppLayout>
    <div class="employees-root">

      <!-- Header -->
      <div class="page-header">
        <div class="header-eyebrow">
          <span class="eyebrow-dot" />
          Management
        </div>
        <h1 class="page-title">Employees</h1>
        <div class="title-bar" />
      </div>

      <!-- Table Card -->
      <div v-if="employees.length > 0" class="table-card">

        <div class="table-card__header">
          <div class="table-card__title-row">
            <div class="table-title-icon">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <span class="table-card__title">Team Members</span>
          </div>
          <div class="header-badges">
            <span class="count-badge count-badge--active">
              {{ employees.filter(e => e.status === 'active').length }} active
            </span>
            <span class="count-badge">{{ employees.length }} total</span>
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
                    Name
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Email
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                    </svg>
                    Department
                  </span>
                </th>
                <th>
                  <span class="th-inner">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Title
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
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(e, i) in employees"
                :key="e.id"
                class="record-row"
                :style="{ animationDelay: `${i * 45}ms` }"
              >
                <!-- Name -->
                <td>
                  <router-link :to="`/manager/employees/${e.id}`" class="employee-link">
                    <div class="employee-avatar" :style="{ background: avatarGradient(e.user?.name) }">
                      {{ (e.user?.name || '?')[0].toUpperCase() }}
                    </div>
                    <div class="employee-link__info">
                      <span class="employee-name">{{ e.user?.name || '—' }}</span>
                    </div>
                  </router-link>
                </td>

                <!-- Email -->
                <td>
                  <span class="email-cell">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ e.user?.email || '—' }}
                  </span>
                </td>

                <!-- Department -->
                <td>
                  <router-link
                    v-if="e.department"
                    :to="`/manager/departments/${e.department.id}`"
                    class="dept-link"
                  >
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                    </svg>
                    {{ e.department.name }}
                  </router-link>
                  <span v-else class="no-value">—</span>
                </td>

                <!-- Title -->
                <td>
                  <span v-if="e.job_title" class="title-tag">{{ e.job_title }}</span>
                  <span v-else class="no-value">—</span>
                </td>

                <!-- Status -->
                <td>
                  <span v-if="e.status === 'active'" class="status-badge status-badge--active">
                    <span class="status-dot status-dot--active" />
                    Active
                  </span>
                  <span v-else class="status-badge status-badge--other">
                    <span class="status-dot status-dot--other" />
                    {{ capital(e.status) }}
                  </span>
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
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
        <p class="empty-state__title">No Employees Yet</p>
        <p class="empty-state__sub">No employees found in your departments.</p>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const employees = ref<any[]>([])

function capital(val: string) {
  if (!val) return '—'
  return val.charAt(0).toUpperCase() + val.slice(1)
}

const GRADIENTS = [
  'linear-gradient(135deg,#818cf8,#6366f1)',
  'linear-gradient(135deg,#34d399,#059669)',
  'linear-gradient(135deg,#fb923c,#ea580c)',
  'linear-gradient(135deg,#f472b6,#db2777)',
  'linear-gradient(135deg,#38bdf8,#0284c7)',
  'linear-gradient(135deg,#a78bfa,#7c3aed)',
  'linear-gradient(135deg,#facc15,#ca8a04)',
]

function avatarGradient(name: string = '') {
  const idx = (name.charCodeAt(0) || 0) % GRADIENTS.length
  return GRADIENTS[idx]
}

async function fetchEmployees() {
  try {
    const res = await api.get('/manager/employees')
    employees.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading employees:', err) }
}

onMounted(() => fetchEmployees())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.employees-root {
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
  background: #f5f3ff;
  color: #7c3aed;
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

.count-badge--active {
  color: #065f46;
  background: #ecfdf5;
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

.record-row {
  border-bottom: 1px solid #f1f5f9;
  opacity: 0;
  animation: rowIn 0.35s ease forwards;
  transition: background 0.15s;
}

.record-row:last-child { border-bottom: none; }
.record-row:hover { background: #fafbff; }

.records-table td {
  padding: 0.85rem 1.2rem;
  vertical-align: middle;
}

/* ─── Employee Link ──────────────────────────────────── */
.employee-link {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  text-decoration: none;
  transition: opacity 0.15s;
}

.employee-link:hover { opacity: 0.8; }

.employee-avatar {
  flex-shrink: 0;
  width: 2.1rem;
  height: 2.1rem;
  border-radius: 50%;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.employee-link__info {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.employee-name {
  font-weight: 600;
  color: #4f46e5;
  white-space: nowrap;
}

/* ─── Email ──────────────────────────────────────────── */
.email-cell {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: #64748b;
}

/* ─── Department Link ────────────────────────────────── */
.dept-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.8rem;
  font-weight: 500;
  color: #6366f1;
  text-decoration: none;
  padding: 0.2rem 0.6rem;
  background: #eef2ff;
  border-radius: 0.45rem;
  transition: background 0.15s, color 0.15s;
}

.dept-link:hover {
  background: #e0e7ff;
  color: #4338ca;
}

/* ─── Title Tag ──────────────────────────────────────── */
.title-tag {
  display: inline-block;
  padding: 0.22rem 0.6rem;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 0.4rem;
}

/* ─── No Value ───────────────────────────────────────── */
.no-value {
  color: #cbd5e1;
  font-size: 0.8rem;
}

/* ─── Status Badges ──────────────────────────────────── */
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

.status-badge--active { background: #ecfdf5; color: #065f46; }
.status-badge--other  { background: #f8fafc; color: #64748b; }

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-dot--active { background: #10b981; }
.status-dot--other  { background: #cbd5e1; }

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
</style>