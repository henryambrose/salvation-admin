<script setup lang="ts">
import { ref, watch, computed, nextTick, onUnmounted } from 'vue'
import { onClickOutside } from '@vueuse/core'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'
import { ChevronDown, X, Search, Loader2, Check } from 'lucide-vue-next'

interface Option {
  id: string | number
  name: string
}

const props = withDefaults(defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes['class']
  options: Option[]
  fetchUrl?: string
  searchParam?: string
  debounceMs?: number
  placeholder?: string
  emptyMessage?: string
  disabled?: boolean
  clearable?: boolean
  searchable?: boolean
  tabindex?: string | number
}>(), {
  searchParam: 'search',
  debounceMs: 300,
  placeholder: 'Select option...',
  emptyMessage: 'No options found',
  disabled: false,
  clearable: true,
  searchable: true,
})

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
  (e: 'search', payload: string): void
}>()

// V-Model
const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

// State
const search = ref('')
const loading = ref(false)
const error = ref<string | null>(null)
const fetchedOptions = ref<Option[]>([])
const open = ref(false)
const highlightedIndex = ref(-1)
const dropdownRef = ref<HTMLElement | null>(null)
const triggerRef = ref<HTMLElement | null>(null)
const searchInputRef = ref<HTMLInputElement | null>(null)
let debounceTimeout: ReturnType<typeof setTimeout> | null = null
let abortController: AbortController | null = null

// Computed: All options (static + fetched)
const allOptions = computed(() => {
  if (props.fetchUrl && fetchedOptions.value.length > 0) {
    return fetchedOptions.value
  }
  return props.options
})

