<template>
  <AppLayout>
    <div class="company-root">

      <!-- ── Header ──────────────────────────────────────── -->
      <div class="page-header">
        <div class="header-left">
          <div class="header-eyebrow">
            <span class="eyebrow-dot" />
            Company
          </div>
          <h1 class="page-title">My Company</h1>
          <div class="title-bar" />
        </div>

        <div class="header-actions">
          <button class="btn btn--ghost" @click="fetchCompany" :disabled="loading">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"
              :class="{ 'spin': loading }">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <button v-if="!editing" class="btn btn--primary" @click="editing = true">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            Edit
          </button>
        </div>
      </div>

      <!-- ── Loading Skeleton ───────────────────────────── -->
      <div v-if="loading" class="info-card">
        <div class="skeleton-header" />
        <div class="skeleton-body">
          <div class="skeleton-row" v-for="i in 4" :key="i">
            <div class="skeleton-icon" />
            <div class="skeleton-lines">
              <div class="skeleton-line skeleton-line--short" />
              <div class="skeleton-line" />
            </div>
          </div>
        </div>
      </div>

      <!-- ── View Mode ──────────────────────────────────── -->
      <div v-else-if="!editing" class="info-card" key="view">

        <!-- Company Identity -->
        <div class="company-identity">
          <div class="company-logo">
            {{ (form.name || 'C')[0].toUpperCase() }}
          </div>
          <div>
            <h2 class="company-name">{{ form.name || '—' }}</h2>
            <p v-if="form.industry" class="company-industry">
              <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              {{ form.industry }}
            </p>
          </div>
        </div>

        <div class="card-divider" />

        <div class="info-card__header">
          <div class="info-card__header-icon info-card__header-icon--blue">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="info-card__header-title">Company Information</span>
        </div>

        <div class="info-grid">

          <div class="info-item">
            <div class="info-item__icon info-item__icon--indigo">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Company Name</p>
              <p class="info-item__value">{{ form.name || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--emerald">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Industry</p>
              <p class="info-item__value">{{ form.industry || '—' }}</p>
            </div>
          </div>

          <div class="info-item info-item--full">
            <div class="info-item__icon info-item__icon--amber">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Address</p>
              <p class="info-item__value">{{ form.address || '—' }}</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--sky">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Website</p>
              <a v-if="form.website" :href="form.website" target="_blank" class="website-link">
                {{ form.website }}
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
              <p v-else class="info-item__value">—</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-item__icon info-item__icon--violet">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="info-item__content">
              <p class="info-item__label">Owner Email</p>
              <p class="info-item__value">{{ form.email || '—' }}</p>
            </div>
          </div>

        </div>
      </div>

      <!-- ── Edit Mode ──────────────────────────────────── -->
      <div v-else class="info-card" key="edit">

        <div class="info-card__header">
          <div class="info-card__header-icon info-card__header-icon--indigo">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
          </div>
          <span class="info-card__header-title">Edit Company Information</span>
        </div>

        <div class="edit-body">
          <div class="form-grid">

            <div class="form-group">
              <label class="form-label">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Company Name
              </label>
              <input v-model="form.name" class="form-input" placeholder="Acme Corporation" />
            </div>

            <div class="form-group">
              <label class="form-label">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Industry
              </label>
              <input v-model="form.industry" class="form-input" placeholder="e.g. Technology" />
            </div>

            <div class="form-group form-group--full">
              <label class="form-label">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Address
              </label>
              <input v-model="form.address" class="form-input" placeholder="123 Main St, City, Country" />
            </div>

            <div class="form-group">
              <label class="form-label">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                Website
              </label>
              <input v-model="form.website" class="form-input" placeholder="https://company.com" type="url" />
            </div>

            <div class="form-group">
              <label class="form-label">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Owner Email
              </label>
              <input v-model="form.email" class="form-input" placeholder="owner@company.com" type="email" />
            </div>
          </div>

          <!-- Error -->
          <div v-if="error" class="error-banner">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ error }}
          </div>

          <!-- Footer -->
          <div class="edit-footer">
            <button class="btn btn--ghost" @click="cancelEdit">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Cancel
            </button>
            <button class="btn btn--primary" @click="save" :disabled="saving">
              <svg v-if="saving" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" class="spin">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <svg v-else width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const loading = ref(false)
