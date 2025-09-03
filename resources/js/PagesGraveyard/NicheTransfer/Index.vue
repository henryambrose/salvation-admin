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
import { ArrowRight, Calendar, Eye, Phone, Plus, Search, User } from 'lucide-vue-next';
import { ref } from 'vue';

interface NicheTransfer {
  id: number;
  transfer_reference: string;
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
  to_niche: {
    niche_no: string;
    section: string;
    row_no: string;
  };
  proposed_transfer_date: string;
  transfer_reason: string;
  transfer_applicant_name: string;
  transfer_contact_no: string;
  niche_cost: number;
  transfer_cost: number;
  total_cost: number;
  balance_amount: number;
  created_at: string;
  creator: {
    name: string;
  };
  approver?: {
    name: string;
  };
}

interface Props {
  transfers: {
    data: NicheTransfer[];
    links: any[];
    meta: any;
  };
  filters: {
    status?: string;
    search?: string;
    due_soon?: boolean;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const dueSoon = ref(props.filters.due_soon || false);

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-blue-100 text-blue-800',
  rejected: 'bg-red-100 text-red-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-gray-100 text-gray-800',
};

const applyFilters = () => {
  router.get(
    route('graveyard.niche-transfers.index'),
    {
      search: search.value,
      status: status.value === 'all' ? '' : status.value,
      due_soon: dueSoon.value ? '1' : '',
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
  dueSoon.value = false;
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

const getDeceasedName = (transfer: NicheTransfer) => {
  return `${transfer.from_booking.dead_first_name} ${transfer.from_booking.dead_last_name}`;
};

const isTransferDue = (transfer: NicheTransfer) => {
  const transferDate = new Date(transfer.proposed_transfer_date);
  const today = new Date();
  const daysUntilTransfer = Math.ceil((transferDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
  return daysUntilTransfer <= 30 && daysUntilTransfer >= 0;
};

const isOverdue = (transfer: NicheTransfer) => {
  return new Date(transfer.proposed_transfer_date) < new Date();
};
</script>

<template>
  <Head title="Niche Transfers" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base leading-6 font-semibold text-gray-900">Niche Transfers</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage transfers from temporary graves to niches</p>
              </div>
              <div class="flex items-center space-x-3">
                <Button as-child>
                  <Link :href="route('graveyard.niche-transfers.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Request Transfer
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
                    placeholder="Search by deceased name, reference, or applicant..."
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
                    <SelectItem value="approved">Approved</SelectItem>
                    <SelectItem value="rejected">Rejected</SelectItem>
                    <SelectItem value="completed">Completed</SelectItem>
                    <SelectItem value="cancelled">Cancelled</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div class="mt-6 flex items-center space-x-2">
                <input id="due_soon" v-model="dueSoon" type="checkbox" class="rounded border-gray-300" />
                <Label for="due_soon" class="text-sm">Due Soon</Label>
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
              <p class="mt-1 text-sm text-gray-500">Get started by creating a new niche transfer request.</p>
              <div class="mt-6">
                <Button as-child>
                  <Link :href="route('graveyard.niche-transfers.create')">
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
                      <TableHead>Transfer Details</TableHead>
                      <TableHead>From → To</TableHead>
                      <TableHead>Deceased</TableHead>
                      <TableHead>Applicant</TableHead>
                      <TableHead>Transfer Date</TableHead>
                      <TableHead>Financial</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="transfer in transfers.data" :key="transfer.id">
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">#{{ transfer.transfer_reference }}</div>
                          <div class="text-sm text-gray-500">
                            {{ transfer.transfer_reason.substring(0, 50) }}{{ transfer.transfer_reason.length > 50 ? '...' : '' }}
                          </div>
                          <div class="text-xs text-gray-400">By {{ transfer.creator.name }}</div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div class="space-y-1">
                          <div class="text-sm">
                            <span class="font-medium">{{ transfer.from_temporary_grave.grave_no }}</span>
                            <span class="text-gray-500"> → </span>
                            <span class="font-medium">{{ transfer.to_niche.niche_no }}</span>
                          </div>
                          <div class="text-xs text-gray-500">{{ transfer.from_temporary_grave.section }} → {{ transfer.to_niche.section }}</div>
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
                            {{ transfer.transfer_applicant_name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ transfer.transfer_contact_no }}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div class="space-y-1">
                          <div class="text-sm">
                            {{ formatDate(transfer.proposed_transfer_date) }}
                          </div>
                          <div class="flex space-x-1">
                            <Badge v-if="isOverdue(transfer) && transfer.status === 'approved'" class="bg-red-100 text-xs text-red-800">
                              Overdue
                            </Badge>
                            <Badge
                              v-else-if="isTransferDue(transfer) && transfer.status === 'approved'"
                              class="bg-yellow-100 text-xs text-yellow-800"
                            >
                              Due Soon
                            </Badge>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ formatCurrency(transfer.total_cost) }}
                          </div>
                          <div v-if="transfer.balance_amount > 0" class="text-sm text-red-600">
                            Balance: {{ formatCurrency(transfer.balance_amount) }}
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge :class="statusColors[transfer.status]">
                          {{ transfer.status }}
                        </Badge>
                      </TableCell>
                      <TableCell class="text-right">
                        <Button variant="outline" size="sm" as-child>
                          <Link :href="route('graveyard.niche-transfers.show', transfer.id)">
                            <Eye class="h-4 w-4" />
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
                      <span>{{ transfer.from_temporary_grave.grave_no }} → {{ transfer.to_niche.niche_no }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <User class="h-4 w-4 text-gray-400" />
                      <span>{{ transfer.transfer_applicant_name }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <Phone class="h-4 w-4 text-gray-400" />
                      <span>{{ transfer.transfer_contact_no }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                      <Calendar class="h-4 w-4 text-gray-400" />
                      <span>Transfer date: {{ formatDate(transfer.proposed_transfer_date) }}</span>
                      <Badge v-if="isOverdue(transfer) && transfer.status === 'approved'" class="bg-red-100 text-xs text-red-800"> Overdue </Badge>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                      <div>
                        <div class="font-medium">{{ formatCurrency(transfer.total_cost) }}</div>
                        <div v-if="transfer.balance_amount > 0" class="text-sm text-red-600">
                          Balance: {{ formatCurrency(transfer.balance_amount) }}
                        </div>
                      </div>
                      <Button variant="outline" size="sm" as-child>
                        <Link :href="route('graveyard.niche-transfers.show', transfer.id)">
                          <Eye class="h-4 w-4" />
                        </Link>
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Pagination -->
              <Pagination
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
