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
import { Eye, Plus, Search, CheckCircle2, Clock } from 'lucide-vue-next'

interface MarriageRecord {
  id: number
  marriage_archive_certificate_id: number
}

interface Certificate {
  id: number
  full_name: string
  reg_year: number
  reg_no: string
  marriage_year: number
  marriage_month: number
  marriage_day: number
  formatted_date?: string
  created_at: string
  marriage_record?: MarriageRecord | null
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
  return `${cert.marriage_day}/${cert.marriage_month}/${cert.marriage_year}`
}

function hasMarriageRecord(cert: Certificate): boolean {
  return !!cert.marriage_record
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
    <Head title="Marriage Archive Certificates" />

    <div class="container mx-auto py-8 px-4">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Marriage Archive Certificates</h1>
          <p class="text-muted-foreground">
            Manage birth archive certificates and create marriage records
          </p>
        </div>

        <Link :href="route('archive.marriage.certificates.create')">
          <Button>
            <Plus class="mr-2 size-4" />
            Add Certificate
          </Button>
        </Link>
      </div>

      <!-- Filters Card -->
      <Card class="mb-6">
        <CardHeader>
          <CardTitle>Filters</CardTitle>
          <CardDescription>Search and filter birth archive certificates</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Search -->
            <div class="space-y-2">
              <label class="text-sm font-medium">Search</label>
              <div class="relative">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  v-model="search"
                  placeholder="Name, reg no, notes..."
                  class="pl-9"
                />
              </div>
            </div>

            <!-- Year -->
            <div class="space-y-2">
              <label class="text-sm font-medium">Marriage Year</label>
              <Input
                v-model="year"
                type="number"
                placeholder="e.g., 1990"
              />
            </div>

            <!-- Month -->
            <div class="space-y-2">
              <label class="text-sm font-medium">Marriage Month</label>
              <Input
                v-model="month"
                type="number"
                placeholder="1-12"
                min="1"
                max="12"
              />
            </div>

            <!-- Day -->
            <div class="space-y-2">
              <label class="text-sm font-medium">Marriage Day</label>
              <Input
                v-model="day"
                type="number"
                placeholder="1-31"
                min="1"
                max="31"
              />
            </div>

            <!-- Per Page -->
            <div class="space-y-2">
              <label class="text-sm font-medium">Per Page</label>
              <Select v-model="perPage">
                <SelectTrigger>
                  <SelectValue placeholder="Select" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="10">10</SelectItem>
                  <SelectItem value="25">25</SelectItem>
                  <SelectItem value="50">50</SelectItem>
                  <SelectItem value="100">100</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- Show Archived -->
            <div class="flex items-end space-x-2">
              <label class="flex cursor-pointer items-center space-x-2">
                <input
                  v-model="isArchived"
                  type="checkbox"
                  class="size-4 rounded border-gray-300"
                />
                <span class="text-sm font-medium">Show Archived</span>
              </label>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
              <Button variant="outline" @click="clearFilters" class="w-full">
                Clear Filters
              </Button>
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
                  <TableHead>Marriage Date</TableHead>
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
                    <Badge v-if="hasMarriageRecord(cert)" variant="default" class="gap-1">
                      <CheckCircle2 class="size-3" />
                      Marriage Created
                    </Badge>
                    <Badge v-else variant="secondary" class="gap-1">
                      <Clock class="size-3" />
                      Pending
                    </Badge>
                  </TableCell>
                  <TableCell class="text-right">
                    <Link :href="route('archive.marriage.certificates.marriage', cert.id)">
                      <Button variant="outline" size="sm">
                        <Eye class="mr-2 size-4" />
                        View
                      </Button>
                    </Link>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Pagination -->
          <div v-if="certificates.last_page > 1" class="mt-4 flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
              Showing {{ ((certificates.current_page - 1) * certificates.per_page) + 1 }}
              to {{ Math.min(certificates.current_page * certificates.per_page, certificates.total) }}
              of {{ certificates.total }} results
            </div>
            <div class="flex gap-2">
              <Button
                v-for="link in certificates.links"
                :key="link.label"
                :variant="link.active ? 'default' : 'outline'"
                size="sm"
                :disabled="!link.url"
                @click="link.url && router.visit(link.url)"
                v-html="link.label"
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
