<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { onClickOutside } from '@vueuse/core'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes['class'],
  options: {
    id: string | number
    name: string
  }[],
  fetchUrl?: string // Optional: for AJAX data source
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

const search = ref('')
const loading = ref(false)
const fetchedOptions = ref<Array<{ id: string | number, name: string }>>([])
const open = ref(false)
const dropdownRef = ref(null)

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


function fetchOption(page = 1) {
  router.get('/member/search-options', {
    // search: search.value,
  }, {
    preserveState: true,
    replace: true,
  });
}

// console.log('fetchOption: ', fetchOption());
</script>

<template>
  <div class="relative w-full" ref="dropdownRef">
    <div
      class="border rounded px-3 py-1 bg-white cursor-pointer flex items-center justify-between text-black"
      @click="open = !open"
    >
      <span>
        {{
          props.options.find(opt => opt.id === modelValue)?.name ||
          'Select Option'
        }}
      </span>
      <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
    <div
      v-if="open"
      class="absolute left-0 right-0 z-10 bg-white border rounded shadow mt-1 max-h-60 overflow-auto"
    >
      <input
        type="text"
        v-model="search"
        class="w-full border-b px-3 py-2 outline-none focus:border-blue-500 text-black"
        placeholder="Search..."
        @keydown.stop
      />
      <div v-if="loading" class="px-3 py-2 text-xs text-gray-400 text-black">Loading...</div>
      <div v-else>
        <div
          v-for="option in displayOptions"
          :key="option.id"
          class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-black"
          @click="emits('update:modelValue', option.id); open = false"
        >
          {{ option.name }}
        </div>
        <div v-if="!displayOptions.length" class="px-3 py-2 text-gray-400 text-sm text-black">No options found</div>
      </div>
    </div>
  </div>
</template>
