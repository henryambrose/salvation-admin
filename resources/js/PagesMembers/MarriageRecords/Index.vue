<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Search, Download } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import { formatDateForDisplay } from '@/lib/utils';
import PdfOptionsModal, { type PdfOptions } from '@/components/PdfOptionsModal.vue';

const props = defineProps<{
  marriageRecords: any;
  filters: any;
}>();

const page = usePage();
const { success, error: showError } = useToast();

const search = ref(props.filters?.search || '');
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const perPage = ref(props.filters?.per_page || 15);
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');

// Format date to DD/MM/YYYY
const formatDate = (dateString: string | null) => {
  if (!dateString) return '-';
  return formatDateForDisplay(dateString) || '-';
};

// Confirmation dialog state
const showDeleteDialog = ref(false);
const showRestoreDialog = ref(false);
const selectedRecordId = ref<number | null>(null);

// PDF options modal state
const showPdfOptionsModal = ref(false);
const pdfRecordId = ref<number | null>(null);

// Watch for flash messages
watch(
  () => page.props.flash,
  (flash: any) => {
    if (flash?.success) {
      success(flash.success);
    }
    if (flash?.error) {
      showError(flash.error);
    }
  },
  { deep: true, immediate: true }
);

watch([search, isArchived, perPage], () => {
  fetch(1);
});

