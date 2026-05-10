<template>
  <AppLayout>
    <div class="departments-root">

      <!-- Header -->
      <div class="page-header">
        <div class="header-eyebrow">
          <span class="eyebrow-dot" />
          Management
        </div>
        <h1 class="page-title">My Departments</h1>
        <div class="title-bar" />
      </div>

      <!-- Departments Grid -->
      <div v-if="departments.length > 0" class="dept-grid">
        <div
          v-for="(d, i) in departments"
          :key="d.id"
          class="dept-card"
          :style="{ animationDelay: `${i * 70}ms` }"
        >
          <!-- Top accent line -->
          <div class="dept-card__accent" />

          <!-- Icon + Info -->
          <div class="dept-card__top">
            <div class="dept-icon">
              <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
              </svg>
            </div>

            <div class="dept-card__info">
              <router-link :to="`/manager/departments/${d.id}`" class="dept-name">
                {{ d.name }}
              </router-link>
              <span v-if="d.code" class="dept-code">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                </svg>
                {{ d.code }}
              </span>
            </div>
          </div>

          <!-- Divider -->
          <div class="dept-card__divider" />

          <!-- Footer -->
          <div class="dept-card__footer">
            <div class="dept-employees">
              <div class="dept-employees__icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <span class="dept-employees__count">{{ d.employees_count || 0 }}</span>
              <span class="dept-employees__label">Employees</span>
            </div>

            <router-link :to="`/manager/departments/${d.id}`" class="dept-view-btn">
              View
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </router-link>
          </div>

          <!-- Hover glow -->
          <div class="dept-card__glow" />
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-state__ring">
          <div class="empty-state__icon">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
            </svg>
          </div>
        </div>
        <p class="empty-state__title">No Departments Yet</p>
        <p class="empty-state__sub">You don't manage any departments at the moment.</p>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const departments = ref<any[]>([])

async function fetchDepartments() {
  try {
    const res = await api.get('/manager/departments')
    departments.value = res.data?.data || res.data || []
  } catch (err) { console.error('Error loading departments:', err) }
}

onMounted(() => fetchDepartments())
</script>

<style scoped>
/* ─── Root ─────────────────────────────────────────── */
.departments-root {
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

.title-bar {
  margin-top: 0.45rem;
  width: 2.8rem;
  height: 3px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

/* ─── Grid ───────────────────────────────────────────── */
.dept-grid {
  display: grid;
  gap: 1.1rem;
  grid-template-columns: 1fr;
}

@media (min-width: 640px) {
  .dept-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1024px) {
  .dept-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ─── Department Card ────────────────────────────────── */
.dept-card {
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 0;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  padding: 1.4rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.03);
  opacity: 0;
  animation: cardIn 0.4s ease forwards;
  transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
  cursor: default;
}

.dept-card:hover {
  border-color: #c7d2fe;
  box-shadow: 0 0 0 1px #c7d2fe, 0 8px 28px rgba(99,102,241,0.1);
  transform: translateY(-3px);
}

.dept-card:hover .dept-card__glow {
  opacity: 1;
}

.dept-card:hover .dept-view-btn {
  background: #6366f1;
  color: #fff;
  gap: 0.5rem;
}

/* Top accent */
.dept-card__accent {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, #6366f1, #818cf8, #22d3ee);
  border-radius: 1.25rem 1.25rem 0 0;
  opacity: 0;
  transition: opacity 0.25s;
}

.dept-card:hover .dept-card__accent {
  opacity: 1;
}

/* Glow blob */
.dept-card__glow {
  position: absolute;
  bottom: -30px;
  right: -30px;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(99,102,241,0.18), transparent 70%);
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.3s;
}

/* ─── Card Top ───────────────────────────────────────── */
.dept-card__top {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
}

.dept-icon {
  flex-shrink: 0;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 0.8rem;
  background: linear-gradient(135deg, #eef2ff, #e0e7ff);
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.dept-card:hover .dept-icon {
  background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
}

.dept-card__info {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  min-width: 0;
}

.dept-name {
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
  text-decoration: none;
  letter-spacing: -0.01em;
  transition: color 0.15s;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}

.dept-name:hover {
  color: #6366f1;
}

.dept-code {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.72rem;
  font-weight: 600;
  color: #94a3b8;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 0.18rem 0.55rem;
  border-radius: 0.4rem;
  letter-spacing: 0.04em;
  width: fit-content;
}

/* ─── Divider ────────────────────────────────────────── */
.dept-card__divider {
  height: 1px;
  background: #f1f5f9;
  margin: 1.1rem 0;
}

/* ─── Card Footer ────────────────────────────────────── */
.dept-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.dept-employees {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.dept-employees__icon {
  width: 1.7rem;
  height: 1.7rem;
  border-radius: 0.45rem;
  background: #f0fdf4;
  color: #16a34a;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dept-employees__count {
  font-size: 1rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.02em;
}

.dept-employees__label {
  font-size: 0.76rem;
  color: #94a3b8;
  font-weight: 500;
}

.dept-view-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.38rem 0.85rem;
  font-size: 0.76rem;
  font-weight: 600;
  color: #6366f1;
  background: #eef2ff;
  border-radius: 0.6rem;
  text-decoration: none;
  transition: background 0.2s, color 0.2s, gap 0.2s;
  white-space: nowrap;
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
  animation: fadeSlideDown 0.5s ease 0.1s both;
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
  font-size: 1rem;
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

@keyframes cardIn {
  from { opacity: 0; transform: translateY(14px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0)  scale(1); }
}
</style>