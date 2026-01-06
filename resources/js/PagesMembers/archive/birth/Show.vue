<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { ArrowLeft, Download, Edit, Eye, EyeOff } from 'lucide-vue-next'

interface User {
  id: number
  name: string
  email: string
}

interface Certificate {
  id: number
  folder_path: string
  file_name: string
  reg_year: number
  reg_no: string
  birth_year: number
  birth_month: number
  birth_day: number
  first_name: string
  middle_name?: string
  last_name: string
  notes?: string
  full_name: string
  file_url: string
  formatted_date?: string
  created_at: string
  updated_at: string
  creator?: User
  updater?: User
}

interface Props {
  certificate: Certificate
}

const props = defineProps<Props>()

const showPdfViewer = ref(false)

const togglePdfViewer = () => {
  showPdfViewer.value = !showPdfViewer.value
}
</script>

<template>
  <AppLayout>
    <Head title="Birth Archive Certificate" />

    <div class="container mx-auto py-8 px-4">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link :href="route('archive.birth.index')">
            <Button variant="outline" size="icon">
              <ArrowLeft class="size-4" />
            </Button>
          </Link>
          <div>
            <h1 class="text-3xl font-bold">Birth Archive Certificate</h1>
            <p class="text-muted-foreground">
              Certificate for {{ certificate.full_name }}
            </p>
          </div>
        </div>

        <div class="flex gap-2">
          <Button variant="outline" @click="togglePdfViewer">
            <Eye v-if="!showPdfViewer" class="mr-2 size-4" />
            <EyeOff v-else class="mr-2 size-4" />
            {{ showPdfViewer ? 'Hide' : 'View' }} PDF
          </Button>
          <a :href="certificate.file_url" target="_blank">
            <Button variant="outline">
              <Download class="mr-2 size-4" />
              Download
            </Button>
          </a>
          <Link :href="route('archive.birth.certificates.edit', certificate.id)">
            <Button variant="outline">
              <Edit class="mr-2 size-4" />
              Edit
            </Button>
          </Link>
        </div>
      </div>

      <!-- PDF Viewer -->
      <Card v-if="showPdfViewer" class="mb-6">
        <CardHeader>
          <CardTitle>Certificate Preview</CardTitle>
          <CardDescription>PDF document viewer</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="w-full overflow-hidden rounded-lg border bg-muted">
            <iframe
              :src="certificate.file_url"
              class="h-[800px] w-full"
              title="Certificate PDF Viewer"
            />
          </div>
        </CardContent>
      </Card>

      <!-- Certificate Details -->
      <div class="grid gap-6 md:grid-cols-2">
        <!-- Personal Information -->
        <Card>
          <CardHeader>
            <CardTitle>Personal Information</CardTitle>
            <CardDescription>Certificate holder details</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="text-sm font-medium text-muted-foreground">Full Name</div>
              <div class="text-base">{{ certificate.full_name }}</div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <div class="text-sm font-medium text-muted-foreground">First Name</div>
                <div class="text-base">{{ certificate.first_name }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-muted-foreground">Middle Name</div>
                <div class="text-base">{{ certificate.middle_name || 'N/A' }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-muted-foreground">Last Name</div>
                <div class="text-base">{{ certificate.last_name }}</div>
              </div>
            </div>

            <div>
              <div class="text-sm font-medium text-muted-foreground">Date of Birth</div>
              <div class="text-base">
                {{ certificate.formatted_date || `${certificate.birth_day}/${certificate.birth_month}/${certificate.birth_year}` }}
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Registration Information -->
        <Card>
          <CardHeader>
            <CardTitle>Registration Information</CardTitle>
            <CardDescription>Certificate registration details</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="text-sm font-medium text-muted-foreground">Registration Year</div>
              <div class="text-base">{{ certificate.reg_year }}</div>
            </div>

            <div>
              <div class="text-sm font-medium text-muted-foreground">Registration Number</div>
              <div class="text-base">{{ certificate.reg_no }}</div>
            </div>

            <div v-if="certificate.notes">
              <div class="text-sm font-medium text-muted-foreground">Notes</div>
              <div class="text-base">{{ certificate.notes }}</div>
            </div>
          </CardContent>
        </Card>

        <!-- File Information -->
        <Card>
          <CardHeader>
            <CardTitle>File Information</CardTitle>
            <CardDescription>Stored file details</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="text-sm font-medium text-muted-foreground">Folder Path</div>
              <div class="font-mono text-sm">{{ certificate.folder_path }}</div>
            </div>

            <div>
              <div class="text-sm font-medium text-muted-foreground">File Name</div>
              <div class="font-mono text-sm">{{ certificate.file_name }}</div>
            </div>
          </CardContent>
        </Card>

        <!-- Metadata -->
        <Card>
          <CardHeader>
            <CardTitle>Metadata</CardTitle>
            <CardDescription>Record tracking information</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="text-sm font-medium text-muted-foreground">Created At</div>
              <div class="text-base">{{ new Date(certificate.created_at).toLocaleString() }}</div>
            </div>

            <div v-if="certificate.creator">
              <div class="text-sm font-medium text-muted-foreground">Created By</div>
              <div class="text-base">{{ certificate.creator.name }}</div>
            </div>

            <div>
              <div class="text-sm font-medium text-muted-foreground">Updated At</div>
              <div class="text-base">{{ new Date(certificate.updated_at).toLocaleString() }}</div>
            </div>

            <div v-if="certificate.updater">
              <div class="text-sm font-medium text-muted-foreground">Updated By</div>
              <div class="text-base">{{ certificate.updater.name }}</div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
