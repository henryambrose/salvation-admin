<script setup lang="ts">
import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import ParishSelection from '@/components/ParishSelection.vue';
import ValidationErrorModal from '@/components/ValidationErrorModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import { SelectInput } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { City, State, Town } from '@/types';
import {
  BloodGroups,
  Cities,
  Communities,
  Countries,
  Designations,
  IncomeRanges,
  Member,
  Relationships,
  States,
  Towns,
  type BreadcrumbItem,
  type CommunityCluster,
  type SharedData,
} from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { List } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

interface Props {
  member?: Member;
  communities: Communities;
  incomeRanges: IncomeRanges;
  bloodGroups: BloodGroups;
  relationships: Relationships;
  countries: Countries;
  states: States;
  cities: Cities;
  towns: Towns;
  designations: Designations;
  genders: any[];
  statuses: any[];
  parishes: any[];
  communityClusters: CommunityCluster[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Member Details',
    href: '/member/profile-details',
  },
];

const page = usePage<SharedData>();
const member = page.props.member as Member;

// Format date for HTML date input (YYYY-MM-DD)
const formatDateForInput = (dateString: string | null | undefined): string => {
  if (!dateString) return '';
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return date.toISOString().split('T')[0]; // Returns YYYY-MM-DD
  } catch (error) {
    console.error('Error formatting date:', error);
    return '';
  }
};

// Validate Indian phone number (mobile or landline)
const validateIndianPhone = (phoneNumber: string): boolean => {
  if (!phoneNumber) return true; // Allow empty values

  // Remove all non-digit characters
  const cleanNumber = phoneNumber.replace(/\D/g, '');

  // Indian Mobile Number: 10 digits starting with 6, 7, 8, 9
  const mobilePattern = /^[6-9]\d{9}$/;

  // Indian Landline Number: 10-11 digits (with or without STD code)
  const landlinePattern = /^(?:[2-4]\d{1,3})?\d{6,8}$/;

  // Check if it's a valid mobile number
  if (mobilePattern.test(cleanNumber)) {
    return true;
  }

  // Check if it's a valid landline number
  if (landlinePattern.test(cleanNumber) && cleanNumber.length >= 10 && cleanNumber.length <= 11) {
    return true;
  }

  return false;
};

// Validate email address
const validateEmail = (email: string): boolean => {
  if (!email) return true; // Allow empty values

  // Basic email format validation
  const emailRegex =
    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

  if (!emailRegex.test(email)) {
    return false;
  }

  // Additional checks
  const emailLower = email.toLowerCase().trim();

  // Check length
  if (emailLower.length > 254) {
    return false;
  }

  // Split email
  const parts = emailLower.split('@');
  if (parts.length !== 2) {
    return false;
  }

  const localPart = parts[0];
  const domain = parts[1];

  // Check local part
  if (localPart.length > 64 || localPart.length === 0) {
    return false;
  }

  // Check for consecutive dots
  if (localPart.includes('..') || domain.includes('..')) {
    return false;
  }

  // Check if starts or ends with dot
  if (localPart.startsWith('.') || localPart.endsWith('.') || domain.startsWith('.') || domain.endsWith('.')) {
    return false;
  }

  // Check TLD
  const tld = domain.split('.').pop();
  if (!tld || tld.length < 2) {
    return false;
  }

  // Check for disposable domains
  const disposableDomains = [
    '10minutemail.com',
    'guerrillamail.com',
    'mailinator.com',
    'tempmail.org',
    'throwaway.email',
    'yopmail.com',
    'temp-mail.org',
    'sharklasers.com',
  ];

  if (disposableDomains.includes(domain)) {
    return false;
  }

  return true;
};

// Aadhar validation function
const validateAadhar = (aadhar: string): boolean => {
  if (!aadhar) return true; // Allow empty values

  // Remove any spaces, dashes, or other separators
  const cleanAadhar = aadhar.replace(/\D/g, '');

  // Check if it's exactly 12 digits
  if (cleanAadhar.length !== 12) {
    return false;
  }

  // Check if it's all zeros (invalid Aadhar)
  if (cleanAadhar === '000000000000') {
    return false;
  }

  // Basic pattern check (12 digits, not all same)
  const digitPattern = /^(\d)\1{11}$/;
  if (digitPattern.test(cleanAadhar)) {
    return false;
  }

  return true;
};

// Date validation function - check if date is not in future
const validateNotFutureDate = (dateStr: string): boolean => {
  if (!dateStr) return true;
  const inputDate = new Date(dateStr);
  const today = new Date();
  today.setHours(23, 59, 59, 999); // End of today
  return inputDate <= today;
};

// Get today's date in YYYY-MM-DD format for HTML5 max attribute
const getTodayDate = (): string => {
  const today = new Date();
  return today.toISOString().split('T')[0]; // YYYY-MM-DD format
};

// Validation error modal state
const showValidationModal = ref(false);
const validationErrors = ref<Array<{ field: string; message: string }>>([]);

