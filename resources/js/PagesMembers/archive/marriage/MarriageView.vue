<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Download, Edit, Eye, EyeOff, Save } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface User {
  id: number;
  name: string;
}

interface Member {
  id: number;
  first_name: string;
  middle_name?: string;
  last_name: string;
  family_no: string;
}

interface MarriageRecord {
  id: number;
  marriage_date?: string;
  marriage_reg_no?: string;
  parish_of_marriage?: string;
  bridegroom_member_id?: number;
  bridegroom_name?: string;
  bridegroom_surname?: string;
  bridegroom_dob?: string;
  bridegroom_nationality?: string;
  bridegroom_profession?: string;
  bridegroom_residence?: string;
  bridegroom_father_name?: string;
  bridegroom_mother_name?: string;
  bridegroom_status?: string;
  bridegroom_if_widower_whose?: string;
  bride_member_id?: number;
  bride_name?: string;
  bride_surname?: string;
  bride_dob?: string;
  bride_nationality?: string;
  bride_profession?: string;
  bride_residence?: string;
  bride_father_name?: string;
  bride_mother_name?: string;
  bride_status?: string;
  bride_if_widow_whose?: string;
  first_witness_name?: string;
  first_witness_residence?: string;
  second_witness_name?: string;
  second_witness_residence?: string;
  minister_name?: string;
  marriage_remarks?: string;
  bridegroom?: Member;
  bride?: Member;
}

interface Certificate {
  id: number;
  full_name: string;
  first_name: string;
  middle_name?: string;
  last_name: string;
  reg_year: number;
  reg_no: string;
  marriage_year: number;
  marriage_month: number;
  marriage_day: number;
  formatted_date?: string;
  file_url: string | null;
  view_url: string | null;
  notes?: string;
  created_at: string;
  creator?: User;
}

interface Props {
  certificate: Certificate;
  marriageRecord?: MarriageRecord;
  mode: 'create' | 'view';
}

const props = defineProps<Props>();

const showPdf = ref(true);
const bridegroomMemberOptions = ref<Array<{ id: number; name: string }>>([]);
const selectedBridegroomMember = ref<any>(null);
const brideMemberOptions = ref<Array<{ id: number; name: string }>>([]);
const selectedBrideMember = ref<any>(null);

const form = useForm({
  marriage_archive_certificate_id: props.certificate.id,
  marriage_date: '',
  marriage_reg_no: '',
  parish_of_marriage: '',
  bridegroom_member_id: null as number | null,
  bridegroom_name: '',
  bridegroom_surname: '',
  bridegroom_dob: '',
  bridegroom_nationality: '',
  bridegroom_profession: '',
  bridegroom_residence: '',
  bridegroom_father_name: '',
  bridegroom_mother_name: '',
  bridegroom_status: '',
  bridegroom_if_widower_whose: '',
  bride_member_id: null as number | null,
  bride_name: '',
  bride_surname: '',
  bride_dob: '',
  bride_nationality: '',
  bride_profession: '',
  bride_residence: '',
  bride_father_name: '',
  bride_mother_name: '',
  bride_status: '',
  bride_if_widow_whose: '',
  first_witness_name: '',
  first_witness_residence: '',
  second_witness_name: '',
  second_witness_residence: '',
  minister_name: '',
  marriage_remarks: '',
  year_of_marriage: '',
});

// Pre-fill marriage date from certificate
const marriageDate = computed(() => {
  if (props.certificate.formatted_date) {
    return props.certificate.formatted_date;
  }
  const year = props.certificate.marriage_year;
  const month = String(props.certificate.marriage_month).padStart(2, '0');
  const day = String(props.certificate.marriage_day).padStart(2, '0');
  return `${year}-${month}-${day}`;
});

// Fetch members for bridegroom
async function searchBridegroomMembers(query: string) {
  if (!query || query.length < 2) {
    if (selectedBridegroomMember.value) {
      bridegroomMemberOptions.value = [selectedBridegroomMember.value];
    }
    return;
  }

  try {
    const response = await fetch(route('member.search-members', { query, limit: 20 }));
    const data = await response.json();

    const transformedOptions = data.map((member: any) => ({
      id: member.id,
      name: member.text,
      ...member,
    }));

    if (selectedBridegroomMember.value && !transformedOptions.find((m) => m.id === selectedBridegroomMember.value.id)) {
      bridegroomMemberOptions.value = [selectedBridegroomMember.value, ...transformedOptions];
    } else {
      bridegroomMemberOptions.value = transformedOptions;
    }
  } catch (error) {
    console.error('Error searching members:', error);
    if (selectedBridegroomMember.value) {
      bridegroomMemberOptions.value = [selectedBridegroomMember.value];
    }
  }
}

