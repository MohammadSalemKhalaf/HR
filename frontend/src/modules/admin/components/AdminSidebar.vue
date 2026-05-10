<template>
  <aside class="w-64 bg-white border-r min-h-screen">
    <nav class="p-4">
      <ul class="space-y-2">
        <li>
          <router-link to="/admin" class="block py-2 px-3 rounded hover:bg-indigo-50">Dashboard</router-link>
        </li>
        <li>
          <router-link to="/admin/users" class="block py-2 px-3 rounded hover:bg-indigo-50">Users</router-link>
        </li>
        <li>
          <router-link to="/admin/companies" class="block py-2 px-3 rounded hover:bg-indigo-50">Companies</router-link>
        </li>
        <li>
          <router-link to="/admin/job-categories" class="block py-2 px-3 rounded hover:bg-indigo-50">Job Categories</router-link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script setup lang="ts"></script>