// Collect all validation errors
const collectValidationErrors = () => {
  const errors: Array<{ field: string; message: string }> = [];

  // Check required fields
  if (!form.first_name?.trim()) {
    errors.push({ field: 'first_name', message: 'First name is required' });
  }

  if (!form.relationship_id) {
    errors.push({ field: 'relationship_id', message: 'Relationship is required' });
  }

  // Check phone number validation
  if (form.contact_no_1 && !validateIndianPhone(form.contact_no_1)) {
    errors.push({
      field: 'contact_no_1',
      message: 'Please enter a valid Indian mobile number (10 digits starting with 6, 7, 8, 9) or landline number (10-11 digits with STD code)',
    });
  }

  if (form.contact_no_2 && !validateIndianPhone(form.contact_no_2)) {
    errors.push({
      field: 'contact_no_2',
      message: 'Please enter a valid Indian mobile number (10 digits starting with 6, 7, 8, 9) or landline number (10-11 digits with STD code)',
    });
  }

  // Check email validation
  if (form.email && !validateEmail(form.email)) {
    errors.push({ field: 'email', message: 'Please enter a valid email address. Disposable email domains are not allowed' });
  }

  // Check Aadhar validation
  if (form.aadhar && !validateAadhar(form.aadhar)) {
    errors.push({ field: 'aadhar', message: 'Please enter a valid 12-digit Aadhar number. It cannot be all zeros or repeated digits' });
  }

  // Check date validation for all date fields
  if (form.date_of_birth && !validateNotFutureDate(form.date_of_birth)) {
    errors.push({ field: 'date_of_birth', message: 'Date of birth cannot be in the future' });
  }

  if (form.baptism_date && !validateNotFutureDate(form.baptism_date)) {
    errors.push({ field: 'baptism_date', message: 'Baptism date cannot be in the future' });
  }

  if (form.confirmation_date && !validateNotFutureDate(form.confirmation_date)) {
    errors.push({ field: 'confirmation_date', message: 'Confirmation date cannot be in the future' });
  }

  if (form.marriage_date && !validateNotFutureDate(form.marriage_date)) {
    errors.push({ field: 'marriage_date', message: 'Marriage date cannot be in the future' });
  }

  if (form.death_date && !validateNotFutureDate(form.death_date)) {
    errors.push({ field: 'death_date', message: 'Death date cannot be in the future' });
  }

  // Check parish validation - only validate if both parish name and ID are provided but don't match
  // For custom parish names, parish_id should be empty/null
  // For selected parishes, parish_id should be set and parish name should match
  if (form.baptism_parish && form.baptism_parish_id) {
    // If both are provided, it should be a valid selection
    const selectedParish = props.parishes?.find((p) => p.id === form.baptism_parish_id);
    if (!selectedParish || selectedParish.name !== form.baptism_parish) {
      errors.push({
        field: 'baptism_parish',
        message: 'Please either select a parish from the dropdown or enter a custom parish name, but not both',
      });
    }
  }

  if (form.confirmation_parish && form.confirmation_parish_id) {
    const selectedParish = props.parishes?.find((p) => p.id === form.confirmation_parish_id);
    if (!selectedParish || selectedParish.name !== form.confirmation_parish) {
      errors.push({
        field: 'confirmation_parish',
        message: 'Please either select a parish from the dropdown or enter a custom parish name, but not both',
      });
    }
  }

  if (form.marriage_parish && form.marriage_parish_id) {
    const selectedParish = props.parishes?.find((p) => p.id === form.marriage_parish_id);
    if (!selectedParish || selectedParish.name !== form.marriage_parish) {
      errors.push({
        field: 'marriage_parish',
        message: 'Please either select a parish from the dropdown or enter a custom parish name, but not both',
      });
    }
  }

  if (form.death_parish && form.death_parish_id) {
    const selectedParish = props.parishes?.find((p) => p.id === form.death_parish_id);
    if (!selectedParish || selectedParish.name !== form.death_parish) {
      errors.push({ field: 'death_parish', message: 'Please either select a parish from the dropdown or enter a custom parish name, but not both' });
    }
  }

  return errors;
};

// Handle validation modal events
const handleFocusField = (field: string) => {
  // Find the field element and focus it
  const fieldElement = document.getElementById(field);
  if (fieldElement) {
    fieldElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    fieldElement.focus();

    // Add highlight effect
    fieldElement.classList.add('highlight-field');
    setTimeout(() => {
      fieldElement.classList.remove('highlight-field');
    }, 2000);
  }
};

const handleSubmitAnyway = () => {
  // Submit the form even with validation errors
  const routeName = member?.id ? 'member.update' : 'member.store';
  const method = member?.id ? 'put' : 'post';

  form[method](route(routeName, { id: member?.id }), {
    preserveScroll: true,
  });
};

const form = useForm({
  id: member?.id ? member.id : '',
  first_name: member?.first_name ? member.first_name : '',
  middle_name: member?.middle_name ? member.middle_name : '',
  last_name: member?.last_name ? member.last_name : '',
  gender_id: member?.gender_id ? member.gender_id : '',
  blood_group_id: member?.blood_group_id ? member.blood_group_id : '',
  status_id: member?.status_id ? member.status_id : '',
  relationship_id: member?.relationship_id ? member.relationship_id : '',
  parish_id: member?.parish_id ? member.parish_id : '',
  date_of_birth: formatDateForInput(member?.date_of_birth),
  contact_no_1: member?.contact_no_1 ? member.contact_no_1 : '',
  contact_no_2: member?.contact_no_2 ? member.contact_no_2 : '',
  email: member?.email ? member.email : '',
  aadhar: member?.aadhar ? member.aadhar : '',
  family_no: member?.family_no ? member.family_no : '',
  member_no: member?.member_no ? member.member_no : '',
  registration_year: member?.registration_year ? member.registration_year : '',
  church_code: member?.church_code ? member.church_code : page.props.church_code,
  family_sequence: member?.family_sequence ? member.family_sequence : '',
  member_sequence: member?.member_sequence ? member.member_sequence : '',
  marital_status: member?.marital_status ? member.marital_status : 'single',
  // current_family_no is managed by business logic (marriage, etc.) - not editable
  mother_id: member?.mother_id || null,
  father_id: member?.father_id || null,
  spouse_id: member?.spouse_id || null,
  community_id: member?.community_id ? member.community_id : '',
  community_cluster_id: member?.community_cluster_id ? member.community_cluster_id : '',
  permanent_add1: member?.permanent_add1 ? member.permanent_add1 : '',
  permanent_add2: member?.permanent_add2 ? member.permanent_add2 : '',
  permanent_add3: member?.permanent_add3 ? member.permanent_add3 : '',
  permanent_town_id: member?.permanent_town_id ? member.permanent_town_id : '',
  permanent_city_id: member?.permanent_city_id ? member.permanent_city_id : 2,
  permanent_pincode: member?.permanent_pincode ? member.permanent_pincode : '',
  permanent_state_id: member?.permanent_state_id ? member.permanent_state_id : 22, // Maharashtra
  permanent_country_id: member?.permanent_country_id ? member.permanent_country_id : 96, // India
  current_add1: member?.current_add1 ? member.current_add1 : '',
  current_add2: member?.current_add2 ? member.current_add2 : '',
  current_add3: member?.current_add3 ? member.current_add3 : '',
  current_town_id: member?.current_town_id ? member.current_town_id : '',
  current_city_id: member?.current_city_id ? member.current_city_id : 2,
  current_pincode: member?.current_pincode ? member.current_pincode : '',
  current_state_id: member?.current_state_id ? member.current_state_id : 22, // Maharashtra
  current_country_id: member?.current_country_id ? member.current_country_id : 96, // India
  school_name: member?.school_name ? member.school_name : '',
  college_name: member?.college_name ? member.college_name : '',
  latest_qualifications: member?.latest_qualifications ? member.latest_qualifications : '',
  company_name: member?.company_name ? member.company_name : '',
  designation_id: member?.designation_id ? member.designation_id : '',
  income_range_id: member?.income_range_id ? member.income_range_id : '',
  baptism_date: formatDateForInput(member?.baptism_date),
  baptism_reg_no: member?.baptism_reg_no ? member.baptism_reg_no : '',
  baptism_parish: member?.baptism_parish ? member.baptism_parish : '',
  baptism_parish_id: member?.baptism_parish_id ? member.baptism_parish_id : '',
  confirmation_date: formatDateForInput(member?.confirmation_date),
  confirmation_reg_no: member?.confirmation_reg_no ? member.confirmation_reg_no : '',
  confirmation_parish: member?.confirmation_parish ? member.confirmation_parish : '',
  confirmation_parish_id: member?.confirmation_parish_id ? member.confirmation_parish_id : '',
  marriage_date: formatDateForInput(member?.marriage_date),
  marriage_reg_no: member?.marriage_reg_no ? member.marriage_reg_no : '',
  marriage_parish: member?.marriage_parish ? member.marriage_parish : '',
  marriage_parish_id: member?.marriage_parish_id ? member.marriage_parish_id : '',
  death_date: formatDateForInput(member?.death_date),
  deaths_reg_no: member?.deaths_reg_no ? member.deaths_reg_no : '',
  death_parish: member?.death_parish ? member.death_parish : '',
  death_parish_id: member?.death_parish_id ? member.death_parish_id : '',
  spouse_source: props.member?.spouse_source || 'Member',
  father_source: props.member?.father_source || 'Member',
  mother_source: props.member?.mother_source || 'Member',
});

