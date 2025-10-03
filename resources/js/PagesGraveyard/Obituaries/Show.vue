<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Copy, CreditCard, Edit, Eye, Key, Lock, QrCode, RefreshCw, Settings, Unlock, UserPlus, UserX } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type PaymentStatus = 'pending' | 'completed' | 'failed';

interface ObituaryPlan {
  id: number;
  name: string;
  description: string;
  cost: number;
  formatted_cost: string;
  duration_in_days: number;
  formatted_duration: string;
  is_active: boolean;
  sort_order: number;
}

interface ObituaryPage {
  id: number;
  uuid: string;
  // service_type: 'basic' | 'premium';
  obituary_plan_id?: number;
  obituary_plan?: ObituaryPlan;
  expires_at?: string;
  is_public: boolean;
  view_count: number;
  qr_scan_count: number;
  profile_image?: string;
  gallery_images?: string[];
  audio_message?: string;
  biography?: string;
  favorite_memory?: string;
  achievements?: string;
  hobbies_interests?: string;
  notes?: string;
  theme_color?: string;
  background_style?: string;
  allow_condolences: boolean;
  allow_memory_sharing: boolean;
  created_at: string;
  permanent_grave_booking?: {
    id: number;
    booking_reference: string;
    valid_member: {
      first_name: string;
      last_name: string;
    };
  };
  temporary_grave_booking?: {
    id: number;
    booking_reference: string;
    dead_first_name: string;
    dead_last_name: string;
  };
  payments?: {
    id: number;
    payment_status: PaymentStatus;
    amount: number;
    payment_reference: string;
    obituary_plan_id?: number;
    obituary_plan?: ObituaryPlan;
  }[];
  obituary_manager?: {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    last_login_at?: string;
    access_granted_at: string;
    access_granted_by: number;
  };
}

interface PaymentMethod {
  id: number;
  name: string;
  description?: string;
}

interface Props {
  obituary: ObituaryPage;
  stats?: any;
  recommendations?: any;
  shareLinks?: any;
  canEdit?: boolean;
  paymentMethods?: PaymentMethod[];
}

const props = defineProps<Props>();
const page = usePage();

// Flash messages
const flashMessage = computed(() => page.props.flash as any);

// Get deceased person's name
const deceasedName = computed(() => {
  if (props.obituary.permanent_grave_booking) {
    const member = props.obituary.permanent_grave_booking.valid_member;
    return `${member.first_name} ${member.last_name}`;
  }
  if (props.obituary.temporary_grave_booking) {
    const booking = props.obituary.temporary_grave_booking;
    return `${booking.dead_first_name} ${booking.dead_last_name}`;
  }
  return 'Unknown';
});

// Get booking reference
const bookingReference = computed(() => {
  return props.obituary.permanent_grave_booking?.booking_reference || props.obituary.temporary_grave_booking?.booking_reference || 'N/A';
});

// Payment status
const pendingPayment = computed(() => {
  return props.obituary.payments?.find((p) => p.payment_status === 'pending');
});

const isPaymentCompleted = computed(() => {
  return props.obituary.payments?.some((p) => p.payment_status === 'completed');
});

// Public URL for sharing
const publicUrl = computed(() => `${window.location.origin}/obituary/${props.obituary.uuid}`);

// Preview URL for admin viewing (bypasses access restrictions)
const previewUrl = computed(() => `${window.location.origin}/obituary/${props.obituary.uuid}/preview`);

// Payment form
const paymentForm = useForm({
  payment_method_id: props.paymentMethods?.length ? String(props.paymentMethods[0].id) : '',
  amount: 0,
  notes: '',
});

const showPaymentDialog = ref(false);
const showQrDialog = ref(false);
const isGeneratingQr = ref(false);

// QR generation form
const qrForm = useForm({
  size: 300,
  margin: 2,
  color: {
    r: 0,
    g: 0,
    b: 0,
  },
  background_color: {
    r: 255,
    g: 255,
    b: 255,
  },
});

