<template>
  <div class="text-center py-12">
    <div class="text-gray-400">{{ message }}</div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ message?: string }>()
</script>
