<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Clock, Eye, MapPin, Phone, Plus, Search, User } from 'lucide-vue-next';
import { ref } from 'vue';

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
  payment_status: 'pending' | 'partial' | 'paid';
  duration_months: number;
  expected_transfer_date: string;
  transfer_requested: boolean;
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

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const transferDue = ref(props.filters.transfer_due || 'all');

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

const paymentStatusColors = {
  pending: 'bg-orange-100 text-orange-800',
  partial: 'bg-blue-100 text-blue-800',
  paid: 'bg-green-100 text-green-800',
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
};

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

const requestTransfer = (bookingId: number) => {
  router.post(route('graveyard.temporary-grave-bookings.request-transfer', bookingId));
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
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage temporary grave bookings and transfers</p>
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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
              <div class="sm:col-span-2">
                <Label for="search">Search</Label>
                <div class="relative mt-1">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <Search class="h-5 w-5 text-gray-400" />
                  </div>
                  <Input
                    id="search"
                    v-model="search"
                    placeholder="Search by deceased name, grave number..."
                    class="pl-10"
                    @keyup.enter="applyFilters"
                  />
                </div>
              </div>

              <div>
                <Label for="status">Status</Label>
                <Select v-model="status">
                  <SelectTrigger class="mt-1">
                    <SelectValue placeholder="All statuses" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All statuses</SelectItem>
                    <SelectItem value="pending">Pending</SelectItem>
                    <SelectItem value="confirmed">Confirmed</SelectItem>
                    <SelectItem value="cancelled">Cancelled</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <Label for="transfer_due">Transfer Status</Label>
                <Select v-model="transferDue">
                  <SelectTrigger class="mt-1">
                    <SelectValue placeholder="All" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All</SelectItem>
                    <SelectItem value="due_soon">Due Soon</SelectItem>
                    <SelectItem value="overdue">Overdue</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div class="flex items-end space-x-2">
                <Button @click="applyFilters" class="flex-1"> Apply </Button>
                <Button variant="outline" @click="clearFilters"> Clear </Button>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="px-4 py-5 sm:p-6">
            <div v-if="bookings.data.length === 0" class="py-12 text-center">
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
              <!-- Desktop Table -->
              <div class="hidden sm:block">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Booking Details</TableHead>
                      <TableHead>Deceased</TableHead>
                      <TableHead>Grave</TableHead>
                      <TableHead>Contact</TableHead>
                      <TableHead>Transfer Status</TableHead>
                      <TableHead>Financial</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="booking in bookings.data" :key="booking.id">
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
                          <div class="text-xs text-gray-400">{{ booking.duration_months }} months</div>
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
                            <Badge v-if="booking.transfer_requested" class="bg-blue-100 text-xs text-blue-800"> Requested </Badge>
                            <Badge v-else-if="isTransferOverdue(booking)" class="bg-red-100 text-xs text-red-800"> Overdue </Badge>
                            <Badge v-else-if="isTransferDueSoon(booking)" class="bg-yellow-100 text-xs text-yellow-800"> Due Soon </Badge>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ formatCurrency(booking.total_cost) }}
                          </div>
                          <Badge :class="paymentStatusColors[booking.payment_status]" class="text-xs">
                            {{ booking.payment_status }}
                          </Badge>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge :class="statusColors[booking.status]">
                          {{ booking.status }}
                        </Badge>
                      </TableCell>
                      <TableCell class="text-right">
                        <div class="flex justify-end space-x-2">
                          <Button variant="outline" size="sm" as-child>
                            <Link :href="route('graveyard.temporary-grave-bookings.show', booking.id)">
                              <Eye class="h-4 w-4" />
                            </Link>
                          </Button>
                          <Button
                            v-if="booking.status === 'confirmed' && !booking.transfer_requested"
                            variant="outline"
                            size="sm"
                            @click="requestTransfer(booking.id)"
                            class="border-blue-200 text-blue-600 hover:bg-blue-50"
                          >
                            <ArrowRight class="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>

              <!-- Mobile Cards -->
              <div class="space-y-4 sm:hidden">
                <Card v-for="booking in bookings.data" :key="booking.id">
                  <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                      <CardTitle class="text-base"> #{{ booking.booking_reference }} </CardTitle>
                      <div class="flex space-x-1">
                        <Badge :class="statusColors[booking.status]">
                          {{ booking.status }}
                        </Badge>
                        <Badge v-if="booking.transfer_requested" class="bg-blue-100 text-xs text-blue-800"> Transfer Requested </Badge>
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
                        <div class="font-medium">{{ formatCurrency(booking.total_cost) }}</div>
                        <Badge :class="paymentStatusColors[booking.payment_status]" class="text-xs">
                          {{ booking.payment_status }}
                        </Badge>
                      </div>
                      <div class="flex space-x-2">
                        <Button variant="outline" size="sm" as-child>
                          <Link :href="route('graveyard.temporary-grave-bookings.show', booking.id)">
                            <Eye class="h-4 w-4" />
                          </Link>
                        </Button>
                        <Button
                          v-if="booking.status === 'confirmed' && !booking.transfer_requested"
                          variant="outline"
                          size="sm"
                          @click="requestTransfer(booking.id)"
                          class="border-blue-200 text-blue-600 hover:bg-blue-50"
                        >
                          <ArrowRight class="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Pagination -->
              <Pagination
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