// Add external family members data
const externalFamilyMembers = ref<Array<{ id: number; name: string }>>([]);

// Watch for family_no changes to refetch external family members
watch(
  () => form.family_no,
  () => {
    fetchFamilyMembers();
    fetchExternalFamilyMembers(); // Also fetch external members when family changes
  },
);

// Add watchers to ensure radio button state updates
watch(
  () => form.spouse_source,
  (newValue) => {
    if (newValue === 'External') {
      fetchExternalFamilyMembers();
    } else {
      fetchFamilyMembers();
    }
  },
);

watch(
  () => form.father_source,
  (newValue) => {
    if (newValue === 'External') {
      fetchExternalFamilyMembers();
    } else {
      fetchFamilyMembers();
    }
  },
);

watch(
  () => form.mother_source,
  (newValue) => {
    if (newValue === 'External') {
      fetchExternalFamilyMembers();
    } else {
      fetchFamilyMembers();
    }
  },
);

// Modify the submit function to include debugging:
const submit = () => {
  // Collect validation errors
  const errors = collectValidationErrors();

  if (errors.length > 0) {
    // Show validation modal
    validationErrors.value = errors;
    showValidationModal.value = true;
    return;
  }

  // Proceed with form submission
  const routeName = member?.id ? 'member.update' : 'member.store';
  const method = member?.id ? 'put' : 'post';

  form[method](route(routeName, { id: member?.id }), {
    preserveScroll: true,
    onError: (errors: any) => {
      // Handle validation errors
    },
  });
};

const communityClusters = ref<CommunityCluster[]>([]);

const fetchCommunityCluster = async () => {
  if (!form.community_id) {
    // If no community is selected but we have a cluster_id, show all clusters
    if (form.community_cluster_id) {
      communityClusters.value = props.communityClusters;
    } else {
      communityClusters.value = [];
    }
    return;
  }

  // Filter community clusters based on the selected community
  const filteredClusters = props.communityClusters.filter((cluster: CommunityCluster) => Number(cluster.community_id) === Number(form.community_id));

  communityClusters.value = filteredClusters;
};

// Debug watcher for communityClusters ref
watch(communityClusters, (newValue) => {});

watch(
  () => form.community_id,
  () => {
    fetchCommunityCluster();
  },
);

// Debug watcher for community_cluster_id
watch(
  () => form.community_cluster_id,
  () => {},
);

// Auto-populate pincode when permanent town is selected
watch(
  () => form.permanent_town_id,
  (newTownId) => {
    if (newTownId) {
      const selectedTown = props.towns?.find((town) => town.id === newTownId);
      if (selectedTown?.pincode) {
        form.permanent_pincode = selectedTown.pincode;
      }
    } else {
      // Clear pincode when town is cleared
      form.permanent_pincode = '';
    }
  },
);

// Auto-populate pincode when current town is selected
watch(
  () => form.current_town_id,
  (newTownId) => {
    if (newTownId) {
      const selectedTown = props.towns?.find((town) => town.id === newTownId);
      if (selectedTown?.pincode) {
        form.current_pincode = selectedTown.pincode;
      }
    } else {
      // Clear pincode when town is cleared
      form.current_pincode = '';
    }
  },
);

// Parish selection computed properties
const baptismParishSelection = computed({
  get: () => ({
    parishId: form.baptism_parish_id ? Number(form.baptism_parish_id) : null,
    parishName: form.baptism_parish || null,
  }),
  set: (value: { parishId?: number | null; parishName?: string | null }) => {
    form.baptism_parish_id = value.parishId || '';
    form.baptism_parish = value.parishName || '';
  },
});

