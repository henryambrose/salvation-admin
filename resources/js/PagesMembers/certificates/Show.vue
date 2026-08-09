<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Download, FileText, RotateCcw, User, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import { useCertificateState } from '@/composables/useCertificateState';

interface Certificate {
  id: number;
  certificate_number: string;
  certificate_type: string;
  member: {
    id: number;
    first_name: string;
    middle_name?: string;
    last_name: string;
    member_no: string;
    family_no: string;
    date_of_birth?: string;
    baptism_date?: string;
    confirmation_date?: string;
    marriage_date?: string;
    baptism_reg_no?: string;
    confirmation_minister?: string;
    marriage_reg_no?: string;
    community?: { name: string };
    parish?: { name: string };
  };
  template?: {
    id: number;
    name: string;
    description?: string;
  };
  issued_date: string;
  issuer?: {
    id: number;
    name: string;
  };
  file_path?: string;
  download_count: number;
  additional_data?: Record<string, any>;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  certificate: Certificate;
  canReprintCertificates: boolean;
  canGenerateCertificates: boolean;
}>();

const { getState } = useCertificateState();

const memberFullName = computed(() => {
  const { first_name, middle_name, last_name } = props.certificate.member;
  return `${first_name} ${middle_name || ''} ${last_name}`.trim();
});

const certificateTypeBadgeClass = computed(() => {
  const classes = {
    baptism: 'bg-blue-100 text-blue-800',
    confirmation: 'bg-green-100 text-green-800',
    marriage: 'bg-pink-100 text-pink-800',
    membership: 'bg-purple-100 text-purple-800',
    death: 'bg-gray-100 text-gray-800',
  };
  return classes[props.certificate.certificate_type as keyof typeof classes] || 'bg-gray-100 text-gray-800';
});

const relevantMemberData = computed(() => {
  const member = props.certificate.member;
  const type = props.certificate.certificate_type;

  switch (type) {
    case 'baptism':
      return {
        date: member.baptism_date,
        regNo: member.baptism_reg_no,
        regNoLabel: 'Reg. No.',
        label: 'Baptism',
      };
    case 'confirmation':
      return {
        date: member.confirmation_date,
        regNo: member.confirmation_minister,
        regNoLabel: 'Minister',
        label: 'Confirmation',
      };
    case 'marriage':
      return {
        date: member.marriage_date,
        regNo: member.marriage_reg_no,
        regNoLabel: 'Reg. No.',
        label: 'Marriage',
      };
    case 'death':
      return {
        date: props.certificate.additional_data?.death_date,
        regNo: props.certificate.additional_data?.burial_reg_no,
        regNoLabel: 'Reg. No.',
        label: 'Death',
      };
    default:
      return null;
  }
});


const breadcrumbs = computed(() => {
  const savedFilters = getState();
  return [
    {
      title: 'Certificates',
      href: '/certificates?' + new URLSearchParams(savedFilters).toString()
    },
    {
      title: `Certificate #${props.certificate.certificate_number}`,
      href: `/certificates/${props.certificate.id}`
    },
  ];
});

function downloadCertificate() {
  if (!props.certificate.file_path) {
    console.log('Certificate PDF not available');
    return;
  }
  window.open(`/certificates/${props.certificate.id}/download`, '_blank');
}

function generatePDF() {
  router.post(
    `/certificates/${props.certificate.id}/generate-pdf`,
    {},
    {
      preserveState: true,
      onSuccess: () => {
        // Reload the page to show updated certificate with file path
        router.reload({ only: ['certificate'] });
      },
      onError: (errors) => {
        console.error('Failed to generate PDF:', errors);
      },
    },
  );
}