const saving  = ref(false)
const editing = ref(false)
const error   = ref<string | null>(null)

const form     = ref({ name: '', industry: '', address: '', website: '', email: '' })
const original = ref({ name: '', industry: '', address: '', website: '', email: '' })

const fetchCompany = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get('/company/profile')
    const c = res.data?.data || {}
    form.value = {
      name:     c.name       || '',
      industry: c.industry   || '',
      address:  c.address    || '',
      website:  c.website    || '',
      email:    c.owner?.email || '',
    }
    original.value = { ...form.value }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load company'
  } finally {
    loading.value = false
  }
}

const cancelEdit = () => {
  editing.value = false
  error.value = null
  form.value = { ...original.value }
}

const save = async () => {
  saving.value = true
  error.value = null
  try {
    await api.put('/company/profile', {
      name:     form.value.name,
      industry: form.value.industry,
      address:  form.value.address,
      website:  form.value.website  || null,
      email:    form.value.email    || null,
    })
    editing.value = false
    await fetchCompany()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to update company'
  } finally {
    saving.value = false
  }
}

onMounted(fetchCompany)
</script>

<style scoped>
/* ── Root ───────────────────────────────────────────── */
.company-root {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ── Header ─────────────────────────────────────────── */
.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  animation: fadeSlideDown 0.4s ease both;
}

.header-left  { display: flex; flex-direction: column; gap: 0.35rem; }

.header-eyebrow {
  display: flex; align-items: center; gap: 0.45rem;
  font-size: 0.7rem; font-weight: 600;
  letter-spacing: 0.13em; text-transform: uppercase;
  color: #94a3b8;
}

.eyebrow-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #22d3ee);
}

.page-title {
  font-size: 1.75rem; font-weight: 800;
  letter-spacing: -0.03em; color: #0f172a; line-height: 1.1;
}

.title-bar {
  width: 2.8rem; height: 3px; border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

.header-actions { display: flex; gap: 0.5rem; }

/* ── Buttons ─────────────────────────────────────────── */
.btn {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.55rem 1.1rem;
  font-size: 0.82rem; font-weight: 600;
  border-radius: 0.65rem; cursor: pointer; border: none;
  transition: background 0.18s, box-shadow 0.18s, transform 0.18s;
}
.btn:active { transform: scale(0.97); }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }

.btn--primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  box-shadow: 0 2px 8px rgba(99,102,241,0.3);
}
.btn--primary:hover:not(:disabled) { box-shadow: 0 4px 14px rgba(99,102,241,0.4); }

.btn--ghost {
  background: #f1f5f9; color: #475569;
  border: 1px solid #e2e8f0;
}
.btn--ghost:hover:not(:disabled) { background: #e2e8f0; }

/* ── Info Card ───────────────────────────────────────── */
.info-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.45s ease 0.08s both;
}

/* Company Identity */
.company-identity {
  display: flex; align-items: center; gap: 1rem;
  padding: 1.4rem 1.5rem 1.2rem;
}

.company-logo {
  flex-shrink: 0;
  width: 3.2rem; height: 3.2rem;
  border-radius: 0.9rem;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  font-size: 1.3rem; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 14px rgba(99,102,241,0.3);
}

.company-name {
  font-size: 1.25rem; font-weight: 800;
  letter-spacing: -0.02em; color: #0f172a;
}

.company-industry {
  display: flex; align-items: center; gap: 0.35rem;
  font-size: 0.8rem; font-weight: 500; color: #94a3b8;
  margin-top: 0.2rem;
}

