<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue'
import { ArrowLeft, Download, Edit, Eye, EyeOff, Save, Link as LinkIcon } from 'lucide-vue-next'

interface User {
  id: number
  name: string
}

interface Parish {
  id: number
  name: string
}

interface Member {
  id: number
  first_name: string
  middle_name?: string
  last_name: string
  family_no: string
}

interface DeathRecord {
  id: number
  member_id?: number
  death_date?: string
  burial_date?: string
  burial_reg_no?: string
  burial_parish_id?: number
  deceased_name?: string
  deceased_surname?: string
  relationship?: string
  residence?: string
  age?: number
  nationality?: string
  cause_of_death?: string
  place_of_burial?: string
  minister_name?: string
  death_remarks?: string
  member?: Member
  burialParish?: Parish
}

interface Certificate {
  id: number
  full_name: string
  first_name: string
  middle_name?: string
  last_name: string
  reg_year: number
  reg_no: string
  death_year: number
  death_month: number
  death_day: number
  formatted_date?: string
  file_url: string | null
  notes?: string
  created_at: string
  creator?: User
}

interface Props {
  certificate: Certificate
  deathRecord?: DeathRecord
  parishes?: Parish[]
  mode: 'create' | 'view'
}

const props = defineProps<Props>()

const showPdf = ref(true)
const memberOptions = ref<Array<{id: number, name: string}>>([])
const selectedMember = ref<any>(null)

const form = useForm({
  member_id: undefined as number | undefined,
  death_archive_certificate_id: props.certificate.id,
  death_date: '',
  burial_date: '',
  burial_reg_no: '',
  burial_parish_id: undefined as number | undefined,
  deceased_name: props.certificate.first_name + (props.certificate.middle_name ? ' ' + props.certificate.middle_name : ''),
  deceased_surname: props.certificate.last_name,
  relationship: '',
  residence: '',
  age: undefined as number | undefined,
  nationality: '',
  cause_of_death: '',
  place_of_burial: '',
  minister_name: '',
  death_remarks: '',
})

// Pre-fill death date from certificate
const deathDate = computed(() => {
  if (props.certificate.formatted_date) {
    return props.certificate.formatted_date
  }
  const year = props.certificate.death_year
  const month = String(props.certificate.death_month).padStart(2, '0')
  const day = String(props.certificate.death_day).padStart(2, '0')
  return `${year}-${month}-${day}`
})

// Fetch members from API
async function searchMembers(query: string) {
  if (!query || query.length < 2) {
    if (selectedMember.value) {
      memberOptions.value = [selectedMember.value]
    }
    return
  }

  try {
    const response = await fetch(
      route('member.search-members', { query, limit: 20 })
    )
    const data = await response.json()

    const transformedOptions = data.map((member: any) => ({
      id: member.id,
      name: member.text,
      ...member
    }))

    if (selectedMember.value && !transformedOptions.find((m: {id: number, name: string}) => m.id === selectedMember.value.id)) {
      memberOptions.value = [selectedMember.value, ...transformedOptions]
    } else {
      memberOptions.value = transformedOptions
    }
  } catch (error) {
    console.error('Error searching members:', error)
    if (selectedMember.value) {
      memberOptions.value = [selectedMember.value]
    }
  }
}

// Watch for member selection
watch(() => form.member_id, (newId) => {
  if (newId) {
    const member = memberOptions.value.find(m => m.id === newId)
    if (member) {
      selectedMember.value = member
    }
  } else {
    selectedMember.value = null
  }
})

