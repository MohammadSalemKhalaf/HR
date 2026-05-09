<template>
  <div>
    <PageHeader title="Companies">
      <template #actions>
        <button @click="openCreate" class="btn btn-primary">New Company</button>
      </template>
    </PageHeader>

    <BaseCard>
      <BaseTable :columns="columns" :items="companies" />
    </BaseCard>

    <CompanyModal v-if="showModal" :company="editing" @close="closeModal" @saved="reload" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BaseTable from '@/components/base/BaseTable.vue'
import BaseCard from '@/components/base/BaseCard.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import CompanyModal from '@/modules/admin/components/companies/CompanyModal.vue'
import api from '@/api/axios'

const companies = ref([] as any[])
const showModal = ref(false)
const editing = ref(null as any)

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' }
]

async function load() {
  const { data } = await api.get('/admin/companies')
  companies.value = data
}

function openCreate() {
  editing.value = null
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function reload() {
  showModal.value = false
  load()
}

onMounted(load)
</script>
