<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="container mx-auto p-4 flex justify-between items-center">
        <h1 class="text-lg font-bold">Backoffice</h1>
        <nav class="space-x-4 text-sm text-gray-600">
          <router-link to="/admin">Dashboard</router-link>
        </nav>
      </div>
    </header>
    <main class="container mx-auto p-6">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
</script>

<style scoped>
</style>