const confirmationParishSelection = computed({
  get: () => ({
    parishId: form.confirmation_parish_id ? Number(form.confirmation_parish_id) : null,
    parishName: form.confirmation_parish || null,
  }),
  set: (value: { parishId?: number | null; parishName?: string | null }) => {
    form.confirmation_parish_id = value.parishId || '';
    form.confirmation_parish = value.parishName || '';
  },
});

const marriageParishSelection = computed({
  get: () => ({
    parishId: form.marriage_parish_id ? Number(form.marriage_parish_id) : null,
    parishName: form.marriage_parish || null,
  }),
  set: (value: { parishId?: number | null; parishName?: string | null }) => {
    form.marriage_parish_id = value.parishId || '';
    form.marriage_parish = value.parishName || '';
  },
});

const deathParishSelection = computed({
  get: () => ({
    parishId: form.death_parish_id ? Number(form.death_parish_id) : null,
    parishName: form.death_parish || null,
  }),
  set: (value: { parishId?: number | null; parishName?: string | null }) => {
    form.death_parish_id = value.parishId || '';
    form.death_parish = value.parishName || '';
  },
});

// Watch for member prop changes and update form data
// Only update if the member ID actually changes (not on validation errors)
watch(
  () => props.member?.id,
  (newMemberId, oldMemberId) => {
    // Only update form data if we're switching to a different member
    // or if this is the initial load (oldMemberId is undefined)
    if (newMemberId !== oldMemberId && props.member) {
      // Update form with new member data
      form.id = props.member.id || '';
      form.first_name = props.member.first_name || '';
      form.middle_name = props.member.middle_name || '';
      form.last_name = props.member.last_name || '';
      form.gender_id = props.member.gender_id || '';
      form.blood_group_id = props.member.blood_group_id || '';
      form.status_id = props.member.status_id || '';
      form.relationship_id = props.member.relationship_id || '';
      form.parish_id = props.member.parish_id || '';
      form.date_of_birth = formatDateForInput(props.member.date_of_birth);
      form.contact_no_1 = props.member.contact_no_1 || '';
      form.contact_no_2 = props.member.contact_no_2 || '';
      form.email = props.member.email || '';
      form.aadhar = props.member.aadhar || '';
      form.family_no = props.member.family_no || '';
      form.member_no = props.member.member_no || '';
      form.registration_year = props.member.registration_year || '';
      form.church_code = props.member.church_code || page.props.church_code;
      form.family_sequence = props.member.family_sequence || '';
      form.member_sequence = props.member.member_sequence || '';
      form.marital_status = props.member.marital_status || 'single';
      form.father_id = props.member.father_id || null;
      form.mother_id = props.member.mother_id || null;
      form.spouse_id = props.member.spouse_id || null;
      form.community_id = props.member.community_id || '';
      form.community_cluster_id = props.member.community_cluster_id || '';
      form.permanent_add1 = props.member.permanent_add1 || '';
      form.permanent_add2 = props.member.permanent_add2 || '';
      form.permanent_add3 = props.member.permanent_add3 || '';
      form.permanent_town_id = props.member.permanent_town_id || '';
      form.permanent_city_id = props.member.permanent_city_id || 2;
      form.permanent_pincode = props.member.permanent_pincode || '';
      form.permanent_state_id = props.member.permanent_state_id || 22;
      form.permanent_country_id = props.member.permanent_country_id || 96;
      form.current_add1 = props.member.current_add1 || '';
      form.current_add2 = props.member.current_add2 || '';
      form.current_add3 = props.member.current_add3 || '';
      form.current_town_id = props.member.current_town_id || '';
      form.current_city_id = props.member.current_city_id || 2;
      form.current_pincode = props.member.current_pincode || '';
      form.current_state_id = props.member.current_state_id || 22;
      form.current_country_id = props.member.current_country_id || 96;
      form.school_name = props.member.school_name || '';
      form.college_name = props.member.college_name || '';
      form.latest_qualifications = props.member.latest_qualifications || '';
      form.company_name = props.member.company_name || '';
      form.designation_id = props.member.designation_id || '';
      form.income_range_id = props.member.income_range_id || '';
      form.baptism_date = formatDateForInput(props.member.baptism_date);
      form.baptism_reg_no = props.member.baptism_reg_no || '';
      form.baptism_parish = props.member.baptism_parish || '';
      form.baptism_parish_id = props.member.baptism_parish_id || '';
      form.confirmation_date = formatDateForInput(props.member.confirmation_date);
      form.confirmation_reg_no = props.member.confirmation_reg_no || '';
      form.confirmation_parish = props.member.confirmation_parish || '';
      form.confirmation_parish_id = props.member.confirmation_parish_id || '';
      form.marriage_date = formatDateForInput(props.member.marriage_date);
      form.marriage_reg_no = props.member.marriage_reg_no || '';
      form.marriage_parish = props.member.marriage_parish || '';
      form.marriage_parish_id = props.member.marriage_parish_id || '';
      form.death_date = formatDateForInput(props.member.death_date);
      form.deaths_reg_no = props.member.deaths_reg_no || '';
      form.death_parish = props.member.death_parish || '';
      form.death_parish_id = props.member.death_parish_id || '';
      form.spouse_source = props.member.spouse_source || 'Member';
      form.father_source = props.member.father_source || 'Member';
      form.mother_source = props.member.mother_source || 'Member';
    }
  },
  { immediate: true },
);

// Initialize community clusters when component loads
watch(
  () => props.communityClusters,
  () => {
    // If we have a community_cluster_id but no community_id, we need to find the community
    if (form.community_cluster_id && !form.community_id) {
      const cluster = props.communityClusters.find((c) => c.id === form.community_cluster_id);
      if (cluster) {
        form.community_id = cluster.community_id;
      }
    }

    if (form.community_id) {
      fetchCommunityCluster();
    }
  },
  { immediate: true },
);

const filteredStatesPermanent = ref<State[]>([]);

const fetchfilteredStatesPermanent = async () => {
  const countryId = form.permanent_country_id || 95; // Default to India if not set
  filteredStatesPermanent.value = props.states.filter((state) => state.country_id === Number(countryId));
};

