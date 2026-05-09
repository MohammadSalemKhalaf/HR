<template>
  <div>
    <PageHeader title="Employees">
      <template #actions>
        <button @click="openCreate" class="btn btn-primary">New Employee</button>
      </template>
    </PageHeader>

    <BaseCard>
      <BaseTable :columns="columns" :items="employees" />
    </BaseCard>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BaseTable from '@/components/base/BaseTable.vue'
import BaseCard from '@/components/base/BaseCard.vue'
import PageHeader from '@/components/base/PageHeader.vue'
import api from '@/api/axios'

const employees = ref([] as any[])

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' }
]

async function load() {
  const { data } = await api.get('/admin/employees')
  employees.value = data
}

function openCreate() {
  // implement modal later
}

onMounted(load)
</script>