const processPayment = () => {
  if (pendingPayment.value) {
    paymentForm.amount = pendingPayment.value.amount;
    showPaymentDialog.value = true;
  }
};

const submitPayment = () => {
  const formData = {
    ...paymentForm.data(),
    payment_method_id: parseInt(paymentForm.payment_method_id as string),
  };

  paymentForm
    .transform(() => formData)
    .post(`/graveyard/obituaries/${props.obituary.uuid}/payment`, {
      onSuccess: () => {
        showPaymentDialog.value = false;
        paymentForm.reset();
      },
    });
};

const copyPublicLink = async () => {
  try {
    await navigator.clipboard.writeText(publicUrl.value);
    // You could add a toast notification here
  } catch (err) {
    console.error('Failed to copy link: ', err);
    // Fallback method for older browsers
    const textArea = document.createElement('textarea');
    textArea.value = publicUrl.value;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
      document.execCommand('copy');
    } catch (fallbackErr) {
      console.error('Fallback copy method failed: ', fallbackErr);
    }
    document.body.removeChild(textArea);
  }
};

const downloadQRCode = () => {
  window.location.href = `/graveyard/obituaries/${props.obituary.uuid}/qr-download?ts=${Date.now()}`;
};

const generateStandardQr = () => {
  isGeneratingQr.value = true;

  // Use the service class to regenerate the QR code
  router.post(
    `/graveyard/obituaries/${props.obituary.uuid}/generate-qr`,
    {},
    {
      onSuccess: () => {
        isGeneratingQr.value = false;
        // Refresh the page to show updated QR
        router.reload();
      },
      onError: () => {
        isGeneratingQr.value = false;
      },
    },
  );
};

const generateCustomQr = () => {
  qrForm.post(`/graveyard/obituaries/${props.obituary.uuid}/generate-qr`, {
    onSuccess: () => {
      showQrDialog.value = false;
      qrForm.reset();
      router.reload();
    },
  });
};

const viewPublicPage = () => {
  window.open(previewUrl.value, '_blank');
};

const goBack = () => {
  router.visit('/graveyard/obituaries');
};

const viewFullImage = (imagePath: string) => {
  const imageUrl = imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
  window.open(imageUrl, '_blank');
};

// const serviceTypeColors = {
//   basic: 'bg-blue-100 text-blue-800',
//   premium: 'bg-purple-100 text-purple-800',
// };

// Since all plans have same features, no upgrade functionality needed

const expirationInfo = computed(() => {
  if (!props.obituary.expires_at) return null;

  const expiryDate = new Date(props.obituary.expires_at);
  const now = new Date();
  const diffTime = expiryDate.getTime() - now.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  let status = 'active';
  let color = 'bg-green-100 text-green-800';
  let message = `Expires in ${diffDays} days`;

  if (diffDays < 0) {
    status = 'expired';
    color = 'bg-red-100 text-red-800';
    message = `Expired ${Math.abs(diffDays)} days ago`;
  } else if (diffDays <= 7) {
    status = 'expiring';
    color = 'bg-yellow-100 text-yellow-800';
    message = `Expires in ${diffDays} day${diffDays === 1 ? '' : 's'}`;
  }

  return {
    status,
    color,
    message,
    date: expiryDate.toLocaleDateString(),
    diffDays,
  };
});

const extendExpiration = () => {
  const days = prompt('Extend expiration by how many days?', '30');
  if (days && !isNaN(parseInt(days))) {
    router.post(`/graveyard/obituaries/${props.obituary.uuid}/extend-expiration`, {
      days: parseInt(days),
    });
  }
};

// External Member Management
const showExternalMemberDialog = ref(false);
const showResetPasswordDialog = ref(false);

const externalMemberForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const resetPasswordForm = useForm({
  password: '',
  password_confirmation: '',
});

