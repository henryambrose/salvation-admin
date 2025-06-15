<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SelectInput } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import MemberLayout from '@/layouts/member/Layout.vue';
import { Community, Member, CellsAndAssociations, type BreadcrumbItem, type SharedData, type User, Communities, FamilyIncomeRanges } from '@/types';
import { List } from 'lucide-vue-next';
import { DropdownMenu } from '@/components/ui/dropdown-menu';
import { ref } from 'vue';
import FormHeader from '@/components/FormHeader.vue';
import FormBody from '@/components/FormBody.vue';


interface Props {
    member?: Member;
    communities: Communities;
    cellsAndAssociations: CellsAndAssociations;
    familyIncomeRanges: FamilyIncomeRanges;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Member Details',
        href: '/member/profile-details',
    },
];

const statusList = [
    'Resident','Non-Resident','Dead','Redevelopment Unsettled'
];
const genderList = ['male', 'female', 'other'];

// const statusMap = Object.fromEntries(statusList.map(status => [status, status]));
const statusArray = statusList.map(status => ({ id: status, name: status }));
const genderArray = genderList.map(gender => ({ id: gender, name: gender.charAt(0).toUpperCase() + gender.slice(1) }));

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const member = page.props.member as Member;

const form = useForm({
    id: member?.id ? member.id : '',
    first_name: member?.first_name ? member.first_name : '',
    middle_name: member?.middle_name ? member.middle_name : '',
    last_name: member?.last_name ? member.last_name : '',
    gender: member?.gender ? member.gender : '',
    blood_group: member?.blood_group ? member.blood_group : '',
    status: member?.status ? member.status : '',
    relationship: member?.relationship ? member.relationship : '',
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
    permanent_town: member?.permanent_town ? member.permanent_town : '',
    permanent_city: member?.permanent_city ? member.permanent_city : '',
    permanent_pincode: member?.permanent_pincode ? member.permanent_pincode : '',
    permanent_state: member?.permanent_state ? member.permanent_state : '',
    permanent_country: member?.permanent_country ? member.permanent_country : '',
    current_add1: member?.current_add1 ? member.current_add1 : '',
    current_add2: member?.current_add2 ? member.current_add2 : '',
    current_add3: member?.current_add3 ? member.current_add3 : '',
    current_town: member?.current_town ? member.current_town : '',
    current_city: member?.current_city ? member.current_city : '',
    current_pincode: member?.current_pincode ? member.current_pincode : '',
    current_state: member?.current_state ? member.current_state : '',
    current_country: member?.current_country ? member.current_country : '',
    cells_and_association_id: member?.cells_and_association_id ? member.cells_and_association_id : '',
    school_name: member?.school_name ? member.school_name : '',
    college_name: member?.college_name ? member.college_name : '',
    latest_qualifications: member?.latest_qualifications ? member.latest_qualifications : '',
    company_name: member?.company_name ? member.company_name : '',
    designation: member?.designation ? member.designation : '',
    family_income_range: member?.family_income_range ? member.family_income_range : '',
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

const communityClusters = ref([])

const fetchCommunityCluster = async () => {
  if (!form.community_id) return

  console.log('page.props.communities', page.props.communities);
  communityClusters.value = page.props.communities.find((community: Community) => community.id === form.community_id)?.community_clusters || [];


}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Members" />
        <FormHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Members</h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/member/index" class="btn btn-secondary">
                        <component :is="List" />
                        <span>Member List</span>
                    </Button>
                </div>
            </div>
        </FormHeader>
        <FormBody>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="first_name">First Name</Label>
                        <Input id="first_name" class="mt-1 block w-full" v-model="form.first_name" autocomplete="first_name" placeholder="First name" />
                        <InputError class="mt-2" :message="form.errors.first_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="middle_name">Middle Name</Label>
                        <Input id="middle_name" class="mt-1 block w-full" v-model="form.middle_name" autocomplete="middle_name" placeholder="Middle name" />
                        <InputError class="mt-2" :message="form.errors.middle_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="last_name">Last Name</Label>
                        <Input id="last_name" class="mt-1 block w-full" v-model="form.last_name" autocomplete="last_name" placeholder="Last name" />
                        <InputError class="mt-2" :message="form.errors.last_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="status">Gender</Label>

                        <SelectInput
                            id="status"
                            v-model="form.gender"
                            :options="genderArray"
                            class="mt-1 block w-full"
                            placeholder="Select Status"
                        />
                        <InputError class="mt-2" :message="form.errors.gender" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="blood_group">Blood Group</Label>
                        <Input id="blood_group" class="mt-1 block w-full" v-model="form.blood_group" autocomplete="blood_group" placeholder="Blood group" />
                        <InputError class="mt-2" :message="form.errors.blood_group" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <!-- <Input id="status" class="mt-1 block w-full" v-model="form.status" autocomplete="status" placeholder="Status" /> -->

                        <SelectInput
                            id="status"
                            v-model="form.status"
                            :options="statusArray"
                            class="mt-1 block w-full"
                            placeholder="Select Status"
                        />
                        <InputError class="mt-2" :message="form.errors.status" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="relationship">Relationship</Label>
                        <Input id="relationship" class="mt-1 block w-full" v-model="form.relationship" autocomplete="relationship" placeholder="Relationship" />
                        <InputError class="mt-2" :message="form.errors.relationship" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="date_of_birth">Date of Birth</Label>
                        <Input id="date_of_birth" type="date" class="mt-1 block w-full" v-model="form.date_of_birth" autocomplete="date_of_birth" placeholder="Date of birth" />
                        <InputError class="mt-2" :message="form.errors.date_of_birth" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="contact_no">Contact No</Label>
                        <Input id="contact_no" class="mt-1 block w-full" v-model="form.contact_no" autocomplete="contact_no" placeholder="Contact no" />
                        <InputError class="mt-2" :message="form.errors.contact_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="email" placeholder="Email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="aadhar">Aadhar</Label>
                        <Input id="aadhar" class="mt-1 block w-full" v-model="form.aadhar" autocomplete="aadhar" placeholder="Aadhar" />
                        <InputError class="mt-2" :message="form.errors.aadhar" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="family_no">Family No</Label>
                        <Input id="family_no" class="mt-1 block w-full" v-model="form.family_no" autocomplete="family_no" placeholder="Family no" />
                        <InputError class="mt-2" :message="form.errors.family_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="community_id">Community ID</Label>
                        <SelectInput
                            id="community_id"
                            v-model="form.community_id"
                            :options="page.props.communities"
                            class="mt-1 block w-full"

                            placeholder="Select Community"
                            @change="fetchCommunityCluster"
                        />
                        <InputError class="mt-2" :message="form.errors.community_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="community_cluster_id">Community Cluster ID</Label>
                        <!-- <Input id="community_cluster_id" class="mt-1 block w-full" v-model="form.community_cluster_id" autocomplete="community_cluster_id" placeholder="Community Cluster ID" /> -->
                        <SelectInput
                            id="community_cluster_id"
                            v-model="form.community_cluster_id"
                            :options="communityClusters"
                            class="mt-1 block w-full"

                            placeholder="Select Community Cluster"
                        />
                        <InputError class="mt-2" :message="form.errors.community_cluster_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="new_olsc_id">New OLSC ID</Label>
                        <Input id="new_olsc_id" class="mt-1 block w-full" v-model="form.new_olsc_id" autocomplete="new_olsc_id" placeholder="New OLSC ID" />
                        <InputError class="mt-2" :message="form.errors.new_olsc_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="old_sal_id">Old SAL ID</Label>
                        <Input id="old_sal_id" class="mt-1 block w-full" v-model="form.old_sal_id" autocomplete="old_sal_id" placeholder="Old SAL ID" />
                        <InputError class="mt-2" :message="form.errors.old_sal_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_add1">Permanent Address 1</Label>
                        <Input id="permanent_add1" class="mt-1 block w-full" v-model="form.permanent_add1" autocomplete="permanent_add1" placeholder="Permanent Address 1" />
                        <InputError class="mt-2" :message="form.errors.permanent_add1" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_add2">Permanent Address 2</Label>
                        <Input id="permanent_add2" class="mt-1 block w-full" v-model="form.permanent_add2" autocomplete="permanent_add2" placeholder="Permanent Address 2" />
                        <InputError class="mt-2" :message="form.errors.permanent_add2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_add3">Permanent Address 3</Label>
                        <Input id="permanent_add3" class="mt-1 block w-full" v-model="form.permanent_add3" autocomplete="permanent_add3" placeholder="Permanent Address 3" />
                        <InputError class="mt-2" :message="form.errors.permanent_add3" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_town">Permanent Town</Label>
                        <Input id="permanent_town" class="mt-1 block w-full" v-model="form.permanent_town" autocomplete="permanent_town" placeholder="Permanent Town" />
                        <InputError class="mt-2" :message="form.errors.permanent_town" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_city">Permanent City</Label>
                        <Input id="permanent_city" class="mt-1 block w-full" v-model="form.permanent_city" autocomplete="permanent_city" placeholder="Permanent City" />
                        <InputError class="mt-2" :message="form.errors.permanent_city" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_pincode">Permanent Pincode</Label>
                        <Input id="permanent_pincode" class="mt-1 block w-full" v-model="form.permanent_pincode" autocomplete="permanent_pincode" placeholder="Permanent Pincode" />
                        <InputError class="mt-2" :message="form.errors.permanent_pincode" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_state">Permanent State</Label>
                        <Input id="permanent_state" class="mt-1 block w-full" v-model="form.permanent_state" autocomplete="permanent_state" placeholder="Permanent State" />
                        <InputError class="mt-2" :message="form.errors.permanent_state" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_country">Permanent Country</Label>
                        <Input id="permanent_country" class="mt-1 block w-full" v-model="form.permanent_country" autocomplete="permanent_country" placeholder="Permanent Country" />
                        <InputError class="mt-2" :message="form.errors.permanent_country" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_add1">Current Address 1</Label>
                        <Input id="current_add1" class="mt-1 block w-full" v-model="form.current_add1" autocomplete="current_add1" placeholder="Current Address 1" />
                        <InputError class="mt-2" :message="form.errors.current_add1" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_add2">Current Address 2</Label>
                        <Input id="current_add2" class="mt-1 block w-full" v-model="form.current_add2" autocomplete="current_add2" placeholder="Current Address 2" />
                        <InputError class="mt-2" :message="form.errors.current_add2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_add3">Current Address 3</Label>
                        <Input id="current_add3" class="mt-1 block w-full" v-model="form.current_add3" autocomplete="current_add3" placeholder="Current Address 3" />
                        <InputError class="mt-2" :message="form.errors.current_add3" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_town">Current Town</Label>
                        <Input id="current_town" class="mt-1 block w-full" v-model="form.current_town" autocomplete="current_town" placeholder="Current Town" />
                        <InputError class="mt-2" :message="form.errors.current_town" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_city">Current City</Label>
                        <Input id="current_city" class="mt-1 block w-full" v-model="form.current_city" autocomplete="current_city" placeholder="Current City" />
                        <InputError class="mt-2" :message="form.errors.current_city" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_pincode">Current Pincode</Label>
                        <Input id="current_pincode" class="mt-1 block w-full" v-model="form.current_pincode" autocomplete="current_pincode" placeholder="Current Pincode" />
                        <InputError class="mt-2" :message="form.errors.current_pincode" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_state">Current State</Label>
                        <Input id="current_state" class="mt-1 block w-full" v-model="form.current_state" autocomplete="current_state" placeholder="Current State" />
                        <InputError class="mt-2" :message="form.errors.current_state" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="current_country">Current Country</Label>
                        <Input id="current_country" class="mt-1 block w-full" v-model="form.current_country" autocomplete="current_country" placeholder="Current Country" />
                        <InputError class="mt-2" :message="form.errors.current_country" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="cells_and_association_id">Cells and Association ID</Label>
                        <!-- <Input id="cells_and_association_id" class="mt-1 block w-full" v-model="form.cells_and_association_id" autocomplete="cells_and_association_id" placeholder="Cells and Association ID" /> -->
                        <SelectInput
                            id="cells_and_association_id"
                            v-model="form.cells_and_association_id"
                            :options="cellsAndAssociations"
                            class="mt-1 block w-full"
                            placeholder="Select Cells & Association"
                        />
                        <InputError class="mt-2" :message="form.errors.cells_and_association_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="school_name">School Name</Label>
                        <Input id="school_name" class="mt-1 block w-full" v-model="form.school_name" autocomplete="school_name" placeholder="School name" />
                        <InputError class="mt-2" :message="form.errors.school_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="college_name">College Name</Label>
                        <Input id="college_name" class="mt-1 block w-full" v-model="form.college_name" autocomplete="college_name" placeholder="College name" />
                        <InputError class="mt-2" :message="form.errors.college_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="latest_qualifications">Latest Qualifications</Label>
                        <Input id="latest_qualifications" class="mt-1 block w-full" v-model="form.latest_qualifications" autocomplete="latest_qualifications" placeholder="Latest qualifications" />
                        <InputError class="mt-2" :message="form.errors.latest_qualifications" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="company_name">Company Name</Label>
                        <Input id="company_name" class="mt-1 block w-full" v-model="form.company_name" autocomplete="company_name" placeholder="Company name" />
                        <InputError class="mt-2" :message="form.errors.company_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="designation">Designation</Label>
                        <Input id="designation" class="mt-1 block w-full" v-model="form.designation" autocomplete="designation" placeholder="Designation" />
                        <InputError class="mt-2" :message="form.errors.designation" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="family_income_range">Family Income Range</Label>
                        <!-- <Input id="family_income_range" class="mt-1 block w-full" v-model="form.family_income_range" autocomplete="family_income_range" placeholder="Family income range" /> -->
                        <SelectInput
                            id="family_income_range"
                            v-model="form.family_income_range"
                            :options="familyIncomeRanges"
                            class="mt-1 block w-full"
                            placeholder="Select Family Income Range"
                        />
                        <InputError class="mt-2" :message="form.errors.family_income_range" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="baptism_date">Baptism Date</Label>
                        <Input id="baptism_date" type="date" class="mt-1 block w-full" v-model="form.baptism_date" autocomplete="baptism_date" placeholder="Baptism date" />
                        <InputError class="mt-2" :message="form.errors.baptism_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="baptism_reg_no">Baptism Registration No</Label>
                        <Input id="baptism_reg_no" class="mt-1 block w-full" v-model="form.baptism_reg_no" autocomplete="baptism_reg_no" placeholder="Baptism registration no" />
                        <InputError class="mt-2" :message="form.errors.baptism_reg_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="baptism_parish">Baptism Parish</Label>
                        <Input id="baptism_parish" class="mt-1 block w-full" v-model="form.baptism_parish" autocomplete="baptism_parish" placeholder="Baptism parish" />
                        <InputError class="mt-2" :message="form.errors.baptism_parish" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="confirmation_date">Confirmation Date</Label>
                        <Input id="confirmation_date" type="date" class="mt-1 block w-full" v-model="form.confirmation_date" autocomplete="confirmation_date" placeholder="Confirmation date" />
                        <InputError class="mt-2" :message="form.errors.confirmation_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="confirmation_reg_no">Confirmation Registration No</Label>
                        <Input id="confirmation_reg_no" class="mt-1 block w-full" v-model="form.confirmation_reg_no" autocomplete="confirmation_reg_no" placeholder="Confirmation registration no" />
                        <InputError class="mt-2" :message="form.errors.confirmation_reg_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="confirmation_parish">Confirmation Parish</Label>
                        <Input id="confirmation_parish" class="mt-1 block w-full" v-model="form.confirmation_parish" autocomplete="confirmation_parish" placeholder="Confirmation parish" />
                        <InputError class="mt-2" :message="form.errors.confirmation_parish" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="marriage_date">Marriage Date</Label>
                        <Input id="marriage_date" type="date" class="mt-1 block w-full" v-model="form.marriage_date" autocomplete="marriage_date" placeholder="Marriage date" />
                        <InputError class="mt-2" :message="form.errors.marriage_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="marriage_reg_no">Marriage Registration No</Label>
                        <Input id="marriage_reg_no" class="mt-1 block w-full" v-model="form.marriage_reg_no" autocomplete="marriage_reg_no" placeholder="Marriage registration no" />
                        <InputError class="mt-2" :message="form.errors.marriage_reg_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="marriage_parish">Marriage Parish</Label>
                        <Input id="marriage_parish" class="mt-1 block w-full" v-model="form.marriage_parish" autocomplete="marriage_parish" placeholder="Marriage parish" />
                        <InputError class="mt-2" :message="form.errors.marriage_parish" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="death_date">Death Date</Label>
                        <Input id="death_date" type="date" class="mt-1 block w-full" v-model="form.death_date" autocomplete="death_date" placeholder="Death date" />
                        <InputError class="mt-2" :message="form.errors.death_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="deaths_reg_no">Death Registration No</Label>
                        <Input id="deaths_reg_no" class="mt-1 block w-full" v-model="form.deaths_reg_no" autocomplete="deaths_reg_no" placeholder="Death registration no" />
                        <InputError class="mt-2" :message="form.errors.deaths_reg_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="death_parish">Death Parish</Label>
                        <Input id="death_parish" class="mt-1 block w-full" v-model="form.death_parish" autocomplete="death_parish" placeholder="Death parish" />
                        <InputError class="mt-2" :message="form.errors.death_parish" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Save</Button>

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
