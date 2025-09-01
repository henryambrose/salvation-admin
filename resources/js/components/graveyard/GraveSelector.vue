<script setup lang="ts">
import { computed, ref, watch } from 'vue';

interface Grave {
  id: number;
  section: string;
  row_no: number;
  grave_no: number;
  owner_name?: string | null;
}

const props = defineProps<{
  modelValuePermanent?: number | string | null;
  modelValueTemporary?: number | string | null;
  graveType: 'permanent' | 'temporary';
  availablePermanentGraves: Grave[];
  availableTemporaryGraves: Grave[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValuePermanent', value: number | string | null): void;
  (e: 'update:modelValueTemporary', value: number | string | null): void;
}>();

const permanentSearch = ref('');

const filteredPermanent = computed(() => {
  const q = permanentSearch.value.trim().toLowerCase();
  if (!q) return props.availablePermanentGraves;
  return props.availablePermanentGraves.filter((g) => {
    const idStr = `${g.section}-${g.row_no}-${g.grave_no}`.toLowerCase();
    return (
      idStr.includes(q) ||
      (g.owner_name ? g.owner_name.toLowerCase().includes(q) : false)
    );
  });
});

const selectPermanent = (id: number) => {
  emit('update:modelValuePermanent', id);
};

const selectTemporary = (id: number) => {
  emit('update:modelValueTemporary', id);
};
</script>

<template>
  <div class="space-y-3">
    <div v-if="graveType === 'permanent'" class="space-y-2">
      <div class="flex items-center gap-2">
        <input
          v-model="permanentSearch"
          type="text"
          placeholder="Search owner / grave no (e.g., A-1-12)"
          class="border rounded-full px-3 py-1.5 w-full"
        />
        <button
          v-if="permanentSearch"
          class="text-sm text-gray-600 hover:text-gray-800"
          @click="permanentSearch = ''"
        >✕</button>
      </div>

      <div class="max-h-64 overflow-auto border rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-blue-50">
            <tr>
              <th class="text-left px-3 py-2">Identifier</th>
              <th class="text-left px-3 py-2">Owner</th>
              <th class="text-right px-3 py-2">Select</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="g in filteredPermanent"
              :key="g.id"
              class="even:bg-gray-50 hover:bg-blue-50 transition"
            >
              <td class="px-3 py-2">{{ g.section }}-{{ g.row_no }}-{{ g.grave_no }}</td>
              <td class="px-3 py-2">{{ g.owner_name || '-' }}</td>
              <td class="px-3 py-2 text-right">
                <input
                  type="radio"
                  name="permanent_grave"
                  :value="g.id"
                  :checked="String(modelValuePermanent || '') === String(g.id)"
                  @change="selectPermanent(g.id)"
                />
              </td>
            </tr>
            <tr v-if="!filteredPermanent.length">
              <td colspan="3" class="px-3 py-4 text-center text-gray-500">No graves found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="space-y-2">
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-64 overflow-auto p-1 border rounded-lg">
        <button
          v-for="g in availableTemporaryGraves"
          :key="g.id"
          class="px-3 py-2 rounded-lg border hover:bg-blue-50 text-left"
          :class="String(modelValueTemporary || '') === String(g.id) ? 'bg-blue-100 border-blue-300' : 'bg-white'"
          @click="selectTemporary(g.id)"
        >
          <div class="text-sm font-medium">{{ g.section }}-{{ g.row_no }}-{{ g.grave_no }}</div>
          <div class="text-xs text-gray-600">Available</div>
        </button>
      </div>
      <div v-if="!availableTemporaryGraves.length" class="text-sm text-gray-500">No temporary graves available.</div>
    </div>
  </div>
</template>

