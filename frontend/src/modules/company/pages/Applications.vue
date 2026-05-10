<template>
  <div class="apps-root">

    <!-- ── Header ── -->
    <div class="page-header">
      <div class="header-left">
        <div class="header-eyebrow">
          <span class="eyebrow-dot" />
          Company
        </div>
        <h1 class="page-title">Job Applications</h1>
        <p class="page-subtitle">Review, manage and track all incoming applicants.</p>
        <div class="title-bar" />
      </div>
      <div class="header-actions">
        <button
          class="toggle-btn"
          :class="showArchived ? 'toggle-btn--active' : 'toggle-btn--inactive'"
          @click="showArchived = false"
        >
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Active
        </button>
        <button
          class="toggle-btn"
          :class="!showArchived ? 'toggle-btn--active' : 'toggle-btn--inactive'"
          @click="showArchived = true"
        >
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
          </svg>
          Archived
        </button>
      </div>
    </div>

    <!-- ── Table Card ── -->
    <div class="table-card">
      <div class="table-wrap">
        <table class="apps-table">
          <thead>
            <tr>
              <th>
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Applicant
              </th>
              <th>
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Vacancy
              </th>
              <th>
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Status
              </th>
              <th class="th-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(app, i) in applications"
              :key="app.id"
              class="table-row"
              :style="{ animationDelay: `${i * 45}ms` }"
            >
              <!-- Applicant -->
              <td class="td-applicant">
                <div class="applicant-cell">
                  <div class="applicant-avatar" :style="{ background: avatarGradient(app.user?.name) }">
                    {{ (app.user?.name || '?')[0].toUpperCase() }}
                  </div>
                  <span class="applicant-name">{{ app.user?.name || '—' }}</span>
                </div>
              </td>

              <!-- Vacancy -->
              <td class="td-vacancy">
                <span class="vacancy-tag">{{ app.jobvacancy?.title || '—' }}</span>
              </td>

              <!-- Status -->
              <td class="td-status">
                <span class="status-badge" :class="statusClass(app.status)">
                  <span class="status-dot" />
                  {{ capitalize(app.status) || '—' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="td-actions">
                <div class="action-group">
                  <button class="act-btn act-btn--slate" @click="viewApplication(app)" title="View">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View
                  </button>

                  <template v-if="!showArchived">
                    <button class="act-btn act-btn--indigo" @click="openEditStatusModal(app)" title="Edit Status">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Edit
                    </button>
                    <button class="act-btn act-btn--emerald" @click="acceptApplication(app)" title="Accept">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Accept
                    </button>
                    <button class="act-btn act-btn--amber" @click="rejectApplication(app)" title="Reject">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Reject
                    </button>
                    <button class="act-btn act-btn--rose" @click="archiveApplication(app)" title="Archive">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                      </svg>
                      Archive
                    </button>
                  </template>
                  <template v-else>
                    <button class="act-btn act-btn--emerald" @click="restoreApplication(app)">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                      Restore
                    </button>
                  </template>
                </div>
              </td>
            </tr>

            <tr v-if="applications.length === 0">
              <td colspan="4" class="td-empty">
                <div class="empty-state">
                  <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <p>No applications found.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <span class="pagination__info">
          Page <strong>{{ pagination.current_page }}</strong> of <strong>{{ pagination.last_page }}</strong>
        </span>
        <div class="pagination__btns">
          <button class="page-btn" :disabled="pagination.current_page <= 1" @click="prevPage">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Prev
          </button>
          <button class="page-btn" :disabled="pagination.current_page >= pagination.last_page" @click="nextPage">
            Next
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- ── View Modal ── -->
    <Transition name="modal">
      <div v-if="selectedApplication" class="modal-backdrop" @click.self="selectedApplication = null">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header__icon modal-header__icon--blue">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </div>
            <div>
              <h3 class="modal-title">Application Details</h3>
              <p class="modal-subtitle">Read-only overview</p>
            </div>
            <button class="modal-close" @click="selectedApplication = null">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="detail-row">
              <span class="detail-key">Applicant</span>
              <span class="detail-val">{{ selectedApplication.user?.name || '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-key">Email</span>
              <span class="detail-val">{{ selectedApplication.user?.email || '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-key">Vacancy</span>
              <span class="detail-val">{{ selectedApplication.jobvacancy?.title || '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-key">Status</span>
              <span class="status-badge" :class="statusClass(selectedApplication.status)">
                <span class="status-dot" />
                {{ capitalize(selectedApplication.status) || '—' }}
              </span>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-secondary" @click="selectedApplication = null">Close</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Edit Status Modal ── -->
    <Transition name="modal">
      <div v-if="showEditStatusModal" class="modal-backdrop" @click.self="closeEditStatusModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header__icon modal-header__icon--indigo">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </div>
            <div>
              <h3 class="modal-title">Edit Application Status</h3>
              <p class="modal-subtitle">{{ statusTargetApplication?.user?.name || '—' }}</p>
            </div>
            <button class="modal-close" @click="closeEditStatusModal">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="field-group">
              <label class="field-label">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Status
              </label>
              <div class="select-wrap">
                <select v-model="selectedStatus" class="field-select">
                  <option value="pending">Pending</option>
                  <option value="accepted">Accepted</option>
                  <option value="rejected">Rejected</option>
                </select>
                <svg class="select-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-secondary" @click="closeEditStatusModal">Cancel</button>
            <button class="btn-primary" @click="submitStatusUpdate">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>
              Update
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import api from '@/api/axios'

const applications           = ref<any[]>([])
const selectedApplication    = ref<any | null>(null)
const showArchived           = ref(false)
const showEditStatusModal    = ref(false)
const statusTargetApplication = ref<any | null>(null)
const selectedStatus         = ref('pending')
const currentPage            = ref(1)
const pagination             = ref({ current_page: 1, last_page: 1 })

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
  return GRADIENTS[(name.charCodeAt(0) || 0) % GRADIENTS.length]
}

function capitalize(val: string) {
  if (!val) return ''
  return val.charAt(0).toUpperCase() + val.slice(1)
}

function statusClass(status: string) {
  if (status === 'accepted') return 'status-badge--emerald'
  if (status === 'rejected') return 'status-badge--rose'
  return 'status-badge--amber'
}

const load = async () => {
  const res     = await api.get('/applications', {
    params: { archived: showArchived.value ? 'true' : 'false', page: currentPage.value },
  })
  const payload = res.data?.data || {}
  applications.value = payload.data || payload || []
  pagination.value   = { current_page: payload.current_page || 1, last_page: payload.last_page || 1 }
}

const viewApplication = async (application: any) => {
  const res = await api.get(`/applications/${application.id}`)
  selectedApplication.value = res.data?.data || application
}

const openEditStatusModal = (application: any) => {
  statusTargetApplication.value = application
  selectedStatus.value          = application.status || 'pending'
  showEditStatusModal.value     = true
}

const closeEditStatusModal = () => {
  showEditStatusModal.value     = false
  statusTargetApplication.value = null
  selectedStatus.value          = 'pending'
}

const submitStatusUpdate = async () => {
  if (!statusTargetApplication.value) return
  await api.put(`/applications/${statusTargetApplication.value.id}`, { status: selectedStatus.value })
  closeEditStatusModal()
  await load()
}

const acceptApplication = async (application: any) => {
  await api.post(`/applications/${application.id}/accept`)
  await load()
}

const rejectApplication = async (application: any) => {
  await api.post(`/applications/${application.id}/reject`)
  await load()
}

const archiveApplication = async (application: any) => {
  if (!confirm('Archive this application?')) return
  await api.delete(`/applications/${application.id}`)
  await load()
}

const restoreApplication = async (application: any) => {
  if (!confirm('Restore this application?')) return
  await api.post(`/applications/${application.id}/restore`)
  await load()
}

const nextPage = () => { if (currentPage.value < pagination.value.last_page) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

watch([showArchived, currentPage], load)
onMounted(load)
</script>

<style scoped>
/* ─── Root ──────────────────────────────────────────── */
.apps-root {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ─── Header ─────────────────────────────────────────── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  animation: fadeSlideDown 0.4s ease both;
}

.header-left { display: flex; flex-direction: column; gap: 0.3rem; }

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
  width: 6px; height: 6px;
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

.page-subtitle { font-size: 0.82rem; color: #94a3b8; font-weight: 500; }

.title-bar {
  margin-top: 0.35rem;
  width: 2.8rem; height: 3px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

/* Toggle buttons */
.header-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-top: 0.3rem;
}

.toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
  font-weight: 600;
  border-radius: 0.65rem;
  border: 1.5px solid transparent;
  cursor: pointer;
  transition: background 0.17s, color 0.17s, border-color 0.17s, transform 0.15s;
}

.toggle-btn--inactive {
  background: #fff;
  border-color: #e2e8f0;
  color: #64748b;
}

.toggle-btn--inactive:hover { background: #f8fafc; }

.toggle-btn--active {
  background: #0f172a;
  border-color: #0f172a;
  color: #fff;
}

/* ─── Table Card ─────────────────────────────────────── */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.4rem;
  overflow: hidden;
  box-shadow: 0 1px 6px rgba(0,0,0,0.06);
  animation: fadeSlideDown 0.45s ease 0.08s both;
}

.table-wrap { overflow-x: auto; }

.apps-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

/* TH */
.apps-table thead tr {
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
}

.apps-table th {
  padding: 0.8rem 1.2rem;
  text-align: left;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94a3b8;
  white-space: nowrap;
  display: table-cell;
}

.apps-table th svg { display: inline; vertical-align: middle; margin-right: 0.35rem; margin-top: -2px; }

.th-right { text-align: right; }

/* TR */
.table-row {
  border-bottom: 1px solid #f8fafc;
  animation: rowIn 0.35s ease both;
  transition: background 0.15s;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #fafbff; }

.apps-table td { padding: 0.85rem 1.2rem; vertical-align: middle; }

/* Applicant cell */
.applicant-cell { display: flex; align-items: center; gap: 0.75rem; }

.applicant-avatar {
  width: 2rem; height: 2rem;
  border-radius: 50%;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.applicant-name { font-weight: 600; color: #1e293b; }

/* Vacancy tag */
.vacancy-tag {
  display: inline-block;
  font-size: 0.78rem;
  font-weight: 600;
  color: #475569;
  background: #f1f5f9;
  padding: 0.22rem 0.65rem;
  border-radius: 0.5rem;
  white-space: nowrap;
  max-width: 18ch;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Status badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.25rem 0.7rem;
  border-radius: 9999px;
  font-size: 0.74rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--emerald { background: #ecfdf5; color: #065f46; }
.status-badge--rose    { background: #fff1f2; color: #be123c; }
.status-badge--amber   { background: #fffbeb; color: #92400e; }

.status-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-badge--emerald .status-dot { background: #10b981; }
.status-badge--rose    .status-dot { background: #f43f5e; }
.status-badge--amber   .status-dot { background: #f59e0b; }

/* Actions */
.td-actions { text-align: right; }

.action-group {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 0.35rem;
}

.act-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.7rem;
  font-size: 0.74rem;
  font-weight: 600;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: opacity 0.15s, transform 0.15s;
  white-space: nowrap;
}

.act-btn:hover { opacity: 0.82; transform: translateY(-1px); }

.act-btn--slate  { background: #f1f5f9; color: #475569; }
.act-btn--indigo { background: #eef2ff; color: #4338ca; }
.act-btn--emerald{ background: #ecfdf5; color: #065f46; }
.act-btn--amber  { background: #fffbeb; color: #92400e; }
.act-btn--rose   { background: #fff1f2; color: #be123c; }

/* Empty */
.td-empty { padding: 3rem 1rem !important; }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  color: #cbd5e1;
  font-size: 0.85rem;
}

/* Pagination */
.pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem 1.2rem;
  border-top: 1px solid #f1f5f9;
  gap: 1rem;
}

.pagination__info { font-size: 0.8rem; color: #64748b; }
.pagination__info strong { color: #1e293b; font-weight: 700; }

.pagination__btns { display: flex; gap: 0.5rem; }

.page-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.4rem 0.9rem;
  font-size: 0.78rem;
  font-weight: 600;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.6rem;
  background: #fff;
  color: #475569;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
}

.page-btn:hover:not(:disabled) { background: #f8fafc; border-color: #cbd5e1; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* ─── Modal ───────────────────────────────────────────── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 50;
  background: rgba(15,23,42,0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.25rem;
  backdrop-filter: blur(2px);
}

.modal-box {
  width: 100%;
  max-width: 32rem;
  background: #fff;
  border-radius: 1.4rem;
  overflow: hidden;
  box-shadow: 0 24px 60px rgba(0,0,0,0.18);
}

.modal-header {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 1.2rem 1.4rem;
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
}

.modal-header__icon {
  flex-shrink: 0;
  width: 2.2rem; height: 2.2rem;
  border-radius: 0.6rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 0.1rem;
}

.modal-header__icon--blue   { background: #eff6ff; color: #2563eb; }
.modal-header__icon--indigo { background: #eef2ff; color: #4338ca; }

.modal-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.3;
}

.modal-subtitle { font-size: 0.78rem; color: #94a3b8; margin-top: 0.15rem; }

.modal-close {
  margin-left: auto;
  width: 2rem; height: 2rem;
  border-radius: 0.5rem;
  border: 1.5px solid #e2e8f0;
  background: #fff;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.modal-close:hover { background: #f1f5f9; color: #475569; }

.modal-body {
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}

.detail-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding-bottom: 0.9rem;
  border-bottom: 1px solid #f8fafc;
}

.detail-row:last-child { border-bottom: none; padding-bottom: 0; }

.detail-key {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  color: #94a3b8;
  white-space: nowrap;
}

.detail-val {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1e293b;
  text-align: right;
}

/* Field inside modal */
.field-group { display: flex; flex-direction: column; gap: 0.45rem; }

.field-label {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.74rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  color: #64748b;
}

.select-wrap { position: relative; }

.field-select {
  width: 100%;
  appearance: none;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.7rem 2.5rem 0.7rem 0.95rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: border-color 0.18s, box-shadow 0.18s;
}

.field-select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}

.select-chevron {
  position: absolute;
  right: 0.85rem;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  padding: 1rem 1.4rem;
  border-top: 1px solid #f1f5f9;
  background: #fafbfc;
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.55rem 1.1rem;
  font-size: 0.82rem;
  font-weight: 600;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.65rem;
  background: #fff;
  color: #475569;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-secondary:hover { background: #f1f5f9; }

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.55rem 1.2rem;
  font-size: 0.82rem;
  font-weight: 700;
  border: none;
  border-radius: 0.65rem;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(99,102,241,0.3);
  transition: opacity 0.15s, transform 0.15s;
}

.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

/* ─── Vue Transitions ─────────────────────────────────── */
.modal-enter-active { animation: modalIn 0.25s ease; }
.modal-leave-active { animation: modalIn 0.2s ease reverse; }

/* ─── Animations ──────────────────────────────────────── */
@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes rowIn {
  from { opacity: 0; transform: translateX(-6px); }
  to   { opacity: 1; transform: translateX(0); }
}

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.96) translateY(10px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}
</style>