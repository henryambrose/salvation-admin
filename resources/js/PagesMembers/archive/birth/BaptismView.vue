<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
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
import { ArrowLeft, Download, Edit, Save, Link as LinkIcon } from 'lucide-vue-next'

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

interface BaptismRecord {
  id: number
  member_id: number
  baptism_date?: string
  baptism_reg_no?: string
  place_of_baptism?: string
  baptism_parish_id?: number
  place_of_birth?: string
  nationality?: string
  father_name?: string
  father_residence?: string
  father_profession?: string
  mother_name?: string
  godfather_name?: string
  godfather_residence?: string
  godmother_name?: string
  godmother_residence?: string
  minister_name?: string
  baptism_remarks?: string
  member?: Member
  baptism_parish?: Parish
}

interface Certificate {
  id: number
  full_name: string
  first_name: string
  middle_name?: string
  last_name: string
  reg_year: number
  reg_no: string
  birth_year: number
  birth_month: number
  birth_day: number
  formatted_date?: string
  file_url: string | null
  view_url: string | null
  notes?: string
  created_at: string
  creator?: User
}

interface Props {
  certificate: Certificate
  baptismRecord?: BaptismRecord
  parishes?: Parish[]
  mode: 'create' | 'view'
}

const props = defineProps<Props>()

const memberOptions = ref<Array<{id: number, name: string}>>([])
const selectedMember = ref<any>(null)

const form = useForm({
  member_id: undefined as number | undefined,
  birth_archive_certificate_id: props.certificate.id,
  baptized_name: props.certificate.first_name + (props.certificate.middle_name ? ' ' + props.certificate.middle_name : ''),
  baptized_surname: props.certificate.last_name,
  baptism_date: '',
  baptism_reg_no: '',
  place_of_baptism: '',
  baptism_parish_id: undefined as string | undefined,
  place_of_birth: '',
  nationality: '',
  father_name: '',
  father_residence: '',
  father_profession: '',
  mother_name: '',
  godfather_name: '',
  godfather_residence: '',
  godmother_name: '',
  godmother_residence: '',
  minister_name: '',
  baptism_remarks: '',
  birth_text: '',
  reg_year: '',
  confirmation_text: '',
})

// Pre-fill birth date from certificate
const birthDate = computed(() => {
  if (props.certificate.formatted_date) {
    return props.certificate.formatted_date
  }
  const year = props.certificate.birth_year
  const month = String(props.certificate.birth_month).padStart(2, '0')
  const day = String(props.certificate.birth_day).padStart(2, '0')
  return `${year}-${month}-${day}`
})