// Computed: Filtered options based on search
const displayOptions = computed(() => {
  if (!search.value) return allOptions.value

  return allOptions.value.filter(opt =>
    opt.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

// Computed: Selected option
const selectedOption = computed(() => {
  return allOptions.value.find(opt => opt.id === modelValue.value)
})

// Computed: Display value
const displayValue = computed(() => {
  return selectedOption.value?.name || props.placeholder
})

// Computed: Show placeholder style
const showPlaceholder = computed(() => !selectedOption.value)

// Force re-render on scroll/resize to update position
const forceUpdate = ref(0)

// Computed: Dropdown position style
const dropdownStyle = computed(() => {
  // Include forceUpdate to trigger recalculation
  const _ = forceUpdate.value

  if (!triggerRef.value || !open.value) {
    return { display: 'none' }
  }
  const rect = triggerRef.value.getBoundingClientRect()
  return {
    position: 'fixed',
    left: `${rect.left}px`,
    top: `${rect.bottom + 4}px`,
    width: `${rect.width}px`,
    maxHeight: '300px',
  }
})

// Close dropdown when clicking outside
onClickOutside(dropdownRef, () => {
  if (open.value) {
    closeDropdown()
  }
}, { ignore: [triggerRef] })

function updatePosition() {
  if (open.value) {
    forceUpdate.value++
  }
}

// Add scroll and resize listeners when dropdown is open
watch(open, (isOpen) => {
  if (isOpen) {
    window.addEventListener('scroll', updatePosition, true)
    window.addEventListener('resize', updatePosition)
  } else {
    window.removeEventListener('scroll', updatePosition, true)
    window.removeEventListener('resize', updatePosition)
  }
})

// Cleanup on unmount
onUnmounted(() => {
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }
  if (abortController) {
    abortController.abort()
  }
  window.removeEventListener('scroll', updatePosition, true)
  window.removeEventListener('resize', updatePosition)
})

/**
 * Debounced search function
 */
function performSearch(query: string) {
  // Clear existing timeout
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }

  // Set new timeout
  debounceTimeout = setTimeout(async () => {
    await fetchFromServer(query)
  }, props.debounceMs)
}

/**
 * Fetch options from server
 */
async function fetchFromServer(query: string) {
  if (!props.fetchUrl) return

  // Abort previous request
  if (abortController) {
    abortController.abort()
  }

  abortController = new AbortController()
  loading.value = true
  error.value = null

  try {
    const url = new URL(props.fetchUrl, window.location.origin)
    url.searchParams.set(props.searchParam, query)

    const response = await fetch(url.toString(), {
      signal: abortController.signal,
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    fetchedOptions.value = data.options || data || []
  } catch (err: any) {
    if (err.name !== 'AbortError') {
      error.value = 'Failed to load options'
      console.error('Search error:', err)
    }
  } finally {
    loading.value = false
    abortController = null
  }
}

/**
 * Watch search query
 */
watch(search, (newQuery) => {
  emits('search', newQuery)
  highlightedIndex.value = -1

  if (props.fetchUrl) {
    performSearch(newQuery)
  }
})

/**
 * Toggle dropdown
 */
function toggleDropdown() {
  if (props.disabled) return

  if (open.value) {
    closeDropdown()
  } else {
    openDropdown()
  }
}

/**
 * Open dropdown
 */
async function openDropdown() {
  if (props.disabled) return

  open.value = true
  search.value = ''
  highlightedIndex.value = -1
  error.value = null

  // Focus search input if searchable
  if (props.searchable) {
    await nextTick()
    searchInputRef.value?.focus()
  }

  // Fetch initial data if fetchUrl provided and no query
  if (props.fetchUrl && !search.value) {
    await fetchFromServer('')
  }
}

/**
 * Close dropdown
 */
function closeDropdown() {
  open.value = false
  search.value = ''
  highlightedIndex.value = -1

  // Cancel any pending requests
  if (abortController) {
    abortController.abort()
  }
}

/**
 * Select option
 */
function selectOption(option: Option) {
  modelValue.value = option.id
  closeDropdown()
}

/**
 * Clear selection
 */
function clearSelection(event: Event) {
  event.stopPropagation()
  modelValue.value = undefined
}

/**
 * Keyboard navigation
 */
function handleKeyDown(event: KeyboardEvent) {
  if (!open.value && (event.key === 'Enter' || event.key === ' ')) {
    event.preventDefault()
    openDropdown()
    return
  }

  if (!open.value) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      highlightedIndex.value = Math.min(
        highlightedIndex.value + 1,
        displayOptions.value.length - 1
      )
      scrollToHighlighted()
      break

    case 'ArrowUp':
      event.preventDefault()
      highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0)
      scrollToHighlighted()
      break

    case 'Home':
      event.preventDefault()
      highlightedIndex.value = 0
      scrollToHighlighted()
      break

    case 'End':
      event.preventDefault()
      highlightedIndex.value = displayOptions.value.length - 1
      scrollToHighlighted()
      break

    case 'Enter':
      event.preventDefault()
      if (highlightedIndex.value >= 0 && displayOptions.value[highlightedIndex.value]) {
        selectOption(displayOptions.value[highlightedIndex.value])
      }
      break

    case 'Escape':
      event.preventDefault()
      closeDropdown()
      break
  }
}

/**
 * Scroll highlighted item into view
 */
async function scrollToHighlighted() {
  await nextTick()

  const container = dropdownRef.value?.querySelector('[role="listbox"]')
  if (!container) return

  const highlightedElement = container.querySelector(
    `[data-index="${highlightedIndex.value}"]`
  ) as HTMLElement

  if (highlightedElement) {
    highlightedElement.scrollIntoView({
      block: 'nearest',
      behavior: 'smooth',
    })
  }
}
</script>

<template>
  <div class="relative w-full">
    <!-- Trigger Button -->
    <button
      ref="triggerRef"
      type="button"
      role="combobox"
      :disabled="disabled"
      :aria-expanded="open"
      aria-haspopup="listbox"
      :aria-label="placeholder"
      :tabindex="tabindex"
      :class="cn(
        'flex h-9 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-xs transition-colors outline-none',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        open && 'border-ring ring-ring/50 ring-[3px]',
        disabled
          ? 'cursor-not-allowed opacity-50'
          : 'cursor-pointer bg-background hover:bg-accent/50',
        showPlaceholder ? 'text-muted-foreground' : 'text-foreground',
        'border-input',
        props.class
      )"
      @click="toggleDropdown"
      @keydown="handleKeyDown"
    >
      <span class="flex-1 truncate text-left">{{ displayValue }}</span>

      <!-- Clear button -->
      <X
        v-if="clearable && selectedOption && !disabled"
        class="h-4 w-4 shrink-0 text-muted-foreground hover:text-foreground transition-colors"
        @click="clearSelection"
      />

      <!-- Chevron icon -->
      <ChevronDown
        :class="cn(
          'h-4 w-4 shrink-0 text-muted-foreground transition-transform duration-200',
          open && 'rotate-180'
        )"
      />
    </button>

    <!-- Dropdown Content -->
    <Teleport to="body">
      <div
        v-if="open"
        ref="dropdownRef"
        :aria-label="`${placeholder} options`"
        :class="cn(
          'fixed z-[9999] rounded-md border border-input bg-popover shadow-lg',
          'animate-in fade-in-0 zoom-in-95'
        )"
        :style="dropdownStyle"
      >
        <!-- Search Input -->
        <div
          v-if="searchable"
          class="flex items-center border-b border-input px-3 py-2"
        >
          <Search class="mr-2 h-4 w-4 shrink-0 text-muted-foreground" />
          <input
            ref="searchInputRef"
            v-model="search"
            type="text"
            class="flex-1 bg-transparent text-sm outline-none placeholder:text-muted-foreground"
            placeholder="Search..."
            @keydown="handleKeyDown"
            @keydown.stop
          />
          <X
            v-if="search"
            class="ml-2 h-4 w-4 cursor-pointer text-muted-foreground hover:text-foreground transition-colors"
            @click="search = ''"
          />
        </div>

        <!-- Options List -->
        <div class="max-h-60 overflow-y-auto p-1" role="listbox">
          <!-- Loading State -->
          <div
            v-if="loading"
            class="flex items-center justify-center py-6 text-sm text-muted-foreground"
          >
            <Loader2 class="mr-2 h-4 w-4 animate-spin" />
            Loading...
          </div>

          <!-- Error State -->
          <div
            v-else-if="error"
            class="py-3 px-2 text-center text-sm text-destructive"
          >
            {{ error }}
          </div>

          <!-- Options -->
          <template v-else-if="displayOptions.length > 0">
            <div
              v-for="(option, index) in displayOptions"
              :key="option.id"
              role="option"
              :aria-selected="modelValue === option.id"
              :data-index="index"
              :class="cn(
                'relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors',
                'focus:bg-accent focus:text-accent-foreground',
                highlightedIndex === index && 'bg-accent text-accent-foreground',
                modelValue === option.id && 'bg-accent/50'
              )"
              @click="selectOption(option)"
              @mouseenter="highlightedIndex = index"
            >
              <span class="flex-1">{{ option.name }}</span>
              <Check
                v-if="modelValue === option.id"
                class="ml-2 h-4 w-4 shrink-0"
              />
            </div>
          </template>

          <!-- Empty State -->
          <div
            v-else
            class="py-6 text-center text-sm text-muted-foreground"
          >
            {{ emptyMessage }}
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
