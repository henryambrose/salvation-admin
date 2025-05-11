<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  data: Object,
  columns: Array,
  filters: Object,
  fetchUrl: String,
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
</script>

<template>
  <div class="p-4 bg-white shadow rounded">
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

    <table class="w-full border text-left">
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
        </tr>
      </tbody>
    </table>

    <div class="mt-3 flex gap-2">
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
    </div>
  </div>
</template>
