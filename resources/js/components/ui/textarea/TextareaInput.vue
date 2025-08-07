
<script setup lang="ts">
    import type { HTMLAttributes } from 'vue'
    import { cn } from '@/lib/utils'
    import { useVModel } from '@vueuse/core'

    const props = defineProps<{
    defaultValue?: string | number
    modelValue?: string | number
    class?: HTMLAttributes['class'],

      name: string,
      id: string,
      placeholder?: string,
      rows?: number,
      error?: string,
    }>()

    const emit = defineEmits(['update:modelValue'])

    // Use useVModel to handle the v-model binding properly
    const modelValue = useVModel(props, 'modelValue', emit, {
      passive: true,
      defaultValue: props.defaultValue,
    })
    </script>

<!-- resources/js/Components/TextareaInput.vue -->
<template>
  <div>
    <textarea
      :id="props.id"
      :name="props.name"
      v-model="modelValue"
      :placeholder="props.placeholder"
      :rows="props.rows || 3"
      :class="cn(
        'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
        props.class,
      )"
    ></textarea>
    <p v-if="props.error" class="mt-1 text-sm text-red-600">{{ props.error }}</p>
  </div>
</template>
