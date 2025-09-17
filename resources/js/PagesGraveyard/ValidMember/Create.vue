<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create Valid Members" />

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center space-x-3">
                  <Button variant="outline" size="sm" as-child>
                    <Link href="/graveyard/valid-members">
                      <ArrowLeft class="h-4 w-4" />
                    </Link>
                  </Button>
                  <div>
                    <h3 class="text-base leading-6 font-semibold text-gray-900">Add Valid Members</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Add up to 5 members who are authorized to use this grave</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="px-4 py-5 sm:p-6">
            <div class="space-y-6">
              <!-- Grave Selection Card -->
              <Card>
                <CardHeader>
                  <CardTitle class="flex items-center space-x-2">
                    <MapPin class="h-5 w-5" />
                    <span>Select Grave</span>
                  </CardTitle>
                  <CardDescription>Choose the grave for which you want to add valid members</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                  <!-- Grave Type Selection -->
                  <div>
                    <Label class="text-base font-medium">Grave Type</Label>
                    <div class="mt-2 flex items-center space-x-6">
                      <label class="flex items-center">
                        <input
                          v-model="formData.grave_type"
                          type="radio"
                          value="permanent_grave"
                          class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">Permanent Grave</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="formData.grave_type"
                          type="radio"
                          value="niche"
                          class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">Niche</span>
                      </label>
                    </div>
                    <div v-if="form.errors.grave_type" class="mt-1 text-sm text-red-600">
                      {{ form.errors.grave_type }}
                    </div>
                  </div>

                  <!-- Grave Selection -->
                  <div v-if="formData.grave_type" class="space-y-4">
                    <Label>{{ formData.grave_type === 'permanent_grave' ? 'Search Permanent Grave' : 'Search Niche' }}</Label>

                    <!-- Selected Grave Display -->
                    <div v-if="selectedGrave" class="rounded-lg border border-green-200 bg-green-50 p-4">
                      <div class="flex items-start justify-between">
                        <div>
                          <h4 class="font-medium text-green-900">{{ selectedGrave.display_name }}</h4>
                          <div class="mt-2 space-y-1">
                            <p class="text-sm text-green-700">Owner: {{ selectedGrave.details.owner_name }}</p>
                            <p v-if="selectedGrave.details.member" class="text-sm text-blue-600">
                              Member: {{ selectedGrave.details.member.full_name }} ({{ selectedGrave.details.member.family_no }})
                            </p>
                            <p v-if="selectedGrave.details.contact_no" class="text-sm text-gray-500">
                              Contact: {{ selectedGrave.details.contact_no }}
                            </p>
                          </div>
                        </div>
                        <Button type="button" variant="outline" size="sm" @click="clearSelection">
                          <X class="h-4 w-4" />
                        </Button>
                      </div>
                    </div>

                    <!-- Search Input -->
                    <div v-else class="space-y-2">
                      <div class="relative">
                        <Input
                          v-model="graveSearchTerm"
                          type="text"
                          :placeholder="
                            formData.grave_type === 'permanent_grave'
                              ? 'Search by owner, contact, grave number, member name...'
                              : 'Search by niche number, location, owner, contact, member name...'
                          "
                          class="pr-10"
                          @input="debouncedSearchGraves"
                          @focus="showDropdown = true"
                        />
                        <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                          <div class="h-4 w-4 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"></div>
                        </div>
                      </div>
                      <p class="text-xs text-gray-500">Start typing to search (minimum 2 characters)</p>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div
                      v-if="showDropdown && searchResults.length > 0"
                      class="max-h-60 space-y-2 overflow-y-auto rounded-lg border border-gray-300 bg-white p-2 shadow-lg"
                    >
                      <div
                        v-for="result in searchResults"
                        :key="result.id"
                        class="cursor-pointer rounded-lg border border-gray-200 p-3 transition-colors hover:border-blue-300 hover:bg-blue-50"
                        @click="selectGrave(result)"
                      >
                        <h4 class="font-medium text-gray-900">{{ result.display_name }}</h4>
                        <p class="text-sm text-gray-600">Owner: {{ result.details.owner_name }}</p>
                        <p v-if="result.details.member" class="text-sm text-blue-600">
                          Member: {{ result.details.member.full_name }} ({{ result.details.member.family_no }})
                        </p>
                        <p v-if="result.details.contact_no" class="text-sm text-gray-500">Contact: {{ result.details.contact_no }}</p>
                      </div>
                    </div>

                    <!-- No Results -->
                    <div
                      v-if="graveSearchTerm.length >= 2 && !isSearching && searchResults.length === 0 && showDropdown"
                      class="rounded-lg border border-gray-300 bg-white p-4 shadow-lg"
                    >
                      <p class="text-center text-sm text-gray-500">
                        No {{ formData.grave_type === 'permanent_grave' ? 'permanent graves' : 'niches' }} found for "{{ graveSearchTerm }}"
                      </p>
                    </div>

                    <div v-if="form.errors.permanent_grave_id || form.errors.niche_id" class="text-sm text-red-600">
                      {{ form.errors.permanent_grave_id || form.errors.niche_id }}
                    </div>
                  </div>
                </CardContent>
              </Card>

              <!-- Members Section -->
              <Card>
                <CardHeader>
                  <div class="flex items-center justify-between">
                    <div>
                      <CardTitle class="flex items-center space-x-2">
                        <Users class="h-5 w-5" />
                        <span>Valid Members ({{ formData.members.length }}/5)</span>
                      </CardTitle>
                      <CardDescription>Add up to 5 members who are authorized to use this grave</CardDescription>
                    </div>
                    <Button type="button" @click="addMember" :disabled="formData.members.length >= 5" size="sm">
                      <Plus class="mr-1 h-4 w-4" />
                      Add Member
                    </Button>
                  </div>
                </CardHeader>
                <CardContent class="space-y-6">
                  <!-- Member Forms -->
                  <div v-for="(member, index) in formData.members" :key="index" class="rounded-lg border border-gray-200 bg-gray-50 p-6">
                    <div class="mb-6 flex items-center justify-between">
                      <h3 class="text-lg font-medium text-gray-900">Member {{ index + 1 }}</h3>
                      <Button v-if="formData.members.length > 1" type="button" variant="outline" size="sm" @click="removeMember(index)">
                        <X class="h-4 w-4" />
                      </Button>
                    </div>

                    <!-- Member Type Selection -->
                    <div class="mb-4">
                      <label class="mb-2 block text-sm font-medium text-gray-700">Member Type</label>
                      <div class="flex gap-4">
                        <label class="flex items-center">
                          <input
                            v-model="member.member_type"
                            type="radio"
                            value="member"
                            class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <span class="ml-2 text-sm text-gray-700">Parish Member</span>
                        </label>
                        <label class="flex items-center">
                          <input
                            v-model="member.member_type"
                            type="radio"
                            value="external"
                            class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <span class="ml-2 text-sm text-gray-700">External Person</span>
                        </label>
                      </div>
                      <div v-if="getFieldError(`members.${index}.member_type`)" class="mt-1 text-sm text-red-600">
                        {{ getFieldError(`members.${index}.member_type`) }}
                      </div>
                    </div>

                    <!-- Parish Member Selection -->
                    <div v-if="member.member_type === 'member'" class="space-y-4">
                      <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Search Parish Member *</label>
                        <div class="relative mt-1">
                          <input
                            v-model="member.member_search"
                            type="text"
                            placeholder="Search by name, family number, or phone..."
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            :class="getFieldError(`members.${index}.member_id`) && 'border-red-500'"
                            @input="searchParishMembers(index, ($event.target as HTMLInputElement).value)"
                          />

                          <!-- Search Results Dropdown -->
                          <div
                            v-if="member.search_results.length > 0 && member.member_search"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-300 bg-white shadow-lg"
                          >
                            <div
                              v-for="result in member.search_results"
                              :key="result.id"
                              @click="selectParishMember(index, result)"
                              class="cursor-pointer border-b border-gray-100 px-4 py-3 last:border-b-0 hover:bg-gray-50"
                            >
                              <div class="font-medium text-gray-900">{{ result.name }}</div>
                              <div class="text-sm text-gray-500">
                                Community No: {{ result.community?.name?.split('-')[0]?.trim() || 'N/A' }} | Family: {{ result.family_no || 'N/A' }} |
                                Member No: {{ result.member_no || 'N/A' }}
                              </div>
                              <div class="text-sm text-gray-500">
                                Address: {{ result.current_add1 || 'N/A' }} | Contact: {{ result.contact_no_1 || 'N/A' }}
                              </div>
                            </div>
                          </div>

                          <!-- Loading State -->
                          <div v-if="member.isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <div class="h-4 w-4 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"></div>
                          </div>
                        </div>

                        <!-- Search Helper Text -->
                        <p class="mt-1 text-xs text-gray-500">Start typing to search (minimum 2 characters)</p>

                        <div v-if="getFieldError(`members.${index}.member_id`)" class="mt-1 text-sm text-red-600">
                          {{ getFieldError(`members.${index}.member_id`) }}
                        </div>
                      </div>

                      <!-- Selected Parish Member Display -->
                      <div v-if="member.selected_member" class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <div class="flex items-start justify-between">
                          <div>
                            <h4 class="font-medium text-blue-900">Selected Member</h4>
                            <div class="mt-2 space-y-1">
                              <div class="font-medium text-blue-800">{{ member.selected_member.name }}</div>
                              <div class="text-sm text-blue-700">
                                Community No: {{ member.selected_member.community?.name?.split('-')[0]?.trim() || 'N/A' }} | Family:
                                {{ member.selected_member.family_no || 'N/A' }}
                              </div>
                              <div class="text-sm text-blue-700">Member No: {{ member.selected_member.member_no || 'N/A' }}</div>
                              <div class="text-sm text-blue-700">Address: {{ member.selected_member.current_add1 || 'N/A' }}</div>
                              <div class="text-sm text-blue-700">Contact: {{ member.selected_member.contact_no_1 || 'N/A' }}</div>
                            </div>
                          </div>
                          <button @click="clearParishMemberSelection(index)" type="button" class="text-blue-600 hover:text-blue-800">
                            <X class="h-4 w-4" />
                          </button>
                        </div>
                      </div>

                      <!-- Optional Contact Override -->
                      <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number (optional override)</label>
                        <div class="mt-1">
                          <input
                            v-model="member.contact_no"
                            type="text"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Override contact number if needed"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- External Member Form -->
                    <div v-if="member.member_type === 'external'" class="space-y-4">
                      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">First Name *</label>
                          <div class="mt-1">
                            <input
                              v-model="member.first_name"
                              type="text"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              :class="getFieldError(`members.${index}.first_name`) && 'border-red-500'"
                            />
                            <div v-if="getFieldError(`members.${index}.first_name`)" class="mt-1 text-sm text-red-600">
                              {{ getFieldError(`members.${index}.first_name`) }}
                            </div>
                          </div>
                        </div>

                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Last Name *</label>
                          <div class="mt-1">
                            <input
                              v-model="member.last_name"
                              type="text"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              :class="getFieldError(`members.${index}.last_name`) && 'border-red-500'"
                            />
                            <div v-if="getFieldError(`members.${index}.last_name`)" class="mt-1 text-sm text-red-600">
                              {{ getFieldError(`members.${index}.last_name`) }}
                            </div>
                          </div>
                        </div>

                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                          <div class="mt-1">
                            <input
                              v-model="member.contact_no"
                              type="text"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                          </div>
                        </div>

                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label>
                          <div class="mt-1">
                            <input
                              v-model="member.date_of_birth"
                              type="date"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                          </div>
                        </div>

                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Gender *</label>
                          <div class="mt-1">
                            <select
                              v-model="member.gender_id"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              :class="getFieldError(`members.${index}.gender_id`) && 'border-red-500'"
                            >
                              <option value="">Select Gender</option>
                              <option v-for="gender in genders" :key="gender.id" :value="gender.id.toString()">
                                {{ gender.name }}
                              </option>
                            </select>
                            <div v-if="getFieldError(`members.${index}.gender_id`)" class="mt-1 text-sm text-red-600">
                              {{ getFieldError(`members.${index}.gender_id`) }}
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Age Details -->
                      <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Age at Time of Record</label>
                        <div class="mt-2 grid grid-cols-3 gap-4">
                          <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Years</label>
                            <div class="mt-1">
                              <input
                                :model-value="member.age ?? ''"
                                @input="
                                  member.age = ($event.target as HTMLInputElement)?.value ? Number(($event.target as HTMLInputElement).value) : null
                                "
                                type="number"
                                min="0"
                                max="150"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Months</label>
                            <div class="mt-1">
                              <input
                                :model-value="member.months ?? ''"
                                @input="
                                  member.months = ($event.target as HTMLInputElement)?.value
                                    ? Number(($event.target as HTMLInputElement).value)
                                    : null
                                "
                                type="number"
                                min="0"
                                max="11"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Days</label>
                            <div class="mt-1">
                              <input
                                :model-value="member.days ?? ''"
                                @input="
                                  member.days = ($event.target as HTMLInputElement)?.value ? Number(($event.target as HTMLInputElement).value) : null
                                "
                                type="number"
                                min="0"
                                max="30"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Nationality</label>
                          <div class="mt-1">
                            <input
                              v-model="member.nationality"
                              type="text"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                          </div>
                        </div>

                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Parish</label>
                          <div class="mt-1">
                            <select
                              v-model="member.parish_id"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                              <option value="">Select Parish</option>
                              <option v-for="parish in parishes" :key="parish.id" :value="parish.id.toString()">
                                {{ parish.name }}
                              </option>
                            </select>
                          </div>
                        </div>

                        <div>
                          <label class="mb-1 block text-sm font-medium text-gray-700">Aadhar Number</label>
                          <div class="mt-1">
                            <input
                              v-model="member.aadhar_no"
                              type="text"
                              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              placeholder="Optional"
                            />
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Common Fields for both Member Types -->
                    <div class="space-y-4">
                      <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Relationship to Grave Owner *</label>
                        <div class="mt-1">
                          <select
                            v-model="member.relationship_id"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            :class="getFieldError(`members.${index}.relationship_id`) && 'border-red-500'"
                          >
                            <option value="">Select Relationship</option>
                            <option v-for="relationship in relationships" :key="relationship.id" :value="relationship.id.toString()">
                              {{ relationship.name }}
                            </option>
                          </select>
                          <div v-if="getFieldError(`members.${index}.relationship_id`)" class="mt-1 text-sm text-red-600">
                            {{ getFieldError(`members.${index}.relationship_id`) }}
                          </div>
                        </div>
                      </div>

                      <div>
                        <Label>Additional Notes</Label>
                        <Textarea v-model="member.notes" :rows="3" placeholder="Any additional information about this person..." class="mt-1" />
                      </div>
                    </div>
                  </div>

                  <!-- Add Member Button -->
                  <div v-if="formData.members.length < 5" class="text-center">
                    <Button type="button" variant="outline" @click="addMember">
                      <Plus class="mr-2 h-4 w-4" />
                      Add Another Member
                    </Button>
                  </div>

                  <div v-if="form.errors.members" class="text-sm text-red-600">
                    {{ form.errors.members }}
                  </div>
                </CardContent>
              </Card>

              <!-- Form Actions -->
              <div class="flex items-center justify-between pt-5">
                <Button variant="outline" as-child>
                  <Link href="/graveyard/valid-members">Cancel</Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Creating Valid Members...' : 'Create Valid Members' }}
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, Plus, Users, X } from 'lucide-vue-next';
import { onMounted, reactive, ref, watch } from 'vue';

