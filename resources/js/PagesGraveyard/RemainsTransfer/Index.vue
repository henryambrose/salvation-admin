<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Pencil, Phone, Plus, Search, User } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
    id: number;
    block: string;
    row: number;
    column: number;
    owner_name?: string;
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
    search?: string;
    transfer_type?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const transferType = ref(props.filters.transfer_type || '');

const applyFilters = () => {
  router.get(
    route('graveyard.remains-transfers.index'),
    { search: search.value, transfer_type: transferType.value },
    { preserveState: true, replace: true },
  );
};

const clearFilters = () => {
  search.value = '';
  transferType.value = '';
  applyFilters();
};

let searchDebounce: ReturnType<typeof setTimeout> | null = null;

watch(search, (val) => {
  if (searchDebounce) clearTimeout(searchDebounce);
  if (val.length >= 2 || val.length === 0) {
    searchDebounce = setTimeout(applyFilters, 300);
  }
});

const transferTypeLabel = (type: string) => {
  if (type === 'niche') return 'Niche Transfer';
  if (type === 'permanent_grave') return 'Permanent Transfer';
  if (type === 'removal') return 'Remains Removed';
  return type;
};

const transferTypeBadgeClass = (type: string) => {
  if (type === 'niche') return 'bg-blue-100 text-blue-700';
  if (type === 'permanent_grave') return 'bg-purple-100 text-purple-700';
  if (type === 'removal') return 'bg-orange-100 text-orange-700';
  return 'bg-gray-100 text-gray-700';
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
            <div class="flex flex-wrap gap-3">
              <div class="relative min-w-0 flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <Search class="h-5 w-5 text-gray-400" />
                </div>
                <Input
                  id="search"
                  v-model="search"
                  placeholder="Search by deceased name, reference, or applicant..."
                  class="pl-10"
                />
              </div>
              <select
                v-model="transferType"
                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              >
                <option value="">All Types</option>
                <option value="niche">Niche Transfer</option>
                <option value="permanent_grave">Permanent Transfer</option>
                <option value="removal">Remains Removed</option>
              </select>
              <Button variant="outline" @click="clearFilters">Clear</Button>
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
              <div class="hidden sm:block overflow-x-auto">
                <Table class="min-w-full">
                  <TableHeader>
                    <TableRow>
                      <TableHead class="min-w-[220px] whitespace-nowrap font-semibold text-gray-700">Transfer Details</TableHead>
                      <TableHead class="min-w-[220px] whitespace-nowrap font-semibold text-gray-700">From → To</TableHead>
                      <TableHead class="min-w-[180px] whitespace-nowrap font-semibold text-gray-700">Deceased</TableHead>
                      <TableHead class="min-w-[180px] whitespace-nowrap font-semibold text-gray-700">Applicant</TableHead>
                      <TableHead class="min-w-[130px] whitespace-nowrap font-semibold text-gray-700">Transfer Date</TableHead>
                      <TableHead class="w-16 text-right font-semibold text-gray-700">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="transfer in transfers.data" :key="transfer.id">
                      <TableCell class="align-top">
                        <div class="whitespace-nowrap">
                          <div class="font-medium text-gray-900">#{{ transfer.transfer_reference }}</div>
                          <div class="mt-1">
                            <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', transferTypeBadgeClass(transfer.transfer_type)]">
                              {{ transferTypeLabel(transfer.transfer_type) }}
                            </span>
                          </div>
                          <div class="mt-1 max-w-[200px] truncate text-sm text-gray-500">
                            {{ transfer.transfer_reason }}
                          </div>
                          <div class="text-xs text-gray-400">By {{ transfer.creator.name }}</div>
                        </div>
                      </TableCell>
                      <TableCell class="align-top">
                        <div class="space-y-1 whitespace-nowrap">
                          <div class="text-sm">
                            <span class="font-medium">{{ transfer.from_temporary_grave.grave_no }}</span>
                            <span class="text-gray-500"> → </span>
                            <span v-if="transfer.transfer_type === 'niche' && transfer.to_niche" class="font-medium">{{ transfer.to_niche.niche_no }}</span>
                            <span v-else-if="transfer.transfer_type === 'permanent_grave' && transfer.to_permanent_grave" class="font-medium">Block {{ transfer.to_permanent_grave.block }}-{{ transfer.to_permanent_grave.row }}-{{ transfer.to_permanent_grave.column }}</span>
                            <span v-else class="italic text-gray-400">Removal</span>
                          </div>
                          <div class="text-xs text-gray-500">
                            {{ transfer.from_temporary_grave.section }}
                            <template v-if="transfer.transfer_type === 'niche' && transfer.to_niche"> → {{ transfer.to_niche.section }}</template>
                            <template v-else-if="transfer.transfer_type === 'permanent_grave' && transfer.to_permanent_grave"> → Block {{ transfer.to_permanent_grave.block }}</template>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell class="align-top">
                        <div class="whitespace-nowrap">
                          <div class="font-medium text-gray-900">{{ getDeceasedName(transfer) }}</div>
                          <div class="text-sm text-gray-500">Buried: {{ formatDate(transfer.from_booking.buried_on) }}</div>
                        </div>
                      </TableCell>
                      <TableCell class="align-top">
                        <div class="whitespace-nowrap">
                          <div class="font-medium text-gray-900">{{ transfer.applicant_name }}</div>
                          <div class="text-sm text-gray-500">{{ transfer.contact_no }}</div>
                        </div>
                      </TableCell>
                      <TableCell class="align-top">
                        <div class="whitespace-nowrap text-sm">
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
                    <div class="flex items-start justify-between">
                      <CardTitle class="text-base">#{{ transfer.transfer_reference }}</CardTitle>
                      <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', transferTypeBadgeClass(transfer.transfer_type)]">
                        {{ transferTypeLabel(transfer.transfer_type) }}
                      </span>
                    </div>
                    <CardDescription> {{ getDeceasedName(transfer) }} • {{ formatDate(transfer.proposed_transfer_date) }} </CardDescription>
                  </CardHeader>
                  <CardContent class="space-y-3">
                    <div class="flex items-center space-x-2 text-sm">
                      <ArrowRight class="h-4 w-4 text-gray-400" />
                      <span>
                        {{ transfer.from_temporary_grave.grave_no }} →
                        <template v-if="transfer.transfer_type === 'niche' && transfer.to_niche">{{ transfer.to_niche.niche_no }}</template>
                        <template v-else-if="transfer.transfer_type === 'permanent_grave' && transfer.to_permanent_grave">Block {{ transfer.to_permanent_grave.block }}-{{ transfer.to_permanent_grave.row }}-{{ transfer.to_permanent_grave.column }}</template>
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
