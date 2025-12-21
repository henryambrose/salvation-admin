<script setup lang="ts">
import { ref, watch, computed, nextTick } from 'vue'
import { onClickOutside, useClickOutside } from '@vueuse/core'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  defaultValue?: (string | number)[]
  modelValue?: (string | number)[]
  class?: HTMLAttributes['class'],
  options: {
    id: string | number
    name: string
  }[],
  fetchUrl?: string // Optional: for AJAX data source
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: (string | number)[]): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue ?? [],
})

const search = ref('')
const loading = ref(false)
const fetchedOptions = ref<Array<{ id: string | number, name: string }>>([])
const open = ref(false)
const dropdownRef = ref(null)
const triggerRef = ref<HTMLElement | null>(null)
const searchInputRef = ref<HTMLInputElement | null>(null)

const displayOptions = computed(() => {
  const source = props.fetchUrl ? fetchedOptions.value : props.options
  if (!search.value) return source
  return source.filter(opt => opt.name.toLowerCase().includes(search.value.toLowerCase()))
})

watch(search, async (val) => {
  if (props.fetchUrl) {
    loading.value = true
    try {
      const res = await fetch(`${props.fetchUrl}?search=${encodeURIComponent(val)}`)
      const data = await res.json()
      fetchedOptions.value = data.options || []
    } catch (e) {
      fetchedOptions.value = []
    }
    loading.value = false
  }
}, { immediate: !!props.fetchUrl })

onClickOutside(dropdownRef, () => open.value = false)

function toggleSelect(id: string | number) {
  const arr = Array.isArray(modelValue.value) ? [...modelValue.value] : []
  const idx = arr.indexOf(id)
  if (idx === -1) {
    arr.push(id)
  } else {
    arr.splice(idx, 1)
  }
  emits('update:modelValue', arr)

  nextTick(() => {
    triggerRef.value?.focus()
  })
}

function removeSelected(id: string | number) {
  const arr = Array.isArray(modelValue.value) ? [...modelValue.value] : []
  emits('update:modelValue', arr.filter(i => i !== id))
}

function closeDropdown() {
  open.value = false
  search.value = ''
}

function handleKeyDown(event: KeyboardEvent) {
  if (event.key === 'Tab' && open.value) {
    closeDropdown()
    nextTick(() => {
      triggerRef.value?.focus()
    })
  } else if (event.key === 'Escape' && open.value) {
    event.preventDefault()
    closeDropdown()
  }
}
</script>

<template>
  <div class="relative w-full" ref="dropdownRef">
    <div
      ref="triggerRef"
      tabindex="0"
      class="border rounded px-3 py-1 bg-[#ffffff] cursor-pointer flex flex-wrap items-center justify-between text-black min-h-[40px]"
      @click="open = !open"
      @keydown="handleKeyDown"
    >
      <template v-if="modelValue && modelValue.length">
        <span
          v-for="id in modelValue"
          :key="id"
          class="bg-blue-100 text-blue-700 px-2 py-1 rounded mr-1 mb-1 flex items-center"
        >
          {{ props.options.find(opt => opt.id === id)?.name || id }}
          <button
            type="button"
            class="ml-1 text-xs text-blue-700 hover:text-red-600"
            @click.stop="removeSelected(id)"
          >×</button>
        </span>
      </template>
      <span v-if="!modelValue || !modelValue.length" class="text-gray-400">Select Options</span>
      <svg class="w-[1rem] h-[1rem] ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
    <div
      v-if="open"
      class="absolute left-0 right-0 z-50 bg-[#ffffff] border rounded shadow mt-1 max-h-60 overflow-auto"
    >
      <input
        ref="searchInputRef"
        type="text"
        v-model="search"
        class="w-full border-b px-3 py-2 outline-none focus:border-blue-500 text-black"
        placeholder="Search..."
        @keydown="handleKeyDown"
        @keydown.stop
      />
      <div v-if="loading" class="px-3 py-2 text-xs text-gray-400 text-black">Loading...</div>
      <div v-else>
        <div
          v-for="option in displayOptions"
          :key="option.id"
          class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-black flex items-center"
          @click.stop="toggleSelect(option.id)"
        >
          <input
            type="checkbox"
            :checked="modelValue && modelValue.includes(option.id)"
            class="mr-2"
            @change.stop="toggleSelect(option.id)"
          />
          {{ option.name }}
        </div>
        <div v-if="!displayOptions.length" class="px-3 py-2 text-gray-400 text-sm text-black">No options found</div>
      </div>
    </div>
  </div>
</template>
