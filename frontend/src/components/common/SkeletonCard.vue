<template>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
    <div
      v-for="n in count"
      :key="n"
      class="bg-surface rounded-lg border border-border/50 overflow-hidden animate-pulse"
    >
      <!-- Image skeleton -->
      <div class="aspect-[3/4] bg-gradient-to-br from-border to-background"></div>
      <!-- Content skeleton -->
      <div class="p-4 space-y-2">
        <div class="h-3 bg-border rounded-full w-1/3"></div>
        <div class="h-4 bg-border rounded-full w-3/4"></div>
        <div class="h-3 bg-border rounded-full w-1/2"></div>
        <div class="h-8 bg-border rounded-sm w-full mt-3"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 8,
  },
})
</script>
