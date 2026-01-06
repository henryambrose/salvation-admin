<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { ArrowLeft } from 'lucide-vue-next'

const form = useForm({
  file: null as File | null,
  reg_year: new Date().getFullYear().toString(),
  reg_no: '',
  marriage_year: new Date().getFullYear(),
  marriage_month: 1,
  marriage_day: 1,
  first_name: '',
  middle_name: '',
  last_name: '',
  notes: '',
})

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.file = target.files[0]
  }
}

function submit() {
  form.post(route('archive.marriage.certificates.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Create Marriage Archive Certificate" />

    <div class="container mx-auto py-8 px-4">
      <div class="mb-6">
        <Button variant="ghost" @click="$inertia.visit(route('archive.marriage.index'))">
          <ArrowLeft class="mr-2 size-4" />
          Back to List
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Create Marriage Archive Certificate</CardTitle>
          <CardDescription>Upload a marriage certificate PDF and enter the details</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- File Upload -->
            <div class="space-y-2">
              <Label for="file">Certificate PDF File *</Label>
              <Input
                id="file"
                type="file"
                accept="application/pdf"
                @change="handleFileChange"
                required
              />
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

            <!-- Marriage Date -->
            <div class="grid gap-4 md:grid-cols-3">
              <div class="space-y-2">
                <Label for="marriage_year">Marriage Year *</Label>
                <Input
                  id="marriage_year"
                  v-model.number="form.marriage_year"
                  type="number"
                  required
                />
              </div>

              <div class="space-y-2">
                <Label for="marriage_month">Marriage Month *</Label>
                <Input
                  id="marriage_month"
                  v-model.number="form.marriage_month"
                  type="number"
                  min="1"
                  max="12"
                  required
                />
              </div>

              <div class="space-y-2">
                <Label for="marriage_day">Marriage Day *</Label>
                <Input
                  id="marriage_day"
                  v-model.number="form.marriage_day"
                  type="number"
                  min="1"
                  max="31"
                  required
                />
              </div>
            </div>

            <!-- Name Details (Bridegroom/Bride) -->
            <div class="grid gap-4 md:grid-cols-3">
              <div class="space-y-2">
                <Label for="first_name">First Name *</Label>
                <Input
                  id="first_name"
                  v-model="form.first_name"
                  required
                />
                <p class="text-xs text-muted-foreground">Bridegroom or Bride</p>
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
                {{ form.processing ? 'Creating...' : 'Create Certificate' }}
              </Button>
              <Button
                type="button"
                variant="outline"
                @click="$inertia.visit(route('archive.marriage.index'))"
              >
                Cancel
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
