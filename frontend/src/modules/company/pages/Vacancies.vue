<template>
  <AppLayout>
    <div class="vac-root">

      <!-- ── Header ──────────────────────────────────────── -->
      <div class="page-header">
        <div class="header-left">
          <div class="header-eyebrow">
            <span class="eyebrow-dot" />
            Company
          </div>
          <h1 class="page-title">
            {{ showArchived ? 'Archived Vacancies' : 'Job Vacancies' }}
          </h1>
          <div class="title-bar" />
        </div>

        <div class="header-actions">
          <template v-if="showArchived">
            <button class="btn btn--ghost" @click="showArchived = false">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
              Back to Active
            </button>
          </template>
          <template v-else>
            <button class="btn btn--ghost" @click="showArchived = true">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" />
              </svg>
              Archived
            </button>
            <button class="btn btn--primary" @click="openCreate">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              New Vacancy
            </button>
          </template>
        </div>
      </div>

      <!-- ── Stats Row ──────────────────────────────────── -->
      <div class="stats-row" v-if="!showArchived">
        <div class="stat-chip stat-chip--indigo">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span>{{ vacancies.length }} Open</span>
        </div>
        <div class="stat-chip stat-chip--emerald" v-if="fullTimeCount">
          <span class="stat-dot stat-dot--emerald" />
          <span>{{ fullTimeCount }} Full-time</span>
        </div>
        <div class="stat-chip stat-chip--violet" v-if="remoteCount">
          <span class="stat-dot stat-dot--violet" />
          <span>{{ remoteCount }} Remote</span>
        </div>
      </div>

      <!-- ── Table Card ─────────────────────────────────── -->
      <div class="table-card">
        <div class="table-wrap">
          <table class="vac-table">
            <thead>
              <tr>
                <th>Position</th>
                <th>Location</th>
                <th>Type</th>
                <th>Salary</th>
                <th class="th-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(job, idx) in vacancies"
                :key="job.id"
                class="vac-row"
                :style="{ animationDelay: idx * 45 + 'ms' }"
              >
                <!-- Title -->
                <td class="td-title">
                  <div class="job-icon">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <span class="job-title">{{ job.title || '—' }}</span>
                </td>

                <!-- Location -->
                <td>
                  <div class="td-with-icon" v-if="job.location">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="td-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ job.location }}
                  </div>
                  <span v-else class="td-empty">—</span>
                </td>

                <!-- Type -->
                <td>
                  <span class="type-badge" :class="typeClass(job.type)">
                    {{ formatType(job.type) }}
                  </span>
                </td>

                <!-- Salary -->
                <td>
                  <span v-if="job.salary" class="salary-value">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="td-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ job.salary }}
                  </span>
                  <span v-else class="td-empty">—</span>
                </td>

                <!-- Actions -->
                <td class="td-actions">
                  <div class="action-group">
                    <button class="action-btn action-btn--view" title="View" @click="viewVacancy(job)">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <template v-if="!showArchived">
                      <button class="action-btn action-btn--edit" title="Edit" @click="openEdit(job)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                      </button>
                      <button class="action-btn action-btn--archive" title="Archive" @click="archiveVacancy(job)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4" />
                        </svg>
                      </button>
                    </template>
                    <template v-else>
                      <button class="action-btn action-btn--restore" title="Restore" @click="restoreVacancy(job)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                      </button>
                    </template>
                  </div>
                </td>
              </tr>

              <tr v-if="vacancies.length === 0">
                <td colspan="5" class="td-empty-state">
                  <div class="empty-state">
                    <div class="empty-icon">
                      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <p>No vacancies found.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ── Pagination ──────────────────────────────── -->
        <div class="pagination">
          <span class="pagination__info">
            Page <strong>{{ pagination.current_page }}</strong> of <strong>{{ pagination.last_page }}</strong>
          </span>
          <div class="pagination__btns">
            <button class="pag-btn" :disabled="pagination.current_page <= 1" @click="prevPage">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
              Prev
            </button>
            <button class="pag-btn" :disabled="pagination.current_page >= pagination.last_page" @click="nextPage">
              Next
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════
           MODAL: Create / Edit Vacancy
      ══════════════════════════════════════════════════ -->
      <Teleport to="body">
        <Transition name="modal">
          <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
            <div class="modal-box">

              <div class="modal-header">
                <div class="modal-header-icon" :class="editing ? 'modal-header-icon--cyan' : 'modal-header-icon--indigo'">
                  <svg v-if="editing" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                  </svg>
                  <svg v-else width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <div>
                  <h3 class="modal-title">{{ editing ? 'Edit Vacancy' : 'New Vacancy' }}</h3>
                  <p class="modal-subtitle">{{ editing ? 'Update job listing details' : 'Post a new job opening' }}</p>
                </div>
                <button class="modal-close" @click="closeModal">
                  <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="modal-body">
                <div class="form-grid">

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      Job Title
                    </label>
                    <input v-model="form.title" class="form-input" placeholder="e.g. Senior Developer" />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      Location
                    </label>
                    <input v-model="form.location" class="form-input" placeholder="e.g. Amman, Jordan" />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Type
                    </label>
                    <select v-model="form.type" class="form-input form-select">
                      <option value="full-time">Full-time</option>
                      <option value="contract">Contract</option>
                      <option value="hybrid">Hybrid</option>
                      <option value="remote">Remote</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Salary
                    </label>
                    <input v-model="form.salary" class="form-input" placeholder="e.g. $3,000 – $4,500 / mo" />
                  </div>

                  <div class="form-group form-group--full">
                    <label class="form-label">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                      </svg>
                      Description
                    </label>
                    <textarea v-model="form.description" rows="4" class="form-input form-textarea" placeholder="Describe the role, responsibilities, and requirements…" />
                  </div>

                </div>
              </div>

              <div class="modal-footer">
                <button class="btn btn--ghost" @click="closeModal">Cancel</button>
                <button class="btn btn--primary" @click="saveVacancy">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ editing ? 'Save Changes' : 'Post Vacancy' }}
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

      <!-- ══════════════════════════════════════════════════
           MODAL: View Vacancy
      ══════════════════════════════════════════════════ -->
      <Teleport to="body">
        <Transition name="modal">
          <div v-if="selectedVacancy" class="modal-backdrop" @click.self="selectedVacancy = null">
            <div class="modal-box">

              <div class="modal-header">
                <div class="modal-header-icon modal-header-icon--indigo">
                  <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="modal-title">{{ selectedVacancy.title }}</h3>
                  <div class="view-meta">
                    <span v-if="selectedVacancy.location" class="view-meta__chip">
                      <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      {{ selectedVacancy.location }}
                    </span>
                    <span class="type-badge type-badge--sm" :class="typeClass(selectedVacancy.type)">
                      {{ formatType(selectedVacancy.type) }}
                    </span>
                    <span v-if="selectedVacancy.salary" class="view-meta__chip">
                      {{ selectedVacancy.salary }}
                    </span>
                  </div>
                </div>
                <button class="modal-close" @click="selectedVacancy = null">
                  <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="modal-body">
                <div v-if="selectedVacancy.description" class="view-description">
                  {{ selectedVacancy.description }}
                </div>
                <p v-else class="view-empty">No description provided.</p>
              </div>

              <div class="modal-footer">
                <button class="btn btn--ghost" @click="selectedVacancy = null">Close</button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const vacancies    = ref<any[]>([])