watch(
  () => form.permanent_country_id,
  () => {
    fetchfilteredStatesPermanent();
  },
);

const filteredCitiesPermanent = ref<City[]>([]);

const fetchfilteredCitiesPermanent = async () => {
  if (!form.permanent_state_id) return;
  filteredCitiesPermanent.value = props.cities.filter((city) => city.state_id === Number(form.permanent_state_id));
};

watch(
  () => form.permanent_state_id,
  () => {
    fetchfilteredCitiesPermanent();
  },
);

const filteredTownPermanent = ref<Town[]>([]);

const fetchfilteredTownPermanent = async () => {
  if (!form.permanent_city_id) return;
  filteredTownPermanent.value = props.towns.filter((town) => town.city_id === Number(form.permanent_city_id));
};

watch(
  () => form.permanent_city_id,
  () => {
    fetchfilteredTownPermanent();
  },
);

const filteredStatesCurrent = ref<State[]>([]);

const fetchfilteredStatesCurrent = async () => {
  const countryId = form.current_country_id || 96; // Default to India if not set
  filteredStatesCurrent.value = props.states.filter((state) => state.country_id === Number(countryId));
};

watch(
  () => form.current_country_id,
  () => {
    fetchfilteredStatesCurrent();
  },
);

const filteredCitiesCurrent = ref<City[]>([]);

const fetchfilteredCitiesCurrent = async () => {
  if (!form.current_state_id) return;
  filteredCitiesCurrent.value = props.cities.filter((city) => city.state_id === Number(form.current_state_id));
};

watch(
  () => form.current_state_id,
  () => {
    fetchfilteredCitiesCurrent();
  },
);

const filteredTownCurrent = ref<Town[]>([]);

const fetchfilteredTownCurrent = async () => {
  if (!form.current_city_id) return;
  filteredTownCurrent.value = props.towns.filter((town) => town.city_id === Number(form.current_city_id));
};

watch(
  () => form.current_city_id,
  () => {
    fetchfilteredTownCurrent();
  },
);

// Initialize filtered arrays on component mount
nextTick(() => {
  fetchfilteredCitiesPermanent();
  fetchfilteredCitiesCurrent();
  fetchfilteredStatesPermanent();
  fetchfilteredStatesCurrent();
  fetchfilteredTownPermanent();
  fetchfilteredTownCurrent();
});

// Initialize community clusters on mount
onMounted(() => {
  if (form.community_cluster_id && !form.community_id) {
    const cluster = props.communityClusters.find((c) => c.id === form.community_cluster_id);
    if (cluster) {
      form.community_id = cluster.community_id;
    }
  }

  if (form.community_id) {
    fetchCommunityCluster();
  }
});

function cancel() {
  window.location.href = '/member/index';
}

const selectedDesignations = ref([]); // For v-model

// Same as permanent address functionality
const sameAsPermanent = ref(false);

const copyPermanentToCurrent = () => {
  if (sameAsPermanent.value) {
    // Copy permanent address to current address
    form.current_add1 = form.permanent_add1;
    form.current_add2 = form.permanent_add2;
    form.current_add3 = form.permanent_add3;
    form.current_town_id = form.permanent_town_id;
    form.current_city_id = form.permanent_city_id;
    form.current_pincode = form.permanent_pincode;
    form.current_state_id = form.permanent_state_id;
    form.current_country_id = form.permanent_country_id;

    // Update filtered dropdowns
    nextTick(() => {
      fetchfilteredTownCurrent();
      fetchfilteredStatesCurrent();
      fetchfilteredCitiesCurrent();
    });
  }
};

function formatDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB'); // dd/mm/yyyy
}

// Add this watcher after the existing watchers (around line 600)
watch(
  () => form.mother_id,
  (newValue, oldValue) => {},
);

// Function to fetch member details for display
const fetchMemberDetails = async (memberId: number) => {
  try {
    const response = await fetch(`/api/members/search?search=${memberId}`);
    const data = await response.json();
    return data.find((member: any) => member.id === memberId);
  } catch (error) {
    console.error('Error fetching member details:', error);
    return null;
  }
};

// Reactive variables to store family members
const familyMembers = ref<Array<{ id: number; name: string }>>([]);

// Function to fetch family members
const fetchFamilyMembers = async () => {
  if (form.family_no) {
    try {
      const response = await fetch(`/member/family-details/${form.family_no}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        credentials: 'same-origin',
      });
      if (response.ok) {
        const data = await response.json();
        // Filter members from the same family and exclude current member
        const currentMemberId = member?.id;

        familyMembers.value = data.members
          .filter((member: any) => member.id !== currentMemberId && member.source === 'Member')

          .map((member: any) => ({
            id: member.id,
            name: member.first_name + ' ' + member.last_name,
          }));
      }
    } catch (error) {
      console.error('Error fetching family members:', error);
      familyMembers.value = [];
    }
  } else {
    familyMembers.value = [];
  }
};

// Function to fetch external family members
const fetchExternalFamilyMembers = async () => {
  if (form.family_no) {
    try {
      // If we have a family number, fetch external members from the same family
      const response = await fetch(`/external-member/family-details/${form.family_no}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        credentials: 'same-origin',
      });

      if (response.ok) {
        const data = await response.json();
        // Filter only external members from the same family
        const currentMemberId = member?.id;

        externalFamilyMembers.value = data.members
          .filter((member: any) => member.id !== currentMemberId && member.source === 'External')
          .map((member: any) => ({
            id: member.id,
            name: member.first_name + ' ' + member.last_name,
          }));
      }
    } catch (error) {
      console.error('Error fetching external family members:', error);
      externalFamilyMembers.value = [];
    }
  } else {
    // If no family number, show empty array
    externalFamilyMembers.value = [];
  }
};
// Watch for family_no changes to refetch family members
watch(
  () => form.family_no,
  () => {
    fetchFamilyMembers();
    fetchExternalFamilyMembers();
  },
);

