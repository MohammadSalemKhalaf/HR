<template>
  <AppLayout>
    <div class="notif-root">

      <!-- ── Header ── -->
      <div class="page-header">
        <div class="header-left">
          <div class="header-eyebrow">
            <span class="eyebrow-dot" />
            Manager Area
          </div>
          <h1 class="page-title">Department Notifications</h1>
          <p class="page-subtitle">Broadcast messages to your department in seconds.</p>
          <div class="title-bar" />
        </div>
      </div>

      <!-- ── Main Grid ── -->
      <div class="main-grid">

        <!-- ══ LEFT: Form Card ══ -->
        <div class="form-card">
          <div class="form-card__header">
            <div class="form-card__header-icon">
              <!-- Send icon -->
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <div>
              <h3 class="form-card__title">Send Notification</h3>
              <p class="form-card__subtitle">Broadcast to the department or target specific employees.</p>
            </div>
          </div>

          <form @submit.prevent="submit" class="form-body">

            <!-- Row 1: Department + Type -->
            <div class="field-row">
              <div class="field-group">
                <label class="field-label">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9"/>
                  </svg>
                  Department
                </label>
                <div class="select-wrap">
                  <select v-model="form.department_id" class="field-select">
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                  <svg class="select-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>
              </div>

              <div class="field-group">
                <label class="field-label">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  Notification Type
                </label>
                <div class="select-wrap">
                  <select v-model="form.type" class="field-select">
                    <option v-for="(label, key) in notificationTypes" :key="key" :value="key">{{ label }}</option>
                  </select>
                  <svg class="select-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Title -->
            <div class="field-group">
              <label class="field-label">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10"/>
                </svg>
                Title
              </label>
              <input
                v-model="form.title"
                type="text"
                placeholder="e.g. Meeting tomorrow at 10 AM"
                class="field-input"
              />
            </div>

            <!-- Message -->
            <div class="field-group">
              <label class="field-label">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Message
              </label>
              <textarea
                v-model="form.message"
                rows="5"
                placeholder="Write the full message that employees should receive..."
                class="field-textarea"
              />
            </div>

            <!-- Recipients -->
            <div class="recipients-box">
              <div class="recipients-box__label">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Recipients
              </div>
              <div class="radio-group">
                <label class="radio-option" :class="{ 'radio-option--active': form.recipient_mode === 'all' }">
                  <input v-model="form.recipient_mode" type="radio" value="all" />
                  <span class="radio-custom" />
                  <span>Send to all employees</span>
                </label>
                <label class="radio-option" :class="{ 'radio-option--active': form.recipient_mode === 'specific' }">
                  <input v-model="form.recipient_mode" type="radio" value="specific" />
                  <span class="radio-custom" />
                  <span>Select specific employees</span>
                </label>
              </div>
            </div>

            <!-- Specific Employees Picker -->
            <Transition name="slide-down">
              <div v-if="form.recipient_mode === 'specific'" class="employee-picker">
                <div class="employee-picker__header">
                  <span class="employee-picker__title">Employees in Department</span>
                  <span class="employee-picker__hint">Check to include</span>
                </div>
                <div class="employee-check-grid">
                  <label
                    v-for="e in departmentEmployees"
                    :key="e.id"
                    class="emp-check"
                    :class="{ 'emp-check--selected': form.employee_ids.includes(e.id) }"
                  >
                    <input type="checkbox" :value="e.id" v-model="form.employee_ids" />
                    <div class="emp-check__avatar" :style="{ background: avatarGradient(e.user?.name) }">
                      {{ (e.user?.name || '?')[0].toUpperCase() }}
                    </div>
                    <div class="emp-check__info">
                      <div class="emp-check__name">{{ e.user?.name }}</div>
                      <div class="emp-check__email">{{ e.user?.email }}</div>
                    </div>
                    <div class="emp-check__tick">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                  </label>
                </div>
              </div>
            </Transition>

            <!-- Actions -->
            <div class="form-actions">
              <button type="submit" class="btn-send" :disabled="submitting">
                <span v-if="submitting" class="btn-spinner" />
                <svg v-else width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                {{ submitting ? 'Sending…' : 'Send Notification' }}
              </button>
              <button type="button" @click="resetForm" class="btn-reset">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset
              </button>
            </div>

            <!-- Feedback -->
            <Transition name="fade">
              <div v-if="errorMessage" class="alert alert--error">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ errorMessage }}
              </div>
            </Transition>
            <Transition name="fade">
              <div v-if="successMessage" class="alert alert--success">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ successMessage }}
              </div>
            </Transition>

            <p class="form-footnote">
              <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Employees receive both database notifications and email notifications.
            </p>
          </form>
        </div>

        <!-- ══ RIGHT: Sidebar ══ -->
        <div class="sidebar">

          <!-- Department Summary -->
          <div class="side-card side-card--dept">
            <div class="side-card__header">
              <div class="side-card__icon side-card__icon--violet">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h4m0 16V5m0 16h6M9 5V3h6v2m0 0h4a2 2 0 012 2v14M15 21V9"/>
                </svg>
              </div>
              <span class="side-card__title">Selected Department</span>
            </div>
            <div class="dept-detail-list">
              <div class="dept-detail-item">
                <span class="dept-detail-item__key">Department</span>
                <span class="dept-detail-item__val">{{ selectedDepartment?.name || 'N/A' }}</span>
              </div>
              <div class="dept-detail-item">
                <span class="dept-detail-item__key">Scope</span>
                <span class="dept-detail-item__val">Own department only</span>
              </div>
              <div class="dept-detail-item">
                <span class="dept-detail-item__key">Delivery</span>
                <span class="dept-detail-item__val delivery-badges">
                  <span class="delivery-badge delivery-badge--db">Database</span>
                  <span class="delivery-badge delivery-badge--email">Email</span>
                </span>
              </div>
            </div>
          </div>

          <!-- Employee List -->
          <div class="side-card">
            <div class="side-card__header">
              <div class="side-card__icon side-card__icon--blue">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <span class="side-card__title">Current Employees</span>
              <span class="emp-count-badge">{{ departmentEmployees.length }}</span>
            </div>
            <div class="side-emp-list">
              <div
                v-for="(e, i) in departmentEmployees"
                :key="e.id"
                class="side-emp-item"
                :style="{ animationDelay: `${i * 60}ms` }"
              >
                <div class="side-emp-item__avatar" :style="{ background: avatarGradient(e.user?.name) }">
                  {{ (e.user?.name || '?')[0].toUpperCase() }}
                </div>
                <div class="side-emp-item__info">
                  <div class="side-emp-item__name">{{ e.user?.name }}</div>
                  <div class="side-emp-item__email">{{ e.user?.email }}</div>
                </div>
              </div>
              <div v-if="!departmentEmployees.length" class="side-emp-empty">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p>No employees found</p>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

