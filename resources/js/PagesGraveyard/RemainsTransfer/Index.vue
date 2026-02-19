<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Pencil, Phone, Plus, Search, User } from 'lucide-vue-next';
import { ref } from 'vue';

interface RemainsTransfer {
  id: number;
  transfer_reference: string;
  transfer_type: 'niche' | 'permanent_grave' | 'removal';
  status: 'pending' | 'approved' | 'rejected' | 'completed' | 'cancelled';
  from_temporary_grave: {
    grave_no: string;
    section: string;
    row_no: string;
  };
  from_booking: {
    dead_first_name: string;
    dead_last_name: string;
    buried_on: string;
    applicant_name: string;
    contact_no: string;
  };
  to_niche?: {
    niche_no: string;
    section: string;
    row_no: string;
  } | null;
  to_permanent_grave?: {
    grave_no: string;
    section: string;
    row_no: string;
  } | null;
  proposed_transfer_date: string;
  transfer_reason?: string | null;
  applicant_name?: string | null;
  contact_no?: string | null;
  created_at: string;
  creator: {
    name: string;
  };
}

interface Props {
  transfers: {
    data: RemainsTransfer[];
    links: any[];
    meta: any;
  };
  filters: {
    status?: string;
    search?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-blue-100 text-blue-800',
  rejected: 'bg-red-100 text-red-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-gray-100 text-gray-800',
};

const applyFilters = () => {
  router.get(
    route('graveyard.remains-transfers.index'),
    {
      search: search.value,
      status: status.value === 'all' ? '' : status.value,
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

const getDeceasedName = (transfer: RemainsTransfer) => {
  return `${transfer.from_booking.dead_first_name} ${transfer.from_booking.dead_last_name}`;
};


</script>

<template>
  <Head title="Remains Transfers" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base leading-6 font-semibold text-gray-900">Remains Transfers</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage remains transfers from temporary graves</p>
              </div>
              <div class="flex items-center space-x-3">
                <Button as-child>
                  <Link :href="route('graveyard.remains-transfers.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Request Transfer
                  </Link>
                </Button>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
              <div class="sm:col-span-2">
                <Label for="search">Search</Label>
                <div class="relative mt-1">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <Search class="h-5 w-5 text-gray-400" />
                  </div>
                  <Input
                    id="search"
                    v-model="search"
                    placeholder="Search by deceased name, reference, or applicant..."
                    class="pl-10"
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
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>

              <div class="flex items-end space-x-2">
                <Button @click="applyFilters" class="flex-1"> Apply </Button>
                <Button variant="outline" @click="clearFilters"> Clear </Button>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="px-4 py-5 sm:p-6">
            <div v-if="transfers.data.length === 0" class="py-12 text-center">
              <ArrowRight class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No transfers found</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating a new remains transfer request.</p>
              <div class="mt-6">
                <Button as-child>
                  <Link :href="route('graveyard.remains-transfers.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Request Transfer
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
                      <TableHead class="text-gray-700 font-semibold">Transfer Details</TableHead>
                      <TableHead class="text-gray-700 font-semibold">From → To</TableHead>
                      <TableHead class="text-gray-700 font-semibold">Deceased</TableHead>
                      <TableHead class="text-gray-700 font-semibold">Applicant</TableHead>
                      <TableHead class="text-gray-700 font-semibold">Transfer Date</TableHead>
                      <!-- <TableHead>Financial</TableHead> -->
                      <TableHead class="text-gray-700 font-semibold">Status</TableHead>
                      <TableHead class="text-right text-gray-700 font-semibold">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="transfer in transfers.data" :key="transfer.id">
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">#{{ transfer.transfer_reference }}</div>
                          <div class="text-sm text-gray-500">
                            {{ transfer.transfer_reason?.substring(0, 50) }}{{ (transfer.transfer_reason?.length ?? 0) > 50 ? '...' : '' }}
                          </div>
                          <div class="text-xs text-gray-400">By {{ transfer.creator.name }}</div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div class="space-y-1">
                          <div class="text-sm">
                            <span class="font-medium">{{ transfer.from_temporary_grave.grave_no }}</span>
                            <span class="text-gray-500"> → </span>
                            <span v-if="transfer.transfer_type === 'niche' && transfer.to_niche" class="font-medium">{{ transfer.to_niche.niche_no }}</span>
                            <span v-else-if="transfer.transfer_type === 'permanent_grave' && transfer.to_permanent_grave" class="font-medium">{{ transfer.to_permanent_grave.grave_no }}</span>
                            <span v-else class="text-gray-400 italic">Removal</span>
                          </div>
                          <div class="text-xs text-gray-500">
                            {{ transfer.from_temporary_grave.section }}
                            <template v-if="transfer.transfer_type === 'niche' && transfer.to_niche"> → {{ transfer.to_niche.section }}</template>
                            <template v-else-if="transfer.transfer_type === 'permanent_grave' && transfer.to_permanent_grave"> → {{ transfer.to_permanent_grave.section }}</template>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ getDeceasedName(transfer) }}
                          </div>
                          <div class="text-sm text-gray-500">Buried: {{ formatDate(transfer.from_booking.buried_on) }}</div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ transfer.applicant_name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ transfer.contact_no }}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div class="text-sm">
                          {{ formatDate(transfer.proposed_transfer_date) }}
                        </div>
                      </TableCell>
                      <!-- <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ formatCurrency(transfer.total_cost) }}
                          </div>
                          <div v-if="transfer.balance_amount > 0" class="text-sm text-red-600">
                            Balance: {{ formatCurrency(transfer.balance_amount) }}
                          </div>
                        </div>
                      </TableCell> -->
                      <TableCell>
                        <Badge :class="statusColors[transfer.status]">
                          {{ transfer.status }}
                        </Badge>
                      </TableCell>
                      <TableCell class="text-right">
                        <Button variant="outline" size="sm" as-child>
                          <Link :href="route('graveyard.remains-transfers.show', transfer.id)">
                            <Pencil class="h-4 w-4" />
                          </Link>
                        </Button>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>

              <!-- Mobile Cards -->
              <div class="space-y-4 sm:hidden">
                <Card v-for="transfer in transfers.data" :key="transfer.id">
                  <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                      <CardTitle class="text-base"> #{{ transfer.transfer_reference }} </CardTitle>
                      <Badge :class="statusColors[transfer.status]">
                        {{ transfer.status }}
                      </Badge>
                    </div>
                    <CardDescription> {{ getDeceasedName(transfer) }} • {{ formatDate(transfer.proposed_transfer_date) }} </CardDescription>
                  </CardHeader>
                  <CardContent class="space-y-3">
                    <div class="flex items-center space-x-2 text-sm">
                      <ArrowRight class="h-4 w-4 text-gray-400" />
                      <span>
                        {{ transfer.from_temporary_grave.grave_no }} →
                        <template v-if="transfer.transfer_type === 'niche' && transfer.to_niche">{{ transfer.to_niche.niche_no }}</template>
                        <template v-else-if="transfer.transfer_type === 'permanent_grave' && transfer.to_permanent_grave">{{ transfer.to_permanent_grave.grave_no }}</template>
                        <template v-else>Removal</template>
                      </span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <User class="h-4 w-4 text-gray-400" />
                      <span>{{ transfer.applicant_name }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <Phone class="h-4 w-4 text-gray-400" />
                      <span>{{ transfer.contact_no }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <Calendar class="h-4 w-4 text-gray-400" />
                      <span>Transfer date: {{ formatDate(transfer.proposed_transfer_date) }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                      <!-- <div>
                        <div class="font-medium">{{ formatCurrency(transfer.total_cost) }}</div>
                        <div v-if="transfer.balance_amount > 0" class="text-sm text-red-600">
                          Balance: {{ formatCurrency(transfer.balance_amount) }}
                        </div>
                      </div> -->
                      <Button variant="outline" size="sm" as-child>
                        <Link :href="route('graveyard.remains-transfers.show', transfer.id)">
                          <Pencil class="h-4 w-4" />
                        </Link>
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Pagination -->
              <Pagination
                v-if="transfers?.meta"
                :links="transfers.links"
                :meta="transfers.meta"
                :current-page="transfers.meta.current_page"
                :last-page="transfers.meta.last_page"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
