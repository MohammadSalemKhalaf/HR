<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Employees</h2>
      </div>
      <div class="flex gap-2">
        <button v-if="showArchived" @click="showArchived = false" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Active</button>
        <template v-else>
          <button @click="openCreate" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">+ New Employee</button>
          <button @click="showArchived = true" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Archived</button>
        </template>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
          <tr>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Department</th>
            <th class="px-4 py-3">Job Title</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="e in employees" :key="e.id">
            <td class="px-4 py-3">{{ e.user?.name || '-' }}</td>
            <td class="px-4 py-3">{{ e.user?.email || '-' }}</td>
            <td class="px-4 py-3">{{ e.department?.name || '-' }}</td>
            <td class="px-4 py-3">{{ e.job_title || '-' }}</td>
            <td class="px-4 py-3">{{ e.status || '-' }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex flex-wrap justify-end gap-x-3 gap-y-1">
                <button @click="viewEmployee(e)" class="font-semibold text-slate-700">View</button>
                <button v-if="!showArchived" @click="openEdit(e)" class="font-semibold text-cyan-700">Edit</button>
                <button v-if="!showArchived" @click="terminateEmployee(e)" class="font-semibold text-amber-700">Terminate</button>
                <button v-if="!showArchived" @click="openAssignManagerModal(e)" class="font-semibold text-indigo-700">Assign Manager</button>
                <button v-if="!showArchived" @click="openTransferDepartmentModal(e)" class="font-semibold text-violet-700">Transfer</button>
                <button v-if="!showArchived" @click="archiveEmployee(e)" class="font-semibold text-rose-700">Archive</button>
                <button v-else @click="restoreEmployee(e)" class="font-semibold text-emerald-700">Restore</button>
              </div>
            </td>
          </tr>
          <tr v-if="employees.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">No employees found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-40 bg-black/40 p-4">
      <div class="mx-auto mt-10 max-w-2xl rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">{{ editing ? 'Edit Employee' : 'Create Employee' }}</h3>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Name</label>
            <input v-model="form.name" :disabled="!!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Email</label>
            <input v-model="form.email" :disabled="!!editing" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 disabled:bg-slate-50" />
          </div>
          <div v-if="!editing">
            <label class="block text-sm font-semibold text-slate-700">Password</label>
            <input type="password" v-model="form.password" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Department</label>
            <select v-model="form.department_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="">None</option>
              <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Job Title</label>
            <input v-model="form.job_title" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Status</label>
            <select v-model="form.status" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="probation">probation</option>
              <option value="active">active</option>
              <option value="terminated">terminated</option>
            </select>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="saveEmployee" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">Save</button>
        </div>
      </div>
    </div>

    <div v-if="selectedEmployee" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="selectedEmployee = null">
      <div class="mx-auto mt-12 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Employee Details</h3>
        <p class="mt-3 text-sm"><span class="font-semibold">Name:</span> {{ selectedEmployee.user?.name || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Email:</span> {{ selectedEmployee.user?.email || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Department:</span> {{ selectedEmployee.department?.name || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Job Title:</span> {{ selectedEmployee.job_title || '-' }}</p>
        <p class="mt-1 text-sm"><span class="font-semibold">Status:</span> {{ selectedEmployee.status || '-' }}</p>
        <div class="mt-5 flex justify-end">
          <button @click="selectedEmployee = null" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">Close</button>
        </div>
      </div>
    </div>

    <div v-if="showAssignManagerModal" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="closeAssignManagerModal">
      <div class="mx-auto mt-16 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Assign Manager</h3>
        <p class="mt-1 text-sm text-slate-600">Employee: {{ managerTargetEmployee?.user?.name || '-' }}</p>

        <div class="mt-4">
          <label class="block text-sm font-semibold text-slate-700">Manager</label>
          <select v-model="selectedManagerId" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">Select manager</option>
            <option v-for="employee in managerCandidates" :key="employee.id" :value="employee.id">
              {{ employee.user?.name || employee.id }}
            </option>
          </select>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeAssignManagerModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="submitAssignManager" :disabled="!selectedManagerId" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-50">Assign</button>
        </div>
      </div>
    </div>

    <div v-if="showTransferDepartmentModal" class="fixed inset-0 z-40 bg-black/40 p-4" @click.self="closeTransferDepartmentModal">
      <div class="mx-auto mt-16 max-w-lg rounded-xl bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">Transfer Department</h3>
        <p class="mt-1 text-sm text-slate-600">Employee: {{ transferTargetEmployee?.user?.name || '-' }}</p>

        <div class="mt-4">
          <label class="block text-sm font-semibold text-slate-700">Target Department</label>
          <select v-model="selectedDepartmentId" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">Select department</option>
            <option v-for="department in departments" :key="department.id" :value="department.id">
              {{ department.name }}
            </option>
          </select>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeTransferDepartmentModal" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">Cancel</button>
          <button @click="submitTransferDepartment" :disabled="!selectedDepartmentId" class="rounded-lg bg-cyan-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-50">Transfer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import api from '@/api/axios'

const employees = ref<any[]>([])
const departments = ref<any[]>([])
const showArchived = ref(false)
const showModal = ref(false)
const showAssignManagerModal = ref(false)
const showTransferDepartmentModal = ref(false)
const editing = ref<any | null>(null)
const selectedEmployee = ref<any | null>(null)
const managerTargetEmployee = ref<any | null>(null)
const transferTargetEmployee = ref<any | null>(null)
const selectedManagerId = ref('')
const selectedDepartmentId = ref('')

const form = ref({
  name: '',
  email: '',
  password: '',
  department_id: '',
  job_title: '',
  status: 'probation',
})

const managerCandidates = computed(() => {
  if (!managerTargetEmployee.value) return []

  return employees.value.filter((employee) => {
    if (employee.id === managerTargetEmployee.value.id) return false
    if (employee.status === 'terminated') return false
    return true
  })
})

const load = async () => {
  const [empRes, depRes] = await Promise.all([
    api.get('/employees', { params: { archived: showArchived.value ? 'true' : 'false' } }),
    api.get('/departments'),
  ])
  employees.value = empRes.data?.data || []
  departments.value = depRes.data?.data || []
}

const openCreate = () => {
  editing.value = null
  form.value = { name: '', email: '', password: '', department_id: '', job_title: '', status: 'probation' }
  showModal.value = true
}

const openEdit = (employee: any) => {
  editing.value = employee
  form.value = {
    name: employee.user?.name || '',
    email: employee.user?.email || '',
    password: '',
    department_id: employee.department_id || '',
    job_title: employee.job_title || '',
    status: employee.status || 'probation',
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveEmployee = async () => {
  if (editing.value) {
    await api.put(`/employees/${editing.value.id}`, {
      department_id: form.value.department_id || null,
      job_title: form.value.job_title || null,
      status: form.value.status,
    })
  } else {
    await api.post('/employees', {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      department_id: form.value.department_id || null,
      job_title: form.value.job_title || null,
      status: form.value.status,
    })
  }
  closeModal()
  await load()
}

const viewEmployee = async (employee: any) => {
  const res = await api.get(`/employees/${employee.id}`)
  selectedEmployee.value = res.data?.data || employee
}

const archiveEmployee = async (employee: any) => {
  if (!confirm(`Archive ${employee.user?.name || 'employee'}?`)) return
  await api.delete(`/employees/${employee.id}`)
  await load()
}

const restoreEmployee = async (employee: any) => {
  if (!confirm(`Restore ${employee.user?.name || 'employee'}?`)) return
  await api.post(`/employees/${employee.id}/restore`)
  await load()
}

const terminateEmployee = async (employee: any) => {
  if (!confirm(`Terminate ${employee.user?.name || 'employee'}?`)) return
  await api.post(`/employees/${employee.id}/terminate`)
  await load()
}

const openAssignManagerModal = (employee: any) => {
  managerTargetEmployee.value = employee
  selectedManagerId.value = employee.manager_id || ''
  showAssignManagerModal.value = true
}

const closeAssignManagerModal = () => {
  showAssignManagerModal.value = false
  managerTargetEmployee.value = null
  selectedManagerId.value = ''
}

const submitAssignManager = async () => {
  if (!managerTargetEmployee.value || !selectedManagerId.value) return

  await api.post(`/employees/${managerTargetEmployee.value.id}/assign-manager`, {
    manager_id: selectedManagerId.value,
  })

  closeAssignManagerModal()
  await load()
}

const openTransferDepartmentModal = (employee: any) => {
  transferTargetEmployee.value = employee
  selectedDepartmentId.value = employee.department_id || ''
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
