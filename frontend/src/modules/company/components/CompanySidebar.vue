<template>
  <aside class="sidebar">

    <!-- Logo / Brand -->
    <div class="sidebar-brand">
      <div class="brand-icon">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
        </svg>
      </div>
      <div class="brand-text">
        <span class="brand-name">WorkSpace</span>
        <span class="brand-role">Company Panel</span>
      </div>
    </div>

    <!-- Divider -->
    <div class="sidebar-divider" />

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <p class="nav-section-label">Main Menu</p>

      <ul class="nav-list">
        <li v-for="(item, i) in navItems" :key="item.to" :style="{ '--i': i }">
          <router-link :to="item.to" class="nav-link" active-class="nav-link--active">
            <span class="nav-icon" v-html="item.icon" />
            <span class="nav-label">{{ item.label }}</span>
            <span class="nav-arrow">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </span>
            <span class="nav-glow" />
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Bottom User Section -->
    <div class="sidebar-footer">
      <div class="sidebar-divider" />
      <div class="user-card">
        <div class="user-avatar">C</div>
        <div class="user-info">
          <p class="user-name">Company Admin</p>
          <p class="user-status">
            <span class="status-dot" />
            Online
          </p>
        </div>
        <button class="logout-btn" title="Logout">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>

  </aside>
</template>

<script setup lang="ts">
const navItems = [
  {
    to: '/company',
    label: 'Dashboard',
    icon: `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>`,
  },
  {
    to: '/company/my-company',
    label: 'My Company',
    icon: `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
    </svg>`,
  },
  {
    to: '/company/departments',
    label: 'Departments',
    icon: `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
    </svg>`,
  },
  {
    to: '/company/employees',
    label: 'Employees',
    icon: `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>`,
  },
  {
    to: '/company/vacancies',
    label: 'Job Vacancies',
    icon: `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>`,
  },
  {
    to: '/company/applications',
    label: 'Job Applications',
    icon: `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>`,
  },
]
</script>

<style scoped>
/* ─── Root ───────────────────────────── */
.sidebar {
  width: 15rem;
  min-height: 100vh;
  background: #fff;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
  animation: sidebarIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* ─── Brand ──────────────────────────── */
.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.25rem 1.1rem 1rem;
  animation: fadeDown 0.45s ease 0.05s both;
}

.brand-icon {
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 0.6rem;
  background: linear-gradient(135deg, #6366f1, #22d3ee);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
}

.brand-text {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.brand-name {
  font-size: 0.88rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.02em;
  line-height: 1.1;
}

.brand-role {
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #94a3b8;
}

/* ─── Divider ────────────────────────── */
.sidebar-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, #e2e8f0 30%, #e2e8f0 70%, transparent);
  margin: 0 0.75rem;
}

/* ─── Nav ────────────────────────────── */
.sidebar-nav {
  flex: 1;
  padding: 0.9rem 0.65rem 0.5rem;
  overflow-y: auto;
}

.nav-section-label {
  font-size: 0.6rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #cbd5e1;
  padding: 0 0.5rem;
  margin-bottom: 0.5rem;
}

.nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

/* staggered entrance */
.nav-list li {
  animation: rowIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
  animation-delay: calc(0.08s + var(--i) * 0.055s);
}

/* ─── Nav Link ───────────────────────── */
.nav-link {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.58rem 0.75rem;
  border-radius: 0.7rem;
  text-decoration: none;
  color: #64748b;
  font-size: 0.83rem;
  font-weight: 500;
  transition: background 0.18s, color 0.18s, transform 0.18s;
  overflow: hidden;
}

.nav-link:hover {
  background: #f1f5f9;
  color: #1e293b;
  transform: translateX(2px);
}

.nav-link:hover .nav-arrow { opacity: 1; transform: translateX(0); }
.nav-link:hover .nav-glow  { opacity: 1; }

/* Active state */
.nav-link--active {
  background: linear-gradient(135deg, #eef2ff, #f0fdfe);
  color: #4f46e5;
  font-weight: 700;
  box-shadow: inset 0 0 0 1px rgba(99,102,241,0.18);
}

.nav-link--active .nav-icon { color: #6366f1; }
.nav-link--active .nav-arrow { opacity: 1; color: #6366f1; transform: translateX(0); }

/* ─── Nav Icon ───────────────────────── */
.nav-icon {
  flex-shrink: 0;
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 0.45rem;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  transition: background 0.18s, color 0.18s;
}

.nav-link:hover .nav-icon {
  background: #e0e7ff;
  color: #6366f1;
}

.nav-link--active .nav-icon {
  background: #e0e7ff;
  color: #6366f1;
}

/* ─── Nav Label ──────────────────────── */
.nav-label { flex: 1; white-space: nowrap; }

/* ─── Nav Arrow ──────────────────────── */
.nav-arrow {
  opacity: 0;
  color: #94a3b8;
  transform: translateX(-4px);
  transition: opacity 0.18s, transform 0.18s, color 0.18s;
  flex-shrink: 0;
}

/* ─── Hover Glow ─────────────────────── */
.nav-glow {
  position: absolute;
  right: -10px;
  top: 50%;
  transform: translateY(-50%);
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(99,102,241,0.15), transparent 70%);
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.25s;
}

/* ─── Footer ─────────────────────────── */
.sidebar-footer {
  padding: 0 0 0.75rem;
  animation: fadeUp 0.4s ease 0.45s both;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.85rem 1rem 0.5rem;
}

.user-avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #22d3ee);
  color: #fff;
  font-size: 0.8rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(99,102,241,0.3);
}

.user-info { flex: 1; min-width: 0; }

.user-name {
  font-size: 0.78rem;
  font-weight: 700;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-status {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.68rem;
  color: #94a3b8;
  margin-top: 0.1rem;
}

.status-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #10b981;
  animation: pulse 2s ease infinite;
}

.logout-btn {
  flex-shrink: 0;
  width: 1.8rem;
  height: 1.8rem;
  border-radius: 0.4rem;
  border: none;
  background: #f1f5f9;
  color: #94a3b8;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.18s, color 0.18s;
}

.logout-btn:hover {
  background: #fee2e2;
  color: #ef4444;
}

/* ─── Animations ─────────────────────── */
@keyframes sidebarIn {
  from { opacity: 0; transform: translateX(-18px); }
  to   { opacity: 1; transform: translateX(0); }
}

@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-8px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes rowIn {
  from { opacity: 0; transform: translateX(-12px); }
  to   { opacity: 1; transform: translateX(0); }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.4; }
}
</style>