<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateForDisplay } from '@/lib/utils';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Clock, Eye, MapPin, Pencil, Phone, Plus, Search, Trash2, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface TemporaryGraveBooking {
  id: number;
  booking_reference: string;
  status: 'pending' | 'confirmed' | 'cancelled';
  temporary_grave: {
    grave_no: string;
    section: string;
    row_no: string;
  };
  dead_first_name: string;
  dead_last_name: string;
  age?: number;
  months?: number;
  days?: number;
  died_on: string;
  buried_on: string;
  gender?: {
    name: string;
  };
  parish?: {
    name: string;
  };
  applicant_name: string;
  contact_no: string;
  total_cost: number;
  paid_amount: number;
  balance_amount: number;
  payment_status: 'pending' | 'partial' | 'paid' | 'completed';
  expected_transfer_date: string;
  transfer_requested: boolean;
  remains_transfers?: Array<{ id: number }>;
  created_at: string;
  creator: {
    name: string;
  };
}

interface Props {
  bookings: {
    data: TemporaryGraveBooking[];
    links: any[];
    meta: any;
  };
  filters: {
    status?: string;
    search?: string;
    transfer_due?: string;
  };
}

const props = defineProps<Props>();

const { success, error } = useToast();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const transferDue = ref(props.filters.transfer_due || 'all');

// Computed property for eligible bookings count
const eligibleBookingsCount = computed(() => {
  return props.bookings?.data?.filter((booking) => canRequestTransfer(booking)).length || 0;
});

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

const paymentStatusColors = {
  pending: 'bg-orange-100 text-orange-800',
  partial: 'bg-blue-100 text-blue-800',
  paid: 'bg-green-100 text-green-800',
  completed: 'bg-green-100 text-green-800',
};

// Debounced search
let searchTimeout: number;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    // Only search if 2+ characters or empty (to clear results)
    if (!search.value || search.value.length >= 2) {
      applyFilters();
    }
  }, 500);
};

const applyFilters = () => {
  router.get(
    route('graveyard.temporary-grave-bookings.index'),
    {
      search: search.value,
      status: status.value === 'all' ? '' : status.value,
      transfer_due: transferDue.value === 'all' ? '' : transferDue.value,
    },
    {
      preserveState: true,
      replace: true,
    },
  );
};

const clearFilters = () => {
  search.value = '';
  status.value = 'all';
  transferDue.value = 'all';
  applyFilters();
  success('Filters cleared successfully!');
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  })
    .format(amount)
    .replace('₹', '₹ ');
};

// Use formatDateForDisplay from utils for consistent date formatting (DD/MM/YYYY)
const formatDate = formatDateForDisplay;

const getDeceasedName = (booking: TemporaryGraveBooking) => {
  return `${booking.dead_first_name} ${booking.dead_last_name}`;
};

const getAge = (booking: TemporaryGraveBooking) => {
  const parts = [];
  if (booking.age) parts.push(`${booking.age}y`);
  if (booking.months) parts.push(`${booking.months}m`);
  if (booking.days) parts.push(`${booking.days}d`);
  return parts.length > 0 ? parts.join(' ') : 'N/A';
};

const isTransferOverdue = (booking: TemporaryGraveBooking) => {
  return new Date(booking.expected_transfer_date) < new Date();
};

const isTransferDueSoon = (booking: TemporaryGraveBooking) => {
  const dueDate = new Date(booking.expected_transfer_date);
  const twoMonthsFromNow = new Date();
  twoMonthsFromNow.setMonth(twoMonthsFromNow.getMonth() + 2);
  return dueDate <= twoMonthsFromNow && dueDate >= new Date();
};

const canRequestTransfer = (booking: TemporaryGraveBooking) => {
  if (booking.transfer_requested) return false;
  const today = new Date();
  const expectedDate = booking.expected_transfer_date ? new Date(booking.expected_transfer_date) : null;
  return booking.status === 'confirmed' && ['paid', 'completed'].includes(booking.payment_status) && expectedDate !== null && today >= expectedDate;
};

const getRowClass = (booking: TemporaryGraveBooking) => {
  if (booking.transfer_requested) {
    return 'bg-purple-50 hover:bg-purple-100 border-l-4 border-purple-400';
  }
  if (canRequestTransfer(booking)) {
    if (isTransferOverdue(booking)) return 'bg-red-50 hover:bg-red-100 border-l-4 border-red-400';
    if (isTransferDueSoon(booking)) return 'bg-yellow-50 hover:bg-yellow-100 border-l-4 border-yellow-400';
    return 'bg-blue-50 hover:bg-blue-100 border-l-4 border-blue-400';
  }
  return 'hover:bg-gray-50';
};

