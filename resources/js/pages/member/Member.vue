<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import { SelectInput } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import {
  BloodGroups,
  CellsAndAssociations,
  Communities,
  Community,
  Countries,
  Designations,
  FamilyIncomeRanges,
  Member,
  Relationships,
  States,
  Towns,
  type BreadcrumbItem,
  type SharedData,
  type User,
  type CommunityCluster,
} from '@/types';
import { List } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import type { State, Town } from '@/types';

interface Props {
  member?: Member;
  communities: Communities;
  cellsAndAssociations: CellsAndAssociations;
  familyIncomeRanges: FamilyIncomeRanges;
  bloodGroups: BloodGroups;
  relationships: Relationships;
  countries: Countries;
  states: States;
  towns: Towns;
  designations: Designations;
  genders: any[];
  statuses: any[];
  parishes: any[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Member Details',
    href: '/member/profile-details',
  },
];

const statusList = ['Resident', 'Non-Resident', 'Dead', 'Redevelopment Unsettled'];
const genderList = ['male', 'female', 'other'];

// const statusMap = Object.fromEntries(statusList.map(status => [status, status]));
const statusArray = statusList.map((status) => ({ id: status, name: status }));
const genderArray = genderList.map((gender) => ({ id: gender, name: gender.charAt(0).toUpperCase() + gender.slice(1) }));

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const member = page.props.member as Member;

const form = useForm({
  id: member?.id ? member.id : '',
  first_name: member?.first_name ? member.first_name : '',
  middle_name: member?.middle_name ? member.middle_name : '',
  last_name: member?.last_name ? member.last_name : '',
  gender: member?.gender_id ? member.gender_id : '',
  blood_group_id: member?.blood_group_id ? member.blood_group_id : '',
  status_id: member?.status_id ? member.status_id : '',
  relationship_id: member?.relationship_id ? member.relationship_id : '',
  parish_id: member?.parish_id ? member.parish_id : '',
  date_of_birth: member?.date_of_birth ? member.date_of_birth : '',
  contact_no: member?.contact_no ? member.contact_no : '',
  email: member?.email ? member.email : '',
  aadhar: member?.aadhar ? member.aadhar : '',
  family_no: member?.family_no ? member.family_no : '',
  community_id: member?.community_id ? member.community_id : '',
  community_cluster_id: member?.community_cluster_id ? member.community_cluster_id : '',
  new_olsc_id: member?.new_olsc_id ? member.new_olsc_id : '',
  old_sal_id: member?.old_sal_id ? member.old_sal_id : '',
  permanent_add1: member?.permanent_add1 ? member.permanent_add1 : '',
  permanent_add2: member?.permanent_add2 ? member.permanent_add2 : '',
  permanent_add3: member?.permanent_add3 ? member.permanent_add3 : '',
  permanent_town_id: member?.permanent_town_id ? member.permanent_town_id : '',
  permanent_city_id: member?.permanent_city_id ? member.permanent_city_id : '',
  permanent_pincode: member?.permanent_pincode ? member.permanent_pincode : '',
  permanent_state_id: member?.permanent_state_id ? member.permanent_state_id : '',
  permanent_country_id: member?.permanent_country_id? member.permanent_country_id : '',
  current_add1: member?.current_add1 ? member.current_add1 : '',
  current_add2: member?.current_add2 ? member.current_add2 : '',
  current_add3: member?.current_add3 ? member.current_add3 : '',
  current_town_id: member?.current_town_id ? member.current_town_id : '',
  current_city_id: member?.current_city_id ? member.current_city_id : '',
  current_pincode: member?.current_pincode ? member.current_pincode : '',
  current_state_id: member?.current_state_id ? member.current_state_id : '',
  current_country_id: member?.current_country_id ? member.current_country_id : '',
  cells_and_association_id: member?.cells_and_association_id ? member.cells_and_association_id : '',
  school_name: member?.school_name ? member.school_name : '',
  college_name: member?.college_name ? member.college_name : '',
  latest_qualifications: member?.latest_qualifications ? member.latest_qualifications : '',
  company_name: member?.company_name ? member.company_name : '',
  designation_id: member?.designation? member.designation : '',
  family_income_range_id: member?.family_income_range? member.family_income_range: '',
  baptism_date: member?.baptism_date ? member.baptism_date : '',
  baptism_reg_no: member?.baptism_reg_no ? member.baptism_reg_no : '',
  baptism_parish: member?.baptism_parish ? member.baptism_parish : '',
  confirmation_date: member?.confirmation_date ? member.confirmation_date : '',
  confirmation_reg_no: member?.confirmation_reg_no ? member.confirmation_reg_no : '',
  confirmation_parish: member?.confirmation_parish ? member.confirmation_parish : '',
  marriage_date: member?.marriage_date ? member.marriage_date : '',
  marriage_reg_no: member?.marriage_reg_no ? member.marriage_reg_no : '',
  marriage_parish: member?.marriage_parish ? member.marriage_parish : '',
  death_date: member?.death_date ? member.death_date : '',
  deaths_reg_no: member?.deaths_reg_no ? member.deaths_reg_no : '',
  death_parish: member?.death_parish ? member.death_parish : '',
});

