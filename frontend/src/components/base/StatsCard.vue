<template>
  <div class="bg-white rounded p-4 shadow-sm">
    <div class="text-sm text-gray-500">{{ title }}</div>
    <div class="text-2xl font-semibold text-gray-800">{{ value }}</div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ title: string; value: string | number }>()
</script>
