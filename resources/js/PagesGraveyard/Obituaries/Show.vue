<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Copy, CreditCard, Download, Edit, ExternalLink, Eye, QrCode, Share2, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type PaymentStatus = 'pending' | 'completed' | 'failed';

interface ObituaryPage {
  id: number;
  uuid: string;
  service_type: 'basic' | 'premium';
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
  }[];
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
  return props.obituary.permanent_grave_booking?.booking_reference || 
         props.obituary.temporary_grave_booking?.booking_reference || 
         'N/A';
});

// Payment status
const pendingPayment = computed(() => {
  return props.obituary.payments?.find(p => p.payment_status === 'pending');
});

const isPaymentCompleted = computed(() => {
  return props.obituary.payments?.some(p => p.payment_status === 'completed');
});

// Public URL
const publicUrl = computed(() => `${window.location.origin}/obituary/${props.obituary.uuid}`);

// Payment form
const paymentForm = useForm({
  payment_method_id: props.paymentMethods?.length ? String(props.paymentMethods[0].id) : '',
  amount: 0,
  notes: '',
});

const showPaymentDialog = ref(false);

const processPayment = () => {
  if (pendingPayment.value) {
    paymentForm.amount = pendingPayment.value.amount;
    showPaymentDialog.value = true;
  }
};

const submitPayment = () => {
  const formData = {
    ...paymentForm.data(),
    payment_method_id: parseInt(paymentForm.payment_method_id as string)
  };
  
  paymentForm.transform(() => formData).post(`/graveyard/obituaries/${props.obituary.uuid}/payment`, {
    onSuccess: () => {
      showPaymentDialog.value = false;
      paymentForm.reset();
    },
  });
};

const copyPublicLink = () => {
  navigator.clipboard.writeText(publicUrl.value);
  // Could add toast notification
};

const downloadQRCode = () => {
  window.open(`/obituary/${props.obituary.uuid}/qr-download`, '_blank');
};

const viewPublicPage = () => {
  window.open(publicUrl.value, '_blank');
};

const goBack = () => {
  router.visit('/graveyard/obituaries');
};

const viewFullImage = (imagePath: string) => {
  const imageUrl = imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
  window.open(imageUrl, '_blank');
};