const departments         = ref<any[]>([])
const departmentEmployees = ref<any[]>([])
const selectedDepartment  = ref<any | null>(null)
const notificationTypes   = ref<Record<string, string>>({})
const submitting          = ref(false)
const successMessage      = ref('')
const errorMessage        = ref('')

const form = ref({
  department_id : '',
  type          : 'general_announcement',
  title         : '',
  message       : '',
  recipient_mode: 'all',
  employee_ids  : [] as any[]
})

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

async function fetchDepartments() {
  try {
    const res  = await api.get('/manager/department-notifications/meta')
    const meta = res.data || {}
    departments.value     = meta.departments     || []
    notificationTypes.value = meta.notificationTypes || {}

    if (!notificationTypes.value[form.value.type]) {
      form.value.type = Object.keys(notificationTypes.value)[0] || 'general_announcement'
    }

    if (departments.value.length > 0) {
      form.value.department_id = meta.selectedDepartmentId || departments.value[0].id
      selectedDepartment.value = meta.selectedDepartment   || null
      await fetchDepartmentEmployees()
    }
  } catch (err) { console.error(err) }
}

async function fetchDepartmentEmployees() {
  try {
    const id = form.value.department_id
    if (!id) return
    const res  = await api.get(`/manager/departments/${id}/employees`)
    const data = res.data
    departmentEmployees.value = data.employees || data
    selectedDepartment.value  = data.department || null
  } catch (err) { console.error(err) }
}

async function submit() {
  submitting.value    = true
  successMessage.value = ''
  errorMessage.value   = ''

  try {
    const payload: any = {
      department_id  : form.value.department_id,
      type           : form.value.type,
      title          : form.value.title,
      message        : form.value.message,
      recipient_mode : form.value.recipient_mode,
    }
    if (form.value.recipient_mode === 'specific') {
      payload.employee_ids = form.value.employee_ids
    }

    const res = await api.post('/manager/department-notifications', payload)
    successMessage.value = res.data?.message || 'Notification sent successfully.'
    const firstType = Object.keys(notificationTypes.value)[0] || 'general_announcement'
    form.value = {
      department_id : form.value.department_id,
      type          : firstType,
      title         : '',
      message       : '',
      recipient_mode: 'all',
      employee_ids  : []
    }
  } catch (err: any) {
    errorMessage.value = err?.response?.data?.message || 'Failed to send notification.'
  } finally {
    submitting.value = false
  }
}

