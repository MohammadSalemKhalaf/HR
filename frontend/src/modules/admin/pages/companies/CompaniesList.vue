<template>
  <div class="space-y-6">
    <PageHeader title="Companies" subtitle="Manage all companies in the system">
      <template #actions>
        <button @click="openCreate" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
          + New Company
        </button>
      </template>
    </PageHeader>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow p-8">
      <div class="flex items-center justify-center space-x-4">
        <LoadingSpinner />
        <span class="text-gray-600">Loading companies...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
      <p class="font-semibold">Error loading companies</p>
      <p class="text-sm mt-1">{{ error }}</p>
      <button @click="load" class="mt-3 px-3 py-1 bg-red-600 text-white rounded text-sm">Retry</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="companies.length === 0" class="bg-white rounded-lg shadow">
      <EmptyState
        title="No companies yet"
        description="Create your first company to get started"
        @action="openCreate"
      />
    </div>

    <!-- Companies Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <BaseTable 
        :columns="columns" 
        :items="companies"
        @edit="editCompany"
        @delete="deleteCompany"
      />
    </div>

    <!-- Company Modal -->
    <CompanyModal v-if="showModal" :company="editing" @close="closeModal" @saved="reload" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BaseTable from '@/components/base/BaseTable.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import CompanyModal from '@/modules/admin/components/companies/CompanyModal.vue'
import LoadingSpinner from '@/components/base/LoadingSpinner.vue'
import EmptyState from '@/components/base/EmptyState.vue'
import api from '@/api/axios'

const companies = ref([] as any[])
const showModal = ref(false)
const editing = ref(null as any)
const loading = ref(false)
const error = ref('')

const columns = [
  { key: 'name', label: 'Company Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'phone', label: 'Phone' },
  { key: 'created_at', label: 'Created' }
]

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/companies')
    companies.value = data.data || data
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Failed to load companies'
    console.error('[Companies] Load error:', e)
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editing.value = null
  showModal.value = true
}

function editCompany(company: any) {
  editing.value = company
  showModal.value = true
}

async function deleteCompany(company: any) {
  if (confirm(`Are you sure you want to delete "${company.name}"?`)) {
    try {
      await api.delete(`/companies/${company.id}`)
      await load()
    } catch (e: any) {
      alert('Failed to delete company: ' + (e.response?.data?.message || e.message))
    }
  }
}

function closeModal() {
  showModal.value = false
}

function reload() {
  closeModal()
  load()
}

onMounted(load)
</script>
