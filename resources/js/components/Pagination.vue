<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { computed } from 'vue';

interface Props {
  currentPage: number;
  lastPage: number;
  prevPageUrl?: string | null;
  nextPageUrl?: string | null;
  from?: number;
  to?: number;
  total?: number;
  showInfo?: boolean;
}

interface Emits {
  (e: 'page-changed', page: number): void;
}

const props = withDefaults(defineProps<Props>(), {
  showInfo: true,
});

const emits = defineEmits<Emits>();

const paginationInfo = computed(() => {
  if (!props.from || !props.to || !props.total) return null;
  return `Showing ${props.from} to ${props.to} of ${props.total} entries`;
});

const goToPage = (page: number) => {
  if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
    emits('page-changed', page);
  }
};

const goToPrevious = () => {
  if (props.prevPageUrl) {
    goToPage(props.currentPage - 1);
  }
};

const goToNext = () => {
  if (props.nextPageUrl) {
    goToPage(props.currentPage + 1);
  }
};
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2">
    <!-- Pagination info -->
    <div v-if="showInfo && paginationInfo" class="text-sm text-gray-700">
      {{ paginationInfo }}
    </div>

    <!-- Pagination controls -->
    <div class="flex items-center gap-2">
      <TooltipProvider>
        <Tooltip>
          <TooltipTrigger asChild>
            <Button
              variant="outline"
              size="sm"
              :disabled="!prevPageUrl"
              @click="goToPrevious"
              class="px-3 py-1"
            >
              ← Prev
            </Button>
          </TooltipTrigger>
          <TooltipContent>
            <p>Go to the previous page</p>
          </TooltipContent>
        </Tooltip>

        <div class="flex items-center gap-1 text-sm text-gray-600">
          <span>Page</span>
          <select
            v-if="lastPage > 1"
            :value="currentPage"
            @change="(event) => goToPage(Number((event.target as HTMLSelectElement).value))"
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
          >
            <option v-for="page in lastPage" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span v-else>{{ currentPage }}</span>
          <span>of {{ lastPage }}</span>
        </div>

        <Tooltip>
          <TooltipTrigger asChild>
            <Button
              variant="outline"
              size="sm"
              :disabled="!nextPageUrl"
              @click="goToNext"
              class="px-3 py-1"
            >
              Next →
            </Button>
          </TooltipTrigger>
          <TooltipContent>
            <p>Go to the next page</p>
          </TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </div>
  </div>
</template>