<template>
  <AppLayout>
    <div class="emp-root">

      <!-- ── Header ─────────────────────────────────────── -->
      <div class="page-header">
        <div class="header-left">
          <div class="header-eyebrow">
            <span class="eyebrow-dot" />
            Company
          </div>
          <h1 class="page-title">
            {{ showArchived ? 'Archived' : 'Employees' }}
          </h1>
          <div class="title-bar" />
        </div>

        <div class="header-actions">
          <template v-if="showArchived">
            <button class="btn btn--ghost" @click="showArchived = false">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
              Back to Active
            </button>
          </template>
          <template v-else>
            <button class="btn btn--ghost" @click="showArchived = true">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" />
              </svg>
              Archived
            </button>
            <button class="btn btn--primary" @click="openCreate">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              New Employee
            </button>
          </template>
        </div>
      </div>

      <!-- ── Stats Row ──────────────────────────────────── -->
      <div class="stats-row" v-if="!showArchived">
        <div class="stat-chip stat-chip--blue">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20H7a5 5 0 010-10 5 5 0 0110 0 5 5 0 010 10z" />
          </svg>
          <span>{{ employees.length }} Total</span>
        </div>
        <div class="stat-chip stat-chip--emerald">
          <span class="stat-dot stat-dot--emerald" />
          <span>{{ employees.filter(e => e.status === 'active').length }} Active</span>
        </div>
        <div class="stat-chip stat-chip--amber">
          <span class="stat-dot stat-dot--amber" />
          <span>{{ employees.filter(e => e.status === 'probation').length }} Probation</span>
        </div>
      </div>

      <!-- ── Table Card ─────────────────────────────────── -->
      <div class="table-card">
        <div class="table-wrap">
          <table class="emp-table">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Email</th>
                <th>Department</th>
                <th>Job Title</th>
                <th>Status</th>
                <th class="th-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(e, idx) in employees"
                :key="e.id"
                class="emp-row"
                :style="{ animationDelay: idx * 45 + 'ms' }"
              >
                <!-- Name + Avatar -->
                <td class="td-name">
                  <div class="avatar" :style="{ background: avatarGradient(e.user?.name) }">
                    {{ (e.user?.name || '?')[0].toUpperCase() }}
                  </div>
                  <span class="emp-name">{{ e.user?.name || '—' }}</span>
                </td>

                <!-- Email -->
                <td class="td-email">
                  <div class="td-with-icon">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" class="td-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ e.user?.email || '—' }}
                  </div>
                </td>

                <!-- Department -->
                <td>
                  <span v-if="e.department" class="dept-badge">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                    </svg>
                    {{ e.department.name }}
                  </span>
                  <span v-else class="td-empty">—</span>
                </td>

                <!-- Job Title -->
                <td>
                  <span v-if="e.job_title" class="job-tag">{{ e.job_title }}</span>
                  <span v-else class="td-empty">—</span>
                </td>

                <!-- Status -->
                <td>
                  <span class="status-badge" :class="statusClass(e.status)">
                    <span class="status-dot" />
                    {{ capital(e.status) || '—' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="td-actions">
                  <div class="action-group">
                    <!-- View -->
                    <button class="action-btn action-btn--view" title="View" @click="viewEmployee(e)">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>

                    <template v-if="!showArchived">
                      <!-- Edit -->
                      <button class="action-btn action-btn--edit" title="Edit" @click="openEdit(e)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                      </button>

                      <!-- Transfer -->
                      <button class="action-btn action-btn--transfer" title="Transfer Department" @click="openTransferDepartmentModal(e)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" />
                        </svg>
                      </button>

                      <!-- Terminate -->
                      <button class="action-btn action-btn--terminate" title="Terminate" @click="terminateEmployee(e)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                      </button>

                      <!-- Archive -->
                      <button class="action-btn action-btn--archive" title="Archive" @click="archiveEmployee(e)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4" />
                        </svg>
                      </button>
                    </template>

                    <template v-else>
                      <!-- Restore -->
                      <button class="action-btn action-btn--restore" title="Restore" @click="restoreEmployee(e)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                      </button>
                    </template>
                  </div>
                </td>
              </tr>

              <tr v-if="employees.length === 0">
                <td colspan="6" class="td-empty-state">
                  <div class="empty-state">
                    <div class="empty-icon">
                      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20H7a5 5 0 010-10 5 5 0 0110 0 5 5 0 010 10z" />
                      </svg>
                    </div>
                    <p>No employees found.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════
           MODAL: Create / Edit Employee
      ══════════════════════════════════════════════════ -->
      <Teleport to="body">
        <Transition name="modal">
          <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
            <div class="modal-box">

              <div class="modal-header">
                <div class="modal-header-icon" :class="editing ? 'modal-header-icon--cyan' : 'modal-header-icon--indigo'">
                  <svg v-if="editing" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                  </svg>
                  <svg v-else width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <div>
                  <h3 class="modal-title">{{ editing ? 'Edit Employee' : 'New Employee' }}</h3>
                  <p class="modal-subtitle">{{ editing ? 'Update employee information' : 'Add a new member to your team' }}</p>
                </div>
                <button class="modal-close" @click="closeModal">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="modal-body">
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      Full Name
                    </label>
                    <input
                      v-model="form.name"
                      :disabled="!!editing"
                      class="form-input"
                      :class="{ 'form-input--disabled': !!editing }"
                      placeholder="John Doe"
                    />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      Email
                    </label>
                    <input
                      v-model="form.email"
                      :disabled="!!editing"
                      class="form-input"
                      :class="{ 'form-input--disabled': !!editing }"
                      placeholder="john@company.com"
                      type="email"
                    />
                  </div>

                  <div v-if="!editing" class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                      Password
                    </label>
                    <input
                      v-model="form.password"
                      type="password"
                      class="form-input"
                      placeholder="••••••••"
                    />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                      </svg>
                      Department
                    </label>
                    <select v-model="form.department_id" class="form-input form-select">
                      <option value="">None</option>
                      <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      Job Title
                    </label>
                    <input v-model="form.job_title" class="form-input" placeholder="e.g. Software Engineer" />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                      </svg>
                      Role
                    </label>
                    <select v-model="form.role" class="form-input form-select">
                      <option value="employee">Employee</option>
                      <option value="manager">Manager</option>
                      <option value="admin">Admin</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                      </svg>
                      Salary
                    </label>
                    <input v-model.number="form.salary" type="number" min="0" step="0.01" class="form-input" placeholder="e.g. 50000.00" />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Status
                    </label>
                    <select v-model="form.status" class="form-input form-select">
                      <option value="probation">Probation</option>
                      <option value="active">Active</option>
                      <option value="terminated">Terminated</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button class="btn btn--ghost" @click="closeModal">Cancel</button>
                <button class="btn btn--primary" @click="saveEmployee">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ editing ? 'Save Changes' : 'Create Employee' }}
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

      <!-- ══════════════════════════════════════════════════
           MODAL: View Employee
      ══════════════════════════════════════════════════ -->
      <Teleport to="body">
        <Transition name="modal">
          <div v-if="selectedEmployee" class="modal-backdrop" @click.self="selectedEmployee = null">
            <div class="modal-box modal-box--sm">
              <div class="modal-header">
                <div class="view-avatar" :style="{ background: avatarGradient(selectedEmployee.user?.name) }">
                  {{ (selectedEmployee.user?.name || '?')[0].toUpperCase() }}
                </div>
                <div>
                  <h3 class="modal-title">{{ selectedEmployee.user?.name || 'Employee' }}</h3>
                  <p class="modal-subtitle">{{ selectedEmployee.job_title || 'No title' }}</p>
                </div>
                <button class="modal-close" @click="selectedEmployee = null">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="modal-body">
                <div class="view-rows">
                  <div class="view-row">
                    <span class="view-row__label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      Email
                    </span>
                    <span class="view-row__value">{{ selectedEmployee.user?.email || '—' }}</span>
                  </div>
                  <div class="view-row">
                    <span class="view-row__label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                      </svg>
                      Department
                    </span>
                    <span class="view-row__value">{{ selectedEmployee.department?.name || '—' }}</span>
                  </div>
                  <div class="view-row">
                    <span class="view-row__label">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Status
                    </span>
                    <span class="status-badge" :class="statusClass(selectedEmployee.status)">
                      <span class="status-dot" />
                      {{ capital(selectedEmployee.status) || '—' }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button class="btn btn--ghost" @click="selectedEmployee = null">Close</button>
                <router-link
                  :to="`/manager/employees/${selectedEmployee.id}`"
                  class="btn btn--primary"
                  @click="selectedEmployee = null"
                >
                  Full Profile
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </router-link>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

      <!-- ══════════════════════════════════════════════════
           MODAL: Transfer Department
      ══════════════════════════════════════════════════ -->
      <Teleport to="body">
        <Transition name="modal">
          <div v-if="showTransferDepartmentModal" class="modal-backdrop" @click.self="closeTransferDepartmentModal">
            <div class="modal-box modal-box--sm">
              <div class="modal-header">
                <div class="modal-header-icon modal-header-icon--violet">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" />
                  </svg>
                </div>
                <div>
                  <h3 class="modal-title">Transfer Department</h3>
                  <p class="modal-subtitle">{{ transferTargetEmployee?.user?.name || '—' }}</p>
                </div>
                <button class="modal-close" @click="closeTransferDepartmentModal">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="modal-body">
                <div class="form-group">
                  <label class="form-label">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9" />
                    </svg>
                    Target Department
                  </label>
                  <select v-model="selectedDepartmentId" class="form-input form-select">
                    <option value="">Select department</option>
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                </div>
              </div>

              <div class="modal-footer">
                <button class="btn btn--ghost" @click="closeTransferDepartmentModal">Cancel</button>
                <button class="btn btn--violet" :disabled="!selectedDepartmentId" @click="submitTransferDepartment">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" />
                  </svg>
                  Transfer
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const router = useRouter()

const employees  = ref<any[]>([])
const departments = ref<any[]>([])
const showArchived = ref(false)
const showModal   = ref(false)
const showTransferDepartmentModal = ref(false)
const editing     = ref<any | null>(null)
const selectedEmployee = ref<any | null>(null)
const transferTargetEmployee = ref<any | null>(null)
const selectedDepartmentId   = ref('')

const form = ref({
  name: '', email: '', password: '',
  department_id: '', job_title: '', status: 'probation',
  role: 'employee', salary: null as number | null,
})

// ── Helpers ───────────────────────────────────────────
const GRADIENTS = [
  'linear-gradient(135deg,#818cf8,#6366f1)',
  'linear-gradient(135deg,#34d399,#059669)',
  'linear-gradient(135deg,#fb923c,#ea580c)',
  'linear-gradient(135deg,#f472b6,#db2777)',
  'linear-gradient(135deg,#38bdf8,#0284c7)',
  'linear-gradient(135deg,#a78bfa,#7c3aed)',
  'linear-gradient(135deg,#facc15,#ca8a04)',
]
function avatarGradient(name = '') {
  return GRADIENTS[(name.charCodeAt(0) || 0) % GRADIENTS.length]
}
function capital(val: string) {
  if (!val) return '—'
  return val.charAt(0).toUpperCase() + val.slice(1)
}
function statusClass(status: string) {
  if (status === 'active')     return 'status-badge--active'
  if (status === 'probation')  return 'status-badge--amber'
  if (status === 'terminated') return 'status-badge--rose'
  return 'status-badge--gray'
}

// ── Data ──────────────────────────────────────────────
const load = async () => {
  const [empRes, depRes] = await Promise.all([
    api.get('/employees', { params: { archived: showArchived.value ? 'true' : 'false' } }),
    api.get('/departments'),
  ])
  employees.value  = empRes.data?.data || []
  departments.value = depRes.data?.data || []
}

// ── Modal: Create / Edit ──────────────────────────────
const openCreate = () => {
  editing.value = null
  form.value = { name: '', email: '', password: '', department_id: '', job_title: '', status: 'probation' }
  showModal.value = true
}
const openEdit = (e: any) => {
  editing.value = e
  form.value = {
    name: e.user?.name || '', email: e.user?.email || '', password: '',
    department_id: e.department_id || '', job_title: e.job_title || '', status: e.status || 'probation',
    role: e.role || 'employee', salary: e.salary ?? null,
  }
  showModal.value = true
}
const closeModal = () => { showModal.value = false }
const saveEmployee = async () => {
  if (editing.value) {
    await api.put(`/employees/${editing.value.id}`, {
      department_id: form.value.department_id || null,
      job_title: form.value.job_title || null,
      status: form.value.status,
      role: form.value.role || null,
      salary: form.value.salary ?? null,
    })
  } else {
    await api.post('/employees', {
      name: form.value.name, email: form.value.email, password: form.value.password,
      department_id: form.value.department_id || null,
      job_title: form.value.job_title || null,
      status: form.value.status,
      role: form.value.role || null,
      salary: form.value.salary ?? null,
    })
  }
  closeModal()
  await load()
}

// ── View ──────────────────────────────────────────────
const viewEmployee = async (e: any) => {
  const res = await api.get(`/employees/${e.id}`)
  selectedEmployee.value = res.data?.data || e
}

// ── Archive / Restore / Terminate ────────────────────
const archiveEmployee = async (e: any) => {
  if (!confirm(`Archive ${e.user?.name || 'employee'}?`)) return
  await api.delete(`/employees/${e.id}`)
  await load()
}
const restoreEmployee = async (e: any) => {
  if (!confirm(`Restore ${e.user?.name || 'employee'}?`)) return
  await api.post(`/employees/${e.id}/restore`)
  await load()
}
const terminateEmployee = async (e: any) => {
  if (!confirm(`Terminate ${e.user?.name || 'employee'}?`)) return
  await api.post(`/employees/${e.id}/terminate`)
  await load()
}

// ── Transfer Department ───────────────────────────────
const openTransferDepartmentModal = (e: any) => {
  transferTargetEmployee.value = e
  selectedDepartmentId.value = e.department_id || ''
  showTransferDepartmentModal.value = true
}
const closeTransferDepartmentModal = () => {
  showTransferDepartmentModal.value = false
  transferTargetEmployee.value = null
  selectedDepartmentId.value = ''
}
const submitTransferDepartment = async () => {
  if (!transferTargetEmployee.value || !selectedDepartmentId.value) return
  await api.post(`/employees/${transferTargetEmployee.value.id}/transfer-department`, {
    department_id: selectedDepartmentId.value,
  })
  closeTransferDepartmentModal()
  await load()
}

watch(showArchived, load)
onMounted(load)
</script>

<style scoped>
/* ── Root ───────────────────────────────────────────── */
.emp-root {
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

.header-left { display: flex; flex-direction: column; gap: 0.35rem; }

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
  font-size: 1.75rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
  line-height: 1.1;
}

.title-bar {
  width: 2.8rem; height: 3px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #6366f1, #22d3ee);
}

.header-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

/* ── Buttons ─────────────────────────────────────────── */
.btn {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.55rem 1.1rem;
  font-size: 0.82rem; font-weight: 600;
  border-radius: 0.65rem;
  cursor: pointer; border: none;
  transition: background 0.18s, box-shadow 0.18s, transform 0.18s;
  text-decoration: none;
}
.btn:active { transform: scale(0.97); }

.btn--primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  box-shadow: 0 2px 8px rgba(99,102,241,0.3);
}
.btn--primary:hover { box-shadow: 0 4px 14px rgba(99,102,241,0.4); }