function submitForm() {
  form.post(route('death-records.store'), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Death record created successfully')
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
    }
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Death Archive Certificate" />

    <div class="container mx-auto py-8 px-4">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link :href="route('archive.death.index')">
            <Button variant="outline" size="sm">
              <ArrowLeft class="mr-2 size-4" />
              Back to List
            </Button>
          </Link>
          <div>
            <h1 class="text-3xl font-bold">Death Archive Certificate</h1>
            <p class="text-muted-foreground">
              {{ mode === 'create' ? 'Create death record from archive' : 'View death record' }}
            </p>
          </div>
        </div>
      </div>

      <!-- CREATE MODE: Side-by-side PDF and Form -->
      <div v-if="mode === 'create'" class="grid gap-6 lg:grid-cols-2">
        <!-- Left Column: PDF Viewer (Sticky) -->
        <div class="lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)] lg:overflow-hidden">
          <Card class="h-full flex flex-col">
            <CardHeader class="flex-none">
              <div class="flex items-center justify-between">
                <CardTitle>Certificate PDF</CardTitle>
                <Button
                  variant="ghost"
                  size="sm"
                  @click="showPdf = !showPdf"
                >
                  <Eye v-if="!showPdf" class="size-4" />
                  <EyeOff v-else class="size-4" />
                </Button>
              </div>
              <CardDescription>
                Reg {{ certificate.reg_year }} / {{ certificate.reg_no }}
              </CardDescription>
            </CardHeader>
            <CardContent v-if="showPdf" class="flex-1 p-0 min-h-0">
              <iframe
                v-if="certificate.file_url"
                :src="certificate.file_url"
                class="w-full h-full"
                frameborder="0"
              />
              <div v-else class="h-full w-full flex items-center justify-center">
                <div class="text-center p-8">
                  <p class="text-lg font-semibold text-muted-foreground mb-2">PDF Not Available</p>
                  <p class="text-sm text-muted-foreground">
                    The certificate PDF has not been uploaded yet.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Right Column: Death Record Form (Scrollable) -->
        <div class="lg:overflow-y-auto lg:h-[calc(100vh-2rem)]">
          <form @submit.prevent="submitForm">
            <!-- Certificate Info Card -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Archive Certificate Information</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">Full Name</Label>
                    <p class="font-medium">{{ certificate.full_name }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">Death Date</Label>
                    <p class="font-medium">{{ deathDate }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">Registration</Label>
                    <p class="font-medium">{{ certificate.reg_year }} / {{ certificate.reg_no }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Death Record Form -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Create Death Record</CardTitle>
                <CardDescription>
                  Fill in the details from the archive certificate. Member selection is optional.
                </CardDescription>
              </CardHeader>
              <CardContent class="space-y-6">
                <!-- Member Selection (Optional) -->
                <div class="space-y-2">
                  <Label>Member (Optional)</Label>
                  <SearchDropdown
                    v-model="form.member_id"
                    :options="memberOptions"
                    placeholder="Search for member (optional)..."
                    @search="searchMembers"
                  />
                  <p class="text-xs text-muted-foreground">
                    Leave empty if deceased was not a parish member
                  </p>
                </div>

                <!-- Deceased Information -->
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="deceased_name">Deceased Name *</Label>
                    <Input
                      id="deceased_name"
                      v-model="form.deceased_name"
                      required
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="deceased_surname">Deceased Surname *</Label>
                    <Input
                      id="deceased_surname"
                      v-model="form.deceased_surname"
                      required
                    />
                  </div>
                </div>

                <!-- Dates -->
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="death_date">Death Date</Label>
                    <Input
                      id="death_date"
                      v-model="form.death_date"
                      type="date"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="burial_date">Burial Date</Label>
                    <Input
                      id="burial_date"
                      v-model="form.burial_date"
                      type="date"
                    />
                  </div>
                </div>

                <!-- Burial Information -->
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="burial_reg_no">Burial Registration No</Label>
                    <Input
                      id="burial_reg_no"
                      v-model="form.burial_reg_no"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="place_of_burial">Place of Burial</Label>
                    <Input
                      id="place_of_burial"
                      v-model="form.place_of_burial"
                    />
                  </div>
                </div>

                <!-- Personal Details -->
                <div class="grid gap-4 md:grid-cols-3">
                  <div class="space-y-2">
                    <Label for="age">Age</Label>
                    <Input
                      id="age"
                      v-model.number="form.age"
                      type="number"
                      min="0"
                      max="150"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="nationality">Nationality</Label>
                    <Input
                      id="nationality"
                      v-model="form.nationality"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="relationship">Relationship</Label>
                    <Input
                      id="relationship"
                      v-model="form.relationship"
                      placeholder="e.g., Father, Mother"
                    />
                  </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-2">
                  <Label for="residence">Residence</Label>
                  <Input
                    id="residence"
                    v-model="form.residence"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="cause_of_death">Cause of Death</Label>
                  <Input
                    id="cause_of_death"
                    v-model="form.cause_of_death"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="minister_name">Minister Name</Label>
                  <Input
                    id="minister_name"
                    v-model="form.minister_name"
                  />
                </div>

                <!-- Remarks -->
                <div class="space-y-2">
                  <Label for="death_remarks">Remarks</Label>
                  <Textarea
                    id="death_remarks"
                    v-model="form.death_remarks"
                    :rows="3"
                  />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-2">
                  <Button
                    type="submit"
                    :disabled="form.processing"
                  >
                    <Save class="mr-2 size-4" />
                    Create Death Record
                  </Button>
                </div>
              </CardContent>
            </Card>
          </form>
        </div>
      </div>

      <!-- VIEW MODE: Death Record Details -->
      <div v-else class="space-y-6">
        <!-- Certificate Card -->
        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>Archive Certificate</CardTitle>
                <CardDescription>{{ certificate.full_name }}</CardDescription>
              </div>
              <div class="flex gap-2">
                <a v-if="certificate.file_url" :href="certificate.file_url" target="_blank">
                  <Button variant="outline" size="sm">
                    <Download class="mr-2 size-4" />
                    Download PDF
                  </Button>
                </a>
                <Button v-else variant="outline" size="sm" disabled>
                  <Download class="mr-2 size-4" />
                  No PDF Available
                </Button>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div class="grid gap-4 md:grid-cols-3">
              <div>
                <Label class="text-sm font-medium text-muted-foreground">Death Date</Label>
                <p class="font-medium">{{ deathDate }}</p>
              </div>
              <div>
                <Label class="text-sm font-medium text-muted-foreground">Registration Year</Label>
                <p class="font-medium">{{ certificate.reg_year }}</p>
              </div>
              <div>
                <Label class="text-sm font-medium text-muted-foreground">Registration No</Label>
                <p class="font-medium">{{ certificate.reg_no }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Death Record Card -->
        <Card v-if="deathRecord">
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>Death Record</CardTitle>
                <CardDescription>
                  <span v-if="deathRecord.member">
                    Member: {{ deathRecord.member.first_name }} {{ deathRecord.member.last_name }}
                  </span>
                  <span v-else>
                    Non-member: {{ deathRecord.deceased_name }} {{ deathRecord.deceased_surname }}
                  </span>
                </CardDescription>
              </div>
              <div class="flex gap-2">
                <Link :href="route('death-records.download-pdf', deathRecord.id)">
                  <Button variant="outline" size="sm">
                    <Download class="mr-2 size-4" />
                    Download Certificate
                  </Button>
                </Link>
                <Link :href="route('death-records.edit', deathRecord.id)">
                  <Button variant="outline" size="sm">
                    <Edit class="mr-2 size-4" />
                    Edit
                  </Button>
                </Link>
              </div>
            </div>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Deceased Information -->
            <div>
              <h3 class="text-lg font-semibold mb-3">Deceased Information</h3>
              <div class="grid gap-4 md:grid-cols-3">
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Name</Label>
                  <p class="font-medium">{{ deathRecord.deceased_name }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Surname</Label>
                  <p class="font-medium">{{ deathRecord.deceased_surname }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Age</Label>
                  <p class="font-medium">{{ deathRecord.age || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Dates -->
            <div>
              <h3 class="text-lg font-semibold mb-3">Important Dates</h3>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Death Date</Label>
                  <p class="font-medium">{{ deathRecord.death_date || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Burial Date</Label>
                  <p class="font-medium">{{ deathRecord.burial_date || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Burial Information -->
            <div>
              <h3 class="text-lg font-semibold mb-3">Burial Information</h3>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Burial Registration No</Label>
                  <p class="font-medium">{{ deathRecord.burial_reg_no || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Place of Burial</Label>
                  <p class="font-medium">{{ deathRecord.place_of_burial || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Additional Details -->
            <div>
              <h3 class="text-lg font-semibold mb-3">Additional Details</h3>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Nationality</Label>
                  <p class="font-medium">{{ deathRecord.nationality || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Residence</Label>
                  <p class="font-medium">{{ deathRecord.residence || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Cause of Death</Label>
                  <p class="font-medium">{{ deathRecord.cause_of_death || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Minister Name</Label>
                  <p class="font-medium">{{ deathRecord.minister_name || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Remarks -->
            <div v-if="deathRecord.death_remarks">
              <h3 class="text-lg font-semibold mb-3">Remarks</h3>
              <p class="text-sm">{{ deathRecord.death_remarks }}</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
