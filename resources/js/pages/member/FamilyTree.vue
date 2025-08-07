<template>
  <div class="p-6">
    <!-- Debug Information -->
    <!-- <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
      <h3 class="text-lg font-semibold text-yellow-800 mb-2">Debug Information</h3>
      <div class="text-sm text-yellow-700 space-y-1">
        <div><strong>Member ID:</strong> {{ member?.id }}</div>
        <div><strong>Member Name:</strong> {{ member?.full_name }}</div>
        <div><strong>Family No:</strong> {{ member?.family_no }}</div>
        <div><strong>Family Head:</strong> {{ familyTree?.familyHead ? familyTree.familyHead.name + ' (ID: ' + familyTree.familyHead.id + ', Gender: ' + familyTree.familyHead.gender + ')' : 'Not found' }}</div>
        <div><strong>Relationship to Head:</strong> {{ familyTree?.familyHead?.relationship_to_head || 'N/A' }}</div>
        <div><strong>Relationships Count:</strong> {{ relationships?.length || 0 }}</div>
        <div><strong>Parents Count:</strong> {{ familyTree?.parents?.length || 0 }}</div>
        <div><strong>Spouse:</strong> {{ familyTree?.spouse ? 'Yes' : 'No' }}</div>
        <div><strong>Children Count:</strong> {{ familyTree?.children?.length || 0 }}</div>
        <div><strong>Siblings Count:</strong> {{ familyTree?.siblings?.length || 0 }}</div>
        <div><strong>Family Members Count:</strong> {{ familyTree?.familyMembers?.length || 0 }}</div>
        <div><strong>Dynamic Relationship Logic:</strong> {{ familyTree?.debug?.currentMemberAsCenter ? 'Active' : 'Inactive' }}</div>
        <div><strong>Approach:</strong> {{ familyTree?.debug?.approach || 'dynamic_relationship_calculation' }}</div>
        <div><strong>Debug Info:</strong> {{ familyTree?.debug ? JSON.stringify(familyTree.debug, null, 2) : 'No debug info' }}</div>
      </div>
      <div class="mt-2 space-x-2">
        <button @click="debugFamilyLinks" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
          Debug Family Links
        </button>
        <button @click="debugSpecificMember" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
          Debug Specific Member
        </button>
        <button @click="debugMemberById" class="px-3 py-1 text-sm bg-purple-600 text-white rounded hover:bg-purple-700">
          Debug Member ID 5
        </button>
      </div>
    </div> -->

    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Family Tree - {{ member.full_name }}</h1>
      <p class="text-gray-600">Family: {{ member.family_no }} | Member: {{ member.member_no }}</p>
    </div>

    <!-- Current Member -->
    <div class="mb-8">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Member</h2>
      <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div>
            <div class="font-medium text-blue-900">{{ member.first_name }} {{ member.last_name }}</div>
            <div class="text-sm text-blue-700">{{ member.gender?.name || 'N/A' }} • {{ member.relationship?.name ||
              'N/A' }}</div>
            <div class="text-xs text-blue-600">{{ member.member_no }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Defined Relationships -->
    <div v-if="familyTree.parents && familyTree.parents.length > 0" class="mb-6">
      <h3 class="text-md font-medium text-gray-700 mb-3">Parents</h3>
      <div class="grid grid-cols-2 gap-4">
        <div v-for="parent in familyTree.parents" :key="parent.member.id"
          class="p-3 bg-green-50 rounded-lg border border-green-200">
          <div class="font-medium">{{ parent.member.full_name }}</div>
          <div class="text-sm text-green-600">{{ parent.relationship }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ parent.member.member_no }}</div>
        </div>
      </div>
    </div>

    <div v-if="familyTree.spouse" class="mb-6">
      <h3 class="text-md font-medium text-gray-700 mb-3">Spouse</h3>
      <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
        <div class="font-medium">{{ familyTree.spouse.member.full_name }}</div>
        <div class="text-sm text-purple-600">{{ familyTree.spouse.relationship }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ familyTree.spouse.member.member_no }}</div>
      </div>
    </div>

    <div v-if="familyTree.children && familyTree.children.length > 0" class="mb-6">
      <h3 class="text-md font-medium text-gray-700 mb-3">Children</h3>
      <div class="grid grid-cols-2 gap-4">
        <div v-for="child in familyTree.children" :key="child.member.id"
          class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
          <div class="font-medium">{{ child.member.full_name }}</div>
          <div class="text-sm text-yellow-600">{{ child.relationship }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ child.member.member_no }}</div>
        </div>
      </div>
    </div>

    <div v-if="familyTree.siblings && familyTree.siblings.length > 0" class="mb-6">
      <h3 class="text-md font-medium text-gray-700 mb-3">Siblings</h3>
      <div class="grid grid-cols-2 gap-4">
        <div v-for="sibling in familyTree.siblings" :key="sibling.member.id"
          class="p-3 bg-orange-50 rounded-lg border border-orange-200">
          <div class="font-medium">{{ sibling.member.full_name }}</div>
          <div class="text-sm text-orange-600">{{ sibling.relationship }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ sibling.member.member_no }}</div>
        </div>
      </div>
    </div>

    <!-- Family Members Section -->
    <div v-if="internalFamilyMembers.length > 0" class="mb-8">
      <h3 class="text-lg font-semibold mb-4 text-blue-800">Family Members</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="familyMember in internalFamilyMembers" 
             :key="familyMember.member.id"
             class="p-4 rounded-lg border-2 bg-blue-50 border-blue-200">
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-900">{{ familyMember.member.full_name }}</h4>
            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              Internal
            </span>
          </div>
          <p class="text-sm text-gray-600 mb-2">{{ familyMember.relationship }}</p>
          <p class="text-xs text-gray-500">{{ familyMember.member.member_no }}</p>
        </div>
      </div>
    </div>

    <!-- External Members Section -->
    <div v-if="externalFamilyMembers.length > 0" class="mb-8">
      <h3 class="text-lg font-semibold mb-4 text-orange-800">External Family Members</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="externalMember in externalFamilyMembers" 
             :key="externalMember.member.id"
             class="p-4 rounded-lg border-2 bg-orange-50 border-orange-200">
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-900">{{ externalMember.member.full_name }}</h4>
            <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
              External
            </span>
          </div>
          <p class="text-sm text-gray-600 mb-2">{{ externalMember.relationship }}</p>
          <p class="text-xs text-gray-500">{{ externalMember.member.family_no }}</p>
          <p v-if="externalMember.member.address" 
             class="text-xs text-gray-500 mt-1">{{ externalMember.member.address }}</p>
        </div>
      </div>
    </div>



    <!-- Add Relationship Modal -->
    <div v-if="showAddRelationshipModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="w-full max-w-md bg-white rounded-lg shadow-xl">
        <div class="p-6">
          <h3 class="text-lg font-semibold mb-4">Add Relationship</h3>

          <!-- Member Search -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search Member</label>
            <input v-model="searchQuery" @input="searchMembers" type="text"
              placeholder="Search by name or member number..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />

            <!-- Search Results -->
            <div v-if="searchResults.length > 0"
              class="mt-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg">
              <div v-for="result in searchResults" :key="result.id" @click="selectMember(result)"
                class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                <div class="font-medium">{{ result.full_name }}</div>
                <div class="text-sm text-gray-600">{{ result.member_no }}</div>
              </div>
            </div>
          </div>

          <!-- Selected Member -->
          <div v-if="selectedMember" class="mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="font-medium">{{ selectedMember.full_name }}</div>
            <div class="text-sm text-gray-600">{{ selectedMember.member_no }}</div>
          </div>

          <!-- Relationship Selection -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Relationship Type</label>
            <select v-model="selectedRelationship"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select relationship...</option>
              <option v-for="rel in relationships" :key="rel.id" :value="rel.id">
                {{ rel.name }}
              </option>
            </select>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button @click="showAddRelationshipModal = false"
              class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
              Cancel
            </button>
            <button @click="addRelationship" :disabled="!selectedMember || !selectedRelationship"
              class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
              Add Relationship
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  member: Object,
  familyTree: Object,
  relationships: Array
});