.card-divider { height: 1px; background: #f1f5f9; margin: 0; }

/* Card Section Header */
.info-card__header {
  display: flex; align-items: center; gap: 0.6rem;
  padding: 0.85rem 1.4rem;
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
}

.info-card__header-icon {
  width: 1.9rem; height: 1.9rem; border-radius: 0.5rem;
  display: flex; align-items: center; justify-content: center;
}
.info-card__header-icon--blue   { background: #eff6ff; color: #3b82f6; }
.info-card__header-icon--indigo { background: #eef2ff; color: #6366f1; }

.info-card__header-title {
  font-size: 0.875rem; font-weight: 700; color: #1e293b;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

@media (max-width: 560px) { .info-grid { grid-template-columns: 1fr; } }

.info-item {
  display: flex; align-items: flex-start; gap: 0.85rem;
  padding: 1.15rem 1.4rem;
  border-bottom: 1px solid #f8fafc;
  transition: background 0.15s;
}
.info-item:hover { background: #fafbff; }
.info-item:last-child { border-bottom: none; }

.info-item--full {
  grid-column: 1 / -1;
}

@media (min-width: 560px) {
  .info-item:nth-child(odd):not(.info-item--full)  { border-right: 1px solid #f1f5f9; }
  .info-item:nth-last-child(-n+2):not(.info-item--full) { border-bottom: none; }
}

.info-item__icon {
  flex-shrink: 0; width: 2.1rem; height: 2.1rem;
  border-radius: 0.55rem;
  display: flex; align-items: center; justify-content: center;
  margin-top: 0.05rem;
}
.info-item__icon--indigo { background: #eef2ff; color: #6366f1; }
.info-item__icon--emerald{ background: #ecfdf5; color: #059669; }
.info-item__icon--amber  { background: #fffbeb; color: #d97706; }
.info-item__icon--sky    { background: #f0f9ff; color: #0284c7; }
.info-item__icon--violet { background: #f5f3ff; color: #7c3aed; }

.info-item__content { display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }

.info-item__label {
  font-size: 0.68rem; font-weight: 600;
  letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8;
}

.info-item__value {
  font-size: 0.9rem; font-weight: 600; color: #1e293b; word-break: break-word;
}

.website-link {
  display: inline-flex; align-items: center; gap: 0.3rem;
  font-size: 0.9rem; font-weight: 600;
  color: #6366f1; text-decoration: none;
  transition: color 0.15s;
  word-break: break-all;
}
.website-link:hover { color: #4338ca; }

/* ── Edit Form ───────────────────────────────────────── */
.edit-body { padding: 1.4rem 1.5rem; display: flex; flex-direction: column; gap: 1.25rem; }

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
@media (max-width: 520px) { .form-grid { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group--full { grid-column: 1 / -1; }

.form-label {
  display: flex; align-items: center; gap: 0.35rem;
  font-size: 0.72rem; font-weight: 700;
  letter-spacing: 0.08em; text-transform: uppercase; color: #64748b;
}

.form-input {
  width: 100%;
  padding: 0.6rem 0.85rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.6rem;
  font-size: 0.875rem; color: #1e293b;
  background: #fff; outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  box-sizing: border-box;
}
.form-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}

.error-banner {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 0.7rem;
  background: #fff1f2; border: 1px solid #fecdd3;
  color: #be123c; font-size: 0.82rem; font-weight: 500;
}

.edit-footer {
  display: flex; justify-content: flex-end; gap: 0.5rem;
  padding-top: 0.5rem;
  border-top: 1px solid #f1f5f9;
}

/* ── Skeleton ────────────────────────────────────────── */
.skeleton-header {
  height: 5rem;
  background: linear-gradient(90deg, #f1f5f9 25%, #e9edf2 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}

.skeleton-body { padding: 1.2rem 1.4rem; display: flex; flex-direction: column; gap: 1rem; }

.skeleton-row { display: flex; align-items: center; gap: 0.85rem; }

.skeleton-icon {
  flex-shrink: 0; width: 2.1rem; height: 2.1rem; border-radius: 0.55rem;
  background: #f1f5f9;
}

.skeleton-lines { flex: 1; display: flex; flex-direction: column; gap: 0.4rem; }

.skeleton-line {
  height: 0.75rem; border-radius: 9999px;
  background: linear-gradient(90deg, #f1f5f9 25%, #e9edf2 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
.skeleton-line--short { width: 35%; }

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ── Animations ──────────────────────────────────────── */
.spin {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: none; }
}
</style>