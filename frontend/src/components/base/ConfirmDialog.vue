<template>
  <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <p class="mt-2 text-sm text-gray-600">{{ message }}</p>
      <div class="mt-4 flex justify-end gap-2">
        <button class="btn" @click="$emit('cancel')">Cancel</button>
        <button class="btn btn-danger" @click="$emit('confirm')">Confirm</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ title?: string; message?: string }>()
</script>
