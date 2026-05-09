<template>
  <div class="overflow-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th v-for="col in columns" :key="col.key" class="text-left px-4 py-2 text-sm text-gray-600">{{ col.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id" class="border-t">
          <td v-for="col in columns" :key="col.key" class="px-4 py-3 text-sm text-gray-700">{{ item[col.key] }}</td>
        </tr>
        <tr v-if="items.length === 0">
          <td :colspan="columns.length" class="px-4 py-6 text-center text-sm text-gray-500">No results</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
defineProps<{ columns: Array<any>; items: Array<any> }>()
</script>
