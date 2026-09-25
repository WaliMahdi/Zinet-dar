<template>
  <div v-if="lastPage > 1" class="flex items-center justify-center gap-1 mt-8 flex-wrap">
    <!-- Prev -->
    <button
      @click="$emit('change', currentPage - 1)"
      :disabled="currentPage <= 1"
      class="btn-ghost btn-sm rounded-sm disabled:opacity-30 px-3"
      aria-label="Page précédente"
    >
      <ChevronLeft :size="16" />
    </button>

    <!-- Pages -->
    <template v-for="page in visiblePages" :key="page">
      <span v-if="page === '...'" class="px-2 text-muted text-sm">…</span>
      <button
        v-else
        @click="$emit('change', page)"
        :class="[
          'btn-sm rounded-sm px-3.5 py-2 text-sm font-medium transition-colors',
          page === currentPage
            ? 'bg-primary text-white shadow-warm-sm'
            : 'text-muted hover:text-primary hover:bg-primary/5',
        ]"
      >
        {{ page }}
      </button>
    </template>

    <!-- Next -->
    <button
      @click="$emit('change', currentPage + 1)"
      :disabled="currentPage >= lastPage"
      class="btn-ghost btn-sm rounded-sm disabled:opacity-30 px-3"
      aria-label="Page suivante"
    >
      <ChevronRight :size="16" />
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronLeft, ChevronRight } from '@lucide/vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage:    { type: Number, required: true },
})
defineEmits(['change'])

const visiblePages = computed(() => {
  const pages = []
  const { currentPage: cur, lastPage: last } = props
  if (last <= 7) {
    for (let i = 1; i <= last; i++) pages.push(i)
  } else {
    pages.push(1)
    if (cur > 3)  pages.push('...')
    for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i)
    if (cur < last - 2) pages.push('...')
    pages.push(last)
  }
  return pages
})
</script>