interface Gender {
  id: number;
  name: string;
}

interface Parish {
  id: number;
  name: string;
}

interface Relationship {
  id: number;
  name: string;
}

interface Props {
  permanentGraves: Array<{
    id: number;
    grave_no: string;
    section: string;
    row_no: string;
    owner_name?: string;
    contact_no?: string;
    member_id?: number;
    member?: {
      id: number;
      full_name: string;
      family_no: string;
    };
  }>;
  niches: Array<{
    id: number;
    niche_no: string;
    location: string;
  }>;
  genders: Gender[];
  parishes: Parish[];
  relationships: Relationship[];
  permanent_grave_id?: number; // Optional prop for pre-selecting a grave
}

// Define interface for parish member search results
interface ParishMemberSearchResult {
  id: number;
  name: string;
  full_name: string;
  member_no: string;
  family_no: string;
  community: {
    name: string;
  };
  current_add1: string;
  contact_no_1: string;
  gender: string;
}

// Define interface for member form data
interface MemberFormData {
  member_type: string;
  member_id: string;
  member_search: string;
  search_results: ParishMemberSearchResult[];
  selected_member: ParishMemberSearchResult | null;
  first_name: string;
  last_name: string;
  date_of_birth: string;
  age: number | null;
  months: number | null;
  days: number | null;
  gender_id: string;
  nationality: string;
  parish_id: string;
  contact_no: string;
  aadhar_no: string;
  relationship_id: string;
  notes: string;
  isSearching: boolean;
}

