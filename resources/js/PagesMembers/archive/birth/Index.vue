<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Download, Upload, Eye, Edit, Trash2, RotateCcw } from 'lucide-vue-next';

interface Certificate {
  id: number;
  full_name: string;
  reg_year: string;
  reg_no: string;
  birth_year: number;
  birth_month: number;
  birth_day: number;
  formatted_date: string;
  notes: string | null;
  file_url: string;
  created_at: string;
  deleted_at: string | null;
}

interface Props {
  fetchUrl: string;
  certificates: {
    data: Certificate[];
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
  };
  filters: {
    search?: string;
    year?: number;
    month?: number;
    day?: number;
    sort?: string;
    direction?: string;
    perPage?: number;
    isArchived?: string;
  };
}

const props = defineProps<Props>();

// Reactive filters
const search = ref(props.filters?.search || '');
const year = ref(props.filters?.year || '');
const month = ref(props.filters?.month || '');
const day = ref(props.filters?.day || '');
const perPage = ref(props.filters?.perPage || 10);
const isArchived = ref(String(props.filters?.isArchived) === 'true');

let searchTimeout: number | null = null;

// Watch for changes and debounce search
watch([search, year, month, day, perPage, isArchived], () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = window.setTimeout(() => fetch(), 300);
});

function fetch(page = 1) {
  router.get(
    props.fetchUrl,
    {
      search: search.value,
      year: year.value || undefined,
      month: month.value || undefined,
      day: day.value || undefined,
      perPage: perPage.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    },
    {
      preserveState: true,
      replace: true,
      only: ['certificates', 'filters'],
    }
  );
}

function deleteCertificate(id: number) {
  if (confirm('Are you sure you want to delete this certificate?')) {
    router.delete(route('archive.birth.certificates.destroy', id), {
      preserveState: true,
      onSuccess: () => fetch(props.certificates.current_page),
    });
  }
}

function restoreCertificate(id: number) {
  if (confirm('Are you sure you want to restore this certificate?')) {
    router.post(
      route('archive.birth.restore', id),
      {},
      {
        preserveState: true,
        onSuccess: () => fetch(props.certificates.current_page),
      }
    );
  }
}

function downloadCertificate(id: number) {
  window.open(route('archive.birth.download', id), '_blank');
}

function changePage(page: number) {
  fetch(page);
}
</script>