const showArchived = ref(false)
const showModal    = ref(false)
const editing      = ref<any | null>(null)
const selectedVacancy = ref<any | null>(null)
const currentPage  = ref(1)
const pagination   = ref({ current_page: 1, last_page: 1 })

const form = ref({
  title: '', location: '', type: 'full-time', salary: '', description: '',
})

// ── Computed Stats ────────────────────────────────────
const fullTimeCount = computed(() => vacancies.value.filter(j => j.type === 'full-time').length)
const remoteCount   = computed(() => vacancies.value.filter(j => j.type === 'remote').length)

// ── Helpers ───────────────────────────────────────────
function formatType(type: string) {
  const map: Record<string, string> = {
    'full-time': 'Full-time',
    'contract':  'Contract',
    'hybrid':    'Hybrid',
    'remote':    'Remote',
  }
  return map[type] || type
}

function typeClass(type: string) {
  const map: Record<string, string> = {
    'full-time': 'type-badge--emerald',
    'contract':  'type-badge--amber',
    'hybrid':    'type-badge--sky',
    'remote':    'type-badge--violet',
  }
  return map[type] || 'type-badge--gray'
}

// ── Data ──────────────────────────────────────────────
const load = async () => {
  const res = await api.get('/vacancies', {
    params: { archived: showArchived.value ? 'true' : 'false', page: currentPage.value },
  })
  const payload = res.data?.data || {}
  vacancies.value = payload.data || payload || []
  pagination.value = {
    current_page: payload.current_page || 1,
    last_page:    payload.last_page    || 1,
  }
}

