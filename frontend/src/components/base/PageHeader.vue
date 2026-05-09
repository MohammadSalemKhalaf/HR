<template>
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-xl font-semibold">{{ title }}</h1>
      <p v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</p>
    </div>
    <div>
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ title: string; subtitle?: string }>()
</script>
