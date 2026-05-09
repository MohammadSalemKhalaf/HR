<template>
  <div>
    <PageHeader title="Departments">
      <template #actions>
        <button @click="openCreate" class="btn btn-primary">New Department</button>
      </template>
    </PageHeader>

    <BaseCard>
      <BaseTable :columns="columns" :items="departments" />
    </BaseCard>

    <div v-if="showModal">
      <!-- Department modal can be implemented similarly to CompanyModal -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BaseTable from '@/components/base/BaseTable.vue'
import BaseCard from '@/components/base/BaseCard.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import api from '@/api/axios'

const departments = ref([] as any[])
const showModal = ref(false)

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'company_name', label: 'Company' }
]

async function load() {
  const { data } = await api.get('/admin/departments')
  departments.value = data
}

function openCreate() {
  showModal.value = true
}

onMounted(load)
</script>