// ── Modal ─────────────────────────────────────────────
const openCreate = () => {
  editing.value = null
  form.value = { title: '', location: '', type: 'full-time', salary: '', description: '' }
  showModal.value = true
}
const openEdit = (job: any) => {
  editing.value = job
  form.value = {
    title:       job.title       || '',
    location:    job.location    || '',
    type:        job.type        || 'full-time',
    salary:      job.salary      || '',
    description: job.description || '',
  }
  showModal.value = true
}
const closeModal = () => { showModal.value = false }

const saveVacancy = async () => {
  const payload = { ...form.value }
  if (editing.value) {
    await api.put(`/job-vacancies/${editing.value.id}`, payload)
  } else {
    await api.post('/job-vacancies', payload)
  }
  closeModal()
  await load()
}

// ── View ──────────────────────────────────────────────
const viewVacancy = async (job: any) => {
  const res = await api.get(`/job-vacancies/${job.id}`)
  selectedVacancy.value = res.data?.data || job
}

// ── Archive / Restore ────────────────────────────────
const archiveVacancy = async (job: any) => {
  if (!confirm(`Archive "${job.title}"?`)) return
  await api.delete(`/job-vacancies/${job.id}`)
  await load()
}
const restoreVacancy = async (job: any) => {
  if (!confirm(`Restore "${job.title}"?`)) return
  await api.post(`/job-vacancies/${job.id}/restore`)
  await load()
}

