<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { debounce } from 'lodash'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Eye, Plus, Search, CheckCircle2, Clock, Edit } from 'lucide-vue-next'

interface DeathRecord {
  id: number
  death_archive_certificate_id: number
}

interface Certificate {
  id: number
  full_name: string
  reg_year: number
  reg_no: string
  death_year: number
  death_month: number
  death_day: number
  formatted_date?: string
  created_at: string
  death_record?: DeathRecord | null
}

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

interface PaginatedCertificates {
  data: Certificate[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  links: PaginationLink[]
}

interface Filters {
  search?: string
  year?: string
  month?: string
  day?: string
  sort?: string
  direction?: string
  perPage?: string
  isArchived?: string
}

interface Props {
  fetchUrl: string
  certificates: PaginatedCertificates
  filters: Filters
}

const props = defineProps<Props>()

// Local state for filters
const search = ref(props.filters.search || '')
const year = ref(props.filters.year || '')
const month = ref(props.filters.month || '')
const day = ref(props.filters.day || '')
const perPage = ref(props.filters.perPage || '10')
const isArchived = ref(props.filters.isArchived === 'true')

// Debounced search
const performSearch = debounce(() => {
  updateFilters()
}, 500)

watch(search, () => {
  performSearch()
})

watch([year, month, day, perPage, isArchived], () => {
  updateFilters()
})

function updateFilters() {
  router.get(
    props.fetchUrl,
    {
      search: search.value || undefined,
      year: year.value || undefined,
      month: month.value || undefined,
      day: day.value || undefined,
      perPage: perPage.value,
      isArchived: isArchived.value ? 'true' : undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  )
}

function formatDate(cert: Certificate): string {
  if (cert.formatted_date) return cert.formatted_date
  return `${cert.death_day}/${cert.death_month}/${cert.death_year}`
}

function hasDeathRecord(cert: Certificate): boolean {
  return !!cert.death_record
}

function clearFilters() {
  search.value = ''
  year.value = ''
  month.value = ''
  day.value = ''
  perPage.value = '10'
  isArchived.value = false
}
</script>

<template>
  <AppLayout>
    <Head title="Death Archive Certificates" />

    <div class="container mx-auto py-8 px-4">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Death Archive Certificates</h1>
          <p class="text-muted-foreground">
            Manage birth archive certificates and create death records
          </p>
        </div>

        <Link :href="route('archive.death.certificates.create')">
          <Button>
            <Plus class="mr-2 size-4" />
            Add Certificate
          </Button>
        </Link>
      </div>

      <!-- Filters Card -->
      <Card class="mb-6">
        <CardHeader>
          <CardTitle class="text-base">Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex gap-3 items-center justify-between">
            <div class="flex gap-3 items-center flex-1">
              <!-- Search -->
              <div class="w-64">
                <Input
                  v-model="search"
                  placeholder="Name, reg no, notes..."
                />
              </div>

              <!-- Year -->
              <div class="w-32">
                <Input
                  v-model="year"
                  type="number"
                  placeholder="Death Year"
                />
              </div>

              <!-- Month -->
              <div class="w-32">
                <Input
                  v-model="month"
                  type="number"
                  placeholder="Month (1-12)"
                  min="1"
                  max="12"
                />
              </div>

              <!-- Day -->
              <div class="w-24">
                <Input
                  v-model="day"
                  type="number"
                  placeholder="Day"
                  min="1"
                  max="31"
                />
              </div>
            </div>

            <div class="flex gap-3 items-center">
              <!-- Show Archived -->
              <div>
                <label class="flex cursor-pointer items-center gap-2 h-10">
                  <button
                    type="button"
                    @click="isArchived = !isArchived"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      isArchived ? 'bg-blue-600' : 'bg-red-500'
                    ]"
                  >
                    <span
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        isArchived ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    />
                  </button>
                  <span class="text-sm font-medium">Show Archived</span>
                </label>
              </div>

              <!-- Clear Filters -->
              <div>
                <Button variant="outline" @click="clearFilters">
                  Clear
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Results Card -->
      <Card>
        <CardHeader>
          <div class="flex items-center justify-between">
            <div>
              <CardTitle>Certificates</CardTitle>
              <CardDescription>
                {{ certificates.total }} total certificate{{ certificates.total !== 1 ? 's' : '' }}
              </CardDescription>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <!-- Table -->
          <div class="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Full Name</TableHead>
                  <TableHead>Death Date</TableHead>
                  <TableHead>Reg Year/No</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="certificates.data.length === 0">
                  <TableCell colspan="5" class="text-center text-muted-foreground">
                    No certificates found
                  </TableCell>
                </TableRow>
                <TableRow v-for="cert in certificates.data" :key="cert.id">
                  <TableCell class="font-medium">
                    {{ cert.full_name }}
                  </TableCell>
                  <TableCell>
                    {{ formatDate(cert) }}
                  </TableCell>
                  <TableCell>
                    {{ cert.reg_year }} / {{ cert.reg_no }}
                  </TableCell>
                  <TableCell>
                    <Badge v-if="hasDeathRecord(cert)" variant="default" class="gap-1">
                      <CheckCircle2 class="size-3" />
                      Death Created
                    </Badge>
                    <Badge v-else variant="secondary" class="gap-1">
                      <Clock class="size-3" />
                      Pending
                    </Badge>
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex justify-end gap-2">
                      <Link :href="route('archive.death.certificates.edit', cert.id)">
                        <Button variant="ghost" size="sm" title="Edit Certificate">
                          <Edit class="size-4" />
                        </Button>
                      </Link>
                      <Link :href="route('archive.death.certificates.death', cert.id)">
                        <Button variant="outline" size="sm">
                          <Eye class="mr-2 size-4" />
                          View
                        </Button>
                      </Link>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Pagination -->
          <div v-if="certificates.total > 0" class="mt-4 flex items-center justify-between px-4 py-3 border-t">
            <div class="text-sm text-gray-700">
              Showing {{ certificates.total }} total certificate{{ certificates.total !== 1 ? 's' : '' }}
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-700">Page</span>
              <select
                :value="certificates.current_page"
                @change="router.get(props.fetchUrl, {
                  search: search,
                  year,
                  month,
                  day,
                  perPage,
                  isArchived: isArchived ? 'true' : undefined,
                  page: ($event.target as HTMLSelectElement).value
                }, { preserveState: true, preserveScroll: true })"
                class="rounded-md border border-gray-300 px-2 py-1 text-sm"
              >
                <option v-for="page in certificates.last_page" :key="page" :value="page">
                  {{ page }}
                </option>
              </select>
              <span class="text-sm text-gray-700">of {{ certificates.last_page }}</span>
              <Button
                variant="outline"
                size="sm"
                :disabled="certificates.current_page === certificates.last_page"
                @click="router.get(props.fetchUrl, {
                  search,
                  year,
                  month,
                  day,
                  perPage,
                  isArchived: isArchived ? 'true' : undefined,
                  page: certificates.current_page + 1
                }, { preserveState: true, preserveScroll: true })"
              >
                Next →
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
