<template>
  <AppLayout title="Payment Methods">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Payment Methods</h2>
        <Button @click="showCreateModal = true" class="bg-blue-600 hover:bg-blue-700">
          <Plus class="w-4 h-4 mr-2" />
          Add Payment Method
        </Button>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <DollarSign class="h-8 w-8 text-blue-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Payment Methods</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <CheckCircle class="h-8 w-8 text-green-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.active }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <X class="h-8 w-8 text-red-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Inactive</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.inactive }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
          <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1">
                <Input
                  v-model="filters.search"
                  placeholder="Search payment methods..."
                  class="w-full"
                  @input="debouncedSearch"
                />
              </div>
              <div class="flex gap-2">
                <SelectInput 
                  v-model="filters.status" 
                  :options="[
                    { id: '', name: 'All Status' },
                    { id: 'active', name: 'Active' },
                    { id: 'inactive', name: 'Inactive' }
                  ]"
                  class="w-40"
                />
                <Button @click="resetFilters" variant="outline">Reset</Button>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div v-if="paymentMethods.data && paymentMethods.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Description
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Sort Order
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="paymentMethod in paymentMethods.data" :key="paymentMethod.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ paymentMethod.name }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ paymentMethod.description || 'No description' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        paymentMethod.is_active
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ paymentMethod.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ paymentMethod.sort_order || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(paymentMethod.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                      <Button
                        @click="viewPaymentMethod(paymentMethod)"
                        variant="outline"
                        size="sm"
                      >
                        <Eye class="w-4 h-4" />
                      </Button>
                      <Button
                        @click="editPaymentMethod(paymentMethod)"
                        variant="outline"
                        size="sm"
                      >
                        <Pencil class="w-4 h-4" />
                      </Button>
                      <Button
                        @click="deletePaymentMethod(paymentMethod)"
                        variant="outline"
                        size="sm"
                        class="text-red-600 hover:text-red-700"
                      >
                        <Trash2 class="w-4 h-4" />
                      </Button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- No Data Message -->
          <div v-else class="p-8 text-center">
            <div class="text-gray-500">
              <p class="text-lg font-medium">No payment methods found</p>
              <p class="text-sm mt-2">Get started by creating your first payment method.</p>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="paymentMethods.links && paymentMethods.links.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link
                  v-if="paymentMethods.links[0] && paymentMethods.links[0].url"
                  :href="paymentMethods.links[0].url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link
                  v-if="paymentMethods.links[paymentMethods.links.length - 1] && paymentMethods.links[paymentMethods.links.length - 1].url"
                  :href="paymentMethods.links[paymentMethods.links.length - 1].url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ paymentMethods.from || 0 }}</span>
                    to
                    <span class="font-medium">{{ paymentMethods.to || 0 }}</span>
                    of
                    <span class="font-medium">{{ paymentMethods.total || 0 }}</span>
                    results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <template v-for="(link, index) in paymentMethods.links" :key="index">
                      <Link
                        v-if="link && link.url"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                          link.active
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        ]"
                      />
                    </template>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingPaymentMethod ? 'Edit Payment Method' : 'Add Payment Method' }}
          </h3>
          <form @submit.prevent="savePaymentMethod" class="space-y-4">
            <div>
              <Label for="name">Name</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                required
                placeholder="Enter payment method name"
              />
            </div>

            <div>
              <Label for="description">Description</Label>
              <Textarea
                id="description"
                v-model="form.description"
                placeholder="Enter description (optional)"
                rows="3"
              />
            </div>

            <div class="flex items-center space-x-2">
              <Checkbox
                id="is_active"
                v-model:checked="form.is_active"
              />
              <Label for="is_active">Active</Label>
            </div>

            <div>
              <Label for="sort_order">Sort Order</Label>
              <Input
                id="sort_order"
                v-model="form.sort_order"
                type="number"
                min="0"
                placeholder="0"
              />
            </div>

            <div class="flex justify-end space-x-2">
              <Button type="button" variant="outline" @click="closeModal">
                Cancel
              </Button>
              <Button type="submit" :disabled="saving">
                {{ saving ? 'Saving...' : (editingPaymentMethod ? 'Update' : 'Create') }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Payment Method</h3>
          <div class="space-y-4">
            <p class="text-gray-600">
              Are you sure you want to delete "{{ deletingPaymentMethod?.name }}"? This action cannot be undone.
            </p>
            <div class="flex justify-end space-x-2">
              <Button variant="outline" @click="showDeleteModal = false">
                Cancel
              </Button>
              <Button variant="destructive" @click="confirmDelete" :disabled="deleting">
                {{ deleting ? 'Deleting...' : 'Delete' }}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Textarea from '@/components/ui/textarea/Textarea.vue'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue'
import SelectInput from '@/components/ui/select/SelectInput.vue'
import { Link } from '@inertiajs/vue3'
import { Plus, Eye, Pencil, Trash2, DollarSign, CheckCircle, X } from 'lucide-vue-next'

interface PaymentMethod {
  id: number
  name: string
  description: string | null
  is_active: boolean
  sort_order: number | null
  created_at: string
  updated_at: string
}

interface Stats {
  total: number
  active: number
  inactive: number
}

interface Filters {
  search: string
  status: string
}

interface Props {
  paymentMethods: {
    data: PaymentMethod[]
    links: any[]
    from?: number
    to?: number
    total?: number
  }
  stats: Stats
  filters: Filters
}

const props = defineProps<Props>()

// Reactive state
const showCreateModal = ref(false)
const showDeleteModal = ref(false)
const editingPaymentMethod = ref<PaymentMethod | null>(null)
const deletingPaymentMethod = ref<PaymentMethod | null>(null)
const saving = ref(false)
const deleting = ref(false)

const form = reactive({
  name: '',
  description: '',
  is_active: true,
  sort_order: 0
})

const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || ''
})

