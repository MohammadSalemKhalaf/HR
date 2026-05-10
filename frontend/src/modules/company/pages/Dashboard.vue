<template>
  <div class="dashboard-root">

    <!-- ── Header ── -->
    <div class="page-header">
      <div class="header-eyebrow">
        <span class="eyebrow-dot" />
        Company Area
      </div>
      <h1 class="page-title">Dashboard</h1>
      <p class="page-subtitle">Overview for company workflows.</p>
      <div class="title-bar" />
    </div>

    <!-- ── Stats Grid ── -->
    <div class="stats-grid">

      <div class="stat-card" style="--delay:0ms">
        <div class="stat-card__icon stat-card__icon--violet">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9"/>
          </svg>
        </div>
        <div class="stat-card__body">
          <p class="stat-card__label">Departments</p>
          <p class="stat-card__value">{{ stats.departments }}</p>
        </div>
        <div class="stat-card__glow stat-card__glow--violet" />
      </div>

      <div class="stat-card" style="--delay:60ms">
        <div class="stat-card__icon stat-card__icon--blue">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div class="stat-card__body">
          <p class="stat-card__label">Employees</p>
          <p class="stat-card__value">{{ stats.employees }}</p>
        </div>
        <div class="stat-card__glow stat-card__glow--blue" />
      </div>

      <div class="stat-card" style="--delay:120ms">
        <div class="stat-card__icon stat-card__icon--emerald">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
        </div>
        <div class="stat-card__body">
          <p class="stat-card__label">Open Vacancies</p>
          <p class="stat-card__value">{{ stats.jobs }}</p>
        </div>
        <div class="stat-card__glow stat-card__glow--emerald" />
      </div>

      <div class="stat-card" style="--delay:180ms">
        <div class="stat-card__icon stat-card__icon--amber">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div class="stat-card__body">
          <p class="stat-card__label">Applications</p>
          <p class="stat-card__value">{{ stats.applications }}</p>
        </div>
        <div class="stat-card__glow stat-card__glow--amber" />
      </div>

    </div>

    <!-- ── Quick Links ── -->
    <div class="section-label">
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      Quick Access
    </div>

    <div class="links-grid">

      <router-link to="/company/my-company" class="link-card" style="--delay:0ms">
        <div class="link-card__icon link-card__icon--violet">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <div class="link-card__body">
          <p class="link-card__label">Settings</p>
          <p class="link-card__title">My Company</p>
        </div>
        <div class="link-card__arrow">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="link-card__glow link-card__glow--violet" />
      </router-link>

      <router-link to="/company/departments" class="link-card" style="--delay:55ms">
        <div class="link-card__icon link-card__icon--blue">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9"/>
          </svg>
        </div>
        <div class="link-card__body">
          <p class="link-card__label">Manage</p>
          <p class="link-card__title">Departments</p>
        </div>
        <div class="link-card__arrow">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="link-card__glow link-card__glow--blue" />
      </router-link>

      <router-link to="/company/employees" class="link-card" style="--delay:110ms">
        <div class="link-card__icon link-card__icon--teal">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div class="link-card__body">
          <p class="link-card__label">Directory</p>
          <p class="link-card__title">Employees</p>
        </div>
        <div class="link-card__arrow">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="link-card__glow link-card__glow--teal" />
      </router-link>

      <router-link to="/company/vacancies" class="link-card" style="--delay:165ms">
        <div class="link-card__icon link-card__icon--emerald">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
        </div>
        <div class="link-card__body">
          <p class="link-card__label">Hiring</p>
          <p class="link-card__title">Job Vacancies</p>
        </div>
        <div class="link-card__arrow">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="link-card__glow link-card__glow--emerald" />
      </router-link>

      <router-link to="/company/applications" class="link-card" style="--delay:220ms">
        <div class="link-card__icon link-card__icon--amber">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div class="link-card__body">
          <p class="link-card__label">Review</p>
          <p class="link-card__title">Job Applications</p>
        </div>
        <div class="link-card__arrow">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="link-card__glow link-card__glow--amber" />
      </router-link>

    </div>

  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

const stats = ref({ departments: 0, employees: 0, jobs: 0, applications: 0 })

const fetchStats = async () => {
  try {
    const res     = await api.get('/company/dashboard-stats')
    const payload = res.data?.data || {}
    stats.value = {
      departments : payload.departments  || 0,
      employees   : payload.employees    || 0,
      jobs        : payload.jobs         || 0,
      applications: payload.applications || 0,
    }
  } catch (err) {
    console.error('Failed to fetch company stats', err)
  }
}

