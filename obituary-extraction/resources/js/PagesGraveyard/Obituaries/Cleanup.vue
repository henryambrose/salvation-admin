<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { AlertTriangle, Calendar, CheckCircle, FileX, HardDrive, RefreshCw, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface OrphanedFile {
  path: string;
  name: string;
  size: number;
  size_human: string;
  folder: string;
  last_modified: number;
}

interface ScanResult {
  files: OrphanedFile[];
  count: number;
  total_size: number;
  total_size_human: string;
}

const { success, error } = useToast();

const orphanedFiles = ref<OrphanedFile[]>([]);
const selectedFiles = ref<string[]>([]);
const isScanning = ref(false);
const isDeleting = ref(false);
const scanCompleted = ref(false);
const totalSize = ref(0);
const totalSizeHuman = ref('');

const selectedFilesData = computed(() => {
  return orphanedFiles.value.filter((file) => selectedFiles.value.includes(file.path));
});

const selectedTotalSize = computed(() => {
  return selectedFilesData.value.reduce((total, file) => total + file.size, 0);
});

const selectedTotalSizeHuman = computed(() => {
  return formatFileSize(selectedTotalSize.value);
});

const allSelected = computed(() => {
  return orphanedFiles.value.length > 0 && selectedFiles.value.length === orphanedFiles.value.length;
});

const someSelected = computed(() => {
  return selectedFiles.value.length > 0 && selectedFiles.value.length < orphanedFiles.value.length;
});

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

const formatDate = (timestamp: number): string => {
  return new Date(timestamp * 1000).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getFolderColor = (folder: string): string => {
  switch (folder) {
    case 'obituaries/images':
      return 'bg-blue-100 text-blue-800';
    case 'obituaries/gallery':
      return 'bg-green-100 text-green-800';
    case 'obituaries/audio':
      return 'bg-purple-100 text-purple-800';
    case 'qr-codes':
      return 'bg-orange-100 text-orange-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const scanForOrphanedFiles = async () => {
  isScanning.value = true;
  scanCompleted.value = false;
  orphanedFiles.value = [];
  selectedFiles.value = [];

  try {
    const response = await axios.post(route('graveyard.obituaries.cleanup.scan'));
    const data: ScanResult = response.data;

    orphanedFiles.value = data.files;
    totalSize.value = data.total_size;
    totalSizeHuman.value = data.total_size_human;
    scanCompleted.value = true;

    if (data.files.length === 0) {
      success('Scan completed! No orphaned files found.');
    } else {
      success(`Scan completed! Found ${data.files.length} orphaned file(s) totaling ${data.total_size_human}.`);
    }
  } catch (err) {
    console.error('Error scanning for orphaned files:', err);
    error('Error scanning for orphaned files. Please try again.');
  } finally {
    isScanning.value = false;
  }
};

const handleSelectAllClick = (event: Event) => {
  event.preventDefault();
  event.stopPropagation();

  if (allSelected.value) {
    selectedFiles.value = [];
  } else {
    selectedFiles.value = [...orphanedFiles.value.map((file) => file.path)];
  }
};

const toggleFileSelection = (filePath: string, checked: boolean | "indeterminate") => {
  const isChecked = checked === true;
  if (isChecked) {
    if (!selectedFiles.value.includes(filePath)) {
      selectedFiles.value = [...selectedFiles.value, filePath];
    }
  } else {
    selectedFiles.value = selectedFiles.value.filter(path => path !== filePath);
  }
};

const deleteSelectedFiles = async () => {
  if (selectedFiles.value.length === 0) {
    error('Please select files to delete.');
    return;
  }

  const confirmMessage = `Are you sure you want to delete ${selectedFiles.value.length} file(s)? This action cannot be undone.`;
  if (!confirm(confirmMessage)) {
    return;
  }

  isDeleting.value = true;

  try {
    const response = await axios.post(route('graveyard.obituaries.cleanup.execute'), {
      files: selectedFiles.value,
    });

    const data = response.data;

    if (data.success) {
      // Remove deleted files from the list
      orphanedFiles.value = orphanedFiles.value.filter((file) => !selectedFiles.value.includes(file.path));
      selectedFiles.value = [];

      // Update totals
      totalSize.value = orphanedFiles.value.reduce((total, file) => total + file.size, 0);
      totalSizeHuman.value = formatFileSize(totalSize.value);

      success(data.message || 'Files deleted successfully!');
    }
  } catch (err) {
    console.error('Error deleting files:', err);
    error('Error deleting files. Please try again.');
  } finally {
    isDeleting.value = false;
  }
};

const goBack = () => {
  router.visit(route('graveyard.obituaries.index'));
};
</script>

<template>
  <Head title="File Cleanup - Obituary Management" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-4 lg:px-6">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">File Cleanup</h1>
              <p class="text-gray-600">Scan and remove orphaned files from obituary folders</p>
            </div>
            <Button variant="outline" @click="goBack"> ← Back to Obituaries </Button>
          </div>
        </div>

        <!-- Scan Section -->
        <Card class="mb-6">
          <CardHeader>
            <CardTitle class="flex items-center">
              <Search class="mr-2 h-5 w-5" />
              Scan for Orphaned Files
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex items-start space-x-3">
                  <AlertTriangle class="mt-0.5 h-5 w-5 text-yellow-600" />
                  <div class="text-sm">
                    <p class="font-medium text-yellow-800">Important Notice</p>
                    <p class="text-yellow-700">
                      This scan will identify files in obituary folders (images, gallery, audio, QR codes) that are no longer referenced by any obituary pages.
                      These files are safe to delete and will help free up storage space.
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex items-center space-x-4">
                <Button @click="scanForOrphanedFiles" :disabled="isScanning" class="bg-blue-600 hover:bg-blue-700">
                  <RefreshCw :class="['mr-2 h-4 w-4', { 'animate-spin': isScanning }]" />
                  {{ isScanning ? 'Scanning...' : 'Start Scan' }}
                </Button>

                <div v-if="scanCompleted" class="flex items-center text-sm text-gray-600">
                  <CheckCircle class="mr-2 h-4 w-4 text-green-600" />
                  Scan completed
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Results Section -->
        <Card v-if="scanCompleted">
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle class="flex items-center">
                <FileX class="mr-2 h-5 w-5" />
                Scan Results
              </CardTitle>
              <div v-if="orphanedFiles.length > 0" class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">{{ orphanedFiles.length }} orphaned files found ({{ totalSizeHuman }})</div>
                <div v-if="selectedFiles.length > 0" class="text-sm font-medium text-blue-600">
                  {{ selectedFiles.length }} selected ({{ selectedTotalSizeHuman }})
                </div>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div v-if="orphanedFiles.length === 0" class="py-8 text-center">
              <CheckCircle class="mx-auto mb-4 h-12 w-12 text-green-500" />
              <h3 class="mb-2 text-lg font-medium text-gray-900">No Orphaned Files Found</h3>
              <p class="text-gray-600">All files in obituary folders are properly referenced. No cleanup needed!</p>
            </div>

            <div v-else class="space-y-4">
              <!-- Actions Bar -->
              <div class="flex items-center justify-between border-b pb-4">
                <div class="flex items-center space-x-4">
                  <label class="flex cursor-pointer items-center space-x-2" @click="handleSelectAllClick">
                    <Checkbox :model-value="allSelected" :indeterminate="someSelected" />
                    <span class="text-sm font-medium">
                      {{ allSelected ? 'Deselect All' : 'Select All' }}
                    </span>
                  </label>

                  <Badge v-if="selectedFiles.length > 0" variant="secondary">
                    {{ selectedFiles.length }} of {{ orphanedFiles.length }} selected
                  </Badge>
                </div>

                <Button v-if="selectedFiles.length > 0" @click="deleteSelectedFiles" :disabled="isDeleting" variant="destructive">
                  <Trash2 :class="['mr-2 h-4 w-4', { 'animate-spin': isDeleting }]" />
                  {{ isDeleting ? 'Deleting...' : `Delete Selected (${selectedFiles.length})` }}
                </Button>
              </div>

              <!-- Files List -->
              <div class="max-h-96 space-y-2 overflow-y-auto">
                <div
                  v-for="file in orphanedFiles"
                  :key="file.path"
                  class="flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50"
                >
                  <div class="flex items-center space-x-3">
                    <Checkbox
                      :model-value="selectedFiles.includes(file.path)"
                      @update:model-value="(checked: boolean | 'indeterminate') => toggleFileSelection(file.path, checked)"
                    />

                    <div class="min-w-0 flex-1">
                      <div class="mb-1 flex items-center space-x-2">
                        <p class="truncate text-sm font-medium text-gray-900">{{ file.name }}</p>
                        <Badge :class="getFolderColor(file.folder)" class="text-xs">
                          {{ file.folder.split('/').pop() }}
                        </Badge>
                      </div>
                      <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span class="flex items-center">
                          <HardDrive class="mr-1 h-3 w-3" />
                          {{ file.size_human }}
                        </span>
                        <span class="flex items-center">
                          <Calendar class="mr-1 h-3 w-3" />
                          {{ formatDate(file.last_modified) }}
                        </span>
                      </div>
                      <p class="truncate text-xs text-gray-400">{{ file.path }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Summary -->
              <div class="rounded-lg border-t bg-gray-50 p-4">
                <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-3">
                  <div class="flex items-center">
                    <FileX class="mr-2 h-4 w-4 text-gray-600" />
                    <span class="font-medium">Total Files:</span>
                    <span class="ml-1">{{ orphanedFiles.length }}</span>
                  </div>
                  <div class="flex items-center">
                    <HardDrive class="mr-2 h-4 w-4 text-gray-600" />
                    <span class="font-medium">Total Size:</span>
                    <span class="ml-1">{{ totalSizeHuman }}</span>
                  </div>
                  <div v-if="selectedFiles.length > 0" class="flex items-center text-blue-600">
                    <CheckCircle class="mr-2 h-4 w-4" />
                    <span class="font-medium">Selected:</span>
                    <span class="ml-1">{{ selectedTotalSizeHuman }}</span>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
