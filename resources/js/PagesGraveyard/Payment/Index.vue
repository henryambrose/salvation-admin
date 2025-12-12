<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { CreditCard, Download, Eye, Filter, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Payment {
  id: number;
  payment_reference: string;
  payment_status: 'pending' | 'partial' | 'completed' | 'refunded';
  total_amount: number;
  paid_amount: number;
  balance_amount: number;
  concession_amount: number;
  payment_date: string;
  payable?: {
    booking_reference: string;
  };
  payable_type: string;
  payment_method?: {
    name: string;
  };
  paymentMethod?: {
    name: string;
  };
  creator?: {
    name: string;
  };
}

interface PendingBooking {
  id: number;
  type: string;
  type_label: string;
  reference: string;
  deceased_name: string;
  grave_info: string;
  total_cost: number;
  status: string;
  created_at: string;
  payment_url: string;
}

interface Props {
  payments: {
    data: Payment[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  pendingBookings: PendingBooking[];
  statusCounts: {
    partial: number;
    completed: number;
    refunded: number;
  };
  filters: {
    status?: string;
    booking_type?: string;
    search?: string;
  };
}

const props = defineProps<Props>();
// Debugging line
// Form for filters
const searchForm = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  booking_type: props.filters.booking_type || '',
});
// Computed
const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  partial: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  refunded: 'bg-red-100 text-red-800',
};

const statusCounts = computed(() => {
  return {
    total: props.payments.total,
    pending: props.pendingBookings?.length || 0, // Pending bookings awaiting payment
    partial: props.statusCounts.partial,
    completed: props.statusCounts.completed,
    refunded: props.statusCounts.refunded,
  };
});

// Methods
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  })
    .format(amount)
    .replace('₹', '₹ ');
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-IN');
};

const search = () => {
  router.get(
    '/graveyard/payments',
    {
      search: searchForm.value.search,
      status: searchForm.value.status,
      booking_type: searchForm.value.booking_type,
    },
    {
      preserveState: true,
      replace: true,
    },
  );
};

const clearFilters = () => {
  searchForm.value = { search: '', status: '', booking_type: '' };
  router.get('/graveyard/payments');
};

const getBookingTypeLabel = (payableType: string) => {
  if (payableType.endsWith('PermanentGraveBooking')) return 'Permanent';
  if (payableType.endsWith('TemporaryGraveBooking')) return 'Temporary';
  if (payableType.endsWith('NicheBooking')) return 'Niche';
  if (payableType.endsWith('PermanentGrave')) return 'Permanent Maint.';
  if (payableType.endsWith('Niche')) return 'Niche Maint.';
  return 'Unknown';
};
</script>