// Fetch members for bride
async function searchBrideMembers(query: string) {
  if (!query || query.length < 2) {
    if (selectedBrideMember.value) {
      brideMemberOptions.value = [selectedBrideMember.value];
    }
    return;
  }

  try {
    const response = await fetch(route('member.search-members', { query, limit: 20 }));
    const data = await response.json();

    const transformedOptions = data.map((member: any) => ({
      id: member.id,
      name: member.text,
      ...member,
    }));

    if (selectedBrideMember.value && !transformedOptions.find((m) => m.id === selectedBrideMember.value.id)) {
      brideMemberOptions.value = [selectedBrideMember.value, ...transformedOptions];
    } else {
      brideMemberOptions.value = transformedOptions;
    }
  } catch (error) {
    console.error('Error searching members:', error);
    if (selectedBrideMember.value) {
      brideMemberOptions.value = [selectedBrideMember.value];
    }
  }
}

// Watch for bridegroom member selection
watch(
  () => form.bridegroom_member_id,
  (newId) => {
    if (newId) {
      const member = bridegroomMemberOptions.value.find((m) => m.id === newId);
      if (member) {
        selectedBridegroomMember.value = member;
      }
    } else {
      selectedBridegroomMember.value = null;
    }
  },
);

// Watch for bride member selection
watch(
  () => form.bride_member_id,
  (newId) => {
    if (newId) {
      const member = brideMemberOptions.value.find((m) => m.id === newId);
      if (member) {
        selectedBrideMember.value = member;
      }
    } else {
      selectedBrideMember.value = null;
    }
  },
);

function submitForm() {
  form.post(route('marriage-records.store'), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Marriage record created successfully');
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
    },
  });
}
</script>

