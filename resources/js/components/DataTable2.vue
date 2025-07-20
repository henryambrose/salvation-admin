<script setup>
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  data: Object,
  columns: Array,
  filters: Object,
  fetchUrl: String,
  hasActions: {
    type: Boolean,
    default: false
  }
});

const search = ref(props.filters.search || '');
const sort = ref(props.filters.sort || '');
const direction = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.perPage || 10);

// Watch inputs
watch([search, sort, direction, perPage], () => {
  fetch();
});

function fetch(page = 1) {
  router.get(props.fetchUrl, {
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    perPage: perPage.value,
    page,
  }, {
    preserveState: true,
    replace: true,
  });
}

function changeSort(field) {
  if (sort.value === field) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value = field;
    direction.value = 'asc';
  }
  fetch();
}

const page = usePage();
const roles = computed(() => page.props.auth?.roles || []);
const isSuperAdmin = computed(() => roles.value && roles.value.includes('superadmin'));

// Helper to check permission (uses $can if available, else fallback)
function can(permission) {
  if (typeof window !== 'undefined' && window?.app?.config?.globalProperties?.$can) {
    return window.app.config.globalProperties.$can(permission);
  }
  // fallback: allow for superadmin
  return isSuperAdmin.value;
}
</script>

<template>
  <div class="p-4 bg-white shadow rounded mt-4 datatable2">
    <div class="flex items-center gap-2 mb-2">
      <input
        v-model="search"
        type="text"
        class="border rounded px-2 py-1"
        placeholder="Search..."
      />
      <select v-model="perPage" class="border rounded px-2 py-1">
        <option :value="2">2</option>
        <option :value="5">5</option>
        <option :value="10">10</option>
        <option :value="25">25</option>
        <option :value="50">50</option>
      </select>
    </div>
    <div class="overflow-x-auto" style="max-width: 100vw;">
        <table class="overflow-x-auto border text-left">
        <thead>
            <tr>
            <th
                v-for="col in columns"
                :key="col.key"
                @click="col.sortable ? changeSort(col.key) : null"
                class="p-2 border cursor-pointer"
            >
                {{ col.label }}
                <span v-if="col.sortable && sort === col.key">
                {{ direction === 'asc' ? '▲' : '▼' }}
                </span>
            </th>
            <th v-if="hasActions" class="p-2 border text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in data.data" :key="item.id">
            <td
                v-for="col in columns"
                :key="col.key"
                class="p-2 border"
            >
                {{ item[col.key] }}
            </td>
            <td v-if="hasActions" class="p-2 border text-right">
                <slot name="actions" :row="item" />
            </td>
            </tr>
        </tbody>
        </table>
    </div>
    <div class="mt-3 flex gap-2 items-center">
      <button
        v-if="data.prev_page_url"
        @click="fetch(data.current_page - 1)"
        class="border rounded px-3 py-1"
      >
        Prev
      </button>
      <button
        v-if="data.next_page_url"
        @click="fetch(data.current_page + 1)"
        class="border rounded px-3 py-1"
      >
        Next
      </button>
      <!-- Page count beside Next button, right aligned -->
      <span
        v-if="data.current_page && data.last_page"
        class="text-sm ml-auto"
        style="margin-left:auto; display:block;"
      >
        Page {{ data.current_page }} of {{ data.last_page }}
      </span>
    </div>
  </div>
</template>