const grantExternalAccess = () => {
  externalMemberForm.post(`/graveyard/obituaries/${props.obituary.uuid}/grant-external-access`, {
    onSuccess: (page) => {
      showExternalMemberDialog.value = false;
      externalMemberForm.reset();
      // Update the obituary data with the new external member
      const flash = page.props.flash as any;
      if (flash?.external_member) {
        props.obituary.obituary_manager = flash.external_member;
      }
    },
  });
};

const toggleExternalAccess = () => {
  const action = props.obituary.obituary_manager?.is_active ? 'disable' : 'enable';
  if (confirm(`Are you sure you want to ${action} external access?`)) {
    router.patch(
      `/graveyard/obituaries/${props.obituary.uuid}/toggle-external-access`,
      {},
      {
        onSuccess: () => {
          // Toggle the local state
          if (props.obituary.obituary_manager) {
            props.obituary.obituary_manager.is_active = !props.obituary.obituary_manager.is_active;
          }
        },
      },
    );
  }
};

const revokeExternalAccess = () => {
  if (confirm('Are you sure you want to completely revoke external access? This will delete the external member account.')) {
    router.delete(`/graveyard/obituaries/${props.obituary.uuid}/revoke-external-access`, {
      onSuccess: () => {
        // Remove the external member from local state
        props.obituary.obituary_manager = undefined;
      },
    });
  }
};

const resetExternalPassword = () => {
  resetPasswordForm.patch(`/graveyard/obituaries/${props.obituary.uuid}/reset-external-password`, {
    onSuccess: () => {
      showResetPasswordDialog.value = false;
      resetPasswordForm.reset();
    },
  });
};

const getExternalAccessUrl = computed(() => {
  // Use route helper if available, otherwise construct URL
  if (typeof window !== 'undefined') {
    return `${window.location.origin}/obituary/${props.obituary.uuid}/manage/login`;
  }
  // Fallback for SSR - construct relative URL
  return `/obituary/${props.obituary.uuid}/manage/login`;
});

const copyExternalAccessUrl = () => {
  let urlToCopy = getExternalAccessUrl.value;
  // If it's a relative URL, make it absolute
  if (urlToCopy.startsWith('/') && typeof window !== 'undefined') {
    urlToCopy = `${window.location.origin}${urlToCopy}`;
  }
  navigator.clipboard.writeText(urlToCopy);
  // You might want to show a toast message here
};

const paymentStatusColors = {
  pending: 'bg-orange-100 text-orange-800',
  completed: 'bg-green-100 text-green-800',
  failed: 'bg-red-100 text-red-800',
};
</script>