// Computed property to filter internal members from familyMembers
const internalFamilyMembers = computed(() => {
  if (!props.familyTree?.familyMembers) return [];
  return props.familyTree.familyMembers.filter(member => !member.is_external);
});

// Computed property to filter external members from familyMembers
const externalFamilyMembers = computed(() => {
  if (!props.familyTree?.familyMembers) return [];
  return props.familyTree.familyMembers.filter(member => member.is_external);
});

// Reactive data
const showAddRelationshipModal = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const selectedMember = ref(null);
const selectedRelationship = ref('');
const selectedFamilyMember = ref(null);

// Search members
const searchMembers = async () => {
  if (searchQuery.value.length < 2) {
    searchResults.value = [];
    return;
  }

  try {
    const response = await fetch(`/family-tree/search-members?q=${encodeURIComponent(searchQuery.value)}`);
    if (response.ok) {
      searchResults.value = await response.json();
    }
  } catch (error) {
    console.error('Error searching members:', error);
  }
};

// Select member from search results
const selectMember = (member) => {
  selectedMember.value = member;
  searchResults.value = [];
  searchQuery.value = member.full_name;
};

// Select family member for relationship
const selectFamilyMemberForRelationship = (familyMember) => {
  selectedFamilyMember.value = familyMember;
  selectedMember.value = familyMember.member;
  selectedRelationship.value = '';
  showAddRelationshipModal.value = true;
};



