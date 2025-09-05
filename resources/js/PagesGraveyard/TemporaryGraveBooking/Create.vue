<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Clock, MapPin, Phone, User } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface ServiceType {
  id: number;
  name: string;
  cost: number;
  description?: string;
}

interface TemporaryGrave {
  id: number;
  grave_no: string;
  section: string;
  row_no: string;
  is_available: boolean;
}

interface Gender {
  id: number;
  name: string;
}

interface Parish {
  id: number;
  name: string;
}

interface Props {
  serviceTypes: ServiceType[];
  availableGraves: TemporaryGrave[];
  genders: Gender[];
  parishes: Parish[];
}

const props = defineProps<Props>();

const form = useForm({
  temporary_grave_id: null as number | null,
  dead_first_name: '',
  dead_last_name: '',
  date_of_birth: '',
  age: null as number | null,
  months: null as number | null,
  days: null as number | null,
  died_on: '',
  buried_on: '',
  gender_id: null as number | null,
  cause_of_death: '',
  nationality: 'Indian',
  parish_id: null as number | null,
  minister: '',
  applicant_type: 'non_member' as 'member' | 'non_member',
  applicant_name: '',
  contact_no: '',
  contact_email: '',
  relationship_to_deceased: '',
  permit_no: '',
  selected_services: [] as number[],
  duration_months: 12,
  special_requirements: '',
});

// Service selection
const selectedServiceIds = ref<number[]>([]);

// Calculate total cost
const totalCost = computed(() => {
  return selectedServiceIds.value.reduce((total, serviceId) => {
    const service = props.serviceTypes.find((s) => s.id === serviceId);
    return total + (service?.cost || 0);
  }, 0);
});

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  })
    .format(amount)
    .replace('₹', '₹ ');
};

// Watch for service selection changes
watch(
  selectedServiceIds,
  (newIds) => {
    form.selected_services = newIds;
  },
  { deep: true },
);

// Calculate expected transfer date based on buried_on and duration
const expectedTransferDate = computed(() => {
  if (form.buried_on && form.duration_months) {
    const burialDate = new Date(form.buried_on);
    burialDate.setMonth(burialDate.getMonth() + form.duration_months);
    return burialDate.toLocaleDateString('en-IN');
  }
  return null;
});

const submit = () => {
  form.post(route('graveyard.temporary-grave-bookings.store'));
};
</script>

