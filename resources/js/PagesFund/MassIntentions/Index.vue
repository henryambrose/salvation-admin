<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Mass Intentions</h1>
            <Link 
              :href="route('fund.mass-intentions.create')"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Book New Intention
            </Link>
          </div>

          <!-- Filters -->
          <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input 
                  v-model="filters.search" 
                  type="text" 
                  placeholder="Search intentions, notes, or member names..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                  v-model="filters.status" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">All Statuses</option>
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mass Type</label>
                <select 
                  v-model="filters.mass_type_id" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">All Types</option>
                  <option v-for="type in massTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intention Type</label>
                <select 
                  v-model="filters.mass_intention_type_id" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">All Types</option>
                  <option v-for="type in massIntentionTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input 
                  v-model="filters.start_date" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input 
                  v-model="filters.end_date" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              
              <div class="flex items-end">
                <button 
                  @click="clearFilters"
                  class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div v-if="massIntentions.data && massIntentions.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                 <tr>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Person</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Family No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mass Date & Type</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                 </tr>
               </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="intention in massIntentions.data" :key="intention.id" class="hover:bg-gray-50">
                                     <td class="px-6 py-4 whitespace-nowrap">
                     <div class="flex space-x-2">
                       <Link 
                         :href="route('fund.mass-intentions.show', intention.id)"
                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200"
                       >
                         <Eye class="w-3 h-3 mr-1" />
                         View
                       </Link>
                       <Link 
                         :href="route('fund.mass-intentions.edit', intention.id)"
                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200"
                       >
                         <Pencil class="w-3 h-3 mr-1" />
                         Edit
                       </Link>
                     </div>
                   </td>
                                       <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        <div v-if="intention.member">
                          {{ intention.member.first_name }} {{ intention.member.last_name }}
                        </div>
                        <div v-else>
                          {{ intention.non_member_name }}
                        </div>
                      </div>
                                            <div v-if="intention.special_instructions" class="text-sm text-gray-500">{{ intention.special_instructions }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        <div v-if="intention.member">
                          {{ intention.member.family_no }}
                        </div>
                        <div v-else class="text-gray-400">
                          -
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(intention.mass_date) }}</div>
                    <div v-if="intention.mass_type" class="text-sm text-gray-500">{{ intention.mass_type.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ intention.mass_intention_type.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">₹{{ intention.amount }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(intention.status)">
                      {{ intention.status }}
                    </span>
                  </td>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                     {{ formatDate(intention.created_at) }}
                   </td>
                   <td class="px-6 py-4 whitespace-nowrap">
                     <button 
                       @click="deleteIntention(intention.id)"
                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200"
                     >
                       <Trash2 class="w-3 h-3 mr-1" />
                       Delete
                     </button>
                   </td>
                 </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ massIntentions.from }} to {{ massIntentions.to }} of {{ massIntentions.total }} results
              </div>
              <div class="flex space-x-2">
                <Link 
                  v-if="massIntentions.prev_page_url"
                  :href="massIntentions.prev_page_url"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link 
                  v-if="massIntentions.next_page_url"
                  :href="massIntentions.next_page_url"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-gray-50 p-8 rounded-lg text-center">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No mass intentions found</h3>
              <p class="mt-1 text-sm text-gray-500">
                {{ filters.search || filters.status || filters.mass_type_id || filters.start_date || filters.end_date 
                   ? 'Try adjusting your filters or search terms.' 
                   : 'Get started by booking the first mass intention.' }}
              </p>
              <div class="mt-6">
                <Link 
                  :href="route('fund.mass-intentions.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Book Intention
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout
});

interface Props {
  massIntentions: any;
  massTypes: any[];
  massIntentionTypes: any[];
  filters: {
    search?: string;
    status?: string;
    mass_type_id?: string;
    mass_intention_type_id?: string;
    start_date?: string;
    end_date?: string;
    per_page?: number;
  };
}

const props = defineProps<Props>();

const { massIntentions, massTypes, massIntentionTypes, filters: initialFilters } = props;

// Local filters state
const filters = ref({
  search: initialFilters.search || '',
  status: initialFilters.status || '',
  mass_type_id: initialFilters.mass_type_id || '',
  mass_intention_type_id: initialFilters.mass_intention_type_id || '',
  start_date: initialFilters.start_date || '',
  end_date: initialFilters.end_date || '',
  per_page: initialFilters.per_page || 15
});

// Watch for filter changes and update the page
watch(filters, (newFilters) => {
  router.get(route('fund.mass-intentions.index'), newFilters, {
    preserveState: true,
    replace: true
  });
}, { deep: true });

// Clear all filters
function clearFilters() {
  filters.value = {
    search: '',
    status: '',
    mass_type_id: '',
    mass_intention_type_id: '',
    start_date: '',
    end_date: '',
    per_page: 15
  };
}

// Delete intention
function deleteIntention(id: number) {
  if (confirm('Are you sure you want to delete this mass intention?')) {
    router.delete(route('fund.mass-intentions.destroy', id));
  }
}

// Utility functions
function formatDate(dateString: string) {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
}



function getStatusBadgeClass(status: string) {
  const classes = {
    'pending': 'px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full',
    'confirmed': 'px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full',
    'completed': 'px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full',
    'cancelled': 'px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full'
  };
  return classes[status as keyof typeof classes] || classes.pending;
}
</script>
