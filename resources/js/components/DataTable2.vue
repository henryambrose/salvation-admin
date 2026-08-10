<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
  data: Object,
  columns: Array,
  filters: Object,
  fetchUrl: String,
  hasActions: {
    type: Boolean,
    default: false,
  },
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
  router.get(
    props.fetchUrl,
    {
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      perPage: perPage.value,
      page,
    },
    {
      preserveState: true,
      replace: true,
    },
  );
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
</script>

<template>
  <div class="datatable2 mt-4 w-full overflow-hidden rounded bg-[#ffffff] p-4 shadow">
    <div class="mb-2 flex items-center gap-2">
      <input v-model="search" type="text" class="rounded border px-2 py-1" placeholder="Search..." />
      <select v-model="perPage" class="rounded border px-2 py-1">
        <option :value="2">2</option>
        <option :value="5">5</option>
        <option :value="10">10</option>
        <option :value="25">25</option>
        <option :value="50">50</option>
      </select>
    </div>
    <div class="w-full overflow-x-auto">
      <table class="border text-left">
        <thead>
          <tr>
            <th v-for="col in columns" :key="col.key" @click="col.sortable ? changeSort(col.key) : null" class="cursor-pointer border p-2">
              {{ col.label }}
              <span v-if="col.sortable && sort === col.key">
                {{ direction === 'asc' ? '▲' : '▼' }}
              </span>
            </th>
            <th v-if="hasActions" class="border p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data.data" :key="item.id">
            <td v-for="col in columns" :key="col.key" class="border p-2">
              {{ item[col.key] }}
            </td>
            <td v-if="hasActions" class="border p-2 text-right">
              <slot name="actions" :row="item" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="mt-3 flex items-center gap-2">
      <button v-if="data.prev_page_url" @click="fetch(data.current_page - 1)" class="rounded border px-3 py-1">Prev</button>
      <button v-if="data.next_page_url" @click="fetch(data.current_page + 1)" class="rounded border px-3 py-1">Next</button>
      <!-- Page count beside Next button, right aligned -->
      <span v-if="data.current_page && data.last_page" class="ml-auto text-sm" style="margin-left: auto; display: block">
        Page {{ data.current_page }} of {{ data.last_page }}
      </span>
    </div>
  </div>
</template>