const requestTransfer = (booking: TemporaryGraveBooking) => {
  router.visit(route('graveyard.remains-transfers.create', { booking_id: booking.id }));
};

const viewTransfer = (booking: TemporaryGraveBooking) => {
  const transferId = booking.remains_transfers?.[0]?.id;
  if (transferId) {
    router.visit(route('graveyard.remains-transfers.show', transferId));
  } else {
    router.visit(route('graveyard.remains-transfers.index'));
  }
};

const canEditBooking = (booking: TemporaryGraveBooking) => {
  return booking.status === 'pending' || booking.status === 'confirmed';
};

const canDeleteBooking = (booking: TemporaryGraveBooking) => {
  return booking.status === 'pending';
};

const deleteBooking = (booking: TemporaryGraveBooking) => {
  if (!canDeleteBooking(booking)) {
    error('Only pending bookings can be deleted.');
    return;
  }

  const { confirm: showConfirm } = useConfirm();
  showConfirm({
    title: 'Delete Booking',
    message: `Are you sure you want to delete booking #${booking.booking_reference}? This action cannot be undone.`,
    confirmText: 'Delete',
    cancelText: 'Cancel',
    type: 'danger',
    onConfirm: () => {
      router.delete(route('graveyard.temporary-grave-bookings.destroy', booking.id), {
        onSuccess: () => {
          success('Temporary grave booking deleted successfully!');
        },
        onError: (errors) => {
          console.error('Failed to delete booking:', errors);
          error('Failed to delete booking. Please try again.');
        },
      });
    },
  });
};
</script>

