<template>
  <AppLayout>
    <div class="dept-detail-root">

      <!-- Header -->
      <div class="page-header">
        <div class="header-top">
          <div class="header-left">
            <div class="header-eyebrow">
              <span class="eyebrow-dot" />
              Management
            </div>
            <h1 class="page-title">{{ department.name || 'Department' }}</h1>
            <div class="title-bar" />
          </div>
          <router-link to="/manager/departments" class="back-btn">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
          </router-link>
        </div>
      </div>

      <!-- Department Info Card -->
      <div class="info-card">
        <div class="info-card__header">
          <div class="info-card__header-icon">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="info-card__header-title">Department Information</span>
        </div>

        <div class="info-grid">
          <div class="info-item">
            <div class="info-item__icon">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Department Name</p>
              <p class="info-item__value">{{ department.name || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--code">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Department Code</p>
              <p class="info-item__value">{{ department.code || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--manager">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Manager</p>
              <p class="info-item__value">{{ department.manager?.user?.name || '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Employees Table Card -->
      <div class="table-card">
        <div class="table-card__header">
          <div class="table-card__title-row">
            <div class="table-title-icon">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <span class="table-card__title">Employees</span>
          </div>
          <span class="table-card__badge">{{ employees.length }} member{{ employees.length !== 1 ? 's' : '' }}</span>
        </div>

        <div v-if="employees.length > 0" class="table-wrapper">
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
                :style="{ animationDelay: `${i * 50}ms` }"
              >
                <td>
                  <router-link :to="`/manager/employees/${e.id}`" class="employee-link">
                    <div class="employee-avatar">{{ (e.user?.name || '?')[0].toUpperCase() }}</div>
                    <span>{{ e.user?.name || '—' }}</span>
                  </router-link>
                </td>
                <td>
                  <span class="email-cell">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ e.user?.email || '—' }}
                  </span>
                </td>
                <td>
                  <span class="title-tag">{{ e.job_title || '—' }}</span>
                </td>
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

        <div v-else class="empty-state">
          <div class="empty-state__ring">
            <div class="empty-state__icon">
              <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
          </div>
          <p class="empty-state__title">No Employees Yet</p>
          <p class="empty-state__sub">No employees have been added to this department.</p>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const route = useRoute()
const department = ref<any>({})
const employees = ref<any[]>([])

function capital(val: string) {
  if (!val) return '—'
  return val.charAt(0).toUpperCase() + val.slice(1)
}

async function fetchDepartment() {
  try {
    const id = route.params.id
    const res = await api.get(`/manager/departments/${id}`)
    const data = res.data
    department.value = data.department || data
    employees.value = data.employees || []
  } catch (err) { console.error(err) }
}

onMounted(() => fetchDepartment())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.dept-detail-root {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ─── Header ─────────────────────────────────────────── */
.page-header {
  animation: fadeSlideDown 0.4s ease both;
}

.header-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.header-left {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
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

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: #6366f1;
  background: #eef2ff;
  border-radius: 0.7rem;
  text-decoration: none;
  transition: background 0.18s, color 0.18s, transform 0.18s;
  white-space: nowrap;
  margin-top: 0.25rem;
}

.back-btn:hover {
  background: #e0e7ff;
  transform: translateX(-2px);
}

/* ─── Info Card ─────────────────────────────────────── */
.info-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.45s ease 0.07s both;
}

.info-card__header {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.95rem 1.4rem;
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
}

.info-card__header-icon {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 0.5rem;
  background: #eef2ff;
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-card__header-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0;
}

@media (min-width: 640px) {
  .info-grid { grid-template-columns: repeat(3, 1fr); }
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 1.25rem 1.4rem;
  border-bottom: 1px solid #f8fafc;
}

@media (min-width: 640px) {
  .info-item {
    border-bottom: none;
    border-right: 1px solid #f1f5f9;
  }
  .info-item:last-child { border-right: none; }
}

.info-item__icon {
  flex-shrink: 0;
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 0.6rem;
  background: #eef2ff;
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 0.1rem;
}

.info-item__icon--code    { background: #f0fdf4; color: #16a34a; }
.info-item__icon--manager { background: #fdf4ff; color: #9333ea; }

.info-item__content {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.info-item__label {
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94a3b8;
}

.info-item__value {
  font-size: 0.92rem;
  font-weight: 700;
  color: #1e293b;
}

/* ─── Table Card ─────────────────────────────────────── */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.5s ease 0.14s both;
}

.table-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
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
  background: #f0fdf4;
  color: #16a34a;
  display: flex;
  align-items: center;
  justify-content: center;
}

.table-card__title {
  font-size: 0.875rem;
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

.employee-link:hover { color: #4338ca; }

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

/* Email */
.email-cell {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: #64748b;
  font-size: 0.8rem;
}

/* Title tag */
.title-tag {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 0.4rem;
}

/* Status */
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

/* ─── Empty State ────────────────────────────────────── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  padding: 3.5rem 1rem;
}

.empty-state__ring {
  width: 4.5rem;
  height: 4.5rem;
  border-radius: 50%;
  border: 2px dashed #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.4rem;
}

.empty-state__icon {
  width: 3.2rem;
  height: 3.2rem;
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