function reprintCertificate() {
  router.post(
    `/certificates/${props.certificate.id}/reprint`,
    {},
    {
      preserveState: true,
      onSuccess: () => {
        // Reload the page to show updated download count
        router.reload({ only: ['certificate'] });
      },
    },
  );
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="`Certificate #${certificate.certificate_number}`" />

    <div class="mx-auto max-w-4xl">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <div class="mb-2 flex items-center gap-3">
            <Button variant="outline" size="sm" @click="router.get('/certificates')" class="flex items-center gap-2">
              <ArrowLeft class="h-4 w-4" />
              Back to Certificates
            </Button>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">Certificate #{{ certificate.certificate_number }}</h1>
          <p class="mt-1 text-gray-600">Certificate details and information</p>
        </div>

        <div class="flex items-center gap-3">
          <Button
            v-if="certificate.file_path"
            @click="downloadCertificate"
            class="flex items-center gap-2 bg-blue-600 text-white hover:bg-blue-700"
          >
            <Download class="h-4 w-4" />
            Download PDF
          </Button>

          <Button
            v-else
            @click="generatePDF"
            class="flex items-center gap-2 bg-green-600 text-white hover:bg-green-700"
          >
            <FileText class="h-4 w-4" />
            Generate PDF
          </Button>

          <Button v-if="canReprintCertificates" @click="reprintCertificate" variant="outline" class="flex items-center gap-2">
            <RotateCcw class="h-4 w-4" />
            Reprint
          </Button>
        </div>
      </div>

      <!-- Main Certificate Information -->
      <div class="space-y-6">
          <!-- Certificate Overview -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <FileText class="h-5 w-5" />
                Certificate Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Certificate Number</label>
                  <p class="rounded bg-gray-50 p-2 font-mono text-lg">{{ certificate.certificate_number }}</p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                  <Badge :class="certificateTypeBadgeClass" class="text-sm capitalize">
                    {{ certificate.certificate_type }}
                  </Badge>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Issued Date</label>
                  <p class="text-gray-900">{{ formatDate(certificate.issued_date) }}</p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Issued By</label>
                  <p class="text-gray-900">{{ certificate.issuer?.name || 'System' }}</p>
                </div>
              </div>

              <div v-if="certificate.template">
                <label class="mb-1 block text-sm font-medium text-gray-700">Template Used</label>
                <p class="text-gray-900">{{ certificate.template.name }}</p>
                <p v-if="certificate.template.description" class="mt-1 text-sm text-gray-600">
                  {{ certificate.template.description }}
                </p>
              </div>
            </CardContent>
          </Card>

          <!-- Member Information -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <User class="h-5 w-5" />
                Member Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ memberFullName }}</h3>
                <p class="text-gray-600">Member #{{ certificate.member.member_no }} | Family #{{ certificate.member.family_no }}</p>
              </div>

              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div v-if="certificate.member.date_of_birth">
                  <label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label>
                  <p class="text-gray-900">{{ formatDate(certificate.member.date_of_birth) }}</p>
                </div>

                <div v-if="certificate.member.community">
                  <label class="mb-1 block text-sm font-medium text-gray-700">Community</label>
                  <p class="text-gray-900">{{ certificate.member.community.name }}</p>
                </div>

                <div v-if="certificate.member.parish">
                  <label class="mb-1 block text-sm font-medium text-gray-700">Parish</label>
                  <p class="text-gray-900">{{ certificate.member.parish.name }}</p>
                </div>

                <!-- Relevant Sacrament Data -->
                <div v-if="relevantMemberData && relevantMemberData.date">
                  <label class="mb-1 block text-sm font-medium text-gray-700">{{ relevantMemberData.label }} Date</label>
                  <p class="text-gray-900">{{ formatDate(relevantMemberData.date) }}</p>
                </div>

                <div v-if="relevantMemberData && relevantMemberData.regNo">
                  <label class="mb-1 block text-sm font-medium text-gray-700">{{ relevantMemberData.label }} {{ relevantMemberData.regNoLabel }}</label>
                  <p class="text-gray-900">{{ relevantMemberData.regNo }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Additional Certificate Data -->
          <Card v-if="certificate.additional_data && Object.keys(certificate.additional_data).length > 0">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Users class="h-5 w-5" />
                Additional Information
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div v-for="(value, key) in certificate.additional_data" :key="key" v-show="value">
                  <label class="mb-1 block text-sm font-medium text-gray-700 capitalize">
                    {{ key.replace(/_/g, ' ') }}
                  </label>
                  <p class="text-gray-900">{{ value }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
      </div>
    </div>
  </AppLayout>
</template>