<template>
  <AppLayout>
    <div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Birth Archive Certificates</h1>
        <p class="mt-1 text-gray-600">Manage scanned historical birth certificates</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" @click="router.visit(route('archive.birth.bulk-import'))">
          <Upload class="mr-2 h-4 w-4" />
          Bulk Import
        </Button>
        <Button @click="router.visit(route('archive.birth.certificates.create'))">
          Add Certificate
        </Button>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-lg border bg-white p-4 shadow-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="md:col-span-2">
          <Input
            v-model="search"
            placeholder="Search by name, reg no, notes..."
            class="w-full"
          />
        </div>
        <div>
          <Input
            v-model="year"
            type="number"
            placeholder="Year"
            min="1800"
            :max="new Date().getFullYear() + 1"
          />
        </div>
        <div>
          <select
            v-model="month"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
          >
            <option value="">All Months</option>
            <option v-for="m in 12" :key="m" :value="m">
              {{ new Date(2000, m - 1).toLocaleString('default', { month: 'long' }) }}
            </option>
          </select>
        </div>
        <div>
          <Input v-model="day" type="number" placeholder="Day" min="1" max="31" />
        </div>
      </div>

      <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-700">Per Page:</label>
          <select
            v-model="perPage"
            class="rounded-md border border-input bg-background px-3 py-1 text-sm"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
        <div class="flex items-center gap-4">
          <label class="flex cursor-pointer items-center gap-2 select-none">
            <Checkbox v-model:checked="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                Reg No
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                Name
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                Birth Date
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                Notes
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                Status
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr
              v-for="cert in certificates.data"
              :key="cert.id"
              class="transition hover:bg-gray-50"
            >
              <td class="whitespace-nowrap px-4 py-4 text-sm">
                {{ cert.reg_year }}/{{ cert.reg_no }}
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
                {{ cert.full_name }}
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                {{ cert.formatted_date }}
              </td>
              <td class="max-w-xs truncate px-4 py-4 text-sm text-gray-600">
                {{ cert.notes || '-' }}
              </td>
              <td class="whitespace-nowrap px-4 py-4">
                <span
                  v-if="cert.deleted_at"
                  class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800"
                >
                  Deleted
                </span>
                <span
                  v-else
                  class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800"
                >
                  Active
                </span>
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                  <Button
                    v-if="!cert.deleted_at"
                    variant="ghost"
                    size="sm"
                    @click="downloadCertificate(cert.id)"
                    title="Download"
                  >
                    <Download class="h-4 w-4" />
                  </Button>
                  <Button
                    v-if="!cert.deleted_at"
                    variant="ghost"
                    size="sm"
                    @click="router.visit(route('archive.birth.certificates.show', cert.id))"
                    title="View"
                  >
                    <Eye class="h-4 w-4" />
                  </Button>
                  <Button
                    v-if="!cert.deleted_at"
                    variant="ghost"
                    size="sm"
                    @click="router.visit(route('archive.birth.certificates.edit', cert.id))"
                    title="Edit"
                  >
                    <Edit class="h-4 w-4" />
                  </Button>
                  <Button
                    v-if="!cert.deleted_at"
                    variant="ghost"
                    size="sm"
                    @click="deleteCertificate(cert.id)"
                    title="Delete"
                  >
                    <Trash2 class="h-4 w-4 text-red-600" />
                  </Button>
                  <Button
                    v-if="cert.deleted_at"
                    variant="ghost"
                    size="sm"
                    @click="restoreCertificate(cert.id)"
                    title="Restore"
                  >
                    <RotateCcw class="h-4 w-4 text-green-600" />
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="certificates.data.length === 0" class="py-12 text-center">
        <p class="text-gray-500">No certificates found</p>
      </div>

      <!-- Pagination -->
      <div v-if="certificates.data.length > 0" class="border-t bg-gray-50 px-4 py-3">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ certificates.from }} to {{ certificates.to }} of
            {{ certificates.total }} results
          </div>
          <div class="flex gap-2">
            <Button
              variant="outline"
              size="sm"
              :disabled="certificates.current_page === 1"
              @click="changePage(certificates.current_page - 1)"
            >
              Previous
            </Button>
            <span class="flex items-center px-3 text-sm">
              Page {{ certificates.current_page }} of {{ certificates.last_page }}
            </span>
            <Button
              variant="outline"
              size="sm"
              :disabled="certificates.current_page === certificates.last_page"
              @click="changePage(certificates.current_page + 1)"
            >
              Next
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AppLayout>
</template>

<style scoped>
.switch-checkbox {
  width: 2.5rem;
  height: 1.25rem;
  border-radius: 9999px;
  background: #ef4444; /* Tailwind red-500 */
  box-shadow:
    0 2px 8px 0 rgba(239, 68, 68, 0.25),
    0 1.5px 4px 0 rgba(0, 0, 0, 0.1);
  position: relative;
  transition:
    background 0.2s,
    box-shadow 0.2s;
}
.switch-checkbox[data-state='checked'] {
  background: #2563eb;
}
.switch-checkbox input[type='checkbox'] {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  margin: 0;
  cursor: pointer;
}
.switch-checkbox [data-slot='checkbox-indicator'] {
  position: absolute;
  left: 0.125rem;
  top: 0.125rem;
  width: 1rem;
  height: 1rem;
  border-radius: 9999px;
  background: #fff;
  transition: left 0.2s;
}
.switch-checkbox[data-state='checked'] [data-slot='checkbox-indicator'] {
  left: 1.375rem;
}
</style>
