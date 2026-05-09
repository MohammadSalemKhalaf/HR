<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
    <select :value="props.modelValue" @change="update($event.target.value)" class="w-full border rounded px-3 py-2">
      <option v-for="opt in props.options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
    </select>
    <p v-if="props.error" class="text-xs text-red-600 mt-1">{{ firstError }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
const props = defineProps<{ modelValue: any; options: Array<any>; label?: string; error?: string | string[] }>()
const emit = defineEmits(['update:modelValue'])
function update(v: any) { emit('update:modelValue', v) }
const firstError = computed(() => Array.isArray(props.error) ? props.error[0] : props.error)
</script>
