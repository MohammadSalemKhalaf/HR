<template>
  <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-50 text-indigo-700">{{ label }}</span>
</template>

<script setup lang="ts">
const props = defineProps<{ label: string }>()
</script>