const props = defineProps<Props>();

// Props received successfully

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Valid Members', href: '/graveyard/valid-members' },
  { title: 'Create', href: '/graveyard/valid-members/create' },
];

// Form data using reactive for better TypeScript support
const formData = reactive({
  grave_type: '',
  permanent_grave_id: '',
  niche_id: '',
  members: [
    {
      member_type: '',
      member_id: '',
      member_search: '',
      search_results: [] as ParishMemberSearchResult[],
      selected_member: null as ParishMemberSearchResult | null,
      first_name: '',
      last_name: '',
      date_of_birth: '',
      age: null as number | null,
      months: null as number | null,
      days: null as number | null,
      gender_id: '',
      nationality: 'Indian',
      parish_id: '16',
      contact_no: '',
      aadhar_no: '',
      relationship_id: '',
      notes: '',
      isSearching: false,
    },
  ] as MemberFormData[],
});

// Form for Inertia
const form = useForm({
  grave_type: '',
  permanent_grave_id: '',
  niche_id: '',
  members: [] as any[],
});

let searchTimeout: any = null;

// Define interface for grave search results
interface GraveSearchResult {
  id: number;
  display_name: string;
  details: {
    owner_name: string;
    contact_no?: string;
    member?: {
      full_name: string;
      family_no: string;
    };
  };
}

