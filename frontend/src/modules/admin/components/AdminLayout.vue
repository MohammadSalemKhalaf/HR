<template>
  <div class="min-h-screen bg-gray-50">
    <AdminNavbar />
    <div class="flex">
      <AdminSidebar />
      <main class="flex-1 p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdminNavbar from './AdminNavbar.vue'
import AdminSidebar from './AdminSidebar.vue'
</script>

<style scoped></style>