<template>
  <Head :title="`Obituary - ${deceasedName}`" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-6xl sm:px-4 lg:px-6">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <Button variant="outline" size="sm" @click="goBack">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Back
              </Button>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ deceasedName }}</h1>
                <p class="text-gray-600">Memorial Page - {{ bookingReference }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <!-- <Badge :class="serviceTypeColors[obituary.service_type]"> {{ obituary.service_type }} Service </Badge> -->
              <Badge v-if="isPaymentCompleted" class="bg-green-100 text-green-800"> Activated </Badge>
              <Badge v-if="expirationInfo" :class="expirationInfo.color">
                {{ expirationInfo.message }}
              </Badge>
              <Badge v-else-if="pendingPayment" class="bg-orange-100 text-orange-800"> Payment Pending </Badge>
            </div>
          </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="flashMessage?.success" class="mb-6 rounded-md bg-green-50 p-4">
          <div class="text-green-800">{{ flashMessage.success }}</div>
        </div>

        <!-- Payment Required Alert -->
        <Card v-if="pendingPayment" class="mb-6 border-orange-200 bg-orange-50">
          <CardContent class="pt-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-semibold text-orange-800">Payment Required</h3>
                <p class="text-orange-700">Complete the payment of ₹{{ pendingPayment.amount }} to activate this obituary page.</p>
              </div>
              <Button @click="processPayment" class="bg-orange-600 hover:bg-orange-700">
                <CreditCard class="mr-2 h-4 w-4" />
                Pay Now
              </Button>
            </div>
          </CardContent>
        </Card>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Main Content -->
          <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information -->
            <Card>
              <CardHeader>
                <CardTitle>Memorial Information</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div>
                    <p class="font-medium text-gray-900">{{ deceasedName }}</p>
                    <p class="text-sm text-gray-600">Booking: {{ bookingReference }}</p>
                    <p class="text-sm text-gray-600">Created: {{ new Date(obituary.created_at).toLocaleDateString() }}</p>
                  </div>
                  <div class="text-right">
                    <!-- <Badge :class="serviceTypeColors[obituary.service_type]" class="mb-2"> {{ obituary.service_type }} Service </Badge> -->
                    <p class="text-sm text-gray-600">Status: {{ obituary.is_public ? 'Public' : 'Private' }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Content Sections -->
            <!-- Profile Image -->
            <Card v-if="obituary.profile_image">
              <CardHeader>
                <CardTitle>Profile Photo</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="flex justify-center">
                  <img
                    :src="obituary.profile_image.startsWith('http') ? obituary.profile_image : `/storage/${obituary.profile_image}`"
                    :alt="deceasedName"
                    class="h-64 w-64 rounded-lg border-2 border-gray-200 object-cover shadow-lg"
                  />
                </div>
              </CardContent>
            </Card>

            <Card v-if="obituary.biography">
              <CardHeader>
                <CardTitle>Biography</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="whitespace-pre-line text-gray-700">{{ obituary.biography }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.favorite_memory">
              <CardHeader>
                <CardTitle>Favorite Memory</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="whitespace-pre-line text-gray-700">{{ obituary.favorite_memory }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.achievements">
              <CardHeader>
                <CardTitle>Achievements</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="whitespace-pre-line text-gray-700">{{ obituary.achievements }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.hobbies_interests">
              <CardHeader>
                <CardTitle>Hobbies & Interests</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="whitespace-pre-line text-gray-700">{{ obituary.hobbies_interests }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.notes">
              <CardHeader>
                <CardTitle>Family Notes & Messages</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="whitespace-pre-line text-gray-700">{{ obituary.notes }}</p>
              </CardContent>
            </Card>

            <!-- Gallery Images (Premium Feature) -->
            <Card v-if="obituary.gallery_images && obituary.gallery_images.length > 0">
              <CardHeader>
                <CardTitle class="flex items-center"> Gallery Photos </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                  <div
                    v-for="(image, index) in obituary.gallery_images"
                    :key="index"
                    class="group aspect-square cursor-pointer"
                    @click="viewFullImage(image)"
                  >
                    <img
                      :src="image.startsWith('http') ? image : `/storage/${image}`"
                      :alt="`Gallery photo ${index + 1}`"
                      class="h-full w-full rounded-lg border border-gray-200 object-cover shadow-md transition-shadow duration-200 hover:shadow-xl"
                    />
                  </div>
                </div>
                <p class="mt-3 text-sm text-gray-500">{{ obituary.gallery_images.length }} photos • Click to view full size</p>
              </CardContent>
            </Card>

            <!-- Audio Message (Premium Feature) -->
            <Card v-if="obituary.audio_message">
              <CardHeader>
                <CardTitle class="flex items-center"> Audio Message </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="rounded-lg bg-gray-50 p-4">
                  <audio controls class="w-full max-w-[448px]">
                    <source
                      :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`"
                      type="audio/mpeg"
                    />
                    <source
                      :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`"
                      type="audio/wav"
                    />
                    <source
                      :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`"
                      type="audio/mp4"
                    />
                    Your browser does not support the audio element.
                  </audio>
                  <p class="mt-2 text-sm text-gray-600">Memorial audio message</p>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Actions Card -->
            <Card>
              <CardHeader>
                <CardTitle>Actions</CardTitle>
              </CardHeader>
              <CardContent class="space-y-3">
                <Button @click="viewPublicPage" class="w-full" variant="outline">
                  <Eye class="mr-2 h-4 w-4" />
                  Preview Page
                </Button>

                <Button @click="copyPublicLink" class="w-full" variant="outline">
                  <Copy class="mr-2 h-4 w-4" />
                  Copy Share Link
                </Button>

                <Button @click="generateStandardQr" class="w-full" variant="outline" :disabled="isGeneratingQr">
                  <RefreshCw class="mr-2 h-4 w-4" :class="{ 'animate-spin': isGeneratingQr }" />
                  {{ isGeneratingQr ? 'Generating...' : 'Generate QR Code' }}
                </Button>

                <Button @click="downloadQRCode" class="w-full" variant="outline">
                  <QrCode class="mr-2 h-4 w-4" />
                  Download QR Code
                </Button>

                <Button @click="showQrDialog = true" class="w-full" variant="outline">
                  <Settings class="mr-2 h-4 w-4" />
                  Custom QR Code
                </Button>

                <Button v-if="canEdit" as-child class="w-full" variant="outline">
                  <Link :href="`/graveyard/obituaries/${obituary.uuid}/edit`">
                    <Edit class="mr-2 h-4 w-4" />
                    Edit Content
                  </Link>
                </Button>

                <Button
                  v-if="canEdit && expirationInfo"
                  @click="extendExpiration"
                  class="w-full"
                  :variant="expirationInfo.status === 'expired' ? 'destructive' : 'outline'"
                >
                  <RefreshCw class="mr-2 h-4 w-4" />
                  Extend Expiration
                </Button>
              </CardContent>
            </Card>

            <!-- Statistics -->
            <Card v-if="stats || obituary.view_count">
              <CardHeader>
                <CardTitle>Statistics</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Page Views:</span>
                    <span class="font-semibold">{{ obituary.view_count || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">QR Scans:</span>
                    <span class="font-semibold">{{ obituary.qr_scan_count || 0 }}</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- External Member Management -->
            <Card v-if="canEdit">
              <CardHeader>
                <CardTitle>External Access Management</CardTitle>
              </CardHeader>
              <CardContent>
                <div v-if="!obituary.obituary_manager" class="space-y-3">
                  <p class="text-sm text-gray-600">No external member access has been granted yet.</p>
                  <Button @click="showExternalMemberDialog = true" class="w-full">
                    <UserPlus class="mr-2 h-4 w-4" />
                    Grant External Access
                  </Button>
                </div>

                <div v-else class="space-y-4">
                  <!-- External Member Info -->
                  <div class="rounded bg-gray-50 p-3">
                    <div class="mb-2 flex items-center justify-between">
                      <h4 class="font-medium">{{ obituary.obituary_manager.name }}</h4>
                      <Badge :class="obituary.obituary_manager.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ obituary.obituary_manager.is_active ? 'Active' : 'Disabled' }}
                      </Badge>
                    </div>
                    <p class="text-sm text-gray-600">{{ obituary.obituary_manager.email }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                      Access granted: {{ new Date(obituary.obituary_manager.access_granted_at).toLocaleDateString() }}
                    </p>
                    <p v-if="obituary.obituary_manager.last_login_at" class="text-xs text-gray-500">
                      Last login: {{ new Date(obituary.obituary_manager.last_login_at).toLocaleDateString() }}
                    </p>
                  </div>

                  <!-- Management URL - Always show if external manager exists -->
                  <div class="rounded bg-blue-50 p-3">
                    <Label class="text-sm font-medium text-blue-800">Management URL:</Label>
                    <div class="mt-1 flex items-center space-x-2">
                      <input
                        :value="getExternalAccessUrl"
                        readonly
                        class="placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 font-mono text-xs shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:ring-1 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        style="min-height: 36px"
                        :placeholder="getExternalAccessUrl || 'Loading...'"
                      />
                      <Button @click="copyExternalAccessUrl" size="sm" variant="outline" title="Copy Management URL">
                        <Copy class="h-3 w-3" />
                      </Button>
                    </div>
                    <p class="mt-1 text-xs text-blue-600">Share this URL with the external member to access their dashboard</p>
                    <!-- Show the actual URL in case Input component has issues -->
                  </div>

                  <!-- Action Buttons -->
                  <div class="space-y-2">
                    <Button @click="toggleExternalAccess" class="w-full" :variant="obituary.obituary_manager.is_active ? 'destructive' : 'default'">
                      <component :is="obituary.obituary_manager.is_active ? Lock : Unlock" class="mr-2 h-4 w-4" />
                      {{ obituary.obituary_manager.is_active ? 'Disable Access' : 'Enable Access' }}
                    </Button>

                    <Button @click="showResetPasswordDialog = true" class="w-full" variant="outline">
                      <Key class="mr-2 h-4 w-4" />
                      Reset Password
                    </Button>

                    <Button @click="revokeExternalAccess" class="w-full" variant="outline">
                      <UserX class="mr-2 h-4 w-4" />
                      Revoke Access
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Payment Information -->
            <Card v-if="obituary.payments?.length">
              <CardHeader>
                <CardTitle>Payment History</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div v-for="payment in obituary.payments" :key="payment.id" class="flex items-center justify-between rounded bg-gray-50 p-3">
                    <div>
                      <p class="font-medium">₹{{ payment.amount }}</p>
                      <p class="text-xs text-gray-600">{{ payment.payment_reference }}</p>
                    </div>
                    <Badge :class="paymentStatusColors[payment.payment_status]">
                      {{ payment.payment_status }}
                    </Badge>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Public URL -->
            <Card>
              <CardHeader>
                <CardTitle>Public Access</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="rounded bg-gray-50 p-3">
                  <Label class="text-sm font-medium">Public URL:</Label>
                  <div class="mt-1 flex items-center space-x-2">
                    <input
                      :value="publicUrl"
                      readonly
                      class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 font-mono text-sm text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                    />
                    <Button size="sm" variant="outline" @click="copyPublicLink">
                      <Copy class="h-3 w-3" />
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>

        <!-- Payment Dialog -->
        <Dialog v-model:open="showPaymentDialog">
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Complete Payment</DialogTitle>
              <DialogDescription> Process the payment for obituary service </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitPayment" class="space-y-4">
              <div>
                <Label>Amount</Label>
                <Input v-model="paymentForm.amount" type="number" readonly />
              </div>

              <div>
                <Label>Payment Method</Label>
                <select
                  v-model="paymentForm.payment_method_id"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="">Select payment method</option>
                  <option v-for="method in paymentMethods" :key="method.id" :value="String(method.id)">
                    {{ method.name }}
                    <span v-if="method.description"> - {{ method.description }}</span>
                  </option>
                </select>
              </div>

              <div>
                <Label>Notes (Optional)</Label>
                <Input v-model="paymentForm.notes" placeholder="Payment notes..." />
              </div>

              <DialogFooter>
                <Button type="button" variant="outline" @click="showPaymentDialog = false"> Cancel </Button>
                <Button type="submit" :disabled="paymentForm.processing">
                  {{ paymentForm.processing ? 'Processing...' : 'Complete Payment' }}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>

        <!-- Custom QR Generation Dialog -->
        <Dialog v-model:open="showQrDialog">
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Generate Custom QR Code</DialogTitle>
              <DialogDescription> Create a customized QR code for this obituary page </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="generateCustomQr" class="space-y-4">
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <Label>Size (pixels)</Label>
                  <input v-model.number="qrForm.size" type="number" min="100" max="1000" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                  <Label>Margin (pixels)</Label>
                  <input v-model.number="qrForm.margin" type="number" min="0" max="10" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
              </div>

              <div>
                <Label>QR Code Color</Label>
                <div class="mb-3 flex gap-2">
                  <button
                    type="button"
                    @click="qrForm.color = { r: 0, g: 0, b: 0 }"
                    class="h-6 w-6 rounded border border-gray-300 bg-black"
                    title="Black"
                  ></button>
                  <button
                    type="button"
                    @click="qrForm.color = { r: 255, g: 0, b: 0 }"
                    class="h-6 w-6 rounded border border-gray-300 bg-red-500"
                    title="Red"
                  ></button>
                  <button
                    type="button"
                    @click="qrForm.color = { r: 0, g: 0, b: 255 }"
                    class="h-6 w-6 rounded border border-gray-300 bg-blue-500"
                    title="Blue"
                  ></button>
                  <button
                    type="button"
                    @click="qrForm.color = { r: 0, g: 128, b: 0 }"
                    class="h-6 w-6 rounded border border-gray-300 bg-green-600"
                    title="Green"
                  ></button>
                  <button
                    type="button"
                    @click="qrForm.color = { r: 128, g: 0, b: 128 }"
                    class="h-6 w-6 rounded border border-gray-300 bg-purple-600"
                    title="Purple"
                  ></button>
                </div>
                <div class="mt-2 grid grid-cols-3 gap-2">
                  <div>
                    <label class="text-xs text-gray-600">Red (0-255)</label>
                    <input
                      v-model.number="qrForm.color.r"
                      type="number"
                      min="0"
                      max="255"
                      class="w-full rounded border border-gray-300 px-2 py-1 text-sm"
                    />
                  </div>
                  <div>
                    <label class="text-xs text-gray-600">Green (0-255)</label>
                    <input
                      v-model.number="qrForm.color.g"
                      type="number"
                      min="0"
                      max="255"
                      class="w-full rounded border border-gray-300 px-2 py-1 text-sm"
                    />
                  </div>
                  <div>
                    <label class="text-xs text-gray-600">Blue (0-255)</label>
                    <input
                      v-model.number="qrForm.color.b"
                      type="number"
                      min="0"
                      max="255"
                      class="w-full rounded border border-gray-300 px-2 py-1 text-sm"
                    />
                  </div>
                </div>
                <div
                  class="mt-2 h-8 w-8 rounded border border-gray-300"
                  :style="{ backgroundColor: `rgb(${qrForm.color.r}, ${qrForm.color.g}, ${qrForm.color.b})` }"
                ></div>
              </div>

              <div>
                <Label>Background Color</Label>
                <div class="mt-2 grid grid-cols-3 gap-2">
                  <div>
                    <label class="text-xs text-gray-600">Red (0-255)</label>
                    <input
                      v-model.number="qrForm.background_color.r"
                      type="number"
                      min="0"
                      max="255"
                      class="w-full rounded border border-gray-300 px-2 py-1 text-sm"
                    />
                  </div>
                  <div>
                    <label class="text-xs text-gray-600">Green (0-255)</label>
                    <input
                      v-model.number="qrForm.background_color.g"
                      type="number"
                      min="0"
                      max="255"
                      class="w-full rounded border border-gray-300 px-2 py-1 text-sm"
                    />
                  </div>
                  <div>
                    <label class="text-xs text-gray-600">Blue (0-255)</label>
                    <input
                      v-model.number="qrForm.background_color.b"
                      type="number"
                      min="0"
                      max="255"
                      class="w-full rounded border border-gray-300 px-2 py-1 text-sm"
                    />
                  </div>
                </div>
                <div
                  class="mt-2 h-8 w-8 rounded border border-gray-300"
                  :style="{ backgroundColor: `rgb(${qrForm.background_color.r}, ${qrForm.background_color.g}, ${qrForm.background_color.b})` }"
                ></div>
              </div>

              <DialogFooter>
                <Button type="button" variant="outline" @click="showQrDialog = false"> Cancel </Button>
                <Button type="submit" :disabled="qrForm.processing">
                  {{ qrForm.processing ? 'Generating...' : 'Generate QR Code' }}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>

        <!-- Grant External Access Dialog -->
        <Dialog v-model:open="showExternalMemberDialog">
          <DialogContent class="max-w-[448px]">
            <DialogHeader>
              <DialogTitle>Grant External Access</DialogTitle>
              <DialogDescription> Create an account for a family member to manage this obituary page. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="grantExternalAccess" class="space-y-4">
              <div>
                <Label for="external_name">Full Name</Label>
                <Input
                  id="external_name"
                  v-model="externalMemberForm.name"
                  type="text"
                  placeholder="Enter full name"
                  required
                  :class="{ 'border-red-500': externalMemberForm.errors.name }"
                />
                <p v-if="externalMemberForm.errors.name" class="mt-1 text-sm text-red-600">{{ externalMemberForm.errors.name }}</p>
              </div>

              <div>
                <Label for="external_email">Email Address</Label>
                <Input
                  id="external_email"
                  v-model="externalMemberForm.email"
                  type="email"
                  placeholder="Enter email address"
                  required
                  :class="{ 'border-red-500': externalMemberForm.errors.email }"
                />
                <p v-if="externalMemberForm.errors.email" class="mt-1 text-sm text-red-600">{{ externalMemberForm.errors.email }}</p>
              </div>

              <div>
                <Label for="external_password">Password</Label>
                <Input
                  id="external_password"
                  v-model="externalMemberForm.password"
                  type="password"
                  placeholder="Enter password (minimum 8 characters)"
                  required
                  :class="{ 'border-red-500': externalMemberForm.errors.password }"
                />
                <p v-if="externalMemberForm.errors.password" class="mt-1 text-sm text-red-600">{{ externalMemberForm.errors.password }}</p>
              </div>

              <div>
                <Label for="external_password_confirmation">Confirm Password</Label>
                <Input
                  id="external_password_confirmation"
                  v-model="externalMemberForm.password_confirmation"
                  type="password"
                  placeholder="Confirm password"
                  required
                  :class="{ 'border-red-500': externalMemberForm.errors.password_confirmation }"
                />
                <p v-if="externalMemberForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                  {{ externalMemberForm.errors.password_confirmation }}
                </p>
              </div>

              <DialogFooter>
                <Button type="button" variant="outline" @click="showExternalMemberDialog = false"> Cancel </Button>
                <Button type="submit" :disabled="externalMemberForm.processing">
                  {{ externalMemberForm.processing ? 'Creating...' : 'Grant Access' }}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>

        <!-- Reset Password Dialog -->
        <Dialog v-model:open="showResetPasswordDialog">
          <DialogContent class="max-w-[448px]">
            <DialogHeader>
              <DialogTitle>Reset External Member Password</DialogTitle>
              <DialogDescription> Set a new password for {{ obituary.obituary_manager?.name }}. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="resetExternalPassword" class="space-y-4">
              <div>
                <Label for="reset_password">New Password</Label>
                <Input
                  id="reset_password"
                  v-model="resetPasswordForm.password"
                  type="password"
                  placeholder="Enter new password (minimum 8 characters)"
                  required
                  :class="{ 'border-red-500': resetPasswordForm.errors.password }"
                />
                <p v-if="resetPasswordForm.errors.password" class="mt-1 text-sm text-red-600">{{ resetPasswordForm.errors.password }}</p>
              </div>

              <div>
                <Label for="reset_password_confirmation">Confirm New Password</Label>
                <Input
                  id="reset_password_confirmation"
                  v-model="resetPasswordForm.password_confirmation"
                  type="password"
                  placeholder="Confirm new password"
                  required
                  :class="{ 'border-red-500': resetPasswordForm.errors.password_confirmation }"
                />
                <p v-if="resetPasswordForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                  {{ resetPasswordForm.errors.password_confirmation }}
                </p>
              </div>

              <DialogFooter>
                <Button type="button" variant="outline" @click="showResetPasswordDialog = false"> Cancel </Button>
                <Button type="submit" :disabled="resetPasswordForm.processing">
                  {{ resetPasswordForm.processing ? 'Resetting...' : 'Reset Password' }}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  </AppLayout>
</template>