// Add relationship
const addRelationship = async () => {
  if (!selectedMember.value || !selectedRelationship.value) return;

  try {
    const response = await fetch('/family-tree/add-relationship', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        member_id: props.member.id,
        related_member_id: selectedMember.value.id,
        relationship_id: selectedRelationship.value
      })
    });

    if (response.ok) {
      router.reload();
    } else {
      const error = await response.json();
      alert(error.error || 'Failed to add relationship');
    }
  } catch (error) {
    console.error('Error adding relationship:', error);
    alert('Failed to add relationship');
  }
};

// Remove relationship
const removeRelationship = async (relatedMemberId) => {
  if (!confirm('Are you sure you want to remove this relationship?')) return;

  try {
    const response = await fetch('/family-tree/remove-relationship', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        member_id: props.member.id,
        related_member_id: relatedMemberId
      })
    });

    if (response.ok) {
      router.reload();
    } else {
      const error = await response.json();
      alert(error.error || 'Failed to remove relationship');
    }
  } catch (error) {
    console.error('Error removing relationship:', error);
    alert('Failed to remove relationship');
  }
};

// Debug family links
const debugFamilyLinks = async () => {
  try {
    const response = await fetch(`/member/${props.member.id}/debug-family-links`);
    if (response.ok) {
      const data = await response.json();
      console.log('Debug Family Links:', data);
      alert('Check browser console for debug information');
    } else {
      alert('Failed to get debug information');
    }
  } catch (error) {
    console.error('Error getting debug information:', error);
    alert('Failed to get debug information');
  }
};



// Debug specific member
const debugSpecificMember = async () => {
  try {
    const response = await fetch(`/debug-member/${props.member.member_no}`);
    if (response.ok) {
      const data = await response.json();
      console.log('Debug Specific Member:', data);
      alert('Check browser console for specific member debug information');
    } else {
      alert('Failed to get specific member debug information');
    }
  } catch (error) {
    console.error('Error getting specific member debug information:', error);
    alert('Failed to get specific member debug information');
  }
};

// Debug member by ID
const debugMemberById = async () => {
  try {
    const response = await fetch(`/debug-member-by-id/5`);
    if (response.ok) {
      const data = await response.json();
      console.log('Debug Member ID 5:', data);
      alert('Check browser console for member ID 5 debug information');
    } else {
      alert('Failed to get member ID 5 debug information');
    }
  } catch (error) {
    console.error('Error getting member ID 5 debug information:', error);
    alert('Failed to get member ID 5 debug information');
  }
};
</script>
