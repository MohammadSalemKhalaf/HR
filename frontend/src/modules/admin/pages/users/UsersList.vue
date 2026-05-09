<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
        <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
          Users
          <span v-if="showArchived" class="ml-2 text-sm font-medium text-slate-500">(Archived)</span>
        </h2>
        <p class="mt-2 text-sm text-slate-600">Review accounts, roles, and archived users.</p>
      </div>

      <div class="flex flex-wrap gap-3">
        <button
          v-if="showArchived"
          @click="showArchived = false"
          class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
        >
          Active Users
        </button>
        <button
          v-else
          @click="showArchived = true"
          class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
        >
          Archived Users
        </button>
      </div>
    </div>

    <!-- Users Table Card -->
    <div class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
      <div class="border-b border-slate-200 px-6 py-4">
        <p class="text-sm font-medium text-slate-500">Users List</p>
        <h3 class="text-lg font-semibold text-slate-900">Total {{ pagination.total }}</h3>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="px-6 py-12 text-center text-slate-500">
        Loading users...
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="px-6 py-12 text-center text-rose-600">
        {{ error }}
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
              <th class="px-6 py-3 text-left">Name</th>
              <th class="px-6 py-3 text-left">Email</th>
              <th class="px-6 py-3 text-left">Role</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100 bg-white text-sm">
            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50">
              <td class="px-6 py-4 font-medium text-slate-900">{{ user.name }}</td>
              <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
              <td class="px-6 py-4 text-slate-600">{{ user.role_name || user.role }}</td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-3">
                  <template v-if="showArchived">
                    <button
                      @click="restoreUser(user.id)"
                      :disabled="restoringId === user.id"
                      class="font-semibold text-emerald-600 hover:text-emerald-700 disabled:opacity-50"
                    >
                      {{ restoringId === user.id ? 'Restoring...' : 'Restore' }}
                    </button>
                  </template>
                  <template v-else>
                    <router-link
                      :to="{ name: 'UserEdit', params: { id: user.id } }"
                      class="font-semibold text-cyan-700 hover:text-cyan-800"
                    >
                      Edit
                    </router-link>
                    <button
                      @click="archiveUser(user.id)"
                      :disabled="archivingId === user.id"
                      class="font-semibold text-rose-600 hover:text-rose-700 disabled:opacity-50"
                    >
                      {{ archivingId === user.id ? 'Archiving...' : 'Archive' }}
                    </button>
                  </template>
                </div>
              </td>
            </tr>
            <tr v-if="users.length === 0">
              <td colspan="4" class="px-6 py-12 text-center text-slate-500">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && !error && users.length > 0" class="border-t border-slate-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">
            Page {{ pagination.current_page }} of {{ pagination.last_page }}
          </div>
          <div class="flex gap-2">
            <button
              @click="previousPage"
              :disabled="pagination.current_page === 1"
              class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
            >
              Previous
            </button>
            <button
              @click="nextPage"
              :disabled="pagination.current_page === pagination.last_page"
              class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import api from '@/api/axios'

interface User {
  id: string
  name: string
  email: string
  role: string
  role_name: string
  deleted_at: string | null
}

interface Pagination {
  current_page: number
  per_page: number
  total: number
  last_page: number
}

const users = ref<User[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const showArchived = ref(false)
const archivingId = ref<string | null>(null)
const restoringId = ref<string | null>(null)
const pagination = ref<Pagination>({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1
})

const currentPage = ref(1)

const fetchUsers = async () => {
  loading.value = true
  error.value = null
  try {
    const params = new URLSearchParams()
    if (showArchived.value) {
      params.append('archived', 'true')
    }
    params.append('page', currentPage.value.toString())

    const response = await api.get('/admin/users', { params })
    users.value = response.data.data.data || response.data.data || []
    pagination.value = {
      current_page: response.data.data.pagination?.current_page || 1,
      per_page: response.data.data.pagination?.per_page || 10,
      total: response.data.data.pagination?.total || 0,
      last_page: response.data.data.pagination?.last_page || 1
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load users'
    console.error('Error fetching users:', err)
  } finally {
    loading.value = false
  }
}

const archiveUser = async (userId: string) => {
  if (!confirm('Archive this user?')) return

  archivingId.value = userId
  try {
    await api.delete(`/admin/users/${userId}`)
    await fetchUsers()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to archive user'
    console.error('Error archiving user:', err)
  } finally {
    archivingId.value = null
  }
}

const restoreUser = async (userId: string) => {
  if (!confirm('Restore this user?')) return

  restoringId.value = userId
  try {
    await api.put(`/admin/users/${userId}/restore`)
    await fetchUsers()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to restore user'
    console.error('Error restoring user:', err)
  } finally {
    restoringId.value = null
  }
}

const nextPage = () => {
  if (currentPage.value < pagination.value.last_page) {
    currentPage.value++
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

watch([showArchived, currentPage], () => {
  currentPage.value = 1
  fetchUsers()
})

onMounted(fetchUsers)
</script>