// ── Pagination ────────────────────────────────────────
const nextPage = () => { if (currentPage.value < pagination.value.last_page) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

watch([showArchived, currentPage], load)
onMounted(load)
</script>

<style scoped>
/* ── Root ───────────────────────────────────────────── */
.vac-root {
  display: flex; flex-direction: column; gap: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ── Header ─────────────────────────────────────────── */
.page-header {
  display: flex; align-items: flex-end; justify-content: space-between;
  gap: 1rem; flex-wrap: wrap;
  animation: fadeSlideDown 0.4s ease both;
}

.header-left { display: flex; flex-direction: column; gap: 0.35rem; }

.header-eyebrow {
  display: flex; align-items: center; gap: 0.45rem;
  font-size: 0.7rem; font-weight: 600;
  letter-spacing: 0.13em; text-transform: uppercase; color: #94a3b8;
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

.header-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

/* ── Buttons ─────────────────────────────────────────── */
.btn {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.55rem 1.1rem;
  font-size: 0.82rem; font-weight: 600;
  border-radius: 0.65rem; cursor: pointer; border: none;
  transition: background 0.18s, box-shadow 0.18s, transform 0.18s;
}
.btn:active { transform: scale(0.97); }

.btn--primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff; box-shadow: 0 2px 8px rgba(99,102,241,0.3);
}
.btn--primary:hover { box-shadow: 0 4px 14px rgba(99,102,241,0.4); }

.btn--ghost {
  background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
}
.btn--ghost:hover { background: #e2e8f0; }

/* ── Stats Row ───────────────────────────────────────── */
.stats-row {
  display: flex; gap: 0.6rem; flex-wrap: wrap;
  animation: fadeSlideDown 0.4s ease 0.06s both;
}

.stat-chip {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.35rem 0.85rem; border-radius: 9999px;
  font-size: 0.75rem; font-weight: 600;
}
.stat-chip--indigo { background: #eef2ff; color: #4338ca; }
.stat-chip--emerald{ background: #ecfdf5; color: #065f46; }
.stat-chip--violet { background: #f5f3ff; color: #6d28d9; }

.stat-dot { width: 6px; height: 6px; border-radius: 50%; }
.stat-dot--emerald { background: #10b981; }
.stat-dot--violet  { background: #8b5cf6; }

/* ── Table Card ──────────────────────────────────────── */
.table-card {
  background: #fff; border: 1px solid #e2e8f0;
  border-radius: 1.25rem; overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.45s ease 0.1s both;
}

.table-wrap { overflow-x: auto; }

.vac-table { width: 100%; border-collapse: collapse; font-size: 0.855rem; }

.vac-table thead tr {
  background: #f8fafc; border-bottom: 1px solid #f1f5f9;
}

.vac-table th {
  padding: 0.8rem 1.1rem; text-align: left;
  font-size: 0.68rem; font-weight: 700;
  letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8;
  white-space: nowrap;
}
.th-actions { text-align: right; }

/* ── Rows ────────────────────────────────────────────── */
.vac-row {
  border-bottom: 1px solid #f8fafc;
  animation: rowIn 0.35s ease both;
  transition: background 0.15s;
}
.vac-row:last-child { border-bottom: none; }
.vac-row:hover { background: #fafbff; }

@keyframes rowIn {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: none; }
}

.vac-table td { padding: 0.85rem 1.1rem; }

/* Title cell */
.td-title {
  display: flex; align-items: center; gap: 0.65rem; white-space: nowrap;
}

.job-icon {
  flex-shrink: 0; width: 2rem; height: 2rem;
  border-radius: 0.5rem; background: #eef2ff; color: #6366f1;
  display: flex; align-items: center; justify-content: center;
}

.job-title { font-weight: 600; color: #1e293b; }

/* Location / salary with icon */
.td-with-icon {
  display: flex; align-items: center; gap: 0.4rem; color: #64748b;
}
.td-icon { color: #94a3b8; flex-shrink: 0; }

/* Salary */
.salary-value {
  display: inline-flex; align-items: center; gap: 0.35rem;
  font-weight: 600; color: #16a34a; font-size: 0.875rem;
}

/* Type badges */
.type-badge {
  display: inline-flex; align-items: center;
  padding: 0.25rem 0.7rem; border-radius: 9999px;
  font-size: 0.74rem; font-weight: 600; white-space: nowrap;
}
.type-badge--sm { font-size: 0.7rem; padding: 0.2rem 0.55rem; }

.type-badge--emerald { background: #ecfdf5; color: #065f46; }
.type-badge--amber   { background: #fffbeb; color: #92400e; }
.type-badge--sky     { background: #f0f9ff; color: #0369a1; }
.type-badge--violet  { background: #f5f3ff; color: #6d28d9; }
.type-badge--gray    { background: #f8fafc; color: #64748b; }

.td-empty { color: #cbd5e1; font-weight: 500; }

/* Actions */
.td-actions { text-align: right; }

.action-group {
  display: flex; align-items: center; justify-content: flex-end; gap: 0.3rem;
}

.action-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 2rem; height: 2rem; border-radius: 0.5rem;
  border: none; cursor: pointer;
  transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
}
.action-btn:hover { transform: translateY(-1px); }
.action-btn:active { transform: scale(0.93); }

.action-btn--view    { background: #eff6ff; color: #2563eb; }
.action-btn--view:hover  { background: #dbeafe; box-shadow: 0 2px 6px rgba(37,99,235,0.2); }
.action-btn--edit    { background: #ecfdf5; color: #059669; }
.action-btn--edit:hover  { background: #d1fae5; box-shadow: 0 2px 6px rgba(5,150,105,0.2); }
.action-btn--archive { background: #fff1f2; color: #e11d48; }
.action-btn--archive:hover { background: #ffe4e6; box-shadow: 0 2px 6px rgba(225,29,72,0.2); }
.action-btn--restore { background: #ecfdf5; color: #059669; }
.action-btn--restore:hover { background: #d1fae5; box-shadow: 0 2px 6px rgba(5,150,105,0.2); }

/* Empty state */
.td-empty-state { padding: 3rem 1rem !important; }
.empty-state {
  display: flex; flex-direction: column; align-items: center; gap: 0.75rem;
  color: #94a3b8;
}
.empty-icon {
  width: 3.5rem; height: 3.5rem; border-radius: 1rem;
  background: #f8fafc; color: #cbd5e1;
  display: flex; align-items: center; justify-content: center;
}
.empty-state p { font-size: 0.875rem; font-weight: 500; }

/* ── Pagination ──────────────────────────────────────── */
.pagination {
  display: flex; align-items: center; justify-content: space-between;
  padding: 0.85rem 1.1rem;
  border-top: 1px solid #f1f5f9;
  background: #fafbfc;
}

.pagination__info {
  font-size: 0.8rem; color: #94a3b8; font-weight: 500;
}
.pagination__info strong { color: #475569; }

.pagination__btns { display: flex; gap: 0.4rem; }

.pag-btn {
  display: inline-flex; align-items: center; gap: 0.3rem;
  padding: 0.4rem 0.85rem;
  font-size: 0.8rem; font-weight: 600;
  border-radius: 0.55rem; border: 1.5px solid #e2e8f0;
  background: #fff; color: #475569; cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
}
.pag-btn:hover:not(:disabled) { background: #f8fafc; border-color: #6366f1; color: #6366f1; }
.pag-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* ── Modals ──────────────────────────────────────────── */
.modal-backdrop {
  position: fixed; inset: 0; z-index: 50;
  background: rgba(15,23,42,0.5);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem;
}

.modal-box {
  background: #fff; border-radius: 1.25rem;
  width: 100%; max-width: 580px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.18);
  overflow: hidden;
}

.modal-header {
  display: flex; align-items: center; gap: 0.9rem;
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid #f1f5f9; background: #fafbfc;
}

.modal-header-icon {
  flex-shrink: 0; width: 2.2rem; height: 2.2rem;
  border-radius: 0.6rem;
  display: flex; align-items: center; justify-content: center;
}
.modal-header-icon--indigo { background: #eef2ff; color: #6366f1; }
.modal-header-icon--cyan   { background: #ecfeff; color: #0891b2; }

.modal-title { font-size: 0.975rem; font-weight: 700; color: #0f172a; }
.modal-subtitle { font-size: 0.775rem; color: #94a3b8; font-weight: 500; margin-top: 0.1rem; }

.modal-close {
  margin-left: auto; flex-shrink: 0;
  width: 2rem; height: 2rem; border-radius: 0.5rem;
  border: none; background: #f1f5f9; color: #64748b;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background 0.15s;
}
.modal-close:hover { background: #e2e8f0; }

.modal-body { padding: 1.4rem 1.5rem; }
.modal-footer {
  display: flex; justify-content: flex-end; gap: 0.5rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9; background: #fafbfc;
}

/* ── Form ────────────────────────────────────────────── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 480px) { .form-grid { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group--full { grid-column: 1 / -1; }

.form-label {
  display: flex; align-items: center; gap: 0.35rem;
  font-size: 0.72rem; font-weight: 700;
  letter-spacing: 0.08em; text-transform: uppercase; color: #64748b;
}

.form-input {
  width: 100%; padding: 0.6rem 0.85rem;
  border: 1.5px solid #e2e8f0; border-radius: 0.6rem;
  font-size: 0.875rem; color: #1e293b; background: #fff; outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  box-sizing: border-box;
}
.form-input:focus {
  border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.form-select { appearance: none; cursor: pointer; }
.form-textarea { resize: vertical; min-height: 7rem; font-family: inherit; }

/* ── View Modal ──────────────────────────────────────── */
.view-meta {
  display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.3rem;
}

.view-meta__chip {
  display: inline-flex; align-items: center; gap: 0.3rem;
  font-size: 0.72rem; font-weight: 500; color: #64748b;
}

.view-description {
  font-size: 0.875rem; color: #334155; line-height: 1.7;
  white-space: pre-wrap;
}

.view-empty { font-size: 0.875rem; color: #94a3b8; font-style: italic; }

/* ── Transitions ─────────────────────────────────────── */
.modal-enter-active { animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1) both; }
.modal-leave-active { animation: modalOut 0.18s ease both; }

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.94) translateY(10px); }
  to   { opacity: 1; transform: none; }
}
@keyframes modalOut {
  to { opacity: 0; transform: scale(0.96) translateY(6px); }
}

@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: none; }
}
</style>