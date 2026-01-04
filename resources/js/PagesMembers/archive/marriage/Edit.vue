<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Props {
  certificate: {
    id: number;
    reg_year: string;
    reg_no: string;
    birth_year: number;
    birth_month: number;
    birth_day: number;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    notes: string | null;
    folder_path: string;
    file_name: string;
  };
}

const props = defineProps<Props>();

const form = useForm({
  _method: 'PUT',
  file: null as File | null,
  reg_year: props.certificate.reg_year,
  reg_no: props.certificate.reg_no,
  birth_year: props.certificate.birth_year,
  birth_month: props.certificate.birth_month,
  birth_day: props.certificate.birth_day,
  first_name: props.certificate.first_name,
  middle_name: props.certificate.middle_name || '',
  last_name: props.certificate.last_name,
  notes: props.certificate.notes || '',
});

const fileInput = ref<HTMLInputElement | null>(null);
const fileName = ref('');

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.file = target.files[0];
    fileName.value = target.files[0].name;
  }
}

function submit() {
  // Use POST with _method spoofing for file upload compatibility
  form.post(route('archive.marriage.certificates.update', props.certificate.id), {
    preserveScroll: true,
    forceFormData: true,
  });
}
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-4xl p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Marriage Archive Certificate</h1>
        <p class="mt-1 text-gray-600">Update the certificate details and optionally replace the file</p>
      </div>

      <form @submit.prevent="submit">
        <Card>
          <CardHeader>
            <CardTitle>Certificate Details</CardTitle>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- File Upload (Optional on Edit) -->
            <div class="space-y-2">
              <Label for="file">Certificate File</Label>
              <div class="flex gap-2">
                <input
                  ref="fileInput"
                  id="file"
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png"
                  @change="handleFileChange"
                  class="hidden"
                />
                <Button type="button" variant="outline" @click="fileInput?.click()">
                  Choose File
                </Button>
                <span class="self-center text-sm text-gray-600">
                  {{ fileName || 'No new file selected' }}
                </span>
              </div>
              <p class="text-xs text-gray-500">
                Leave empty to keep existing file: <strong>{{ certificate.file_name }}</strong>
              </p>
              <p class="text-xs text-gray-500">Or upload new: PDF, JPG, JPEG, or PNG (max 10MB)</p>
              <p v-if="form.errors.file" class="text-sm text-red-600">{{ form.errors.file }}</p>
            </div>

            <!-- Registration Info -->
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="reg_year">Registration Year *</Label>
                <Input
                  id="reg_year"
                  v-model="form.reg_year"
                  type="number"
                  min="1900"
                  :max="new Date().getFullYear() + 1"
                  required
                />
                <p v-if="form.errors.reg_year" class="text-sm text-red-600">
                  {{ form.errors.reg_year }}
                </p>
              </div>
              <div class="space-y-2">
                <Label for="reg_no">Registration Number *</Label>
                <Input id="reg_no" v-model="form.reg_no" required />
                <p v-if="form.errors.reg_no" class="text-sm text-red-600">
                  {{ form.errors.reg_no }}
                </p>
              </div>
            </div>

            <!-- Marriage Date -->
            <div class="grid grid-cols-3 gap-4">
              <div class="space-y-2">
                <Label for="birth_year">Marriage Year *</Label>
                <Input
                  id="birth_year"
                  v-model.number="form.birth_year"
                  type="number"
                  min="1800"
                  :max="new Date().getFullYear() + 1"
                  required
                />
                <p v-if="form.errors.birth_year" class="text-sm text-red-600">
                  {{ form.errors.birth_year }}
                </p>
              </div>
              <div class="space-y-2">
                <Label for="birth_month">Month *</Label>
                <Input
                  id="birth_month"
                  v-model.number="form.birth_month"
                  type="number"
                  min="1"
                  max="12"
                  required
                />
                <p v-if="form.errors.birth_month" class="text-sm text-red-600">
                  {{ form.errors.birth_month }}
                </p>
              </div>
              <div class="space-y-2">
                <Label for="birth_day">Day *</Label>
                <Input
                  id="birth_day"
                  v-model.number="form.birth_day"
                  type="number"
                  min="1"
                  max="31"
                  required
                />
                <p v-if="form.errors.birth_day" class="text-sm text-red-600">
                  {{ form.errors.birth_day }}
                </p>
              </div>
            </div>

            <!-- Name Fields -->
            <div class="grid grid-cols-3 gap-4">
              <div class="space-y-2">
                <Label for="first_name">First Name *</Label>
                <Input id="first_name" v-model="form.first_name" required />
                <p v-if="form.errors.first_name" class="text-sm text-red-600">
                  {{ form.errors.first_name }}
                </p>
              </div>
              <div class="space-y-2">
                <Label for="middle_name">Middle Name</Label>
                <Input id="middle_name" v-model="form.middle_name" />
              </div>
              <div class="space-y-2">
                <Label for="last_name">Last Name *</Label>
                <Input id="last_name" v-model="form.last_name" required />
                <p v-if="form.errors.last_name" class="text-sm text-red-600">
                  {{ form.errors.last_name }}
                </p>
              </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
              <Label for="notes">Notes</Label>
              <Textarea
                id="notes"
                v-model="form.notes"
                :rows="4"
                placeholder="Additional information about this certificate..."
              />
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2">
              <Button
                type="button"
                variant="outline"
                @click="router.visit(route('archive.marriage.index'))"
              >
                Cancel
              </Button>
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Updating...' : 'Update Certificate' }}
              </Button>
            </div>
          </CardContent>
        </Card>
      </form>
    </div>
  </AppLayout>
</template>