const submit = () => {
  const routeName = member?.id ? 'member.update' : 'member.store'; // Determine the route
  const method = member?.id ? 'put' : 'post'; // Determine the HTTP method

  form[method](route(routeName, { id: member?.id }), {
    preserveScroll: true,
  });
};

const communityClusters = ref<CommunityCluster[]>([]);

const fetchCommunityCluster = async () => {
  if (!form.community_id) return;
  const clusters = props.communities.find((community: Community) => community.id === form.community_id)?.community_clusters;
  communityClusters.value = Array.isArray(clusters) ? clusters : clusters ? [clusters] : [];
};

watch(
  () => form.community_id,
  () => {
    fetchCommunityCluster();
  },
);

const filteredTownPermanent = ref<Town[]>([]);

const fetchfilteredTownPermanent = async () => {
  if (!form.permanent_country_id) return;
  filteredTownPermanent.value = props.towns.filter((town) => town.state_id === Number(form.permanent_state_id));
};

watch(
  () => form.permanent_state_id,
  () => {
    fetchfilteredTownPermanent();
  },
);

const filteredStatesPermanent = ref<State[]>([]);

const fetchfilteredStatesPermanent = async () => {
  if (!form.permanent_country_id) return;
  filteredStatesPermanent.value = props.states.filter((state) => state.country_id === Number(form.permanent_country_id));
};

watch(
  () => form.permanent_country_id,
  () => {
    fetchfilteredStatesPermanent();
  },
);

const filteredTownCurrent = ref<Town[]>([]);

const fetchfilteredTownCurrent = async () => {
  if (!form.current_country_id) return;
  filteredTownCurrent.value = props.towns.filter((town) => town.state_id === Number(form.current_state_id));
};

watch(
  () => form.current_state_id,
  () => {
    fetchfilteredTownCurrent();
  },
);

const filteredStatesCurrent = ref<State[]>([]);

const fetchfilteredStatesCurrent = async () => {
  if (!form.current_country_id) return;
  filteredStatesCurrent.value = props.states.filter((state) => state.country_id === Number(form.current_country_id));
};

watch(
  () => form.current_country_id,
  () => {
    fetchfilteredStatesCurrent();
  },
);

function cancel() {
  window.location.href = '/member/index';
}