// Grave search functionality
const graveSearchTerm = ref('');
const searchResults = ref<GraveSearchResult[]>([]);
const isSearching = ref(false);
const showDropdown = ref(false);
const selectedGrave = ref<any>(null);

// Debounced search for graves
let graveSearchTimeout: any = null;
const debouncedSearchGraves = () => {
  if (graveSearchTimeout) {
    clearTimeout(graveSearchTimeout);
  }

  graveSearchTimeout = setTimeout(() => {
    searchGraves();
  }, 300);
};

// Search graves based on selected grave type
const searchGraves = async () => {
  if (graveSearchTerm.value.length < 2) {
    searchResults.value = [];
    showDropdown.value = false;
    return;
  }

  isSearching.value = true;
  showDropdown.value = true;

  try {
    const response = await fetch('/graveyard/valid-members/search-graves', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        search_term: graveSearchTerm.value,
        grave_type: formData.grave_type,
      }),
    });

    const data = await response.json();
    searchResults.value = data.results;
  } catch (error) {
    console.error('Search failed:', error);
    searchResults.value = [];
  } finally {
    isSearching.value = false;
  }
};

// Select a grave from search results
const selectGrave = (grave: any) => {
  selectedGrave.value = grave;
  graveSearchTerm.value = '';
  searchResults.value = [];
  showDropdown.value = false;

  // Update form based on grave type
  if (formData.grave_type === 'permanent_grave') {
    formData.permanent_grave_id = grave.id;
    formData.niche_id = '';
  } else {
    formData.niche_id = grave.id;
    formData.permanent_grave_id = '';
  }
};

