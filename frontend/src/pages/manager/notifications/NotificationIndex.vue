<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-slate-500">Manager Area</p>
          <h2 class="text-3xl font-bold text-slate-900">Department Notifications</h2>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Send Department Notification</h3>
            <p class="mt-1 text-sm text-slate-500">Broadcast to the entire department or select specific employees.</p>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label for="department_id" class="mb-2 block text-sm font-medium text-slate-700">Department</label>
                <select v-model="form.department_id" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                  <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>

              <div>
                <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Notification Type</label>
                <select v-model="form.type" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                  <option value="general_announcement">General Announcement</option>
                  <option value="urgent">Urgent</option>
                  <option value="reminder">Reminder</option>
                  <option value="update">Update</option>
                </select>
              </div>
            </div>

            <div>
              <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Title</label>
              <input v-model="form.title" type="text" placeholder="Meeting tomorrow at 10 AM" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500" />
            </div>

            <div>
              <label for="message" class="mb-2 block text-sm font-medium text-slate-700">Message</label>
              <textarea v-model="form.message" rows="5" placeholder="Write the full message that employees should receive." class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500"></textarea>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="mb-3 text-sm font-semibold text-slate-800">Recipients</div>
              <div class="flex flex-wrap gap-4 text-sm">
                <label class="inline-flex items-center gap-2">
                  <input v-model="form.recipient_mode" type="radio" value="all" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" />
                  <span>Send to all employees</span>
                </label>
                <label class="inline-flex items-center gap-2">
                  <input v-model="form.recipient_mode" type="radio" value="specific" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" />
                  <span>Select specific employees</span>
                </label>
              </div>
            </div>

            <div v-if="form.recipient_mode === 'specific'" class="rounded-2xl border border-slate-200 bg-white p-4">
              <div class="mb-3">
                <div class="text-sm font-semibold text-slate-800">Employees in Department</div>
                <div class="text-xs text-slate-500">Use checkboxes to target individual employees.</div>
              </div>
              <div class="grid gap-3 md:grid-cols-2">
                <label v-for="e in departmentEmployees" :key="e.id" class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                  <input type="checkbox" :value="e.id" v-model="form.employee_ids" class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" />
                  <div>
                    <div class="font-semibold text-slate-900">{{ e.user?.name }}</div>
                    <div class="text-xs text-slate-500">{{ e.user?.email }}</div>
                  </div>
                </label>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700">Send Notification</button>
              <button type="reset" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</button>
            </div>

            <div class="text-xs text-slate-500">
              Employees receive both database notifications and email notifications.
            </div>
          </form>
        </div>

        <div class="space-y-6">
          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Selected Department</h3>
            <div class="mt-4 space-y-3 text-sm text-slate-600">
              <div><span class="font-medium text-slate-900">Department:</span> {{ selectedDepartment?.name || 'N/A' }}</div>
              <div><span class="font-medium text-slate-900">Manager Scope:</span> Your own department employees only</div>
              <div><span class="font-medium text-slate-900">Delivery:</span> Database + Email</div>
            </div>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Current Employees</h3>
            <div class="mt-4 space-y-3 text-sm text-slate-600">
              <div v-for="e in departmentEmployees" :key="e.id" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <div class="font-semibold text-slate-900">{{ e.user?.name }}</div>
                <div class="text-xs text-slate-500">{{ e.user?.email }}</div>
              </div>
              <div v-if="!departmentEmployees.length" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-slate-500">No employees found in this department.</div>
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

const departments = ref<any[]>([])
const departmentEmployees = ref<any[]>([])
const selectedDepartment = ref<any | null>(null)
const form = ref({
  department_id: '',
  type: 'general_announcement',
  title: '',
  message: '',
  recipient_mode: 'all',
  employee_ids: [] as any[]
})

async function fetchDepartments() {
  try {
    const res = await fetch('/api/manager/departments')
    if (!res.ok) throw new Error('Fetch failed')
    departments.value = await res.json()
    if (departments.value.length > 0) {
      form.value.department_id = departments.value[0].id
      await fetchDepartmentEmployees()
    }
  } catch (err) { console.error(err) }
}

async function fetchDepartmentEmployees() {
  try {
    const id = form.value.department_id
    if (!id) return
    const res = await fetch(`/api/manager/departments/${id}/employees`)
    if (!res.ok) throw new Error('Fetch failed')
    const data = await res.json()
    departmentEmployees.value = data.employees || data
    selectedDepartment.value = data.department || null
  } catch (err) { console.error(err) }
}

async function submit() {
  try {
    const res = await fetch('/api/manager/department-notifications', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value)
    })
    if (!res.ok) throw new Error('Send failed')
    alert('Notification sent successfully!')
    form.value = { department_id: form.value.department_id, type: 'general_announcement', title: '', message: '', recipient_mode: 'all', employee_ids: [] }
  } catch (err) { console.error(err) }
}

watch(() => form.value.department_id, () => fetchDepartmentEmployees())
onMounted(() => fetchDepartments())
</script>