<template>
  <Head title="Payments" />

  <AppLayout>
    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl leading-7 font-bold text-gray-900">Payments</h1>
              <p class="mt-1 text-sm text-gray-500">Manage and track all payment records</p>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <CreditCard class="h-8 w-8 text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Total Payments</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statusCounts.total }}</dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                    <div class="h-3 w-3 rounded-full bg-green-600"></div>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Completed</dt>
                    <dd class="text-lg font-medium text-green-600">{{ statusCounts.completed }}</dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                    <div class="h-3 w-3 rounded-full bg-blue-600"></div>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Partial</dt>
                    <dd class="text-lg font-medium text-blue-600">{{ statusCounts.partial }}</dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100">
                    <div class="h-3 w-3 rounded-full bg-yellow-600"></div>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Pending</dt>
                    <dd class="text-lg font-medium text-yellow-600">{{ statusCounts.pending }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <Card class="mb-6">
          <CardHeader>
            <CardTitle class="flex items-center space-x-2">
              <Filter class="h-5 w-5" />
              <span>Filters & Search</span>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
              <div>
                <Label for="search">Search</Label>
                <Input id="search" v-model="searchForm.search" placeholder="Payment ref, booking ref..." @keyup.enter="search" />
              </div>

              <div>
                <Label for="status">Status</Label>
                <select
                  id="status"
                  v-model="searchForm.status"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">All Statuses</option>
                  <option value="pending">Pending</option>
                  <option value="partial">Partial</option>
                  <option value="completed">Completed</option>
                  <option value="refunded">Refunded</option>
                </select>
              </div>

              <div>
                <Label for="booking_type">Booking Type</Label>
                <select
                  id="booking_type"
                  v-model="searchForm.booking_type"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">All Types</option>
                  <option value="permanent">Permanent Grave</option>
                  <option value="temporary">Temporary Grave</option>
                  <option value="niche">Niche</option>
                </select>
              </div>

              <div class="flex items-end space-x-2">
                <Button @click="search" class="flex-1">
                  <Search class="mr-2 h-4 w-4" />
                  Search
                </Button>
                <Button @click="clearFilters" variant="outline"> Clear </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Pending Bookings Section -->
        <Card v-if="pendingBookings && pendingBookings.length > 0" class="mb-6 border-yellow-200 bg-yellow-50">
          <CardHeader>
            <CardTitle class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <CreditCard class="h-5 w-5 text-yellow-600" />
                <span class="text-yellow-900">Pending Payments ({{ pendingBookings.length }})</span>
              </div>
              <Badge class="bg-yellow-100 text-yellow-800">Awaiting Payment</Badge>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div
                v-for="booking in pendingBookings"
                :key="booking.type + '-' + booking.id"
                class="flex items-center justify-between rounded-lg border border-yellow-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow"
              >
                <div class="flex-1">
                  <div class="flex items-center space-x-3">
                    <Badge variant="outline" class="text-xs">{{ booking.type_label }}</Badge>
                    <h3 class="font-semibold text-gray-900">{{ booking.reference }}</h3>
                  </div>
                  <div class="mt-2 grid grid-cols-1 gap-2 text-sm sm:grid-cols-3">
                    <div>
                      <span class="text-gray-500">Deceased:</span>
                      <span class="ml-1 font-medium text-gray-900">{{ booking.deceased_name }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">{{ booking.grave_info }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">Status:</span>
                      <span class="ml-1 font-medium text-gray-900">{{ booking.status }}</span>
                    </div>
                  </div>
                  <div class="mt-2 text-sm">
                    <span class="text-gray-500">Created:</span>
                    <span class="ml-1 text-gray-700">{{ formatDate(booking.created_at) }}</span>
                  </div>
                </div>
                <div class="ml-6 flex flex-col items-end space-y-2">
                  <div class="text-right">
                    <div class="text-sm text-gray-500">Total Cost</div>
                    <div class="text-xl font-bold text-gray-900">
                      {{ booking.total_cost > 0 ? formatCurrency(booking.total_cost) : 'Not Set' }}
                    </div>
                  </div>
                  <Link :href="booking.payment_url">
                    <Button size="sm" class="bg-yellow-600 hover:bg-yellow-700">
                      <CreditCard class="mr-2 h-4 w-4" />
                      Make Payment
                    </Button>
                  </Link>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Payments Table -->
        <Card>
          <CardHeader>
            <CardTitle>Payment Records</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ payment.payment_reference }}</div>
                        <div class="text-sm text-gray-500">{{ payment.paymentMethod?.name || payment.payment_method?.name || 'Free' }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ payment.payable?.booking_reference || payment.payment_reference || 'N/A' }}
                      </div>
                      <div class="text-sm text-gray-500">{{ getBookingTypeLabel(payment.payable_type) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ formatCurrency(payment.paid_amount) }}</div>
                        <div v-if="payment.balance_amount > 0" class="text-sm text-red-600">
                          Balance: {{ formatCurrency(payment.balance_amount) }}
                        </div>
                        <div v-if="payment.concession_amount > 0" class="text-xs text-orange-600">
                          Concession: {{ formatCurrency(payment.concession_amount) }}
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Badge :class="statusColors[payment.payment_status]">
                        {{ payment.payment_status }}
                      </Badge>
                    </td>
                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                      {{ formatDate(payment.payment_date) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                      <div class="flex space-x-2">
                        <Button size="sm" variant="outline" as-child>
                          <a :href="route('graveyard.payments.receipt', payment.id)" target="_blank" rel="noopener noreferrer">
                            <Download class="mr-1 h-4 w-4" />
                            Receipt
                          </a>
                        </Button>
                        <Button size="sm" variant="outline" as-child>
                          <Link :href="route('graveyard.payments.show', payment.id)">
                            <Eye class="mr-1 h-4 w-4" />
                            View
                          </Link>
                        </Button>
                        <Button
                          v-if="payment.payment_status === 'partial' && payment.balance_amount > 0"
                          size="sm"
                          as-child
                          class="bg-blue-600 hover:bg-blue-700"
                        >
                          <Link :href="route('graveyard.payments.balance', payment.id)"> Balance </Link>
                        </Button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Empty State -->
              <div v-if="payments.data.length === 0" class="py-12 text-center">
                <CreditCard class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
                <p class="mt-1 text-sm text-gray-500">
                  {{
                    filters.search || filters.status || filters.booking_type
                      ? 'Try adjusting your search filters.'
                      : 'No payments have been recorded yet.'
                  }}
                </p>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="payments.last_page > 1" class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ (payments.current_page - 1) * payments.per_page + 1 }} to
                {{ Math.min(payments.current_page * payments.per_page, payments.total) }}
                of {{ payments.total }} results
              </div>
              <div class="flex space-x-1">
                <Button
                  v-for="page in Math.min(payments.last_page, 10)"
                  :key="page"
                  @click="router.get(route('graveyard.payments.index'), { ...filters, page })"
                  :variant="page === payments.current_page ? 'default' : 'outline'"
                  size="sm"
                >
                  {{ page }}
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