<template>
  <Head title="Book Temporary Grave" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center space-x-3">
                  <Button variant="outline" size="sm" as-child>
                    <Link :href="route('graveyard.temporary-grave-bookings.index')">
                      <ArrowLeft class="h-4 w-4" />
                    </Link>
                  </Button>
                  <div>
                    <h3 class="text-base leading-6 font-semibold text-gray-900">Book Temporary Grave</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Create a new temporary grave booking</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="space-y-6 px-4 py-5 sm:p-6">
            <!-- Grave Selection -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <MapPin class="h-5 w-5" />
                  <span>Select Temporary Grave</span>
                </CardTitle>
                <CardDescription> Choose an available temporary grave </CardDescription>
              </CardHeader>
              <CardContent>
                <div>
                  <Label for="temporary_grave_id">Available Graves *</Label>
                  <Select v-model="form.temporary_grave_id">
                    <SelectTrigger class="mt-1" :class="form.errors.temporary_grave_id && 'border-red-500'">
                      <SelectValue placeholder="Select a grave" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="grave in availableGraves" :key="grave.id" :value="grave.id">
                        {{ grave.grave_no }} - {{ grave.section }}, Row {{ grave.row_no }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <div v-if="form.errors.temporary_grave_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.temporary_grave_id }}
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Deceased Person Details -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <User class="h-5 w-5" />
                  <span>Deceased Person Details</span>
                </CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="dead_first_name">First Name *</Label>
                    <Input
                      id="dead_first_name"
                      v-model="form.dead_first_name"
                      :class="form.errors.dead_first_name && 'border-red-500'"
                      class="mt-1"
                    />
                    <div v-if="form.errors.dead_first_name" class="mt-1 text-sm text-red-600">
                      {{ form.errors.dead_first_name }}
                    </div>
                  </div>

                  <div>
                    <Label for="dead_last_name">Last Name *</Label>
                    <Input id="dead_last_name" v-model="form.dead_last_name" :class="form.errors.dead_last_name && 'border-red-500'" class="mt-1" />
                    <div v-if="form.errors.dead_last_name" class="mt-1 text-sm text-red-600">
                      {{ form.errors.dead_last_name }}
                    </div>
                  </div>

                  <div>
                    <Label for="date_of_birth">Date of Birth</Label>
                    <Input id="date_of_birth" v-model="form.date_of_birth" type="date" class="mt-1" />
                  </div>

                  <div>
                    <Label for="gender_id">Gender *</Label>
                    <Select v-model="form.gender_id">
                      <SelectTrigger class="mt-1" :class="form.errors.gender_id && 'border-red-500'">
                        <SelectValue placeholder="Select gender" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem v-for="gender in genders" :key="gender.id" :value="gender.id">
                          {{ gender.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                    <div v-if="form.errors.gender_id" class="mt-1 text-sm text-red-600">
                      {{ form.errors.gender_id }}
                    </div>
                  </div>
                </div>

                <!-- Age Details -->
                <div>
                  <Label class="text-base font-medium">Age at Death</Label>
                  <div class="mt-2 grid grid-cols-3 gap-4">
                    <div>
                      <Label for="age">Years</Label>
                      <Input id="age" :model-value="form.age ?? ''" @input="form.age = $event.target.value ? Number($event.target.value) : null" type="number" min="0" max="150" class="mt-1" />
                    </div>
                    <div>
                      <Label for="months">Months</Label>
                      <Input id="months" :model-value="form.months ?? ''" @input="form.months = $event.target.value ? Number($event.target.value) : null" type="number" min="0" max="11" class="mt-1" />
                    </div>
                    <div>
                      <Label for="days">Days</Label>
                      <Input id="days" :model-value="form.days ?? ''" @input="form.days = $event.target.value ? Number($event.target.value) : null" type="number" min="0" max="30" class="mt-1" />
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="nationality">Nationality</Label>
                    <Input id="nationality" v-model="form.nationality" class="mt-1" />
                  </div>

                  <div>
                    <Label for="parish_id">Parish</Label>
                    <Select v-model="form.parish_id">
                      <SelectTrigger class="mt-1">
                        <SelectValue placeholder="Select parish" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem v-for="parish in parishes" :key="parish.id" :value="parish.id">
                          {{ parish.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Death & Burial Details -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <Calendar class="h-5 w-5" />
                  <span>Death & Burial Details</span>
                </CardTitle>
              </CardHeader>
              <CardContent class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <Label for="died_on">Date of Death *</Label>
                  <Input id="died_on" v-model="form.died_on" type="date" :class="form.errors.died_on && 'border-red-500'" class="mt-1" />
                  <div v-if="form.errors.died_on" class="mt-1 text-sm text-red-600">
                    {{ form.errors.died_on }}
                  </div>
                </div>

                <div>
                  <Label for="buried_on">Date of Burial *</Label>
                  <Input id="buried_on" v-model="form.buried_on" type="date" :class="form.errors.buried_on && 'border-red-500'" class="mt-1" />
                  <div v-if="form.errors.buried_on" class="mt-1 text-sm text-red-600">
                    {{ form.errors.buried_on }}
                  </div>
                </div>

                <div class="sm:col-span-2">
                  <Label for="cause_of_death">Cause of Death *</Label>
                  <Input id="cause_of_death" v-model="form.cause_of_death" :class="form.errors.cause_of_death && 'border-red-500'" class="mt-1" />
                  <div v-if="form.errors.cause_of_death" class="mt-1 text-sm text-red-600">
                    {{ form.errors.cause_of_death }}
                  </div>
                </div>

                <div class="sm:col-span-2">
                  <Label for="minister">Minister</Label>
                  <Input id="minister" v-model="form.minister" class="mt-1" />
                </div>
              </CardContent>
            </Card>

            <!-- Duration & Transfer -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <Clock class="h-5 w-5" />
                  <span>Duration & Transfer</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="duration_months">Duration (Months)</Label>
                    <Select v-model="form.duration_months">
                      <SelectTrigger class="mt-1">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem :value="6">6 Months</SelectItem>
                        <SelectItem :value="12">12 Months (Default)</SelectItem>
                        <SelectItem :value="18">18 Months</SelectItem>
                        <SelectItem :value="24">24 Months</SelectItem>
                      </SelectContent>
                    </Select>
                    <p class="mt-1 text-sm text-gray-500">Duration before transfer to permanent grave or niche is required</p>
                  </div>

                  <div v-if="expectedTransferDate">
                    <Label>Expected Transfer Date</Label>
                    <div class="mt-1 rounded-lg border border-blue-200 bg-blue-50 p-2">
                      <p class="text-sm font-medium text-blue-900">
                        {{ expectedTransferDate }}
                      </p>
                      <p class="text-xs text-blue-700">Based on burial date and duration</p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Applicant Details -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <Phone class="h-5 w-5" />
                  <span>Applicant Details</span>
                </CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div>
                  <Label for="applicant_type">Applicant Type *</Label>
                  <Select v-model="form.applicant_type">
                    <SelectTrigger class="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="member">Church Member</SelectItem>
                      <SelectItem value="non_member">Non-Member</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="applicant_name">Applicant Name *</Label>
                    <Input id="applicant_name" v-model="form.applicant_name" :class="form.errors.applicant_name && 'border-red-500'" class="mt-1" />
                    <div v-if="form.errors.applicant_name" class="mt-1 text-sm text-red-600">
                      {{ form.errors.applicant_name }}
                    </div>
                  </div>

                  <div>
                    <Label for="contact_no">Contact Number *</Label>
                    <Input id="contact_no" v-model="form.contact_no" :class="form.errors.contact_no && 'border-red-500'" class="mt-1" />
                    <div v-if="form.errors.contact_no" class="mt-1 text-sm text-red-600">
                      {{ form.errors.contact_no }}
                    </div>
                  </div>

                  <div>
                    <Label for="contact_email">Email</Label>
                    <Input id="contact_email" v-model="form.contact_email" type="email" class="mt-1" />
                  </div>

                  <div>
                    <Label for="relationship_to_deceased">Relationship to Deceased</Label>
                    <Input id="relationship_to_deceased" v-model="form.relationship_to_deceased" class="mt-1" />
                  </div>

                  <div class="sm:col-span-2">
                    <Label for="permit_no">BMC Permit Number</Label>
                    <Input id="permit_no" v-model="form.permit_no" class="mt-1" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Services Selection -->
            <Card>
              <CardHeader>
                <CardTitle>Additional Services</CardTitle>
                <CardDescription> Select any additional services required for the burial </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div v-for="service in serviceTypes" :key="service.id" class="flex items-start space-x-3 rounded-lg border p-3">
                    <Checkbox
                      :id="`service-${service.id}`"
                      :checked="selectedServiceIds.includes(service.id)"
                      @update:checked="
                        (checked: boolean) => {
                          if (checked) {
                            selectedServiceIds.push(service.id);
                          } else {
                            const index = selectedServiceIds.indexOf(service.id);
                            if (index > -1) selectedServiceIds.splice(index, 1);
                          }
                        }
                      "
                    />
                    <div class="flex-1">
                      <Label :for="`service-${service.id}`" class="cursor-pointer">
                        <div class="flex items-start justify-between">
                          <div>
                            <span class="font-medium">{{ service.name }}</span>
                            <p v-if="service.description" class="mt-1 text-sm text-gray-600">
                              {{ service.description }}
                            </p>
                          </div>
                          <span class="font-medium text-green-600">
                            {{ formatCurrency(service.cost) }}
                          </span>
                        </div>
                      </Label>
                    </div>
                  </div>
                </div>

                <!-- Total Cost Display -->
                <div v-if="totalCost > 0" class="mt-4 border-t pt-4">
                  <div class="flex items-center justify-between">
                    <span class="text-lg font-medium">Total Cost:</span>
                    <span class="text-xl font-bold text-green-600">
                      {{ formatCurrency(totalCost) }}
                    </span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Special Requirements -->
            <Card>
              <CardHeader>
                <CardTitle>Additional Information</CardTitle>
              </CardHeader>
              <CardContent>
                <div>
                  <Label for="special_requirements">Special Requirements</Label>
                  <Textarea
                    id="special_requirements"
                    v-model="form.special_requirements"
                    rows="3"
                    placeholder="Any special requirements or notes..."
                    class="mt-1"
                  />
                </div>
              </CardContent>
            </Card>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-5">
              <Button variant="outline" as-child>
                <Link :href="route('graveyard.temporary-grave-bookings.index')"> Cancel </Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Creating Booking...' : 'Create Booking' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