// Clear grave selection
const clearSelection = () => {
  selectedGrave.value = null;
  graveSearchTerm.value = '';
  searchResults.value = [];
  showDropdown.value = false;
  formData.permanent_grave_id = '';
  formData.niche_id = '';
};

// Hide dropdown when clicking outside
document.addEventListener('click', (e) => {
  const target = e.target as HTMLElement;
  if (!target.closest('.relative')) {
    showDropdown.value = false;
  }
});

// Helper function to get form errors safely
const getFieldError = (fieldPath: string): string | undefined => {
  return (form.errors as any)[fieldPath];
};

// Add member
const addMember = () => {
  if (formData.members.length < 5) {
    formData.members.push({
      member_type: '',
      member_id: '',
      member_search: '',
      search_results: [] as ParishMemberSearchResult[],
      selected_member: null as ParishMemberSearchResult | null,
      first_name: '',
      last_name: '',
      date_of_birth: '',
      age: null as number | null,
      months: null as number | null,
      days: null as number | null,
      gender_id: '',
      nationality: 'Indian',
      parish_id: '16',
      contact_no: '',
      aadhar_no: '',
      relationship_id: '',
      notes: '',
      isSearching: false,
    });
  }
};

// Remove member
const removeMember = (index: number) => {
  formData.members.splice(index, 1);
};

