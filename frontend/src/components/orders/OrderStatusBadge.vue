<template>
  <span :class="statusClass">{{ statusLabel }}</span>
</template>

<script setup>
import { computed } from 'vue'
import { ORDER_STATUS_LABELS, getStatusClass } from '@/utils/helpers'

const props = defineProps({
  statut: { type: String, required: true },
})

const statusLabel = computed(() => ORDER_STATUS_LABELS[props.statut] || props.statut)
const statusClass = computed(() => getStatusClass(props.statut))
</script>