onMounted(fetchStats)
</script>

<style scoped>
/* ─── Root ──────────────────────────────────────────── */
.dashboard-root {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
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

.page-subtitle {
  font-size: 0.82rem;
  color: #94a3b8;
  font-weight: 500;
}

.title-bar {
  margin-top: 0.35rem;
  width: 2.8rem;
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
  .stats-grid { grid-template-columns: repeat(4, 1fr); }
}

.stat-card {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem 1.3rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.45s ease both;
  animation-delay: var(--delay, 0ms);
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.09);
}

.stat-card__icon {
  flex-shrink: 0;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 0.85rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-card__icon--violet  { background: #f5f3ff; color: #7c3aed; }
.stat-card__icon--blue    { background: #eff6ff; color: #2563eb; }
.stat-card__icon--emerald { background: #ecfdf5; color: #059669; }
.stat-card__icon--amber   { background: #fffbeb; color: #d97706; }

.stat-card__body { flex: 1; min-width: 0; }

.stat-card__label {
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #94a3b8;
}

.stat-card__value {
  font-size: 2rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.04em;
  line-height: 1.1;
  margin-top: 0.2rem;
}

.stat-card__glow {
  position: absolute;
  bottom: -18px;
  right: -18px;
  width: 72px;
  height: 72px;
  border-radius: 50%;
  pointer-events: none;
  opacity: 0.55;
}

.stat-card__glow--violet  { background: radial-gradient(circle, rgba(139,92,246,0.2), transparent 70%); }
.stat-card__glow--blue    { background: radial-gradient(circle, rgba(59,130,246,0.2), transparent 70%); }
.stat-card__glow--emerald { background: radial-gradient(circle, rgba(16,185,129,0.2), transparent 70%); }
.stat-card__glow--amber   { background: radial-gradient(circle, rgba(245,158,11,0.2), transparent 70%); }

/* ─── Section Label ──────────────────────────────────── */
.section-label {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #64748b;
  animation: fadeSlideDown 0.45s ease 0.22s both;
}

/* ─── Links Grid ─────────────────────────────────────── */
.links-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.85rem;
  animation: fadeSlideDown 0.45s ease 0.26s both;
}

@media (min-width: 640px) {
  .links-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1024px) {
  .links-grid { grid-template-columns: repeat(3, 1fr); }
}

.link-card {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.1rem 1.25rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.1rem;
  text-decoration: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  animation: rowIn 0.38s ease both;
  animation-delay: var(--delay, 0ms);
  transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
}

.link-card:hover {
  border-color: transparent;
  box-shadow: 0 0 0 2px #6366f1, 0 8px 24px rgba(99,102,241,0.13);
  transform: translateY(-2px);
}

.link-card:hover .link-card__arrow {
  transform: translateX(4px);
  color: #6366f1;
}

.link-card:hover .link-card__glow { opacity: 1; }

.link-card__icon {
  flex-shrink: 0;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 0.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.link-card__icon--violet  { background: #f5f3ff; color: #7c3aed; }
.link-card__icon--blue    { background: #eff6ff; color: #2563eb; }
.link-card__icon--teal    { background: #f0fdfa; color: #0d9488; }
.link-card__icon--emerald { background: #ecfdf5; color: #059669; }
.link-card__icon--amber   { background: #fffbeb; color: #d97706; }

.link-card__body { flex: 1; min-width: 0; }

.link-card__label {
  font-size: 0.67rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #94a3b8;
}

.link-card__title {
  font-size: 0.92rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 0.18rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.link-card__arrow {
  color: #cbd5e1;
  flex-shrink: 0;
  transition: transform 0.2s, color 0.2s;
}

.link-card__glow {
  position: absolute;
  bottom: -20px;
  right: -20px;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.3s;
}

.link-card__glow--violet  { background: radial-gradient(circle, rgba(139,92,246,0.18), transparent 70%); }
.link-card__glow--blue    { background: radial-gradient(circle, rgba(59,130,246,0.18), transparent 70%); }
.link-card__glow--teal    { background: radial-gradient(circle, rgba(13,148,136,0.18), transparent 70%); }
.link-card__glow--emerald { background: radial-gradient(circle, rgba(16,185,129,0.18), transparent 70%); }
.link-card__glow--amber   { background: radial-gradient(circle, rgba(245,158,11,0.18), transparent 70%); }

/* ─── Animations ──────────────────────────────────────── */
@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes rowIn {
  from { opacity: 0; transform: translateX(-8px); }
  to   { opacity: 1; transform: translateX(0); }
}
</style>