// Search parish members
const searchParishMembers = (index: number, query: string) => {
  const member = formData.members[index];
  member.member_search = query;

  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  if (query.length < 2) {
    member.search_results = [];
    return;
  }

  member.isSearching = true;
  searchTimeout = setTimeout(async () => {
    try {
      const response = await fetch(`/graveyard/valid-members/search-members?search=${encodeURIComponent(query)}`);
      const results = await response.json();
      member.search_results = results;
    } catch (error) {
      console.error('Error searching members:', error);
      member.search_results = [];
    } finally {
      member.isSearching = false;
    }
  }, 300);
};

// Select parish member
const selectParishMember = (index: number, selectedMember: ParishMemberSearchResult) => {
  const member = formData.members[index];
  member.selected_member = selectedMember;
  member.member_id = selectedMember.id.toString();
  member.member_search = '';
  member.search_results = [];
  member.contact_no = selectedMember.contact_no_1 || '';
};

// Clear parish member selection
const clearParishMemberSelection = (index: number) => {
  const member = formData.members[index];
  member.selected_member = null;
  member.member_id = '';
  member.member_search = '';
  member.search_results = [];
  member.contact_no = '';
};

// Watch for member_type changes to clear form fields
watch(
  () => formData.members.map((member) => member.member_type),
  (newTypes, oldTypes) => {
    if (oldTypes) {
      newTypes.forEach((newType, index) => {
        const oldType = oldTypes[index];
        if (newType !== oldType && oldType !== '') {
          // Clear fields when member_type changes
          const member = formData.members[index];
          if (newType === 'external') {
            // Clear parish member fields
            member.member_id = '';
            member.member_search = '';
            member.search_results = [];
            member.selected_member = null;
          } else if (newType === 'member') {
            // Clear external member fields
            member.first_name = '';
            member.last_name = '';
            member.date_of_birth = '';
            member.age = null;
            member.months = null;
            member.days = null;
            member.gender_id = '';
            member.nationality = 'Indian';
            member.parish_id = '16';
            member.aadhar_no = '';
          }
          // Always clear contact as it's used by both types, but keep relationship_id and notes
          member.contact_no = '';
        }
      });
    }
  },
  { deep: true },
);

