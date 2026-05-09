<template>
  <BaseModal @close="$emit('close')">
    <template #title>{{ company ? 'Edit Company' : 'New Company' }}</template>
    <form @submit.prevent="save" class="space-y-4">
      <BaseInput v-model="form.name" label="Name" :error="errors.name" />
      <BaseInput v-model="form.email" label="Email" :error="errors.email" />
      <div class="flex justify-end gap-2">
        <button type="button" class="btn" @click="$emit('close')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import api, { ensureCsrf } from '@/api/axios'

const props = defineProps<{ company?: any }>()
const emit = defineEmits(['close', 'saved'])

const form = reactive({ id: null, name: '', email: '' })
const errors: any = reactive({})

watch(() => props.company, (v) => {
  if (v) Object.assign(form, v)
  else Object.assign(form, { id: null, name: '', email: '' })
}, { immediate: true })

async function save() {
  try {
    await ensureCsrf()
    if (form.id) {
      await api.put(`/admin/companies/${form.id}`, form)
    } else {
      await api.post('/admin/companies', form)
    }
    // clear errors
    for (const k in errors) delete errors[k]
    ;(window as any).$emit?.('company:saved')
    emit('saved')
  } catch (e: any) {
    if (e.type === 'validation') {
      Object.assign(errors, e.validation)
    } else {
      console.error(e)
    }
  }
}
</script>