<template>
  <Head title="Temporary Grave Bookings" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base leading-6 font-semibold text-gray-900">Temporary Grave Bookings</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                  Manage temporary grave bookings and transfers
                  <Badge v-if="eligibleBookingsCount > 0" class="ml-2 bg-purple-100 text-purple-800">
                    {{ eligibleBookingsCount }} eligible for transfer
                  </Badge>
                </p>
              </div>
              <div class="flex items-center space-x-3">
                <Button as-child>
                  <Link :href="route('graveyard.temporary-grave-bookings.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Book Temporary Grave
                  </Link>
                </Button>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
              <div class="sm:col-span-1">
                <Label for="search">Search</Label>
                <div class="relative mt-1">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <Search class="h-5 w-5 text-gray-400" />
                  </div>
                  <Input
                    id="search"
                    v-model="search"
                    placeholder="Search by deceased name, grave number, or permit..."
                    class="pl-10"
                    @input="debouncedSearch"
                    @keyup.enter="applyFilters"
                  />
                </div>
              </div>

              <div>
                <Label for="status">Status</Label>
                <select
                  v-model="status"
                  @change="applyFilters"
                  class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="all">All statuses</option>
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>

              <div>
                <Label for="transfer_due">Transfer Status</Label>
                <select
                  v-model="transferDue"
                  @change="applyFilters"
                  class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="all">All</option>
                  <option value="eligible">Eligible for Transfer</option>
                  <option value="due_soon">Due Soon</option>
                  <option value="overdue">Overdue</option>
                  <option value="requested">Transferred</option>
                </select>
              </div>

              <div class="flex items-end">
                <Button variant="outline" @click="clearFilters"> Clear Filters </Button>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="px-4 py-5 sm:p-6">
            <div v-if="!bookings?.data?.length" class="py-12 text-center">
              <Calendar class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings found</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating a new temporary grave booking.</p>
              <div class="mt-6">
                <Button as-child>
                  <Link :href="route('graveyard.temporary-grave-bookings.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Book Temporary Grave
                  </Link>
                </Button>
              </div>
            </div>

            <div v-else class="space-y-6">
              <!-- Color Legend for Eligible Bookings -->
              <div class="rounded-lg border border-purple-200 bg-purple-50 p-4">
                <h4 class="mb-2 text-sm font-medium text-purple-900">Transfer Eligibility Status</h4>
                <p class="mb-3 text-xs text-purple-800">
                  Bookings eligible for transfer are highlighted. Click the arrow button to initiate or view a transfer.
                </p>
                <div class="grid grid-cols-1 gap-2 text-xs sm:grid-cols-4">
                  <div class="flex items-center space-x-2">
                    <div class="h-4 w-8 rounded border-l-4 border-red-400 bg-red-100"></div>
                    <span class="text-gray-700">Overdue - Immediate action needed</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <div class="h-4 w-8 rounded border-l-4 border-yellow-400 bg-yellow-100"></div>
                    <span class="text-gray-700">Due Soon - Within 2 months</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <div class="h-4 w-8 rounded border-l-4 border-blue-400 bg-blue-100"></div>
                    <span class="text-gray-700">Eligible - Ready for transfer</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <div class="h-4 w-8 rounded border-l-4 border-purple-400 bg-purple-100"></div>
                    <span class="text-gray-700">Transfer Initiated</span>
                  </div>
                </div>
              </div>

              <!-- Desktop Table -->
              <div class="hidden sm:block">
                <Table>
                  <TableHeader>
                    <TableRow class="border-b">
                      <TableHead class="font-medium text-gray-900">Booking Details</TableHead>
                      <TableHead class="font-medium text-gray-900">Deceased</TableHead>
                      <TableHead class="font-medium text-gray-900">Grave</TableHead>
                      <TableHead class="font-medium text-gray-900">Contact</TableHead>
                      <TableHead class="font-medium text-gray-900">Transfer Status</TableHead>
                      <TableHead class="font-medium text-gray-900">Payment Details</TableHead>
                      <TableHead class="font-medium text-gray-900">Status</TableHead>
                      <TableHead class="text-right font-medium text-gray-900">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="booking in bookings?.data || []" :key="booking.id" :class="getRowClass(booking)">
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">#{{ booking.booking_reference }}</div>
                          <div class="text-sm text-gray-500">Buried: {{ formatDate(booking.buried_on) }}</div>
                          <div class="text-xs text-gray-400">By {{ booking.creator.name }}</div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ getDeceasedName(booking) }}
                          </div>
                          <div class="text-sm text-gray-500">Age: {{ getAge(booking) }}</div>
                          <div class="text-sm text-gray-500">Died: {{ formatDate(booking.died_on) }}</div>
                          <div v-if="booking.gender" class="text-xs text-gray-400">
                            {{ booking.gender.name }}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ booking.temporary_grave.grave_no }}
                          </div>
                          <div class="text-sm text-gray-500">{{ booking.temporary_grave.section }}, Row {{ booking.temporary_grave.row_no }}</div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ booking.applicant_name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ booking.contact_no }}
                          </div>
                          <div v-if="booking.parish" class="text-xs text-gray-400">
                            {{ booking.parish.name }}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div class="space-y-1">
                          <div class="text-sm">
                            {{ formatDate(booking.expected_transfer_date) }}
                          </div>
                          <div class="flex space-x-1">
                            <Badge v-if="booking.transfer_requested" class="bg-purple-100 text-xs text-purple-800"> Transferred </Badge>
                            <Badge v-else-if="isTransferOverdue(booking)" class="bg-red-100 text-xs text-red-800"> Overdue </Badge>
                            <Badge v-else-if="isTransferDueSoon(booking)" class="bg-yellow-100 text-xs text-yellow-800"> Due Soon </Badge>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">Total: {{ formatCurrency(booking.total_cost) }}</div>
                          <div v-if="booking.payment_status === 'partial'" class="text-sm">
                            <div class="text-green-600">Paid: {{ formatCurrency(booking.paid_amount) }}</div>
                            <div class="font-medium text-red-600">Balance: {{ formatCurrency(booking.balance_amount) }}</div>
                          </div>
                          <div v-else-if="booking.payment_status === 'paid' || booking.payment_status === 'completed'" class="text-sm text-green-600">
                            Fully Paid
                          </div>
                          <div v-else class="text-sm text-gray-500">Not Paid</div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge :class="statusColors[booking.status]">
                          {{ booking.status }}
                        </Badge>
                      </TableCell>
                      <TableCell class="text-right">
                        <div class="flex items-center justify-end space-x-2">
                          <!-- Initiate Transfer (eligible, not yet requested) -->
                          <Button
                            v-if="canRequestTransfer(booking)"
                            size="sm"
                            class="bg-purple-600 hover:bg-purple-700"
                            title="Initiate Remains Transfer"
                            @click="requestTransfer(booking)"
                          >
                            <ArrowRight class="h-4 w-4" />
                          </Button>

                          <!-- View/Edit Existing Transfer (already requested) -->
                          <Button
                            v-if="booking.transfer_requested"
                            size="sm"
                            class="bg-blue-600 hover:bg-blue-700"
                            title="View / Edit Transfer"
                            @click="viewTransfer(booking)"
                          >
                            <ArrowRight class="h-4 w-4" />
                          </Button>

                          <!-- Edit Booking -->
                          <Button v-if="canEditBooking(booking)" variant="outline" size="sm" as-child>
                            <Link :href="route('graveyard.temporary-grave-bookings.edit', booking.id)">
                              <Pencil class="h-4 w-4" />
                            </Link>
                          </Button>

                          <!-- View Details -->
                          <Button variant="outline" size="sm" as-child>
                            <Link :href="route('graveyard.temporary-grave-bookings.show', booking.id)">
                              <Eye class="h-4 w-4" />
                            </Link>
                          </Button>

                          <!-- Delete -->
                          <Button
                            v-if="canDeleteBooking(booking)"
                            variant="outline"
                            size="sm"
                            @click="deleteBooking(booking)"
                            class="text-red-600 hover:bg-red-50 hover:text-red-800"
                          >
                            <Trash2 class="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>

              <!-- Mobile Cards -->
              <div class="space-y-4 sm:hidden">
                <Card v-for="booking in bookings?.data || []" :key="booking.id" :class="getRowClass(booking)">
                  <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                      <CardTitle class="text-base"> #{{ booking.booking_reference }} </CardTitle>
                      <div class="flex space-x-1">
                        <Badge :class="statusColors[booking.status]">
                          {{ booking.status }}
                        </Badge>
                        <Badge v-if="booking.transfer_requested" class="bg-purple-100 text-xs text-purple-800"> Transferred </Badge>
                      </div>
                    </div>
                    <CardDescription> {{ getDeceasedName(booking) }} • {{ formatDate(booking.buried_on) }} </CardDescription>
                  </CardHeader>
                  <CardContent class="space-y-3">
                    <div class="flex items-center space-x-2 text-sm">
                      <MapPin class="h-4 w-4 text-gray-400" />
                      <span
                        >{{ booking.temporary_grave.grave_no }} - {{ booking.temporary_grave.section }}, Row
                        {{ booking.temporary_grave.row_no }}</span
                      >
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <User class="h-4 w-4 text-gray-400" />
                      <span>{{ booking.applicant_name }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <Phone class="h-4 w-4 text-gray-400" />
                      <span>{{ booking.contact_no }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <Clock class="h-4 w-4 text-gray-400" />
                      <span>Transfer due: {{ formatDate(booking.expected_transfer_date) }}</span>
                      <Badge v-if="isTransferOverdue(booking)" class="bg-red-100 text-xs text-red-800"> Overdue </Badge>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                      <div>
                        <div class="font-medium">Total: {{ formatCurrency(booking.total_cost) }}</div>
                        <div v-if="booking.payment_status === 'partial'" class="space-y-1 text-sm">
                          <div class="text-green-600">Paid: {{ formatCurrency(booking.paid_amount) }}</div>
                          <div class="font-medium text-red-600">Balance: {{ formatCurrency(booking.balance_amount) }}</div>
                        </div>
                        <Badge :class="paymentStatusColors[booking.payment_status]" class="mt-1 text-xs">
                          {{ booking.payment_status }}
                        </Badge>
                      </div>
                      <div class="flex items-center space-x-2">
                        <!-- Initiate Transfer -->
                        <Button
                          v-if="canRequestTransfer(booking)"
                          size="sm"
                          class="bg-purple-600 hover:bg-purple-700"
                          title="Initiate Remains Transfer"
                          @click="requestTransfer(booking)"
                        >
                          <ArrowRight class="h-4 w-4" />
                        </Button>

                        <!-- View/Edit Existing Transfer -->
                        <Button
                          v-if="booking.transfer_requested"
                          size="sm"
                          class="bg-blue-600 hover:bg-blue-700"
                          title="View / Edit Transfer"
                          @click="viewTransfer(booking)"
                        >
                          <ArrowRight class="h-4 w-4" />
                        </Button>

                        <!-- Edit Booking -->
                        <Button v-if="canEditBooking(booking)" variant="outline" size="sm" as-child>
                          <Link :href="route('graveyard.temporary-grave-bookings.edit', booking.id)">
                            <Pencil class="h-4 w-4" />
                          </Link>
                        </Button>

                        <!-- View Details -->
                        <Button variant="outline" size="sm" as-child>
                          <Link :href="route('graveyard.temporary-grave-bookings.show', booking.id)">
                            <Eye class="h-4 w-4" />
                          </Link>
                        </Button>

                        <!-- Delete -->
                        <Button
                          v-if="canDeleteBooking(booking)"
                          variant="outline"
                          size="sm"
                          @click="deleteBooking(booking)"
                          class="text-red-600 hover:bg-red-50 hover:text-red-800"
                        >
                          <Trash2 class="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Pagination -->
              <Pagination
                v-if="bookings?.meta"
                :current-page="bookings.meta.current_page"
                :last-page="bookings.meta.last_page"
                :prev-page-url="bookings.meta.prev_page_url"
                :next-page-url="bookings.meta.next_page_url"
                :from="bookings.meta.from"
                :to="bookings.meta.to"
                :total="bookings.meta.total"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
