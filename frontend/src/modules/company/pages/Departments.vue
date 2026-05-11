<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Departments</h2>
      </div>
      <button @click="openCreate" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white">+ New Department</button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
          <tr>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Code</th>
            <th class="px-4 py-3">Manager</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="d in departments" :key="d.id">
            <td class="px-4 py-3">{{ d.name }}</td>
            <td class="px-4 py-3">{{ d.code || '-' }}</td>
            <td class="px-4 py-3">{{ d.manager?.user?.name || '-' }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex flex-wrap justify-end gap-x-3 gap-y-1">
                <button @click="viewDepartment(d)" class="font-semibold text-slate-700">View</button>
                <button @click="openEdit(d)" class="font-semibold text-cyan-700">Edit</button>
                <button @click="openAssignManager(d)" class="font-semibold text-amber-700">Assign Manager</button>
                <button @click="removeDepartment(d)" class="font-semibold text-rose-700">Delete</button>
              </div>
            </td>
          </tr>
          <tr v-if="departments.length === 0">
            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No departments found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-40 bg-black/40 p-4">
      <div class="mx-auto mt-16 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">{{ editing ? 'Edit Department' : 'Create Department' }}</h3>
        <div class="mt-4 space-y-3">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Name</label>
            <input v-model="form.name" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Code</label>
            <input v-model="form.code" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="saveDepartment" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">Save</button>
        </div>
      </div>
    </div>

    <div v-if="showAssignManagerModal" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="closeAssignManagerModal">
      <div class="mx-auto mt-16 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Assign Department Manager</h3>
        <p class="mt-1 text-sm text-slate-600">Department: {{ managerDepartment?.name }}</p>

        <div class="mt-4">
          <label class="block text-sm font-semibold text-slate-700">Manager</label>
          <select v-model="selectedManagerEmployeeId" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">Select employee</option>
            <option v-for="employee in activeEmployees" :key="employee.id" :value="employee.id">
              {{ employee.user?.name || employee.id }}
            </option>
          </select>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeAssignManagerModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="submitManagerAssignment" :disabled="!selectedManagerEmployeeId" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-50">Assign</button>
        </div>
      </div>
    </div>

    <div v-if="selectedDepartment" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="selectedDepartment = null">
      <div class="mx-auto mt-16 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Department Details</h3>
        <p class="mt-3 text-sm"><span class="font-semibold">Name:</span> {{ selectedDepartment.name }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Code:</span> {{ selectedDepartment.code || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Manager:</span> {{ selectedDepartment.manager?.user?.name || '-' }}</p>
        <div class="mt-5 flex justify-end">
          <button @click="selectedDepartment = null" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import api from '@/api/axios'

const departments = ref<any[]>([])
const employees = ref<any[]>([])
const showModal = ref(false)
const showAssignManagerModal = ref(false)
const editing = ref<any | null>(null)
const selectedDepartment = ref<any | null>(null)
const managerDepartment = ref<any | null>(null)
const selectedManagerEmployeeId = ref('')
const form = ref({ name: '', code: '' })

const activeEmployees = computed(() => employees.value.filter((employee) => employee.status !== 'terminated'))

const load = async () => {
  const [deps, emps] = await Promise.all([
    api.get('/departments'),
    api.get('/employees'),
  ])
  departments.value = deps.data?.data || []
  employees.value = emps.data?.data || []
}

const openCreate = () => {
  editing.value = null
  form.value = { name: '', code: '' }
  showModal.value = true
}

const openEdit = (d: any) => {
  editing.value = d
  form.value = { name: d.name || '', code: d.code || '' }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveDepartment = async () => {
  if (editing.value) {
    await api.put(`/departments/${editing.value.id}`, {
      name: form.value.name,
      code: form.value.code || null,
    })
  } else {
    await api.post('/departments', {
      name: form.value.name,
      code: form.value.code || null,
    })
  }
  closeModal()
  await load()
}

const removeDepartment = async (d: any) => {
  if (!confirm(`Delete department \"${d.name}\"?`)) return
  await api.delete(`/departments/${d.id}`)
  await load()
}

const openAssignManager = (department: any) => {
  managerDepartment.value = department
  selectedManagerEmployeeId.value = department?.manager?.id || ''
  showAssignManagerModal.value = true
}

const closeAssignManagerModal = () => {
  showAssignManagerModal.value = false
  managerDepartment.value = null
  selectedManagerEmployeeId.value = ''
}

const submitManagerAssignment = async () => {
  if (!managerDepartment.value || !selectedManagerEmployeeId.value) return

  await api.post(`/departments/${managerDepartment.value.id}/assign-manager`, {
    manager_employee_id: selectedManagerEmployeeId.value,
  })

  closeAssignManagerModal()
  await load()
}

const viewDepartment = (d: any) => {
  selectedDepartment.value = d
}

onMounted(load)
</script>