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
  fetchUrl?: string, // Optional: for AJAX data source
  tabindex?: string | number
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
  // Combine static options with fetched options
  const allOptions = [...props.options, ...fetchedOptions.value];
  
  if (!search.value) return allOptions;
  return allOptions.filter(opt => opt.name.toLowerCase().includes(search.value.toLowerCase()));
})

// watch(search, async (val) => {
//   if (props.fetchUrl) {
//     loading.value = true
//     try {
//       const res = await fetch(`${props.fetchUrl}?search=${encodeURIComponent(val)}`)
//       const data = await res.json()
//       fetchedOptions.value = data.options || []
//     } catch (e) {
//       fetchedOptions.value = []
//     }
//     loading.value = false
//   }
// }, { immediate: !!props.fetchUrl })

onClickOutside(dropdownRef, () => open.value = false)


function fetchOption(page = 1) {
  router.get('/member/search-options', {
    // search: search.value,
  }, {
    preserveState: true,
    replace: true,
  });
}

</script>

<template>
  <div class="relative w-full" ref="dropdownRef">
    <div
      :class="cn(
        'border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] cursor-pointer items-center justify-between',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'shadow-xs focus:ring-2 focus:ring-gray-900 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
        props.class,
      )"
      @click="open = !open"
      tabindex="0"
    >
      <span class="text-foreground flex-1">
        {{
          (props.options.find(opt => opt.id === modelValue)?.name ||
           fetchedOptions.find(opt => opt.id === modelValue)?.name) ||
          'Select Option'
        }}
      </span>
      <svg class="absolute top-[2px] right-[15px] h-full w-[1rem] h-[1rem] text-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
    <div
      v-if="open"
      class="absolute left-0 right-0 z-50 bg-background border border-input rounded-md shadow-lg mt-1 max-h-60 overflow-auto"
    >
      <input
        type="text"
        v-model="search"
        class="w-full border-b border-input px-3 py-2 outline-none focus:border-ring focus:ring-ring/50 focus:ring-[3px] text-foreground bg-transparent"
        placeholder="Search..."
        @keydown.stop
      />
      <div v-if="loading" class="px-3 py-2 text-xs text-muted-foreground">Loading...</div>
      <div v-else>
        <div
          v-for="option in displayOptions"
          :key="option.id"
          class="px-3 py-2 cursor-pointer hover:bg-accent text-foreground"
          @click="emits('update:modelValue', option.id); open = false"
        >
          {{ option.name }}
        </div>
        <div v-if="!displayOptions.length" class="px-3 py-2 text-muted-foreground text-sm">No options found</div>
      </div>
    </div>
  </div>
</template>
