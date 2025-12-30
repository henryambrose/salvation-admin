<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface Props {
  type: 'birth' | 'marriage' | 'death';
}

const props = defineProps<Props>();

const form = useForm({
  csv_file: null as File | null,
  certificate_files: [] as File[],
});

const csvFileName = ref('');
const certificateCount = ref(0);
const csvInput = ref<HTMLInputElement | null>(null);
const filesInput = ref<HTMLInputElement | null>(null);

function handleCsvChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.csv_file = target.files[0];
    csvFileName.value = target.files[0].name;
  }
}

function handleFilesChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.certificate_files = Array.from(target.files);
    certificateCount.value = target.files.length;
  }
}

function submit() {
  form.post(route(`archive.${props.type}.bulk-import.store`));
}

const typeLabel = props.type.charAt(0).toUpperCase() + props.type.slice(1);

const csvFormat = `file_name,reg_year,reg_no,year,month,day,first_name,middle_name,last_name,notes
certificate1.pdf,2020,001,1990,5,15,John,Michael,Doe,Additional notes
certificate2.pdf,2020,002,1992,8,20,Jane,Marie,Smith,`;
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-4xl p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">
        Bulk Import {{ typeLabel }} Archive Certificates
      </h1>
      <p class="mt-1 text-gray-600">Upload multiple certificates with a CSV metadata file</p>
    </div>

    <Alert class="mb-6">
      <AlertDescription>
        <p class="mb-2 font-semibold">CSV Format:</p>
        <pre class="overflow-x-auto rounded bg-gray-100 p-2 text-xs">{{ csvFormat }}</pre>
        <p class="mt-2 text-sm">
          <strong>Important:</strong> The <code>file_name</code> in CSV must exactly match the
          uploaded certificate filenames.
        </p>
      </AlertDescription>
    </Alert>

    <form @submit.prevent="submit">
      <Card>
        <CardHeader>
          <CardTitle>Upload Files</CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
          <!-- CSV Upload -->
          <div class="space-y-2">
            <label class="text-sm font-medium">CSV Metadata File *</label>
            <div class="flex gap-2">
              <input
                ref="csvInput"
                type="file"
                accept=".csv"
                @change="handleCsvChange"
                class="hidden"
              />
              <Button type="button" variant="outline" @click="csvInput?.click()">
                Choose CSV
              </Button>
              <span class="self-center text-sm text-gray-600">
                {{ csvFileName || 'No file selected' }}
              </span>
            </div>
            <p v-if="form.errors.csv_file" class="text-sm text-red-600">
              {{ form.errors.csv_file }}
            </p>
          </div>

          <!-- Certificate Files Upload -->
          <div class="space-y-2">
            <label class="text-sm font-medium">Certificate Files *</label>
            <div class="flex gap-2">
              <input
                ref="filesInput"
                type="file"
                accept=".pdf,.jpg,.jpeg,.png"
                multiple
                @change="handleFilesChange"
                class="hidden"
              />
              <Button type="button" variant="outline" @click="filesInput?.click()">
                Choose Files
              </Button>
              <span class="self-center text-sm text-gray-600">
                {{
                  certificateCount > 0 ? `${certificateCount} files selected` : 'No files selected'
                }}
              </span>
            </div>
            <p class="text-xs text-gray-500">Select multiple PDF, JPG, JPEG, or PNG files</p>
            <p v-if="form.errors.certificate_files" class="text-sm text-red-600">
              {{ form.errors.certificate_files }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-2">
            <Button
              type="button"
              variant="outline"
              @click="router.visit(route(`archive.${type}.index`))"
            >
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing">
              {{ form.processing ? 'Importing...' : 'Import Certificates' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </form>
  </div>
  </AppLayout>
</template>