const serviceTypeColors = {
  basic: 'bg-blue-100 text-blue-800',
  premium: 'bg-purple-100 text-purple-800',
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
              <Badge :class="serviceTypeColors[obituary.service_type]">
                {{ obituary.service_type }} Service
              </Badge>
              <Badge v-if="isPaymentCompleted" class="bg-green-100 text-green-800">
                Activated
              </Badge>
              <Badge v-else-if="pendingPayment" class="bg-orange-100 text-orange-800">
                Payment Pending
              </Badge>
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
                <p class="text-orange-700">
                  Complete the payment of ₹{{ pendingPayment.amount }} to activate this obituary page.
                </p>
              </div>
              <Button @click="processPayment" class="bg-orange-600 hover:bg-orange-700">
                <CreditCard class="mr-2 h-4 w-4" />
                Pay Now
              </Button>
            </div>
          </CardContent>
        </Card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <Card>
              <CardHeader>
                <CardTitle>Memorial Information</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="font-medium text-gray-900">{{ deceasedName }}</p>
                    <p class="text-sm text-gray-600">Booking: {{ bookingReference }}</p>
                    <p class="text-sm text-gray-600">Created: {{ new Date(obituary.created_at).toLocaleDateString() }}</p>
                  </div>
                  <div class="text-right">
                    <Badge :class="serviceTypeColors[obituary.service_type]" class="mb-2">
                      {{ obituary.service_type }} Service
                    </Badge>
                    <p class="text-sm text-gray-600">
                      Status: {{ obituary.is_public ? 'Public' : 'Private' }}
                    </p>
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
                    class="w-64 h-64 object-cover rounded-lg shadow-lg border-2 border-gray-200"
                  />
                </div>
              </CardContent>
            </Card>

            <Card v-if="obituary.biography">
              <CardHeader>
                <CardTitle>Biography</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="text-gray-700 whitespace-pre-line">{{ obituary.biography }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.favorite_memory">
              <CardHeader>
                <CardTitle>Favorite Memory</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="text-gray-700 whitespace-pre-line">{{ obituary.favorite_memory }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.achievements">
              <CardHeader>
                <CardTitle>Achievements</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="text-gray-700 whitespace-pre-line">{{ obituary.achievements }}</p>
              </CardContent>
            </Card>

            <Card v-if="obituary.hobbies_interests">
              <CardHeader>
                <CardTitle>Hobbies & Interests</CardTitle>
              </CardHeader>
              <CardContent>
                <p class="text-gray-700 whitespace-pre-line">{{ obituary.hobbies_interests }}</p>
              </CardContent>
            </Card>

            <!-- Gallery Images (Premium Feature) -->
            <Card v-if="obituary.gallery_images && obituary.gallery_images.length > 0">
              <CardHeader>
                <CardTitle class="flex items-center">
                  Gallery Photos
                  <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Premium</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div 
                    v-for="(image, index) in obituary.gallery_images" 
                    :key="index"
                    class="aspect-square group cursor-pointer"
                    @click="viewFullImage(image)"
                  >
                    <img 
                      :src="image.startsWith('http') ? image : `/storage/${image}`"
                      :alt="`Gallery photo ${index + 1}`"
                      class="w-full h-full object-cover rounded-lg shadow-md hover:shadow-xl transition-shadow duration-200 border border-gray-200"
                    />
                  </div>
                </div>
                <p class="text-sm text-gray-500 mt-3">{{ obituary.gallery_images.length }} photos • Click to view full size</p>
              </CardContent>
            </Card>

            <!-- Audio Message (Premium Feature) -->
            <Card v-if="obituary.audio_message">
              <CardHeader>
                <CardTitle class="flex items-center">
                  Audio Message
                  <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Premium</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="bg-gray-50 rounded-lg p-4">
                  <audio controls class="w-full max-w-md">
                    <source :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`" type="audio/mpeg">
                    <source :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`" type="audio/wav">
                    <source :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`" type="audio/mp4">
                    Your browser does not support the audio element.
                  </audio>
                  <p class="text-sm text-gray-600 mt-2">Memorial audio message</p>
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
                  View Public Page
                </Button>
                
                <Button @click="copyPublicLink" class="w-full" variant="outline">
                  <Copy class="mr-2 h-4 w-4" />
                  Copy Share Link
                </Button>
                
                <Button @click="downloadQRCode" class="w-full" variant="outline">
                  <QrCode class="mr-2 h-4 w-4" />
                  Download QR Code
                </Button>
                
                <Button v-if="canEdit" as-child class="w-full" variant="outline">
                  <Link :href="`/graveyard/obituaries/${obituary.uuid}/edit`">
                    <Edit class="mr-2 h-4 w-4" />
                    Edit Content
                  </Link>
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

            <!-- Payment Information -->
            <Card v-if="obituary.payments?.length">
              <CardHeader>
                <CardTitle>Payment History</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div v-for="payment in obituary.payments" :key="payment.id" class="flex justify-between items-center p-3 bg-gray-50 rounded">
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
                <div class="p-3 bg-gray-50 rounded">
                  <Label class="text-sm font-medium">Public URL:</Label>
                  <div class="flex items-center space-x-2 mt-1">
                    <Input :value="publicUrl" readonly class="text-sm text-gray-800 bg-white" />
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
              <DialogDescription>
                Process the payment for {{ obituary.service_type }} obituary service
              </DialogDescription>
            </DialogHeader>
            
            <form @submit.prevent="submitPayment" class="space-y-4">
              <div>
                <Label>Amount</Label>
                <Input v-model="paymentForm.amount" type="number" readonly />
              </div>
              
              <div>
                <Label>Payment Method</Label>
                <Select v-model="paymentForm.payment_method_id">
                  <SelectTrigger>
                    <SelectValue placeholder="Select payment method" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem 
                      v-for="method in paymentMethods" 
                      :key="method.id" 
                      :value="String(method.id)"
                    >
                      {{ method.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>
              
              <div>
                <Label>Notes (Optional)</Label>
                <Input v-model="paymentForm.notes" placeholder="Payment notes..." />
              </div>
              
              <DialogFooter>
                <Button type="button" variant="outline" @click="showPaymentDialog = false">
                  Cancel
                </Button>
                <Button type="submit" :disabled="paymentForm.processing">
                  {{ paymentForm.processing ? 'Processing...' : 'Complete Payment' }}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  </AppLayout>
</template>