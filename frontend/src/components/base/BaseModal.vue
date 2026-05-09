<template>
  <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
      <header class="mb-4">
        <h3 class="text-lg font-semibold"><slot name="title" /></h3>
      </header>
      <div>
        <slot />
      </div>
      <button class="absolute top-4 right-4 text-gray-500" @click="$emit('close')">✕</button>
    </div>
  </div>
</template>

<script setup lang="ts"></script>
