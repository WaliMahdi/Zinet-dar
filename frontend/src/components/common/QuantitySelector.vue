<template>
  <div class="inline-flex items-center border border-border rounded-sm overflow-hidden">
    <button
      @click="decrement"
      :disabled="modelValue <= min || disabled"
      class="w-9 h-9 flex items-center justify-center text-muted hover:text-primary hover:bg-background
             disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
      aria-label="Diminuer"
    >
      <Minus :size="14" />
    </button>

    <span class="w-10 text-center text-sm font-medium text-primary select-none tabular-nums">
      {{ modelValue }}
    </span>

    <button
      @click="increment"
      :disabled="modelValue >= max || disabled"
      class="w-9 h-9 flex items-center justify-center text-muted hover:text-primary hover:bg-background
             disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
      aria-label="Augmenter"
    >
      <Plus :size="14" />
    </button>
  </div>
</template>

<script setup>
import { Minus, Plus } from '@lucide/vue'

const props = defineProps({
  modelValue: { type: Number, default: 1 },
  min: { type: Number, default: 1 },
  max: { type: Number, default: 99 },
  disabled: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

function decrement() {
  if (props.modelValue > props.min) emit('update:modelValue', props.modelValue - 1)
}
function increment() {
  if (props.modelValue < props.max) emit('update:modelValue', props.modelValue + 1)
}
</script>