function resetForm() {
  successMessage.value = ''
  errorMessage.value   = ''
  form.value = {
    department_id : form.value.department_id,
    type          : Object.keys(notificationTypes.value)[0] || 'general_announcement',
    title         : '',
    message       : '',
    recipient_mode: 'all',
    employee_ids  : []
  }
}

watch(() => form.value.department_id, () => fetchDepartmentEmployees())
onMounted(() => fetchDepartments())
</script>

<style scoped>
/* ─── Root ──────────────────────────────────────────── */
.notif-root {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

/* ─── Header ─────────────────────────────────────────── */
.page-header {
  animation: fadeSlideDown 0.4s ease both;
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
  font-size: 1.9rem;
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

/* ─── Main Grid ───────────────────────────────────────── */
.main-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  animation: fadeSlideDown 0.45s ease 0.08s both;
}

@media (min-width: 1024px) {
  .main-grid { grid-template-columns: 1.2fr 0.8fr; }
}

/* ─── Form Card ───────────────────────────────────────── */
.form-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 1px 6px rgba(0,0,0,0.06);
}

.form-card__header {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 1.25rem 1.6rem;
  background: linear-gradient(to right, #fafbff, #f8fafc);
  border-bottom: 1px solid #f1f5f9;
}

.form-card__header-icon {
  flex-shrink: 0;
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 0.65rem;
  background: #eef2ff;
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 0.1rem;
}

.form-card__title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.3;
}

.form-card__subtitle {
  font-size: 0.78rem;
  color: #94a3b8;
  margin-top: 0.2rem;
}

/* ─── Form Body ───────────────────────────────────────── */
.form-body {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  padding: 1.6rem;
}

.field-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 640px) {
  .field-row { grid-template-columns: 1fr 1fr; }
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}

.field-label {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.74rem;
  font-weight: 600;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #64748b;
}

/* Select */
.select-wrap {
  position: relative;
}

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
  transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
  cursor: pointer;
}