<template>
  <AppLayout>
    <Head title="Marriage Archive Certificate" />

    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link :href="route('archive.marriage.index')">
            <Button variant="outline" size="sm">
              <ArrowLeft class="mr-2 size-4" />
              Back to List
            </Button>
          </Link>
          <div>
            <h1 class="text-3xl font-bold">Marriage Archive Certificate</h1>
            <p class="text-muted-foreground">
              {{ mode === 'create' ? 'Create marriage record from archive' : 'View marriage record' }}
            </p>
          </div>
        </div>
      </div>

      <!-- CREATE MODE: Side-by-side PDF and Form -->
      <div v-if="mode === 'create'" class="grid gap-6 lg:grid-cols-2">
        <!-- Left Column: PDF Viewer (Sticky) -->
        <div class="lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)] lg:overflow-hidden">
          <Card class="flex h-full flex-col">
            <CardHeader class="flex-none">
              <div class="flex items-center justify-between">
                <CardTitle>Certificate PDF</CardTitle>
                <Button variant="ghost" size="sm" @click="showPdf = !showPdf">
                  <Eye v-if="!showPdf" class="size-4" />
                  <EyeOff v-else class="size-4" />
                </Button>
              </div>
              <CardDescription> Reg {{ certificate.reg_year }} / {{ certificate.reg_no }} </CardDescription>
            </CardHeader>
            <CardContent v-if="showPdf" class="min-h-0 flex-1 p-0">
              <iframe v-if="certificate.view_url" :src="certificate.view_url" class="h-full w-full" frameborder="0" />
              <div v-else class="flex h-full w-full items-center justify-center">
                <div class="p-8 text-center">
                  <p class="text-muted-foreground mb-2 text-lg font-semibold">PDF Not Available</p>
                  <p class="text-muted-foreground text-sm">The certificate PDF has not been uploaded yet.</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Right Column: Marriage Record Form (Scrollable) -->
        <div class="lg:h-[calc(100vh-2rem)] lg:overflow-y-auto">
          <form @submit.prevent="submitForm">
            <!-- Certificate Info Card -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Archive Certificate Information</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label class="text-muted-foreground text-sm font-medium">Marriage Date</Label>
                    <p class="font-medium">{{ marriageDate }}</p>
                  </div>
                  <div>
                    <Label class="text-muted-foreground text-sm font-medium">Registration</Label>
                    <p class="font-medium">{{ certificate.reg_year }} / {{ certificate.reg_no }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Marriage Details -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Marriage Details</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="marriage_date">Marriage Date</Label>
                    <Input id="marriage_date" v-model="form.marriage_date" type="date" />
                  </div>
                  <div class="space-y-2">
                    <Label for="marriage_reg_no">Marriage Registration No</Label>
                    <Input id="marriage_reg_no" v-model="form.marriage_reg_no" />
                  </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="year_of_marriage">Year of Marriage</Label>
                    <Input id="year_of_marriage" v-model="form.year_of_marriage" placeholder="Enter year of marriage" />
                  </div>
                </div>
                <div class="space-y-2">
                  <Label for="parish_of_marriage">Parish of Marriage</Label>
                  <Input id="parish_of_marriage" v-model="form.parish_of_marriage" />
                </div>
                <div class="space-y-2">
                  <Label for="minister_name">Minister Name</Label>
                  <Input id="minister_name" v-model="form.minister_name" />
                </div>
              </CardContent>
            </Card>

            <!-- Bridegroom Information -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Bridegroom Information</CardTitle>
                <CardDescription>Member selection is optional</CardDescription>
              </CardHeader>
              <CardContent class="space-y-6">
                <div class="space-y-2">
                  <Label>Bridegroom Member (Optional)</Label>
                  <SearchDropdown
                    v-model="form.bridegroom_member_id"
                    :options="bridegroomMemberOptions"
                    placeholder="Search for bridegroom member (optional)..."
                    @search="searchBridegroomMembers"
                  />
                  <p class="text-muted-foreground text-xs">Leave empty if bridegroom is not a parish member</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bridegroom_name">Bridegroom Name *</Label>
                    <Input id="bridegroom_name" v-model="form.bridegroom_name" required />
                  </div>
                  <div class="space-y-2">
                    <Label for="bridegroom_surname">Bridegroom Surname *</Label>
                    <Input id="bridegroom_surname" v-model="form.bridegroom_surname" required />
                  </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bridegroom_dob">Date of Birth</Label>
                    <Input id="bridegroom_dob" v-model="form.bridegroom_dob" type="date" />
                  </div>
                  <div class="space-y-2">
                    <Label for="bridegroom_nationality">Nationality</Label>
                    <Input id="bridegroom_nationality" v-model="form.bridegroom_nationality" />
                  </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bridegroom_profession">Profession</Label>
                    <Input id="bridegroom_profession" v-model="form.bridegroom_profession" />
                  </div>
                  <div class="space-y-2">
                    <Label for="bridegroom_status">Status</Label>
                    <Input id="bridegroom_status" v-model="form.bridegroom_status" placeholder="e.g., Bachelor, Widower" />
                  </div>
                </div>

                <div class="space-y-2">
                  <Label for="bridegroom_residence">Residence</Label>
                  <Input id="bridegroom_residence" v-model="form.bridegroom_residence" />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bridegroom_father_name">Father's Name</Label>
                    <Input id="bridegroom_father_name" v-model="form.bridegroom_father_name" />
                  </div>
                  <div class="space-y-2">
                    <Label for="bridegroom_mother_name">Mother's Name</Label>
                    <Input id="bridegroom_mother_name" v-model="form.bridegroom_mother_name" />
                  </div>
                </div>

                <div class="space-y-2">
                  <Label for="bridegroom_if_widower_whose">If Widower, Whose</Label>
                  <Input id="bridegroom_if_widower_whose" v-model="form.bridegroom_if_widower_whose" />
                </div>
              </CardContent>
            </Card>

            <!-- Bride Information -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Bride Information</CardTitle>
                <CardDescription>Member selection is optional</CardDescription>
              </CardHeader>
              <CardContent class="space-y-6">
                <div class="space-y-2">
                  <Label>Bride Member (Optional)</Label>
                  <SearchDropdown
                    v-model="form.bride_member_id"
                    :options="brideMemberOptions"
                    placeholder="Search for bride member (optional)..."
                    @search="searchBrideMembers"
                  />
                  <p class="text-muted-foreground text-xs">Leave empty if bride is not a parish member</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bride_name">Bride Name *</Label>
                    <Input id="bride_name" v-model="form.bride_name" required />
                  </div>
                  <div class="space-y-2">
                    <Label for="bride_surname">Bride Surname *</Label>
                    <Input id="bride_surname" v-model="form.bride_surname" required />
                  </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bride_dob">Date of Birth</Label>
                    <Input id="bride_dob" v-model="form.bride_dob" type="date" />
                  </div>
                  <div class="space-y-2">
                    <Label for="bride_nationality">Nationality</Label>
                    <Input id="bride_nationality" v-model="form.bride_nationality" />
                  </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bride_profession">Profession</Label>
                    <Input id="bride_profession" v-model="form.bride_profession" />
                  </div>
                  <div class="space-y-2">
                    <Label for="bride_status">Status</Label>
                    <Input id="bride_status" v-model="form.bride_status" placeholder="e.g., Spinster, Widow" />
                  </div>
                </div>

                <div class="space-y-2">
                  <Label for="bride_residence">Residence</Label>
                  <Input id="bride_residence" v-model="form.bride_residence" />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="bride_father_name">Father's Name</Label>
                    <Input id="bride_father_name" v-model="form.bride_father_name" />
                  </div>
                  <div class="space-y-2">
                    <Label for="bride_mother_name">Mother's Name</Label>
                    <Input id="bride_mother_name" v-model="form.bride_mother_name" />
                  </div>
                </div>

                <div class="space-y-2">
                  <Label for="bride_if_widow_whose">If Widow, Whose</Label>
                  <Input id="bride_if_widow_whose" v-model="form.bride_if_widow_whose" />
                </div>
              </CardContent>
            </Card>

            <!-- Witnesses -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Witnesses</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="first_witness_name">First Witness Name</Label>
                    <Input id="first_witness_name" v-model="form.first_witness_name" />
                  </div>
                  <div class="space-y-2">
                    <Label for="first_witness_residence">First Witness Residence</Label>
                    <Input id="first_witness_residence" v-model="form.first_witness_residence" />
                  </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-2">
                    <Label for="second_witness_name">Second Witness Name</Label>
                    <Input id="second_witness_name" v-model="form.second_witness_name" />
                  </div>
                  <div class="space-y-2">
                    <Label for="second_witness_residence">Second Witness Residence</Label>
                    <Input id="second_witness_residence" v-model="form.second_witness_residence" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Remarks -->
            <Card class="mb-6">
              <CardHeader>
                <CardTitle>Remarks</CardTitle>
              </CardHeader>
              <CardContent>
                <Textarea id="marriage_remarks" v-model="form.marriage_remarks" :rows="3" />
              </CardContent>
            </Card>

            <!-- Submit Button -->
            <div class="flex justify-end gap-2">
              <Button type="submit" :disabled="form.processing">
                <Save class="mr-2 size-4" />
                Create Marriage Record
              </Button>
            </div>
          </form>
        </div>
      </div>

      <!-- VIEW MODE: Marriage Record Details -->
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
                <Label class="text-muted-foreground text-sm font-medium">Marriage Date</Label>
                <p class="font-medium">{{ marriageDate }}</p>
              </div>
              <div>
                <Label class="text-muted-foreground text-sm font-medium">Registration Year</Label>
                <p class="font-medium">{{ certificate.reg_year }}</p>
              </div>
              <div>
                <Label class="text-muted-foreground text-sm font-medium">Registration No</Label>
                <p class="font-medium">{{ certificate.reg_no }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Marriage Record Card -->
        <Card v-if="marriageRecord">
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>Marriage Record</CardTitle>
                <CardDescription>
                  {{ marriageRecord.bridegroom_name }} {{ marriageRecord.bridegroom_surname }} &amp; {{ marriageRecord.bride_name }}
                  {{ marriageRecord.bride_surname }}
                </CardDescription>
              </div>
              <div class="flex gap-2">
                <Link :href="route('marriage-records.download-pdf', marriageRecord.id)">
                  <Button variant="outline" size="sm">
                    <Download class="mr-2 size-4" />
                    Download Certificate
                  </Button>
                </Link>
                <Link :href="route('marriage-records.edit', marriageRecord.id)">
                  <Button variant="outline" size="sm">
                    <Edit class="mr-2 size-4" />
                    Edit
                  </Button>
                </Link>
              </div>
            </div>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Marriage Details -->
            <div>
              <h3 class="mb-3 text-lg font-semibold">Marriage Details</h3>
              <div class="grid gap-4 md:grid-cols-3">
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Marriage Date</Label>
                  <p class="font-medium">{{ marriageRecord.marriage_date || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Registration No</Label>
                  <p class="font-medium">{{ marriageRecord.marriage_reg_no || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Parish</Label>
                  <p class="font-medium">{{ marriageRecord.parish_of_marriage || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Year of Marriage</Label>
                  <p class="font-medium">{{ marriageRecord.year_of_marriage || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Bridegroom -->
            <div>
              <h3 class="mb-3 text-lg font-semibold">Bridegroom</h3>
              <div class="grid gap-4 md:grid-cols-3">
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Name</Label>
                  <p class="font-medium">{{ marriageRecord.bridegroom_name }} {{ marriageRecord.bridegroom_surname }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Nationality</Label>
                  <p class="font-medium">{{ marriageRecord.bridegroom_nationality || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Profession</Label>
                  <p class="font-medium">{{ marriageRecord.bridegroom_profession || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Bride -->
            <div>
              <h3 class="mb-3 text-lg font-semibold">Bride</h3>
              <div class="grid gap-4 md:grid-cols-3">
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Name</Label>
                  <p class="font-medium">{{ marriageRecord.bride_name }} {{ marriageRecord.bride_surname }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Nationality</Label>
                  <p class="font-medium">{{ marriageRecord.bride_nationality || 'N/A' }}</p>
                </div>
                <div>
                  <Label class="text-muted-foreground text-sm font-medium">Profession</Label>
                  <p class="font-medium">{{ marriageRecord.bride_profession || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Remarks -->
            <div v-if="marriageRecord.marriage_remarks">
              <h3 class="mb-3 text-lg font-semibold">Remarks</h3>
              <p class="text-sm">{{ marriageRecord.marriage_remarks }}</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
