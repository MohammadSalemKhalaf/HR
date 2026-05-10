<template>
  <AppLayout>
    <div class="emp-detail-root">

      <!-- Header -->
      <div class="page-header">
        <div class="header-top">
          <div class="header-left">
            <div class="header-eyebrow">
              <span class="eyebrow-dot" />
              Management
            </div>
            <div class="header-identity">
              <div class="header-avatar" :style="{ background: avatarGradient(employee.user?.name) }">
                {{ (employee.user?.name || '?')[0].toUpperCase() }}
              </div>
              <div>
                <h1 class="page-title">{{ employee.user?.name || 'Employee' }}</h1>
                <p v-if="employee.job_title" class="page-subtitle">{{ employee.job_title }}</p>
              </div>
            </div>
            <div class="title-bar" />
          </div>
          <router-link to="/manager/employees" class="back-btn">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
          </router-link>
        </div>
      </div>

      <!-- Employee Information Card -->
      <div class="info-card">
        <div class="info-card__header">
          <div class="info-card__header-icon info-card__header-icon--blue">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <span class="info-card__header-title">Employee Information</span>
        </div>

        <div class="info-grid info-grid--2">
          <div class="info-item">
            <div class="info-item__icon">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Full Name</p>
              <p class="info-item__value">{{ employee.user?.name || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--teal">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Email Address</p>
              <p class="info-item__value">{{ employee.user?.email || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--violet">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Department</p>
              <router-link
                v-if="employee.department"
                :to="`/manager/departments/${employee.department.id}`"
                class="dept-value-link"
              >
                {{ employee.department.name }}
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
              <p v-else class="info-item__value">—</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--amber">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Employee ID</p>
              <p class="info-item__value">{{ employee.employee_number || '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Employment Details Card -->
      <div class="info-card info-card--delayed">
        <div class="info-card__header">
          <div class="info-card__header-icon info-card__header-icon--emerald">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <span class="info-card__header-title">Employment Details</span>
        </div>

        <div class="info-grid info-grid--2">
          <div class="info-item">
            <div class="info-item__icon info-item__icon--orange">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Job Title</p>
              <p class="info-item__value">{{ employee.job_title || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--emerald">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Status</p>
              <span v-if="employee.status === 'active'" class="status-badge status-badge--active">
                <span class="status-dot" />
                Active
              </span>
              <span v-else-if="employee.status" class="status-badge status-badge--other">
                <span class="status-dot status-dot--other" />
                {{ capital(employee.status) }}
              </span>
              <span v-else class="info-item__value">—</span>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--green">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Salary
                <span class="readonly-tag">Read-only</span>
              </p>
              <p class="info-item__value salary-value">
                {{ employee.salary ? '$' + Number(employee.salary).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '—' }}
              </p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--sky">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Hired Date</p>
              <p class="info-item__value">
                {{ employee.hired_at ? new Date(employee.hired_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Access Cards -->
      <div class="quick-grid">
        <router-link to="/manager/attendance" class="quick-card">
          <div class="quick-card__icon quick-card__icon--blue">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="quick-card__body">
            <p class="quick-card__label">View Attendance</p>
            <p class="quick-card__title">Attendance Records</p>
          </div>
          <div class="quick-card__arrow">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
          <div class="quick-card__glow" />
        </router-link>

        <router-link to="/manager/leaves" class="quick-card">
          <div class="quick-card__icon quick-card__icon--amber">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="quick-card__body">
            <p class="quick-card__label">View Requests</p>
            <p class="quick-card__title">Leave Requests</p>
          </div>
          <div class="quick-card__arrow">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
          <div class="quick-card__glow quick-card__glow--amber" />
        </router-link>
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
const employee = ref<any>({})

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

async function fetchEmployee() {
  try {
    const id = route.params.id
    const res = await api.get(`/manager/employees/${id}`)
    employee.value = res.data
  } catch (err) { console.error(err) }
}

onMounted(() => fetchEmployee())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.emp-detail-root {
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
  gap: 0.4rem;
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

.header-identity {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.header-avatar {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  color: #fff;
  font-size: 1.1rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.18);
  flex-shrink: 0;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
  line-height: 1.1;
}

.page-subtitle {
  font-size: 0.82rem;
  color: #94a3b8;
  margin-top: 0.15rem;
  font-weight: 500;
}

.title-bar {
  margin-top: 0.2rem;
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
  transition: background 0.18s, transform 0.18s;
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

.info-card--delayed {
  animation-delay: 0.14s;
}

.info-card__header {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.9rem 1.4rem;
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
}

.info-card__header-icon {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-card__header-icon--blue    { background: #eff6ff; color: #3b82f6; }
.info-card__header-icon--emerald { background: #ecfdf5; color: #059669; }

.info-card__header-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
}

/* ─── Info Grid ──────────────────────────────────────── */
.info-grid {
  display: grid;
  grid-template-columns: 1fr;
}

.info-grid--2 {
  grid-template-columns: 1fr;
}

@media (min-width: 640px) {
  .info-grid--2 { grid-template-columns: repeat(2, 1fr); }
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 1.15rem 1.4rem;
  border-bottom: 1px solid #f8fafc;
  transition: background 0.15s;
}

.info-item:hover { background: #fafbff; }

@media (min-width: 640px) {
  .info-item:nth-child(odd)  { border-right: 1px solid #f1f5f9; }
  .info-item:nth-last-child(-n+2) { border-bottom: none; }
}

.info-item:last-child { border-bottom: none; }

.info-item__icon {
  flex-shrink: 0;
  width: 2.1rem;
  height: 2.1rem;
  border-radius: 0.55rem;
  background: #eef2ff;
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 0.05rem;
}

.info-item__icon--teal   { background: #f0fdfa; color: #0d9488; }
.info-item__icon--violet { background: #f5f3ff; color: #7c3aed; }
.info-item__icon--amber  { background: #fffbeb; color: #d97706; }
.info-item__icon--orange { background: #fff7ed; color: #ea580c; }
.info-item__icon--emerald{ background: #ecfdf5; color: #059669; }
.info-item__icon--green  { background: #f0fdf4; color: #16a34a; }
.info-item__icon--sky    { background: #f0f9ff; color: #0284c7; }

.info-item__content {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  min-width: 0;
}

.info-item__label {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94a3b8;
}

.readonly-tag {
  font-size: 0.6rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: #94a3b8;
  background: #f1f5f9;
  padding: 0.1rem 0.4rem;
  border-radius: 0.3rem;
  text-transform: none;
}

.info-item__value {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
  word-break: break-word;
}

.salary-value {
  font-size: 1rem;
  font-weight: 800;
  color: #16a34a;
  letter-spacing: -0.02em;
}

/* Department link inside info */
.dept-value-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: #6366f1;
  text-decoration: none;
  transition: color 0.15s;
}

.dept-value-link:hover { color: #4338ca; }

/* ─── Status Badge ───────────────────────────────────── */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.3rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.74rem;
  font-weight: 600;
}

.status-badge--active { background: #ecfdf5; color: #065f46; }
.status-badge--other  { background: #f8fafc; color: #64748b; }

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10b981;
  flex-shrink: 0;
}

.status-dot--other { background: #cbd5e1; }

/* ─── Quick Access Cards ─────────────────────────────── */
.quick-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  animation: fadeSlideDown 0.5s ease 0.21s both;
}

@media (max-width: 480px) {
  .quick-grid { grid-template-columns: 1fr; }
}

.quick-card {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.2rem 1.3rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.1rem;
  text-decoration: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
}

.quick-card:hover {
  border-color: transparent;
  box-shadow: 0 0 0 2px #6366f1, 0 8px 24px rgba(99,102,241,0.12);
  transform: translateY(-2px);
}

.quick-card:hover .quick-card__arrow {
  transform: translateX(3px);
  color: #6366f1;
}

.quick-card:hover .quick-card__glow { opacity: 1; }

.quick-card__icon {
  flex-shrink: 0;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 0.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.quick-card__icon--blue  { background: #eff6ff; color: #2563eb; }
.quick-card__icon--amber { background: #fffbeb; color: #d97706; }

.quick-card__body {
  flex: 1;
  min-width: 0;
}

.quick-card__label {
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94a3b8;
}

.quick-card__title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 0.2rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.quick-card__arrow {
  color: #cbd5e1;
  flex-shrink: 0;
  transition: transform 0.2s, color 0.2s;
}

.quick-card__glow {
  position: absolute;
  bottom: -20px;
  right: -20px;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(99,102,241,0.18), transparent 70%);
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.3s;
}

.quick-card__glow--amber {
  background: radial-gradient(circle, rgba(245,158,11,0.18), transparent 70%);
}

/* ─── Animations ─────────────────────────────────────── */
@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>