.field-select:focus {
  outline: none;
  border-color: #6366f1;
  background: #fff;
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

/* Input */
.field-input {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.7rem 0.95rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #1e293b;
  transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
}

.field-input::placeholder { color: #cbd5e1; }

.field-input:focus {
  outline: none;
  border-color: #6366f1;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}

/* Textarea */
.field-textarea {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.75rem 0.95rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #1e293b;
  resize: vertical;
  font-family: inherit;
  transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
}

.field-textarea::placeholder { color: #cbd5e1; }

.field-textarea:focus {
  outline: none;
  border-color: #6366f1;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}

/* ─── Recipients Box ─────────────────────────────────── */
.recipients-box {
  background: #f8fafc;
  border: 1px solid #e8edf3;
  border-radius: 0.9rem;
  padding: 1rem 1.1rem;
}

.recipients-box__label {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.74rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  color: #475569;
  margin-bottom: 0.75rem;
}

.radio-group {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

.radio-option {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.5rem 0.9rem;
  border-radius: 0.6rem;
  border: 1.5px solid #e2e8f0;
  font-size: 0.83rem;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  background: #fff;
  transition: border-color 0.15s, background 0.15s, color 0.15s;
}

.radio-option input { display: none; }

.radio-option--active {
  border-color: #6366f1;
  background: #eef2ff;
  color: #4338ca;
  font-weight: 600;
}

.radio-custom {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 2px solid #cbd5e1;
  display: inline-block;
  position: relative;
  transition: border-color 0.15s;
}

.radio-option--active .radio-custom {
  border-color: #6366f1;
  background: #6366f1;
  box-shadow: inset 0 0 0 3px #fff;
}

/* ─── Employee Picker ────────────────────────────────── */
.employee-picker {
  background: #f8fafc;
  border: 1px dashed #c7d2e0;
  border-radius: 0.9rem;
  padding: 1rem 1.1rem;
}

.employee-picker__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.8rem;
}

.employee-picker__title {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #475569;
}

.employee-picker__hint {
  font-size: 0.7rem;
  color: #94a3b8;
}

.employee-check-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.55rem;
}

@media (min-width: 600px) {
  .employee-check-grid { grid-template-columns: 1fr 1fr; }
}

.emp-check {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.7rem 0.85rem;
  background: #fff;
  border: 1.5px solid #e8edf3;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.emp-check input { display: none; }

.emp-check--selected {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}

.emp-check__avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  color: #fff;
  font-size: 0.78rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.emp-check__info { flex: 1; min-width: 0; }

.emp-check__name {
  font-size: 0.82rem;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.emp-check__email {
  font-size: 0.7rem;
  color: #94a3b8;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.emp-check__tick {
  width: 1.3rem;
  height: 1.3rem;
  border-radius: 50%;
  background: #eef2ff;
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: scale(0.6);
  transition: opacity 0.15s, transform 0.15s;
  flex-shrink: 0;
}

.emp-check--selected .emp-check__tick {
  opacity: 1;
  transform: scale(1);
  background: #6366f1;
  color: #fff;
}

/* ─── Actions ─────────────────────────────────────────── */
.form-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.btn-send {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1.4rem;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  font-size: 0.875rem;
  font-weight: 700;
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(99,102,241,0.35);
  transition: opacity 0.18s, transform 0.18s, box-shadow 0.18s;
}

.btn-send:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99,102,241,0.45);
}

.btn-send:disabled { opacity: 0.65; cursor: not-allowed; }

.btn-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255,255,255,0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.btn-reset {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.65rem 1.2rem;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.875rem;
  font-weight: 600;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-reset:hover { background: #e8edf3; }

/* ─── Alerts ──────────────────────────────────────────── */
.alert {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.8rem 1rem;
  border-radius: 0.75rem;
  font-size: 0.82rem;
  font-weight: 500;
}

.alert--error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
.alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }

/* ─── Footnote ────────────────────────────────────────── */
.form-footnote {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.73rem;
  color: #94a3b8;
}

/* ─── Sidebar ─────────────────────────────────────────── */
.sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

/* ─── Side Card ───────────────────────────────────────── */
.side-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: fadeSlideDown 0.5s ease 0.14s both;
}

.side-card--dept { animation-delay: 0.11s; }

.side-card__header {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.9rem 1.2rem;
  background: #fafbfc;
  border-bottom: 1px solid #f1f5f9;
}

.side-card__icon {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.side-card__icon--violet { background: #f5f3ff; color: #7c3aed; }
.side-card__icon--blue   { background: #eff6ff; color: #2563eb; }

.side-card__title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
  flex: 1;
}

.emp-count-badge {
  font-size: 0.7rem;
  font-weight: 700;
  background: #eef2ff;
  color: #4338ca;
  padding: 0.15rem 0.55rem;
  border-radius: 9999px;
}

/* ─── Dept Detail List ────────────────────────────────── */
.dept-detail-list {
  padding: 1rem 1.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.dept-detail-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.dept-detail-item__key {
  font-size: 0.74rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #94a3b8;
}

.dept-detail-item__val {
  font-size: 0.82rem;
  font-weight: 600;
  color: #1e293b;
  text-align: right;
}

.delivery-badges {
  display: flex;
  gap: 0.4rem;
}

.delivery-badge {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.15rem 0.5rem;
  border-radius: 9999px;
}

.delivery-badge--db    { background: #eff6ff; color: #1d4ed8; }
.delivery-badge--email { background: #ecfdf5; color: #065f46; }

/* ─── Side Employee List ──────────────────────────────── */
.side-emp-list {
  padding: 0.85rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-height: 340px;
  overflow-y: auto;
}

.side-emp-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.8rem;
  border-radius: 0.75rem;
  border: 1px solid #f1f5f9;
  background: #fafbff;
  animation: rowIn 0.35s ease both;
  transition: background 0.15s;
}

.side-emp-item:hover { background: #f0f4ff; }

.side-emp-item__avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.side-emp-item__info { flex: 1; min-width: 0; }

.side-emp-item__name {
  font-size: 0.82rem;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.side-emp-item__email {
  font-size: 0.68rem;
  color: #94a3b8;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.side-emp-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 2rem 1rem;
  color: #cbd5e1;
  font-size: 0.8rem;
  text-align: center;
}

/* ─── Vue Transitions ─────────────────────────────────── */
.slide-down-enter-active { animation: slideDown 0.28s ease; }
.slide-down-leave-active { animation: slideDown 0.22s ease reverse; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.22s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* ─── Animations ──────────────────────────────────────── */
@keyframes fadeSlideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes rowIn {
  from { opacity: 0; transform: translateX(8px); }
  to   { opacity: 1; transform: translateX(0); }
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>