.btn--ghost {
  background: #f1f5f9; color: #475569;
  border: 1px solid #e2e8f0;
}
.btn--ghost:hover { background: #e2e8f0; }

.btn--violet {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: #fff;
  box-shadow: 0 2px 8px rgba(124,58,237,0.3);
}
.btn--violet:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Stats Row ───────────────────────────────────────── */
.stats-row {
  display: flex; gap: 0.6rem; flex-wrap: wrap;
  animation: fadeSlideDown 0.4s ease 0.06s both;
}

.stat-chip {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
  font-size: 0.75rem; font-weight: 600;
}

.stat-chip--blue    { background: #eff6ff; color: #1d4ed8; }
.stat-chip--emerald { background: #ecfdf5; color: #065f46; }
.stat-chip--amber   { background: #fffbeb; color: #92400e; }

.stat-dot {
  width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}
.stat-dot--emerald { background: #10b981; }
.stat-dot--amber   { background: #f59e0b; }

/* ── Table Card ──────────────────────────────────────── */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.45s ease 0.1s both;
}

.table-wrap { overflow-x: auto; }

.emp-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.855rem;
}

.emp-table thead tr {
  background: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
}

.emp-table th {
  padding: 0.8rem 1.1rem;
  text-align: left;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94a3b8;
  white-space: nowrap;
}

.th-actions { text-align: right; }

/* ── Table Rows ──────────────────────────────────────── */
.emp-row {
  border-bottom: 1px solid #f8fafc;
  animation: rowIn 0.35s ease both;
  transition: background 0.15s;
}
.emp-row:last-child { border-bottom: none; }
.emp-row:hover { background: #fafbff; }

@keyframes rowIn {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: none; }
}

.emp-table td { padding: 0.85rem 1.1rem; }

/* Name cell */
.td-name {
  display: flex; align-items: center; gap: 0.65rem;
  white-space: nowrap;
}

.avatar {
  flex-shrink: 0;
  width: 2.1rem; height: 2.1rem;
  border-radius: 50%;
  color: #fff;
  font-size: 0.8rem; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.14);
}

.emp-name { font-weight: 600; color: #1e293b; }

/* Email cell */
.td-with-icon {
  display: flex; align-items: center; gap: 0.4rem;
  color: #64748b;
}

.td-icon { color: #94a3b8; flex-shrink: 0; }

/* Dept badge */
.dept-badge {
  display: inline-flex; align-items: center; gap: 0.35rem;
  padding: 0.25rem 0.65rem;
  border-radius: 9999px;
  background: #f5f3ff; color: #6d28d9;
  font-size: 0.76rem; font-weight: 600;
  white-space: nowrap;
}

/* Job tag */
.job-tag {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  border-radius: 0.4rem;
  background: #f1f5f9; color: #475569;
  font-size: 0.76rem; font-weight: 600;
}

/* Status badges */
.status-badge {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.25rem 0.7rem;
  border-radius: 9999px;
  font-size: 0.74rem; font-weight: 600;
  white-space: nowrap;
}
.status-badge--active    { background: #ecfdf5; color: #065f46; }
.status-badge--amber     { background: #fffbeb; color: #92400e; }
.status-badge--rose      { background: #fff1f2; color: #9f1239; }
.status-badge--gray      { background: #f8fafc; color: #64748b; }

.status-dot {
  width: 6px; height: 6px; border-radius: 50%;
}
.status-badge--active  .status-dot { background: #10b981; }
.status-badge--amber   .status-dot { background: #f59e0b; }
.status-badge--rose    .status-dot { background: #f43f5e; }
.status-badge--gray    .status-dot { background: #cbd5e1; }

.td-empty { color: #cbd5e1; font-weight: 500; }

/* Action buttons */
.td-actions { text-align: right; }

.action-group {
  display: flex; align-items: center; justify-content: flex-end; gap: 0.3rem;
}

.action-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 2rem; height: 2rem;
  border-radius: 0.5rem;
  border: none; cursor: pointer;
  transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
}
.action-btn:hover { transform: translateY(-1px); }
.action-btn:active { transform: scale(0.93); }

.action-btn--view      { background: #eff6ff; color: #2563eb; }
.action-btn--view:hover { background: #dbeafe; box-shadow: 0 2px 6px rgba(37,99,235,0.2); }

.action-btn--edit      { background: #ecfdf5; color: #059669; }
.action-btn--edit:hover { background: #d1fae5; box-shadow: 0 2px 6px rgba(5,150,105,0.2); }

.action-btn--transfer      { background: #f5f3ff; color: #7c3aed; }
.action-btn--transfer:hover { background: #ede9fe; box-shadow: 0 2px 6px rgba(124,58,237,0.2); }

.action-btn--terminate      { background: #fffbeb; color: #d97706; }
.action-btn--terminate:hover { background: #fef3c7; box-shadow: 0 2px 6px rgba(217,119,6,0.2); }

.action-btn--archive      { background: #fff1f2; color: #e11d48; }
.action-btn--archive:hover { background: #ffe4e6; box-shadow: 0 2px 6px rgba(225,29,72,0.2); }

.action-btn--restore      { background: #ecfdf5; color: #059669; }
.action-btn--restore:hover { background: #d1fae5; box-shadow: 0 2px 6px rgba(5,150,105,0.2); }

/* Empty state */
.td-empty-state { padding: 3rem 1rem !important; }

.empty-state {
  display: flex; flex-direction: column; align-items: center; gap: 0.75rem;
  color: #94a3b8;
}

.empty-icon {
  width: 3.5rem; height: 3.5rem;
  border-radius: 1rem;
  background: #f8fafc;
  display: flex; align-items: center; justify-content: center;
  color: #cbd5e1;
}

.empty-state p { font-size: 0.875rem; font-weight: 500; }

/* ── Modals ──────────────────────────────────────────── */
.modal-backdrop {
  position: fixed; inset: 0; z-index: 50;
  background: rgba(15,23,42,0.5);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem;
}

.modal-box {
  background: #fff;
  border-radius: 1.25rem;
  width: 100%; max-width: 560px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.18);
  overflow: hidden;
}

.modal-box--sm { max-width: 420px; }

.modal-header {
  display: flex; align-items: center; gap: 0.9rem;
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  background: #fafbfc;
}

.modal-header-icon {
  flex-shrink: 0;
  width: 2.2rem; height: 2.2rem;
  border-radius: 0.6rem;
  display: flex; align-items: center; justify-content: center;
}
.modal-header-icon--indigo { background: #eef2ff; color: #6366f1; }
.modal-header-icon--cyan   { background: #ecfeff; color: #0891b2; }
.modal-header-icon--violet { background: #f5f3ff; color: #7c3aed; }

.view-avatar {
  flex-shrink: 0;
  width: 2.4rem; height: 2.4rem;
  border-radius: 50%;
  color: #fff; font-size: 0.9rem; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.18);
}

.modal-title {
  font-size: 0.975rem; font-weight: 700; color: #0f172a;
  line-height: 1.2;
}
.modal-subtitle {
  font-size: 0.775rem; color: #94a3b8; font-weight: 500;
  margin-top: 0.1rem;
}

.modal-close {
  margin-left: auto; flex-shrink: 0;
  width: 2rem; height: 2rem;
  border-radius: 0.5rem; border: none;
  background: #f1f5f9; color: #64748b;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background 0.15s;
}
.modal-close:hover { background: #e2e8f0; }

.modal-body { padding: 1.4rem 1.5rem; }
.modal-footer {
  display: flex; justify-content: flex-end; gap: 0.5rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
  background: #fafbfc;
}

/* ── Form ────────────────────────────────────────────── */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

@media (max-width: 480px) { .form-grid { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; gap: 0.4rem; }

.form-label {
  display: flex; align-items: center; gap: 0.35rem;
  font-size: 0.72rem; font-weight: 700;
  letter-spacing: 0.08em; text-transform: uppercase;
  color: #64748b;
}

.form-input {
  width: 100%;
  padding: 0.6rem 0.85rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.6rem;
  font-size: 0.875rem; color: #1e293b;
  background: #fff;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  box-sizing: border-box;
}
.form-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.form-input--disabled { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }

.form-select { appearance: none; cursor: pointer; }

/* ── View Rows ───────────────────────────────────────── */
.view-rows { display: flex; flex-direction: column; gap: 0.1rem; }

.view-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f8fafc;
}
.view-row:last-child { border-bottom: none; }

.view-row__label {
  display: flex; align-items: center; gap: 0.4rem;
  font-size: 0.78rem; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
  color: #94a3b8;
}

.view-row__value {
  font-size: 0.875rem; font-weight: 600; color: #1e293b;
  text-align: right;
}

/* ── Modal Transitions ───────────────────────────────── */
.modal-enter-active { animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1) both; }
.modal-leave-active { animation: modalOut 0.18s ease both; }

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.94) translateY(10px); }
  to   { opacity: 1; transform: none; }
}
@keyframes modalOut {
  to { opacity: 0; transform: scale(0.96) translateY(6px); }
}

/* ── Page Animations ─────────────────────────────────── */
@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: none; }
}
</style>