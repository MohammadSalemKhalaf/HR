<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">Admin Login</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <BaseInput v-model="form.email" label="Email" />
        <BaseInput v-model="form.password" label="Password" type="password" />
        <div>
          <button class="btn btn-primary w-full" type="submit">Login</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '@/components/base/BaseInput.vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })

async function submit() {
  try {
    await auth.login(form)
    router.push('/admin')
  } catch (e) {
    // errors handled in auth store
  }
}
</script>
