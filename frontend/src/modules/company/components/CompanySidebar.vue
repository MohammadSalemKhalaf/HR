<template>
  <aside class="w-64 bg-white border-r min-h-screen">
    <nav class="p-4">
      <ul class="space-y-2">
        <li>
          <router-link to="/company" class="block py-2 px-3 rounded hover:bg-cyan-50">Dashboard</router-link>
        </li>
        <li>
          <router-link to="/company/my-company" class="block py-2 px-3 rounded hover:bg-cyan-50">My Company</router-link>
        </li>
        <li>
          <router-link to="/company/departments" class="block py-2 px-3 rounded hover:bg-cyan-50">Departments</router-link>
        </li>
        <li>
          <router-link to="/company/employees" class="block py-2 px-3 rounded hover:bg-cyan-50">Employees</router-link>
        </li>
        <li>
          <router-link to="/company/vacancies" class="block py-2 px-3 rounded hover:bg-cyan-50">Job Vacancies</router-link>
        </li>
        <li>
          <router-link to="/company/applications" class="block py-2 px-3 rounded hover:bg-cyan-50">Job Applications</router-link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script setup lang="ts"></script>
