<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { ArrowLeft } from 'lucide-vue-next'

interface Props {
  certificate: {
    id: number
    reg_year: number
    reg_no: string
    birth_year: number
    birth_month: number
    birth_day: number
    first_name: string
    middle_name: string | null
    last_name: string
    notes: string | null
  }
}

const props = defineProps<Props>()
const page = usePage()

const form = useForm({
  file: null as File | null,
  reg_year: props.certificate.reg_year.toString(),
  reg_no: props.certificate.reg_no,
  birth_year: props.certificate.birth_year,
  birth_month: props.certificate.birth_month,
  birth_day: props.certificate.birth_day,
  first_name: props.certificate.first_name,
  middle_name: props.certificate.middle_name || '',
  last_name: props.certificate.last_name,
  notes: props.certificate.notes || '',
})

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.file = target.files[0]
  }
}

function submit() {
  form.put(route('archive.birth.certificates.update', props.certificate.id), {
    preserveScroll: true,
    forceFormData: true,
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Edit Birth Archive Certificate" />

    <div class="container mx-auto py-8 px-4">
      <div class="mb-6">
        <Button variant="ghost" as-child>
          <a :href="route('archive.birth.index')">
            <ArrowLeft class="mr-2 size-4" />
            Back to List
          </a>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Edit Birth Archive Certificate</CardTitle>
          <CardDescription>Update certificate details or replace the PDF file</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- File Upload (Optional for edit) -->
            <div class="space-y-2">
              <Label for="file">Replace Certificate PDF File (Optional)</Label>
              <Input
                id="file"
                type="file"
                accept="application/pdf"
                @change="handleFileChange"
              />
              <p class="text-sm text-muted-foreground">
                Leave empty to keep the existing file
              </p>
              <p v-if="form.errors.file" class="text-sm text-destructive">
                {{ form.errors.file }}
              </p>
            </div>

            <!-- Registration Details -->
            <div class="grid gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <Label for="reg_year">Registration Year *</Label>
                <Input
                  id="reg_year"
                  v-model="form.reg_year"
                  type="number"
                  required
                />
                <p v-if="form.errors.reg_year" class="text-sm text-destructive">
                  {{ form.errors.reg_year }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="reg_no">Registration Number *</Label>
                <Input
                  id="reg_no"
                  v-model="form.reg_no"
                  required
                />
                <p v-if="form.errors.reg_no" class="text-sm text-destructive">
                  {{ form.errors.reg_no }}
                </p>
              </div>
            </div>

            <!-- Birth Date -->
            <div class="grid gap-4 md:grid-cols-3">
              <div class="space-y-2">
                <Label for="birth_year">Birth Year *</Label>
                <Input
                  id="birth_year"
                  v-model.number="form.birth_year"
                  type="number"
                  required
                />
              </div>

              <div class="space-y-2">
                <Label for="birth_month">Birth Month *</Label>
                <Input
                  id="birth_month"
                  v-model.number="form.birth_month"
                  type="number"
                  min="1"
                  max="12"
                  required
                />
              </div>

              <div class="space-y-2">
                <Label for="birth_day">Birth Day *</Label>
                <Input
                  id="birth_day"
                  v-model.number="form.birth_day"
                  type="number"
                  min="1"
                  max="31"
                  required
                />
              </div>
            </div>

            <!-- Name Details -->
            <div class="grid gap-4 md:grid-cols-3">
              <div class="space-y-2">
                <Label for="first_name">First Name *</Label>
                <Input
                  id="first_name"
                  v-model="form.first_name"
                  required
                />
                <p v-if="form.errors.first_name" class="text-sm text-destructive">
                  {{ form.errors.first_name }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="middle_name">Middle Name</Label>
                <Input
                  id="middle_name"
                  v-model="form.middle_name"
                />
              </div>

              <div class="space-y-2">
                <Label for="last_name">Last Name *</Label>
                <Input
                  id="last_name"
                  v-model="form.last_name"
                  required
                />
                <p v-if="form.errors.last_name" class="text-sm text-destructive">
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
                :rows="3"
                placeholder="Any additional notes..."
              />
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Updating...' : 'Update Certificate' }}
              </Button>
              <Button
                variant="outline"
                as-child
              >
                <a :href="route('archive.birth.index')">
                  Cancel
                </a>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
