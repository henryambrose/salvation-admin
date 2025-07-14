<template>
    <div class="w-full p-10">
      <!-- Search and controls -->
      <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search..."
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
          />
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600">Show</span>
          <select
            v-model="perPage"
            class="border rounded-lg px-2 py-1 focus:ring-2 focus:ring-primary focus:border-primary"
          >
            <option v-for="option in [5, 10, 25, 50, 100]" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <span class="text-sm text-gray-600">entries</span>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                v-for="column in columns"
                :key="column.key"
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                @click="sortBy(column.key)"
              >
                <div class="flex items-center space-x-1">
                  <span>{{ column.label }}</span>
                  <span v-if="sortColumn === column.key" class="text-gray-700">
                    <svg v-if="sortDirection === 'asc'" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-up"><path d="m18 15-6-6-6 6"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                  </span>
                </div>
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(item, index) in paginatedData" :key="index" class="hover:bg-gray-50">
              <td
                v-for="column in columns"
                :key="column.key"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
              >
                {{ item[column.key] }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <slot name="actions" :row="item">
                  <button
                    @click="editItem(item)"
                    class="text-primary hover:text-primary-dark mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteItem(item)"
                    class="text-red-600 hover:text-red-800"
                  >
                    Delete
                  </button>
                </slot>
              </td>
            </tr>
            <tr v-if="filteredData.length === 0">
              <td :colspan="columns.length + 1" class="px-6 py-4 text-center text-sm text-gray-500">
                No data found
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex flex-col sm:flex-row justify-between items-center">
        <div class="text-sm text-gray-600 mb-4 sm:mb-0">
          Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ filteredData.length }} entries
        </div>
        <div class="flex justify-center">
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button
              @click="currentPage = 1"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">First</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevrons-left"><path d="m11 17-5-5 5-5"/><path d="m18 17-5-5 5-5"/></svg>
            </button>
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Previous</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button
              v-for="page in displayedPages"
              :key="page"
              @click="currentPage = page"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                currentPage === page
                  ? 'z-10 bg-primary border-primary text-white'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Next</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            <button
              @click="currentPage = totalPages"
              :disabled="currentPage === totalPages"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Last</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevrons-right"><path d="m13 17 5-5-5-5"/><path d="m6 17 5-5-5-5"/></svg>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import { router } from '@inertiajs/vue3';
  // Props
  const props = defineProps({
    data: {
      type: Array,
      default: () => []
    },
    columns: {
      type: Array,
      default: () => [
        { key: 'id', label: 'ID' },
        { key: 'name', label: 'Name' },
        { key: 'email', label: 'Email' },
        { key: 'created_at', label: 'Created At' }
      ]
    },
    filters: Object,
    fetchUrl: String,
  });

  // Emits
  const emit = defineEmits(['edit', 'delete']);

  // State
  const searchQuery = ref(props.filters.search || '');
  const sortColumn = ref(props.filters.sort || '');
  const sortDirection = ref(props.filters.direction || 'asc');
  const currentPage = ref(1);
  const perPage = ref(props.filters.perPage || 10);

    // const search = ref(props.filters.search || '');
    // const sort = ref(props.filters.sort || '');
    // const direction = ref(props.filters.direction || 'asc');
    // const perPage = ref(props.filters.perPage || 10);

  // Sample data for demonstration
  const sampleData = ref([
    { id: 1, name: 'John Doe', email: 'john@example.com', created_at: '2023-05-15' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', created_at: '2023-05-16' },
    { id: 3, name: 'Bob Johnson', email: 'bob@example.com', created_at: '2023-05-17' },
    { id: 4, name: 'Alice Brown', email: 'alice@example.com', created_at: '2023-05-18' },
    { id: 5, name: 'Charlie Wilson', email: 'charlie@example.com', created_at: '2023-05-19' },
    { id: 6, name: 'Diana Miller', email: 'diana@example.com', created_at: '2023-05-20' },
    { id: 7, name: 'Edward Davis', email: 'edward@example.com', created_at: '2023-05-21' },
    { id: 8, name: 'Fiona Clark', email: 'fiona@example.com', created_at: '2023-05-22' },
    { id: 9, name: 'George White', email: 'george@example.com', created_at: '2023-05-23' },
    { id: 10, name: 'Hannah Green', email: 'hannah@example.com', created_at: '2023-05-24' },
    { id: 11, name: 'Ian Black', email: 'ian@example.com', created_at: '2023-05-25' },
    { id: 12, name: 'Julia Reed', email: 'julia@example.com', created_at: '2023-05-26' },
  ]);

  // Computed properties
  const tableData = computed(() => {
    return props.data.length > 0 ? props.data : sampleData.value;
  });

  const filteredData = computed(() => {
    if (!searchQuery.value) return tableData.value;

    const query = searchQuery.value.toLowerCase();
    return tableData.value.filter(item => {
      return Object.keys(item).some(key => {
        const value = item[key];
        return value && value.toString().toLowerCase().includes(query);
      });
    });
  });

  const sortedData = computed(() => {
    return [...filteredData.value].sort((a, b) => {
      const aValue = a[sortColumn.value];
      const bValue = b[sortColumn.value];

      if (aValue === bValue) return 0;

      const modifier = sortDirection.value === 'asc' ? 1 : -1;

      if (typeof aValue === 'string' && typeof bValue === 'string') {
        return aValue.localeCompare(bValue) * modifier;
      }

      return aValue < bValue ? -1 * modifier : 1 * modifier;
    });
  });

  const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / perPage.value);
  });

  const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return sortedData.value.slice(start, end);
  });

  const paginationInfo = computed(() => {
    const from = filteredData.value.length === 0 ? 0 : (currentPage.value - 1) * perPage.value + 1;
    const to = Math.min(currentPage.value * perPage.value, filteredData.value.length);
    return { from, to };
  });

  const displayedPages = computed(() => {
    const range = [];
    const maxPagesToShow = 5;

    if (totalPages.value <= maxPagesToShow) {
      for (let i = 1; i <= totalPages.value; i++) {
        range.push(i);
      }
    } else {
      let start = Math.max(1, currentPage.value - Math.floor(maxPagesToShow / 2));
      let end = start + maxPagesToShow - 1;

      if (end > totalPages.value) {
        end = totalPages.value;
        start = Math.max(1, end - maxPagesToShow + 1);
      }

      for (let i = start; i <= end; i++) {
        range.push(i);
      }
    }

    return range;
  });

  // Methods
  const sortBy = (column) => {
    if (sortColumn.value === column) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortColumn.value = column;
      sortDirection.value = 'asc';
    }
  };

  const editItem = (item) => {
    emit('edit', item);
  };

  const deleteItem = (item) => {
    emit('delete', item);
  };

  // Lifecycle hooks
  onMounted(() => {
    // You could fetch data from Laravel API here
    // Example:
    // fetch('/api/users')
    //   .then(response => response.json())
    //   .then(data => {
    //     sampleData.value = data;
    //   });

    // router.get(props.fetchUrl, {
    //     search: searchQuery.value,
    //     sort: sortColumn.value,
    //     direction: sortDirection.value,
    //     perPage: perPage.value,
    //     page,
    // }, {
    //     preserveState: true,
    //     replace: true,
    // });
  });
  </script>

  <style>
  /* You can add custom styles here if needed */
  :root {
    --color-primary: #10b981;
    --color-primary-dark: #059669;
  }

  .text-primary {
    color: var(--color-primary);
  }

  .text-primary-dark {
    color: var(--color-primary-dark);
  }

  .bg-primary {
    background-color: var(--color-primary);
  }

  .border-primary {
    border-color: var(--color-primary);
  }

  .focus\:ring-primary:focus {
    --tw-ring-color: var(--color-primary);
  }

  .focus\:border-primary:focus {
    border-color: var(--color-primary);
  }
  </style>