// Fetch members from API
async function searchMembers(query: string) {
  // Don't clear options if query is empty - keep selected option visible
  if (!query || query.length < 2) {
    // Keep the selected member in the options if one is selected
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

    // Transform API response to match SearchDropdown format
    const transformedOptions = data.map((member: any) => ({
      id: member.id,
      name: member.text, // API returns 'text' field
      ...member // Keep all other fields for reference
    }))

    // Always include the currently selected member if not in the list
    if (selectedMember.value && !transformedOptions.find((m: {id: number, name: string}) => m.id === selectedMember.value.id)) {
      memberOptions.value = [selectedMember.value, ...transformedOptions]
    } else {
      memberOptions.value = transformedOptions
    }
  } catch (error) {
    console.error('Error searching members:', error)
    // Keep selected member even on error
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
  form.post(route('baptism-records.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout>
    <Head :title="`Birth Archive Certificate - ${mode === 'create' ? 'Create Baptism Record' : 'View Baptism Record'}`" />

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
            <h1 class="text-3xl font-bold">
              {{ mode === 'create' ? 'Create Baptism Record from Archive' : 'Baptism Record' }}
            </h1>
            <p class="text-muted-foreground">
              Certificate for {{ certificate.full_name }}
            </p>
          </div>
        </div>

        <div v-if="mode === 'view'" class="flex gap-2">
          <a v-if="certificate.file_url" :href="certificate.file_url" target="_blank">
            <Button variant="outline">
              <Download class="mr-2 size-4" />
              Download Archive PDF
            </Button>
          </a>
          <Button v-else variant="outline" disabled>
            <Download class="mr-2 size-4" />
            No PDF Available
          </Button>
          <a v-if="baptismRecord" :href="route('baptism-records.download-pdf', baptismRecord.id)" target="_blank">
            <Button>
              <Download class="mr-2 size-4" />
              Download Baptism Certificate
            </Button>
          </a>
          <Link v-if="baptismRecord" :href="route('baptism-records.edit', baptismRecord.id)">
            <Button variant="outline">
              <Edit class="mr-2 size-4" />
              Edit Record
            </Button>
          </Link>
        </div>
      </div>

      <!-- CREATE MODE: Side-by-side PDF and Form -->
      <div v-if="mode === 'create'" class="grid gap-6 lg:grid-cols-2">
        <!-- Left Column: PDF Viewer -->
        <div class="lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)] lg:overflow-hidden">
          <Card class="h-full">
            <CardHeader>
              <CardTitle>Certificate PDF</CardTitle>
              <CardDescription>Reference document - {{ certificate.full_name }}</CardDescription>
            </CardHeader>
            <CardContent class="h-[calc(100%-5rem)]">
              <div v-if="certificate.view_url" class="h-full w-full overflow-hidden rounded-lg border bg-muted">
                <iframe
                  :src="certificate.view_url"
                  class="h-full w-full"
                  title="Certificate PDF Viewer"
                />
              </div>
              <div v-else class="h-full w-full flex items-center justify-center rounded-lg border bg-muted">
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

        <!-- Right Column: Form (Scrollable) -->
        <div class="lg:overflow-y-auto lg:h-[calc(100vh-2rem)]">
          <Card>
          <CardHeader>
            <CardTitle>Create Baptism Record</CardTitle>
            <CardDescription>
              Enter baptism information from the PDF certificate above
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Member Selection (Optional) -->
              <div class="space-y-2">
                <Label for="member_id">Member (Optional)</Label>
                <p class="text-sm text-muted-foreground mb-2">
                  Search by name, member number, or family number. Leave empty if not a parish member.
                </p>
                <SearchDropdown
                  v-model="form.member_id"
                  :options="memberOptions"
                  placeholder="Type to search members..."
                  empty-message="No members found. Try a different search."
                  @search="searchMembers"
                />
                <p v-if="form.errors.member_id" class="text-sm text-destructive">
                  {{ form.errors.member_id }}
                </p>
                <p v-if="selectedMember" class="text-xs text-muted-foreground">
                  Selected: {{ selectedMember.name }}
                </p>
              </div>

              <!-- Baptized Name Fields -->
              <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                  <Label for="baptized_name">Baptized Name *</Label>
                  <Input
                    id="baptized_name"
                    v-model="form.baptized_name"
                    required
                  />
                  <p v-if="form.errors.baptized_name" class="text-sm text-destructive">
                    {{ form.errors.baptized_name }}
                  </p>
                </div>

                <div class="space-y-2">
                  <Label for="baptized_surname">Baptized Surname *</Label>
                  <Input
                    id="baptized_surname"
                    v-model="form.baptized_surname"
                    required
                  />
                  <p v-if="form.errors.baptized_surname" class="text-sm text-destructive">
                    {{ form.errors.baptized_surname }}
                  </p>
                </div>
              </div>

              <!-- Baptism Details -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Baptism Details</h3>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="baptism_date">Baptism Date</Label>
                    <Input
                      id="baptism_date"
                      v-model="form.baptism_date"
                      type="date"
                    />
                    <p v-if="form.errors.baptism_date" class="text-sm text-destructive">
                      {{ form.errors.baptism_date }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <Label for="birth_text">Birth Text</Label>
                    <Input
                      id="birth_text"
                      v-model="form.birth_text"
                      placeholder="Enter birth text"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="reg_year">Reg Year</Label>
                    <Input
                      id="reg_year"
                      v-model="form.reg_year"
                      placeholder="Enter registration year"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="baptism_reg_no">Baptism Registration No</Label>
                    <Input
                      id="baptism_reg_no"
                      v-model="form.baptism_reg_no"
                      placeholder="e.g., 2024/001"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="place_of_baptism">Place of Baptism</Label>
                    <Input
                      id="place_of_baptism"
                      v-model="form.place_of_baptism"
                      placeholder="e.g., St. Mary's Church"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="baptism_parish_id">Baptism Parish</Label>
                    <Select v-model="form.baptism_parish_id">
                      <SelectTrigger>
                        <SelectValue placeholder="Select parish" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem
                          v-for="parish in parishes"
                          :key="parish.id"
                          :value="parish.id.toString()"
                        >
                          {{ parish.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </div>

              <!-- Birth Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Birth Information</h3>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="place_of_birth">Place of Birth</Label>
                    <Input
                      id="place_of_birth"
                      v-model="form.place_of_birth"
                      placeholder="e.g., Mumbai, India"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="nationality">Nationality</Label>
                    <Input
                      id="nationality"
                      v-model="form.nationality"
                      placeholder="e.g., Indian"
                    />
                  </div>
                </div>
              </div>

              <!-- Parents Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Parents Information</h3>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="father_name">Father's Name</Label>
                    <Input
                      id="father_name"
                      v-model="form.father_name"
                      placeholder="Full name"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="mother_name">Mother's Name</Label>
                    <Input
                      id="mother_name"
                      v-model="form.mother_name"
                      placeholder="Full name"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="father_profession">Father's Profession</Label>
                    <Input
                      id="father_profession"
                      v-model="form.father_profession"
                      placeholder="e.g., Engineer"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="father_residence">Father's Residence</Label>
                    <Textarea
                      id="father_residence"
                      v-model="form.father_residence"
                      placeholder="Full address"
                      :rows="2"
                    />
                  </div>
                </div>
              </div>

              <!-- Godparents Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Godparents Information</h3>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="godfather_name">Godfather's Name</Label>
                    <Input
                      id="godfather_name"
                      v-model="form.godfather_name"
                      placeholder="Full name"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="godmother_name">Godmother's Name</Label>
                    <Input
                      id="godmother_name"
                      v-model="form.godmother_name"
                      placeholder="Full name"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="godfather_residence">Godfather's Residence</Label>
                    <Textarea
                      id="godfather_residence"
                      v-model="form.godfather_residence"
                      placeholder="Full address"
                      :rows="2"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="godmother_residence">Godmother's Residence</Label>
                    <Textarea
                      id="godmother_residence"
                      v-model="form.godmother_residence"
                      placeholder="Full address"
                      :rows="2"
                    />
                  </div>
                </div>
              </div>

              <!-- Confirmation -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Confirmation</h3>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="confirmation_text">Confirmation Text</Label>
                    <Input
                      id="confirmation_text"
                      v-model="form.confirmation_text"
                      placeholder="Enter confirmation text"
                    />
                  </div>
                </div>
              </div>

              <!-- Minister and Remarks -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Additional Information</h3>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="minister_name">Minister/Priest Name</Label>
                    <Input
                      id="minister_name"
                      v-model="form.minister_name"
                      placeholder="Rev. Fr. Name"
                    />
                  </div>

                  <div class="space-y-2">
                    <Label for="baptism_remarks">Remarks</Label>
                    <Textarea
                      id="baptism_remarks"
                      v-model="form.baptism_remarks"
                      placeholder="Any additional notes..."
                      :rows="2"
                    />
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end gap-4">
                <Link :href="route('archive.birth.index')">
                  <Button type="button" variant="outline">
                    Cancel
                  </Button>
                </Link>
                <Button type="submit" :disabled="form.processing">
                  <Save class="mr-2 size-4" />
                  {{ form.processing ? 'Creating...' : 'Create Baptism Record' }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
        </div>
      </div>

      <!-- VIEW MODE: Baptism Record Details -->
      <div v-if="mode === 'view' && baptismRecord">
        <!-- Archive Certificate Info -->
        <Card class="mb-6">
          <CardHeader>
            <CardTitle>Archive Certificate Details</CardTitle>
            <CardDescription>Birth certificate from archive</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="grid gap-4 md:grid-cols-4">
              <div>
                <div class="text-sm font-medium text-muted-foreground">Full Name</div>
                <div class="text-base">{{ certificate.full_name }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-muted-foreground">Birth Date</div>
                <div class="text-base">{{ birthDate }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-muted-foreground">Registration</div>
                <div class="text-base">{{ certificate.reg_year }} / {{ certificate.reg_no }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-muted-foreground">PDF Document</div>
                <a v-if="certificate.file_url" :href="certificate.file_url" target="_blank" class="text-base text-primary hover:underline">
                  View Original PDF
                </a>
                <span v-else class="text-base text-muted-foreground">
                  Not available
                </span>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Link Indicator & Actions -->
        <Card class="mb-6 border-primary/50 bg-primary/5">
          <CardContent class="pt-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-sm text-primary">
                <LinkIcon class="size-4" />
                <span class="font-medium">
                  This baptism record is linked to the archive certificate shown above
                </span>
              </div>
              <a :href="route('baptism-records.download-pdf', baptismRecord.id)" target="_blank">
                <Button size="sm">
                  <Download class="mr-2 size-4" />
                  Download Baptism Certificate
                </Button>
              </a>
            </div>
          </CardContent>
        </Card>

        <!-- Baptism Details -->
        <div class="grid gap-6 md:grid-cols-2">
          <!-- Baptism Information -->
          <Card>
            <CardHeader>
              <CardTitle>Baptism Details</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div v-if="baptismRecord.baptism_date">
                <div class="text-sm font-medium text-muted-foreground">Baptism Date</div>
                <div class="text-base">{{ new Date(baptismRecord.baptism_date).toLocaleDateString() }}</div>
              </div>
              <div v-if="baptismRecord.baptism_reg_no">
                <div class="text-sm font-medium text-muted-foreground">Registration Number</div>
                <div class="text-base">{{ baptismRecord.baptism_reg_no }}</div>
              </div>
              <div v-if="baptismRecord.place_of_baptism">
                <div class="text-sm font-medium text-muted-foreground">Place of Baptism</div>
                <div class="text-base">{{ baptismRecord.place_of_baptism }}</div>
              </div>
              <div v-if="baptismRecord.baptism_parish">
                <div class="text-sm font-medium text-muted-foreground">Parish</div>
                <div class="text-base">{{ baptismRecord.baptism_parish.name }}</div>
              </div>
            </CardContent>
          </Card>

          <!-- Birth Information -->
          <Card>
            <CardHeader>
              <CardTitle>Birth Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div v-if="baptismRecord.place_of_birth">
                <div class="text-sm font-medium text-muted-foreground">Place of Birth</div>
                <div class="text-base">{{ baptismRecord.place_of_birth }}</div>
              </div>
              <div v-if="baptismRecord.nationality">
                <div class="text-sm font-medium text-muted-foreground">Nationality</div>
                <div class="text-base">{{ baptismRecord.nationality }}</div>
              </div>
            </CardContent>
          </Card>

          <!-- Parents Information -->
          <Card>
            <CardHeader>
              <CardTitle>Parents</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div v-if="baptismRecord.father_name">
                <div class="text-sm font-medium text-muted-foreground">Father's Name</div>
                <div class="text-base">{{ baptismRecord.father_name }}</div>
              </div>
              <div v-if="baptismRecord.father_profession">
                <div class="text-sm font-medium text-muted-foreground">Father's Profession</div>
                <div class="text-base">{{ baptismRecord.father_profession }}</div>
              </div>
              <div v-if="baptismRecord.father_residence">
                <div class="text-sm font-medium text-muted-foreground">Father's Residence</div>
                <div class="text-base whitespace-pre-wrap">{{ baptismRecord.father_residence }}</div>
              </div>
              <div v-if="baptismRecord.mother_name">
                <div class="text-sm font-medium text-muted-foreground">Mother's Name</div>
                <div class="text-base">{{ baptismRecord.mother_name }}</div>
              </div>
            </CardContent>
          </Card>

          <!-- Godparents Information -->
          <Card>
            <CardHeader>
              <CardTitle>Godparents</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div v-if="baptismRecord.godfather_name">
                <div class="text-sm font-medium text-muted-foreground">Godfather's Name</div>
                <div class="text-base">{{ baptismRecord.godfather_name }}</div>
              </div>
              <div v-if="baptismRecord.godfather_residence">
                <div class="text-sm font-medium text-muted-foreground">Godfather's Residence</div>
                <div class="text-base whitespace-pre-wrap">{{ baptismRecord.godfather_residence }}</div>
              </div>
              <div v-if="baptismRecord.godmother_name">
                <div class="text-sm font-medium text-muted-foreground">Godmother's Name</div>
                <div class="text-base">{{ baptismRecord.godmother_name }}</div>
              </div>
              <div v-if="baptismRecord.godmother_residence">
                <div class="text-sm font-medium text-muted-foreground">Godmother's Residence</div>
                <div class="text-base whitespace-pre-wrap">{{ baptismRecord.godmother_residence }}</div>
              </div>
            </CardContent>
          </Card>

          <!-- Additional Information -->
          <Card class="md:col-span-2">
            <CardHeader>
              <CardTitle>Additional Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div v-if="baptismRecord.minister_name">
                <div class="text-sm font-medium text-muted-foreground">Minister/Priest</div>
                <div class="text-base">{{ baptismRecord.minister_name }}</div>
              </div>
              <div v-if="baptismRecord.baptism_remarks">
                <div class="text-sm font-medium text-muted-foreground">Remarks</div>
                <div class="text-base whitespace-pre-wrap">{{ baptismRecord.baptism_remarks }}</div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