const selectedDesignations = ref([]); // For v-model
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Members" />
    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Members</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/member/index" class="flex items-center gap-2 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
            <component :is="List" />
            <span>Member List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-8">

        <!-- Personal Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Personal Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="first_name">First Name</Label>
              <Input id="first_name" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.first_name" autocomplete="first_name" placeholder="First name" />
              <InputError class="mt-2" :message="form.errors.first_name" />
            </div>
            <div class="grid gap-2">
              <Label for="middle_name">Middle Name</Label>
              <Input id="middle_name" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.middle_name" autocomplete="middle_name" placeholder="Middle name" />
              <InputError class="mt-2" :message="form.errors.middle_name" />
            </div>
            <div class="grid gap-2">
              <Label for="last_name">Last Name</Label>
              <Input id="last_name" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.last_name" autocomplete="last_name" placeholder="Last name" />
              <InputError class="mt-2" :message="form.errors.last_name" />
            </div>
            <div class="grid gap-2">
              <Label for="gender">Gender</Label>
              <SelectInput id="gender" v-model="form.gender" :options="props.genders" class="mt-1 block w-full rounded-full" placeholder="Select Gender" />
              <InputError class="mt-2" :message="form.errors.gender" />
            </div>
            <div class="grid gap-2">
              <Label for="date_of_birth">Date of Birth</Label>
              <Input
                id="date_of_birth"
                type="date"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.date_of_birth"
                autocomplete="date_of_birth"
                placeholder="Date of birth"
              />
              <InputError class="mt-2" :message="form.errors.date_of_birth" />
            </div>
            <div class="grid gap-2">
              <Label for="status_id">Status</Label>
              <SelectInput id="status_id" v-model="form.status_id" :options="props.statuses" class="mt-1 block w-full rounded-full" placeholder="Select Status" />
              <InputError class="mt-2" :message="form.errors.status_id" />
            </div>
            <div class="grid gap-2">
              <Label for="relationship_id">Relationship</Label>
              <SearchDropdown
                id="relationship_id"
                v-model="form.relationship_id"
                :options="props.relationships"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Relationship"
              />
              <InputError class="mt-2" :message="form.errors.relationship_id" />
            </div>
            <div class="grid gap-2">
              <Label for="blood_group_id">Blood Group</Label>
              <SelectInput
                id="blood_group_id"
                v-model="form.blood_group_id"
                :options="bloodGroups"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Blood Group"
              />
              <InputError class="mt-2" :message="form.errors.blood_group_id" />
            </div>
            <div class="grid gap-2">
              <Label for="contact_no">Contact No</Label>
              <Input id="contact_no" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.contact_no" autocomplete="contact_no" placeholder="Contact no" />
              <InputError class="mt-2" :message="form.errors.contact_no" />
            </div>
            <div class="grid gap-2">
              <Label for="email">Email</Label>
              <Input id="email" type="email" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.email" autocomplete="email" placeholder="Email" />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="grid gap-2">
              <Label for="aadhar">Aadhar</Label>
              <Input id="aadhar" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.aadhar" autocomplete="aadhar" placeholder="Aadhar" />
              <InputError class="mt-2" :message="form.errors.aadhar" />
            </div>
            <div class="grid gap-2">
              <Label for="family_no">Family No</Label>
              <Input id="family_no" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.family_no" autocomplete="family_no" placeholder="Family no" />
              <InputError class="mt-2" :message="form.errors.family_no" />
            </div>
          </div>
        </div>

        <!-- Community Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Community Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="community_id">Community</Label>
              <SearchDropdown
                id="community_id"
                v-model="form.community_id"
                :options="props.communities"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Community"
                @change="fetchCommunityCluster"
              />
              <InputError class="mt-2" :message="form.errors.community_id" />
            </div>
            <div class="grid gap-2">
              <Label for="community_cluster_id">Community Cluster</Label>
              <SearchDropdown
                id="community_cluster_id"
                v-model="form.community_cluster_id"
                :options="communityClusters"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Community Cluster"
              />
              <InputError class="mt-2" :message="form.errors.community_cluster_id" />
            </div>
          </div>
        </div>

        <!-- Permanent Address -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Permanent Address</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="permanent_add1">Address 1</Label>
              <Input
                id="permanent_add1"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.permanent_add1"
                autocomplete="permanent_add1"
                placeholder="Permanent Address 1"
              />
              <InputError class="mt-2" :message="form.errors.permanent_add1" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_add2">Address 2</Label>
              <Input
                id="permanent_add2"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.permanent_add2"
                autocomplete="permanent_add2"
                placeholder="Permanent Address 2"
              />
              <InputError class="mt-2" :message="form.errors.permanent_add2" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_add3">Address 3</Label>
              <Input
                id="permanent_add3"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.permanent_add3"
                autocomplete="permanent_add3"
                placeholder="Permanent Address 3"
              />
              <InputError class="mt-2" :message="form.errors.permanent_add3" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_town_id">Town</Label>
              <SearchDropdown
                id="permanent_town_id"
                v-model="form.permanent_town_id"
                :options="filteredTownPermanent"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Permanent Town"
              />
              <InputError class="mt-2" :message="form.errors.permanent_town_id" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_city">City</Label>
              <Input
                id="permanent_city"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.permanent_city_id"
                autocomplete="permanent_city"
                placeholder="Permanent City"
              />
              <InputError class="mt-2" :message="form.errors.permanent_city_id" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_pincode">Pincode</Label>
              <Input
                id="permanent_pincode"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.permanent_pincode"
                autocomplete="permanent_pincode"
                placeholder="Permanent Pincode"
              />
              <InputError class="mt-2" :message="form.errors.permanent_pincode" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_state_id">State</Label>
              <SearchDropdown
                id="permanent_state_id"
                v-model="form.permanent_state_id"
                :options="filteredStatesPermanent"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Permanent State"
              />
              <InputError class="mt-2" :message="form.errors.permanent_state_id" />
            </div>
            <div class="grid gap-2">
              <Label for="permanent_country_id">Country</Label>
              <SearchDropdown
                id="permanent_country_id"
                v-model="form.permanent_country_id"
                :options="props.countries"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Permanent Country"
              />
              <InputError class="mt-2" :message="form.errors.permanent_country_id" />
            </div>
          </div>
        </div>

        <!-- Current Address -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Current Address</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="current_add1">Address 1</Label>
              <Input
                id="current_add1"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.current_add1"
                autocomplete="current_add1"
                placeholder="Current Address 1"
              />
              <InputError class="mt-2" :message="form.errors.current_add1" />
            </div>
            <div class="grid gap-2">
              <Label for="current_add2">Address 2</Label>
              <Input
                id="current_add2"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.current_add2"
                autocomplete="current_add2"
                placeholder="Current Address 2"
              />
              <InputError class="mt-2" :message="form.errors.current_add2" />
            </div>
            <div class="grid gap-2">
              <Label for="current_add3">Address 3</Label>
              <Input
                id="current_add3"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.current_add3"
                autocomplete="current_add3"
                placeholder="Current Address 3"
              />
              <InputError class="mt-2" :message="form.errors.current_add3" />
            </div>
            <div class="grid gap-2">
              <Label for="current_town_id">Town</Label>
              <SearchDropdown
                id="current_town_id"
                v-model="form.current_town_id"
                :options="filteredTownCurrent"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Current Town"
              />
              <InputError class="mt-2" :message="form.errors.current_town_id" />
            </div>
            <div class="grid gap-2">
              <Label for="current_city">City</Label>
              <Input id="current_city_id" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.current_city_id" autocomplete="current_city_id" placeholder="Current City" />
              <InputError class="mt-2" :message="form.errors.current_city_id" />
            </div>
            <div class="grid gap-2">
              <Label for="current_pincode">Pincode</Label>
              <Input
                id="current_pincode"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.current_pincode"
                autocomplete="current_pincode"
                placeholder="Current Pincode"
              />
              <InputError class="mt-2" :message="form.errors.current_pincode" />
            </div>
            <div class="grid gap-2">
              <Label for="current_state_id">State</Label>
              <SearchDropdown
                id="current_state_id"
                v-model="form.current_state_id"
                :options="filteredStatesCurrent"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Current State"
              />
              <InputError class="mt-2" :message="form.errors.current_state_id" />
            </div>
            <div class="grid gap-2">
              <Label for="current_country_id">Country</Label>
              <SearchDropdown
                id="current_country_id"
                v-model="form.current_country_id"
                :options="props.countries"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Current Country"
              />
              <InputError class="mt-2" :message="form.errors.current_country_id" />
            </div>
          </div>
        </div>

        <!-- Education & Work Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Education & Work Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="school_name">School Name</Label>
              <Input id="school_name" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.school_name" autocomplete="school_name" placeholder="School name" />
              <InputError class="mt-2" :message="form.errors.school_name" />
            </div>
            <div class="grid gap-2">
              <Label for="college_name">College Name</Label>
              <Input id="college_name" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.college_name" autocomplete="college_name" placeholder="College name" />
              <InputError class="mt-2" :message="form.errors.college_name" />
            </div>
            <div class="grid gap-2">
              <Label for="latest_qualifications">Latest Qualifications</Label>
              <Input
                id="latest_qualifications"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.latest_qualifications"
                autocomplete="latest_qualifications"
                placeholder="Latest qualifications"
              />
              <InputError class="mt-2" :message="form.errors.latest_qualifications" />
            </div>
            <div class="grid gap-2">
              <Label for="company_name">Company Name</Label>
              <Input id="company_name" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.company_name" autocomplete="company_name" placeholder="Company name" />
              <InputError class="mt-2" :message="form.errors.company_name" />
            </div>
            <div class="grid gap-2">
              <Label for="designation_id">Designation</Label>
              <SearchDropdown
                id="designation_id"
                v-model="form.designation_id"
                :options="props.designations"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Designation"
              />
              <InputError class="mt-2" :message="form.errors.designation_id" />
            </div>
            <div class="grid gap-2">
              <Label for="family_income_range_id">Family Income Range</Label>
              <SelectInput
                id="family_income_range_id"
                v-model="form.family_income_range_id"
                :options="familyIncomeRanges"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Family Income Range"
              />
              <InputError class="mt-2" :message="form.errors.family_income_range_id" />
            </div>
          </div>
        </div>

        <!-- Cells & Associations -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Cells & Associations</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="cells_and_association_id">Cells and Association</Label>
              <SearchDropdown
                id="cells_and_association_id"
                v-model="form.cells_and_association_id"
                :options="cellsAndAssociations"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Cells & Association"
              />
              <InputError class="mt-2" :message="form.errors.cells_and_association_id" />
            </div>
          </div>
        </div>

        <!-- Sacrament Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Sacrament Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="baptism_date">Baptism Date</Label>
              <Input
                id="baptism_date"
                type="date"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.baptism_date"
                autocomplete="baptism_date"
                placeholder="Baptism date"
              />
              <InputError class="mt-2" :message="form.errors.baptism_date" />
            </div>
            <div class="grid gap-2">
              <Label for="baptism_reg_no">Baptism Registration No</Label>
              <Input
                id="baptism_reg_no"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.baptism_reg_no"
                autocomplete="baptism_reg_no"
                placeholder="Baptism registration no"
              />
              <InputError class="mt-2" :message="form.errors.baptism_reg_no" />
            </div>
            <div class="grid gap-2">
              <Label for="baptism_parish">Baptism Parish</Label>
              <Input
                id="baptism_parish"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.baptism_parish"
                autocomplete="baptism_parish"
                placeholder="Baptism parish"
              />
              <InputError class="mt-2" :message="form.errors.baptism_parish" />
            </div>
            <div class="grid gap-2">
              <Label for="confirmation_date">Confirmation Date</Label>
              <Input
                id="confirmation_date"
                type="date"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.confirmation_date"
                autocomplete="confirmation_date"
                placeholder="Confirmation date"
              />
              <InputError class="mt-2" :message="form.errors.confirmation_date" />
            </div>
            <div class="grid gap-2">
              <Label for="confirmation_reg_no">Confirmation Registration No</Label>
              <Input
                id="confirmation_reg_no"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.confirmation_reg_no"
                autocomplete="confirmation_reg_no"
                placeholder="Confirmation registration no"
              />
              <InputError class="mt-2" :message="form.errors.confirmation_reg_no" />
            </div>
            <div class="grid gap-2">
              <Label for="confirmation_parish">Confirmation Parish</Label>
              <Input
                id="confirmation_parish"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.confirmation_parish"
                autocomplete="confirmation_parish"
                placeholder="Confirmation parish"
              />
              <InputError class="mt-2" :message="form.errors.confirmation_parish" />
            </div>
            <div class="grid gap-2">
              <Label for="marriage_date">Marriage Date</Label>
              <Input
                id="marriage_date"
                type="date"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.marriage_date"
                autocomplete="marriage_date"
                placeholder="Marriage date"
              />
              <InputError class="mt-2" :message="form.errors.marriage_date" />
            </div>
            <div class="grid gap-2">
              <Label for="marriage_reg_no">Marriage Registration No</Label>
              <Input
                id="marriage_reg_no"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.marriage_reg_no"
                autocomplete="marriage_reg_no"
                placeholder="Marriage registration no"
              />
              <InputError class="mt-2" :message="form.errors.marriage_reg_no" />
            </div>
            <div class="grid gap-2">
              <Label for="marriage_parish">Marriage Parish</Label>
              <Input
                id="marriage_parish"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.marriage_parish"
                autocomplete="marriage_parish"
                placeholder="Marriage parish"
              />
              <InputError class="mt-2" :message="form.errors.marriage_parish" />
            </div>
            <div class="grid gap-2">
              <Label for="death_date">Death Date</Label>
              <Input
                id="death_date"
                type="date"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.death_date"
                autocomplete="death_date"
                placeholder="Death date"
              />
              <InputError class="mt-2" :message="form.errors.death_date" />
            </div>
            <div class="grid gap-2">
              <Label for="deaths_reg_no">Death Registration No</Label>
              <Input
                id="deaths_reg_no"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.deaths_reg_no"
                autocomplete="deaths_reg_no"
                placeholder="Death registration no"
              />
              <InputError class="mt-2" :message="form.errors.deaths_reg_no" />
            </div>
            <div class="grid gap-2">
              <Label for="death_parish">Death Parish</Label>
              <Input id="death_parish" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.death_parish" autocomplete="death_parish" placeholder="Death parish" />
              <InputError class="mt-2" :message="form.errors.death_parish" />
            </div>
          </div>
        </div>

        <!-- Other Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Other Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="new_olsc_id">New OLSC ID</Label>
              <Input id="new_olsc_id" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.new_olsc_id" autocomplete="new_olsc_id" placeholder="New OLSC ID" />
              <InputError class="mt-2" :message="form.errors.new_olsc_id" />
            </div>
            <div class="grid gap-2">
              <Label for="old_sal_id">Old SAL ID</Label>
              <Input id="old_sal_id" class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200" v-model="form.old_sal_id" autocomplete="old_sal_id" placeholder="Old SAL ID" />
              <InputError class="mt-2" :message="form.errors.old_sal_id" />
            </div>
          </div>
        </div>

        <!-- Action Bar -->
        <div class="sticky bottom-0 left-0 right-0 z-10 flex items-center gap-4 bg-gray-50 p-4 rounded-b-2xl shadow-inner">
          <Button :disabled="form.processing" class="bg-blue-600 text-white px-6 py-2 rounded-full shadow hover:bg-blue-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Save
          </Button>
          <Button type="button" @click="cancel" variant="outline" class="rounded-full px-6 py-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            Cancel
          </Button>
          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
          </Transition>
        </div>
      </form>
    </FormBody>
  </AppLayout>
</template>