// Methods
function openCreateModal() {
  editingPaymentMethod.value = null
  resetForm()
  showCreateModal.value = true
}

function editPaymentMethod(paymentMethod: PaymentMethod) {
  editingPaymentMethod.value = paymentMethod
  form.name = paymentMethod.name
  form.description = paymentMethod.description || ''
  form.is_active = paymentMethod.is_active
  form.sort_order = paymentMethod.sort_order || 0
  showCreateModal.value = true
}

function viewPaymentMethod(paymentMethod: PaymentMethod) {
  router.visit(`/fund/payment-methods/${paymentMethod.id}`)
}

function deletePaymentMethod(paymentMethod: PaymentMethod) {
  deletingPaymentMethod.value = paymentMethod
  showDeleteModal.value = true
}

function confirmDelete() {
  if (!deletingPaymentMethod.value) return
  
  deleting.value = true
  router.delete(`/fund/payment-methods/${deletingPaymentMethod.value.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false
      deletingPaymentMethod.value = null
      deleting.value = false
    },
    onError: () => {
      deleting.value = false
    }
  })
}

function savePaymentMethod() {
  saving.value = true
  
  if (editingPaymentMethod.value) {
    router.put(`/fund/payment-methods/${editingPaymentMethod.value.id}`, form, {
      onSuccess: () => {
        closeModal()
        saving.value = false
      },
      onError: () => {
        saving.value = false
      }
    })
  } else {
    router.post('/fund/payment-methods', form, {
      onSuccess: () => {
        closeModal()
        saving.value = false
      },
      onError: () => {
        saving.value = false
      }
    })
  }
}

function closeModal() {
  showCreateModal.value = false
  editingPaymentMethod.value = null
  resetForm()
}

function resetForm() {
  form.name = ''
  form.description = ''
  form.is_active = true
  form.sort_order = 0
}

function resetFilters() {
  filters.search = ''
  filters.status = ''
  router.visit('/fund/payment-methods', {
    data: { search: '', status: '' },
    preserveState: false
  })
}

function debouncedSearch() {
  router.visit('/fund/payment-methods', {
    data: filters,
    preserveState: true
  })
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString()
}

// Watch for filter changes
watch(filters, () => {
  debouncedSearch()
}, { deep: true })
</script>