// Fetch family members on mount
onMounted(() => {
  // Fetch family members since Member is the default
  fetchFamilyMembers();

  // Also fetch external members in case they're needed later
  fetchExternalFamilyMembers();
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Members" />
    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Members</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/member/index" class="flex items-center gap-2 rounded-full bg-blue-100 text-blue-700 transition hover:bg-blue-200">
            <component :is="List" />
            <span>Member List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Personal Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Personal Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="first_name">First Name</Label>
              <Input
                id="first_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.first_name"
                autocomplete="first_name"
                placeholder="First name"
              />
              <InputError class="mt-2" :message="form.errors.first_name" />
            </div>
            <div class="grid gap-2">
              <Label for="middle_name">Middle Name</Label>
              <Input
                id="middle_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.middle_name"
                autocomplete="middle_name"
                placeholder="Middle name"
              />
              <InputError class="mt-2" :message="form.errors.middle_name" />
            </div>
            <div class="grid gap-2">
              <Label for="last_name">Last Name</Label>
              <Input
                id="last_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.last_name"
                autocomplete="last_name"
                placeholder="Last name"
              />
              <InputError class="mt-2" :message="form.errors.last_name" />
            </div>
            <div class="grid gap-2">
              <Label for="gender">Gender</Label>
              <SelectInput
                id="gender"
                v-model="form.gender_id"
                :options="props.genders"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Gender"
              />
              <InputError class="mt-2" :message="form.errors.gender_id" />
            </div>
            <div class="grid gap-2">
              <Label for="date_of_birth">Date of Birth</Label>
              <Input
                id="date_of_birth"
                type="date"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.date_of_birth && !validateNotFutureDate(form.date_of_birth)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.date_of_birth"
                :max="getTodayDate()"
                autocomplete="date_of_birth"
                placeholder="Date of birth"
              />
              <div v-if="!showValidationModal && form.date_of_birth && !validateNotFutureDate(form.date_of_birth)" class="mt-1 text-sm text-red-500">
                Date cannot be in the future.
              </div>
              <InputError class="mt-2" :message="form.errors.date_of_birth" />
            </div>
            <div class="grid gap-2">
              <Label for="status_id">Status</Label>
              <SelectInput
                id="status_id"
                v-model="form.status_id"
                :options="props.statuses"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Status"
              />
              <InputError class="mt-2" :message="form.errors.status_id" />
            </div>
            <div class="grid gap-2">
              <Label for="relationship_id">
                Relationship <span class="text-red-500">*</span>
                <span class="text-xs font-normal text-gray-500">(with the head of the family)</span>
              </Label>
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
              <Label for="contact_no_1">Contact No 1</Label>
              <Input
                id="contact_no_1"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.contact_no_1 && !validateIndianPhone(form.contact_no_1)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.contact_no_1"
                autocomplete="contact_no_1"
                placeholder="Primary contact number (e.g., 9876543210 or 022-12345678)"
              />
              <div v-if="!showValidationModal && form.contact_no_1 && !validateIndianPhone(form.contact_no_1)" class="mt-1 text-sm text-red-500">
                Please enter a valid Indian mobile number (10 digits starting with 6, 7, 8, 9) or landline number (10-11 digits with STD code).
              </div>
              <InputError class="mt-2" :message="form.errors.contact_no_1" />
            </div>
            <div class="grid gap-2">
              <Label for="contact_no_2">Contact No 2</Label>
              <Input
                id="contact_no_2"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.contact_no_2 && !validateIndianPhone(form.contact_no_2)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.contact_no_2"
                autocomplete="contact_no_2"
                placeholder="Secondary contact number (e.g., 9876543210 or 022-12345678)"
              />
              <div v-if="!showValidationModal && form.contact_no_2 && !validateIndianPhone(form.contact_no_2)" class="mt-1 text-sm text-red-500">
                Please enter a valid Indian mobile number (10 digits starting with 6, 7, 8, 9) or landline number (10-11 digits with STD code).
              </div>
              <InputError class="mt-2" :message="form.errors.contact_no_2" />
            </div>
            <div class="grid gap-2">
              <Label for="email">Email</Label>
              <Input
                id="email"
                type="email"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.email && !validateEmail(form.email) ? 'border-red-300 focus:ring-red-200' : 'border-gray-300',
                ]"
                v-model="form.email"
                autocomplete="email"
                placeholder="Enter email address (e.g., user@example.com)"
              />
              <div v-if="!showValidationModal && form.email && !validateEmail(form.email)" class="mt-1 text-sm text-red-500">
                Please enter a valid email address. Disposable email domains are not allowed.
              </div>
              <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="grid gap-2">
              <Label for="aadhar">Aadhar</Label>
              <Input
                id="aadhar"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.aadhar && !validateAadhar(form.aadhar) ? 'border-red-300 focus:ring-red-200' : 'border-gray-300',
                ]"
                v-model="form.aadhar"
                autocomplete="aadhar"
                placeholder="Enter 12-digit Aadhar number (e.g., 234567890123)"
              />
              <div v-if="!showValidationModal && form.aadhar && !validateAadhar(form.aadhar)" class="mt-1 text-sm text-red-500">
                Please enter a valid 12-digit Aadhar number. It cannot start with 0 or 1, and cannot be all zeros or repeated digits.
              </div>
              <InputError class="mt-2" :message="form.errors.aadhar" />
            </div>

            <div class="grid gap-2">
              <Label for="marital_status">Marital Status</Label>
              <SelectInput
                id="marital_status"
                v-model="form.marital_status"
                :options="[
                  { id: 'single', name: 'Single' },
                  { id: 'married', name: 'Married' },
                  { id: 'divorced', name: 'Divorced' },
                  { id: 'widowed', name: 'Widowed' },
                ]"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Marital Status"
              />
              <InputError class="mt-2" :message="form.errors.marital_status" />
            </div>
            <div class="grid gap-2">
              <Label for="current_family_no">Current Family No</Label>
              <div class="mt-1 block w-full rounded-full border border-gray-300 bg-gray-50 px-4 py-2 text-gray-600">
                <span v-if="member?.current_family_no">{{ member.current_family_no }}</span>
                <span v-else class="mt-1 text-xs text-gray-500"> Managed automatically through marriage and family changes </span>
              </div>
            </div>
          </div>

          <!-- Family Information Display -->
          <div v-if="form.family_no" class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
            <h4 class="mb-3 text-sm font-semibold text-blue-800">Family Information</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div class="flex items-center gap-2">
                <span class="font-medium text-gray-700">Family Number:</span>
                <span class="rounded-full bg-green-100 px-2 py-1 font-mono text-xs font-medium text-green-800">
                  {{ form.family_no }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-medium text-gray-700">Member Number:</span>
                <span class="rounded-full bg-purple-100 px-2 py-1 font-mono text-xs font-medium text-purple-800">
                  {{ form.member_no || 'Auto-generated' }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-medium text-gray-700">Church Code:</span>
                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                  {{ form.church_code || page.props.church_code }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-medium text-gray-700">Registration Year:</span>
                <span class="text-gray-900">{{ form.registration_year || 'Current Year' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Family Tree Relationships Section - NEW -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Family Tree Relationships</h3>
          <p class="mb-4 text-sm text-gray-600">
            Define family relationships for building the family tree. Select whether each relationship is with a member or external person.
          </p>

          <!-- Spouse Relationship -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-md mb-3 font-semibold text-gray-800">Spouse</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Source Type</Label>
                <div class="flex gap-4">
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.spouse_source"
                      value="Member"
                      class="text-blue-600 focus:ring-[#3b82f6]"
                      :checked="form.spouse_source === 'Member'"
                    />
                    <span class="text-sm">Member</span>
                  </label>
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.spouse_source"
                      value="External"
                      class="text-blue-600 focus:ring-[#3b82f6]"
                      :checked="form.spouse_source === 'External'"
                    />
                    <span class="text-sm">External</span>
                  </label>
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="spouse_id">Spouse</Label>
                <div class="flex gap-2">
                  <SearchDropdown
                    :model-value="form.spouse_id || undefined"
                    @update:model-value="(value) => (form.spouse_id = Number(value))"
                    :options="form.spouse_source === 'Member' ? familyMembers : externalFamilyMembers"
                    class="mt-1 block w-full rounded-full"
                    :placeholder="form.spouse_source === 'Member' ? 'Search for spouse (member)...' : 'Search for spouse (external)...'"
                  />
                  <Button type="button" @click="form.spouse_id = null" variant="outline" class="border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    Clear
                  </Button>
                </div>
                <InputError class="mt-2" :message="form.errors.spouse_id" />
              </div>
            </div>
          </div>

          <!-- Father Relationship -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-md mb-3 font-semibold text-gray-800">Father</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Source Type</Label>
                <div class="flex gap-4">
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.father_source"
                      value="Member"
                      class="text-blue-600 focus:ring-[#3b82f6]"
                      :checked="form.father_source === 'Member'"
                    />
                    <span class="text-sm">Member</span>
                  </label>
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.father_source"
                      value="External"
                      class="text-blue-600 focus:ring-[#3b82f6]"
                      :checked="form.father_source === 'External'"
                    />
                    <span class="text-sm">External</span>
                  </label>
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="father_id">Father</Label>
                <div class="flex gap-2">
                  <SearchDropdown
                    :model-value="form.father_id || undefined"
                    @update:model-value="(value) => (form.father_id = Number(value))"
                    :options="form.father_source === 'Member' ? familyMembers : externalFamilyMembers"
                    class="mt-1 block w-full rounded-full"
                    :placeholder="form.father_source === 'Member' ? 'Search for father (member)...' : 'Search for father (external)...'"
                  />
                  <Button type="button" @click="form.father_id = null" variant="outline" class="border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    Clear
                  </Button>
                </div>
                <InputError class="mt-2" :message="form.errors.father_id" />
              </div>
            </div>
          </div>

          <!-- Mother Relationship -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-md mb-3 font-semibold text-gray-800">Mother</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Source Type</Label>
                <div class="flex gap-4">
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.mother_source"
                      value="Member"
                      class="text-blue-600 focus:ring-[#3b82f6]"
                      :checked="form.mother_source === 'Member'"
                    />
                    <span class="text-sm">Member</span>
                  </label>
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.mother_source"
                      value="External"
                      class="text-blue-600 focus:ring-[#3b82f6]"
                      :checked="form.mother_source === 'External'"
                    />
                    <span class="text-sm">External</span>
                  </label>
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="mother_id">Mother</Label>
                <div class="flex gap-2">
                  <SearchDropdown
                    :model-value="form.mother_id || undefined"
                    @update:model-value="(value) => (form.mother_id = Number(value))"
                    :options="form.mother_source === 'Member' ? familyMembers : externalFamilyMembers"
                    class="mt-1 block w-full rounded-full"
                    :placeholder="form.mother_source === 'Member' ? 'Search for mother (member)...' : 'Search for mother (external)...'"
                  />
                  <Button type="button" @click="form.mother_id = null" variant="outline" class="border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    Clear
                  </Button>
                </div>
                <InputError class="mt-2" :message="form.errors.mother_id" />
              </div>
            </div>
          </div>
        </div>

        <!-- Community Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Community Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="community_id">Community <span class="text-red-500">*</span></Label>
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
              <Label for="community_cluster_id">Community Cluster <span class="text-red-500">*</span></Label>
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
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Permanent Address</h3>
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
              <Label for="permanent_city">City</Label>
              <SearchDropdown
                id="permanent_city"
                v-model="form.permanent_city_id"
                :options="filteredCitiesPermanent"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Permanent City"
              />
              <InputError class="mt-2" :message="form.errors.permanent_city_id" />
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
          </div>
        </div>

        <!-- Current Address -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Current Address</h3>

          <!-- Same as Permanent Address Checkbox -->
          <div class="mb-4">
            <label class="flex cursor-pointer items-center gap-2 select-none">
              <input
                type="checkbox"
                v-model="sameAsPermanent"
                @change="copyPermanentToCurrent"
                class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
              />
              <span class="text-sm font-medium text-gray-700">Same as Permanent Address</span>
            </label>
          </div>

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
              <Label for="current_city">City</Label>
              <SearchDropdown
                id="current_city"
                v-model="form.current_city_id"
                :options="filteredCitiesCurrent"
                class="mt-1 block w-full rounded-full"
                placeholder="Select Current City"
              />
              <InputError class="mt-2" :message="form.errors.current_city_id" />
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
          </div>
        </div>

        <!-- Education & Work Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Education & Work Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="school_name">School Name</Label>
              <Input
                id="school_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.school_name"
                autocomplete="school_name"
                placeholder="School name"
              />
              <InputError class="mt-2" :message="form.errors.school_name" />
            </div>
            <div class="grid gap-2">
              <Label for="college_name">College Name</Label>
              <Input
                id="college_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.college_name"
                autocomplete="college_name"
                placeholder="College name"
              />
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
              <Input
                id="company_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                v-model="form.company_name"
                autocomplete="company_name"
                placeholder="Company name"
              />
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
              <Label for="income_range_id">Income Range</Label>
              <SelectInput
                id="income_range_id"
                v-model="form.income_range_id"
                :options="props.incomeRanges"
                placeholder="Select income range"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
              />
              <InputError class="mt-2" :message="form.errors.income_range_id" />
            </div>
          </div>
        </div>

        <!-- Sacrament Details -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Sacrament Details</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="baptism_date">Baptism Date</Label>
              <Input
                id="baptism_date"
                type="date"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.baptism_date && !validateNotFutureDate(form.baptism_date)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.baptism_date"
                :max="getTodayDate()"
                autocomplete="baptism_date"
                placeholder="Baptism date"
              />
              <div v-if="!showValidationModal && form.baptism_date && !validateNotFutureDate(form.baptism_date)" class="mt-1 text-sm text-red-500">
                Date cannot be in the future.
              </div>
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
              <ParishSelection id="baptism_parish" label="Baptism Parish" v-model="baptismParishSelection" :parishes="props.parishes" />
              <InputError class="mt-2" :message="form.errors.baptism_parish" />
              <InputError class="mt-2" :message="form.errors.baptism_parish_id" />
            </div>
            <div class="grid gap-2">
              <Label for="confirmation_date">Confirmation Date</Label>
              <Input
                id="confirmation_date"
                type="date"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.confirmation_date && !validateNotFutureDate(form.confirmation_date)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.confirmation_date"
                :max="getTodayDate()"
                autocomplete="confirmation_date"
                placeholder="Confirmation date"
              />
              <div
                v-if="!showValidationModal && form.confirmation_date && !validateNotFutureDate(form.confirmation_date)"
                class="mt-1 text-sm text-red-500"
              >
                Date cannot be in the future.
              </div>
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
              <ParishSelection
                id="confirmation_parish"
                label="Confirmation Parish"
                v-model="confirmationParishSelection"
                :parishes="props.parishes"
              />
              <InputError class="mt-2" :message="form.errors.confirmation_parish" />
              <InputError class="mt-2" :message="form.errors.confirmation_parish_id" />
            </div>
            <div class="grid gap-2">
              <Label for="marriage_date">Marriage Date</Label>
              <Input
                id="marriage_date"
                type="date"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.marriage_date && !validateNotFutureDate(form.marriage_date)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.marriage_date"
                :max="getTodayDate()"
                autocomplete="marriage_date"
                placeholder="Marriage date"
              />
              <div v-if="!showValidationModal && form.marriage_date && !validateNotFutureDate(form.marriage_date)" class="mt-1 text-sm text-red-500">
                Date cannot be in the future.
              </div>
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
              <ParishSelection id="marriage_parish" label="Marriage Parish" v-model="marriageParishSelection" :parishes="props.parishes" />
              <InputError class="mt-2" :message="form.errors.marriage_parish" />
              <InputError class="mt-2" :message="form.errors.marriage_parish_id" />
            </div>
            <div class="grid gap-2">
              <Label for="death_date">Death Date</Label>
              <Input
                id="death_date"
                type="date"
                :class="[
                  'mt-1 block w-full rounded-full px-4 py-2 shadow focus:ring-2 focus:ring-blue-200',
                  !showValidationModal && form.death_date && !validateNotFutureDate(form.death_date)
                    ? 'border-red-300 focus:ring-red-200'
                    : 'border-gray-300',
                ]"
                v-model="form.death_date"
                :max="getTodayDate()"
                autocomplete="death_date"
                placeholder="Death date"
              />
              <div v-if="!showValidationModal && form.death_date && !validateNotFutureDate(form.death_date)" class="mt-1 text-sm text-red-500">
                Date cannot be in the future.
              </div>
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
              <ParishSelection id="death_parish" label="Death Parish" v-model="deathParishSelection" :parishes="props.parishes" />
              <InputError class="mt-2" :message="form.errors.death_parish" />
              <InputError class="mt-2" :message="form.errors.death_parish_id" />
            </div>
          </div>
        </div>

        <!-- Family Numbering Details -->

        <!-- Action Bar -->
        <div class="sticky right-0 bottom-0 left-0 z-10 flex items-center gap-4 rounded-b-2xl bg-gray-50 p-4 shadow-inner">
          <Button
            :disabled="form.processing"
            class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-2 text-white shadow transition hover:bg-blue-700"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ member ? 'Update Member' : 'Create Member' }}
          </Button>
          <Button type="button" @click="cancel" variant="outline" class="flex items-center gap-2 rounded-full px-6 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
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

  <!-- Validation Error Modal -->
  <ValidationErrorModal
    v-model="showValidationModal"
    :errors="validationErrors"
    @focus-field="handleFocusField"
    @submit-anyway="handleSubmitAnyway"
  />
</template>

<style>
.highlight-field {
  animation: highlight-fade 2s;
  background-color: #fef08a !important; /* Tailwind yellow-200 */
  border-color: #f59e0b !important; /* Tailwind amber-500 */
}
@keyframes highlight-fade {
  0% {
    background-color: #fde047; /* Tailwind yellow-300 */
    border-color: #f59e0b; /* Tailwind amber-500 */
  }
  100% {
    background-color: inherit;
    border-color: inherit;
  }
}
</style>