// Submit form
const submitForm = () => {
  // Prepare the data for submission
  const submitData = {
    grave_type: formData.grave_type,
    permanent_grave_id: formData.grave_type === 'permanent_grave' ? formData.permanent_grave_id : null,
    niche_id: formData.grave_type === 'niche' ? formData.niche_id : null,
    members: formData.members.map((member) => ({
      member_type: member.member_type,
      member_id: member.member_type === 'member' ? member.member_id : null,
      first_name: member.member_type === 'external' ? member.first_name : '',
      last_name: member.member_type === 'external' ? member.last_name : '',
      date_of_birth: member.member_type === 'external' ? member.date_of_birth || null : null,
      age: member.member_type === 'external' ? member.age : null,
      months: member.member_type === 'external' ? member.months : null,
      days: member.member_type === 'external' ? member.days : null,
      gender_id: member.member_type === 'external' ? (member.gender_id ? parseInt(member.gender_id) : null) : null,
      nationality: member.member_type === 'external' ? member.nationality : null,
      parish_id: member.member_type === 'external' ? (member.parish_id ? parseInt(member.parish_id) : null) : null,
      contact_no: member.contact_no || null,
      aadhar_no: member.member_type === 'external' ? member.aadhar_no || null : null,
      relationship_id: member.relationship_id ? parseInt(member.relationship_id) : null,
      notes: member.notes || null,
    })),
  };

  form
    .transform(() => submitData)
    .post('/graveyard/valid-members', {
      onSuccess: () => {
        // Success is handled by the redirect
      },
      onError: (errors) => {
        console.error('Form errors:', errors);
      },
    });
};

// Handle pre-selected grave from URL parameters
const handlePreSelectedGrave = async () => {
  if (props.permanent_grave_id) {
    // Set grave type to permanent_grave
    formData.grave_type = 'permanent_grave';

    try {
      // Try to find the grave in the permanentGraves prop first (faster)
      const graveFromProps = props.permanentGraves.find((grave) => grave.id === props.permanent_grave_id);

      if (graveFromProps) {
        // Create a complete grave object for selection using props data
        const graveForSelection = {
          id: graveFromProps.id,
          type: 'permanent_grave',
          display_name: `Grave ${graveFromProps.grave_no} (${graveFromProps.section}, Row ${graveFromProps.row_no})`,
          details: {
            owner_name: graveFromProps.owner_name || 'Unknown Owner',
            contact_no: graveFromProps.contact_no || null,
            member: graveFromProps.member
              ? {
                  full_name: graveFromProps.member.full_name,
                  family_no: graveFromProps.member.family_no,
                }
              : null,
          },
        };

        selectGrave(graveForSelection);
      } else {
        // Fallback: Fetch the specific grave details using the show endpoint
        const response = await fetch(`/graveyard/permanent-graves/${props.permanent_grave_id}`, {
          method: 'GET',
          headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
        });

        if (response.ok) {
          const graveData = await response.json();

          // Create a grave object in the format expected by selectGrave
          const graveForSelection = {
            id: graveData.permanentGrave?.id || graveData.id,
            type: 'permanent_grave',
            display_name: `Grave ${graveData.permanentGrave?.grave_no || graveData.grave_no} (${graveData.permanentGrave?.section || graveData.section}, Row ${graveData.permanentGrave?.row_no || graveData.row_no})`,
            details: {
              owner_name: graveData.permanentGrave?.owner_name || graveData.owner_name || 'Unknown Owner',
              contact_no: graveData.permanentGrave?.contact_no || graveData.contact_no,
              member:
                graveData.permanentGrave?.member || graveData.member
                  ? {
                      full_name: (graveData.permanentGrave?.member || graveData.member).full_name,
                      family_no: (graveData.permanentGrave?.member || graveData.member).family_no,
                    }
                  : null,
            },
          };

          selectGrave(graveForSelection);
        }
      }
    } catch (error) {
      console.error('Failed to load pre-selected grave:', error);
    }
  }
};

// Call the function when component mounts
onMounted(() => {
  handlePreSelectedGrave();
});
</script>

<style scoped>
/* Custom styles for better form appearance */
</style>
