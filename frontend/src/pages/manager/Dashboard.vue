<template>
  <AppLayout>
    <div class="dashboard-root">

      <!-- Header -->
      <div class="dashboard-header">
        <div class="header-label">Manager Area</div>
        <h1 class="header-title">Manager Dashboard</h1>
        <div class="header-line" />
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card stat-card--departments">
          <div class="stat-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Departments</p>
            <p class="stat-value">{{ stats.departmentsCount || 0 }}</p>
          </div>
          <div class="stat-glow stat-glow--blue" />
        </div>

        <div class="stat-card stat-card--employees">
          <div class="stat-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Employees</p>
            <p class="stat-value">{{ stats.employeesCount || 0 }}</p>
          </div>
          <div class="stat-glow stat-glow--violet" />
        </div>

        <div class="stat-card stat-card--leaves">
          <div class="stat-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Pending Leaves</p>
            <p class="stat-value">{{ stats.pendingLeaves || 0 }}</p>
          </div>
          <div class="stat-glow stat-glow--amber" />
        </div>

        <div class="stat-card stat-card--attendance">
          <div class="stat-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Today Attendance</p>
            <p class="stat-value">{{ stats.todayAttendance || 0 }}</p>
          </div>
          <div class="stat-glow stat-glow--emerald" />
        </div>
      </div>

      <!-- Quick Access Section -->
      <div class="section-header">
        <span class="section-tag">QUICK ACCESS</span>
        <div class="section-divider" />
      </div>

      <div class="nav-grid">
        <router-link to="/manager/tasks" class="nav-card">
          <div class="nav-card__icon nav-card__icon--tasks">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <div class="nav-card__body">
            <p class="nav-card__title">Tasks</p>
            <p class="nav-card__sub">Manage & assign work</p>
          </div>
          <div class="nav-card__arrow">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </router-link>

        <router-link to="/manager/employees" class="nav-card">
          <div class="nav-card__icon nav-card__icon--employees">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div class="nav-card__body">
            <p class="nav-card__title">Employees</p>
            <p class="nav-card__sub">View & manage team</p>
          </div>
          <div class="nav-card__arrow">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </router-link>

        <router-link to="/manager/leaves" class="nav-card">
          <div class="nav-card__icon nav-card__icon--leaves">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="nav-card__body">
            <p class="nav-card__title">Leaves</p>
            <p class="nav-card__sub">Approve or reject requests</p>
          </div>
          <div class="nav-card__arrow">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </router-link>

        <router-link to="/manager/attendance" class="nav-card">
          <div class="nav-card__icon nav-card__icon--attendance">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="nav-card__body">
            <p class="nav-card__title">Attendance</p>
            <p class="nav-card__sub">Track daily presence</p>
          </div>
          <div class="nav-card__arrow">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </router-link>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const stats = ref({ departmentsCount: 0, employeesCount: 0, pendingLeaves: 0, todayAttendance: 0 })

async function fetchStats() {
  try {
    const res = await api.get('/manager/dashboard-stats')
    Object.assign(stats.value, res.data)
  } catch (err) { console.error('Error loading dashboard stats:', err) }
}

onMounted(() => fetchStats())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.dashboard-root {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  padding: 0.5rem 0;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ─── Header ────────────────────────────────────────── */
.dashboard-header {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.header-label {
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #94a3b8;
}

.header-title {
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
  line-height: 1.1;
}

.header-line {
  margin-top: 0.5rem;
  width: 3rem;
  height: 3px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

/* ─── Stats Grid ─────────────────────────────────────── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

@media (min-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-card {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem 1.4rem;
  border-radius: 1.25rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 24px rgba(0,0,0,0.09);
}

.stat-icon {
  flex-shrink: 0;
  width: 2.6rem;
  height: 2.6rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
  color: #64748b;
}

.stat-card--departments .stat-icon { background: #eff6ff; color: #3b82f6; }
.stat-card--employees  .stat-icon  { background: #f5f3ff; color: #7c3aed; }
.stat-card--leaves     .stat-icon  { background: #fffbeb; color: #d97706; }
.stat-card--attendance .stat-icon  { background: #ecfdf5; color: #059669; }

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.stat-label {
  font-size: 0.73rem;
  font-weight: 500;
  color: #94a3b8;
  white-space: nowrap;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  letter-spacing: -0.04em;
  color: #0f172a;
  line-height: 1;
}

.stat-glow {
  position: absolute;
  bottom: -18px;
  right: -18px;
  width: 70px;
  height: 70px;
  border-radius: 50%;
  opacity: 0.12;
  pointer-events: none;
}

.stat-glow--blue    { background: #3b82f6; }
.stat-glow--violet  { background: #7c3aed; }
.stat-glow--amber   { background: #f59e0b; }
.stat-glow--emerald { background: #10b981; }

/* ─── Section Header ─────────────────────────────────── */
.section-header {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.section-tag {
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  color: #94a3b8;
  white-space: nowrap;
}

.section-divider {
  flex: 1;
  height: 1px;
  background: #e2e8f0;
}

/* ─── Nav Grid ───────────────────────────────────────── */
.nav-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.85rem;
}

@media (min-width: 768px) {
  .nav-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.nav-card {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  padding: 1.3rem 1.2rem;
  border-radius: 1.25rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  text-decoration: none;
  transition: all 0.22s ease;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  position: relative;
  overflow: hidden;
}

.nav-card:hover {
  border-color: transparent;
  box-shadow: 0 0 0 2px #6366f1, 0 8px 24px rgba(99,102,241,0.12);
  transform: translateY(-2px);
}

.nav-card:hover .nav-card__arrow {
  transform: translateX(3px);
  color: #6366f1;
}

.nav-card__icon {
  width: 2.6rem;
  height: 2.6rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.nav-card__icon--tasks      { background: #f0fdf4; color: #16a34a; }
.nav-card__icon--employees  { background: #f5f3ff; color: #7c3aed; }
.nav-card__icon--leaves     { background: #fff7ed; color: #ea580c; }
.nav-card__icon--attendance { background: #eff6ff; color: #2563eb; }

.nav-card__body {
  flex: 1;
}

.nav-card__title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  letter-spacing: -0.01em;
}

.nav-card__sub {
  font-size: 0.72rem;
  color: #94a3b8;
  margin-top: 0.15rem;
}

.nav-card__arrow {
  color: #cbd5e1;
  transition: transform 0.2s ease, color 0.2s ease;
  align-self: flex-end;
}
</style>