function fetch(page = 1) {
  router.get(
    '/marriage-records',
    {
      search: search.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page: page.toString(),
      per_page: perPage.value.toString(),
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  );
}

function confirmDelete(id: number) {
  selectedRecordId.value = id;
  showDeleteDialog.value = true;
}

function deleteRecord() {
  if (selectedRecordId.value) {
    router.delete(`/marriage-records/${selectedRecordId.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteDialog.value = false;
        selectedRecordId.value = null;
      },
    });
  }
}

function confirmRestore(id: number) {
  selectedRecordId.value = id;
  showRestoreDialog.value = true;
}

function restoreRecord() {
  if (selectedRecordId.value) {
    router.post(`/marriage-records/${selectedRecordId.value}/restore`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        showRestoreDialog.value = false;
        selectedRecordId.value = null;
      },
    });
  }
}

function downloadPdf(id: number) {
  pdfRecordId.value = id;
  showPdfOptionsModal.value = true;
}

function handlePdfOptionsConfirm(options: PdfOptions) {
  if (pdfRecordId.value) {
    const params = new URLSearchParams({
      include_remark: options.includeRemark ? '1' : '0',
      sign_by: options.signBy,
      signee_name: options.signeeName,
      print_date: options.printDate,
    });
    window.open(
      route('marriage-records.download-pdf', pdfRecordId.value) + '?' + params.toString(),
      '_blank'
    );
  }
  showPdfOptionsModal.value = false;
  pdfRecordId.value = null;
}
</script>

<template>
  <AppLayout title="Marriage Records">
    <Head title="Marriage Records" />

    <div class="p-6">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Marriage Records</h1>
        <div class="flex gap-4 items-center">
          <div class="flex items-center gap-2">
            <div class="relative inline-block">
              <input
                type="checkbox"
                id="showArchived"
                v-model="isArchived"
                class="sr-only peer"
              />
              <label
                for="showArchived"
                class="relative inline-flex h-5 w-10 cursor-pointer items-center rounded-full transition-colors peer-checked:bg-blue-600 bg-red-500"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform peer-checked:translate-x-5 translate-x-0.5"
                ></span>
              </label>
            </div>
            <label for="showArchived" class="text-sm font-medium cursor-pointer">
              Show Archived
            </label>
          </div>
          <Link v-if="!serverArchived" :href="route('marriage-records.create')">
            <Button>
              <Plus class="mr-2 h-4 w-4" />
              Add Marriage Record
            </Button>
          </Link>
        </div>
      </div>

      <div class="mb-4 flex items-center gap-4">
        <div class="relative flex-1 max-w-xs">
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          <Input
            v-model="search"
            @keyup.enter="fetch(1)"
            placeholder="Search by reg no, bridegroom, bride..."
            class="pl-10"
          />
        </div>
        <select
          v-model.number="perPage"
          class="h-9 rounded-md border border-input bg-background px-3 py-1 text-sm"
        >
          <option :value="10">10</option>
          <option :value="15">15</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
      </div>

      <!-- Pagination Info -->
      <div class="mb-4 flex items-center justify-between text-sm text-gray-600">
        <div>
          Showing {{ marriageRecords.total }} total marriage records
        </div>
        <div v-if="marriageRecords.last_page > 1" class="flex items-center gap-2">
          <span>Page</span>
          <select
            :value="marriageRecords.current_page"
            @change="fetch($event.target.value)"
            class="h-8 rounded-md border border-input bg-background px-2 py-1 text-sm"
          >
            <option v-for="page in marriageRecords.last_page" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span>of {{ marriageRecords.last_page }}</span>
          <Button
            v-if="marriageRecords.current_page < marriageRecords.last_page"
            variant="outline"
            size="sm"
            @click="fetch(marriageRecords.current_page + 1)"
          >
            Next →
          </Button>
        </div>
      </div>

      <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full">
          <thead class="border-b bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-medium">Actions</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Reg No</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Marriage Date</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Bridegroom</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Bride</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Witnesses</th>
              <th class="px-4 py-3 text-left text-sm font-medium">{{ serverArchived ? 'Restore' : 'Delete' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="record in marriageRecords.data"
              :key="record.id"
              class="border-b hover:bg-gray-50"
            >
              <td class="px-4 py-3">
                <div class="flex gap-2">
                  <Link v-if="!serverArchived" :href="route('marriage-records.edit', record.id)">
                    <Button
                      class="bg-yellow-100 text-yellow-800 hover:bg-yellow-200 border-0"
                      size="sm"
                    >
                      Edit
                    </Button>
                  </Link>
                  <Button
                    v-if="!serverArchived"
                    @click="downloadPdf(record.id)"
                    class="bg-blue-100 text-blue-800 hover:bg-blue-200 border-0"
                    size="sm"
                  >
                    <Download class="h-4 w-4 mr-1" />
                    PDF
                  </Button>
                </div>
              </td>
              <td class="px-4 py-3 text-sm">{{ record.marriage_reg_no || '-' }}</td>
              <td class="px-4 py-3 text-sm">
                {{ formatDate(record.marriage_date) }}
              </td>
              <td class="px-4 py-3 text-sm">
                <span v-if="record.bridegroom_member_id && record.bridegroom">
                  {{ record.bridegroom.first_name }} {{ record.bridegroom.last_name }}
                </span>
                <span v-else-if="record.bridegroom_name || record.bridegroom_surname" class="italic text-gray-600">
                  {{ record.bridegroom_name }} {{ record.bridegroom_surname }}
                  <span class="text-xs">(Non-member)</span>
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span v-if="record.bride_member_id && record.bride">
                  {{ record.bride.first_name }} {{ record.bride.last_name }}
                </span>
                <span v-else-if="record.bride_name || record.bride_surname" class="italic text-gray-600">
                  {{ record.bride_name }} {{ record.bride_surname }}
                  <span class="text-xs">(Non-member)</span>
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-4 py-3 text-sm">
                {{ record.first_witness_name || '' }}{{ record.first_witness_name && record.second_witness_name ? ', ' : '' }}{{ record.second_witness_name || '' }}
              </td>
              <td class="px-4 py-3">
                <Button
                  v-if="!serverArchived"
                  @click="confirmDelete(record.id)"
                  class="bg-red-100 text-red-800 hover:bg-red-200 border-0"
                  size="sm"
                >
                  Delete
                </Button>
                <Button
                  v-else
                  @click="confirmRestore(record.id)"
                  class="bg-green-100 text-green-800 hover:bg-green-200 border-0"
                  size="sm"
                >
                  Restore
                </Button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="marriageRecords.data.length === 0" class="p-8 text-center text-gray-500">
          No marriage records found.
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="showDeleteDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete Marriage Record</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete this marriage record? This action will move the record to the archive.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteDialog = false">
            Cancel
          </Button>
          <Button
            class="bg-red-600 hover:bg-red-700 text-white"
            @click="deleteRecord"
          >
            Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Restore Confirmation Dialog -->
    <Dialog v-model:open="showRestoreDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Restore Marriage Record</DialogTitle>
          <DialogDescription>
            Are you sure you want to restore this marriage record? This will move the record back to the active list.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showRestoreDialog = false">
            Cancel
          </Button>
          <Button
            class="bg-green-600 hover:bg-green-700 text-white"
            @click="restoreRecord"
          >
            Restore
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- PDF Options Modal -->
    <PdfOptionsModal
      v-model:open="showPdfOptionsModal"
      title="Marriage Certificate PDF Options"
      @confirm="handlePdfOptionsConfirm"
      @cancel="showPdfOptionsModal = false"
    />
  </AppLayout>
</template>
