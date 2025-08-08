<template>
  <div>
    <!-- Loading state -->
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500 text-lg">Loading family tree...</div>
    </div>

    <!-- No family tree data -->
    <div v-else-if="!familyTree" class="text-center py-8">
      <div class="text-gray-500 text-lg">
        There are no members assigned for Family Tree
      </div>
    </div>

    <!-- Empty family tree -->
    <div v-else-if="!hasAnyMembers" class="text-center py-8">
      <div class="text-gray-500 text-lg">
        This family has no members yet
      </div>
      <div class="text-gray-400 text-sm mt-2">
        Add family members to see the family tree
      </div>
    </div>

    <!-- Show family tree content -->
    <div v-else>
      <!-- Your existing family tree content -->
      <div v-if="familyTree?.familyMembers?.length > 0">
        <h3>Family Members</h3>
        <div v-for="member in familyTree.familyMembers.filter(m => !m.is_external)" :key="member.member.id">
          <!-- Your member display code -->
        </div>
      </div>
      
      <div v-if="familyTree?.externalMembers?.length > 0">
        <h3>External Members</h3>
        <div v-for="member in familyTree.externalMembers" :key="member.member.id">
          <!-- Your external member display code -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  familyTree: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Computed property to check if there are any members
const hasAnyMembers = computed(() => {
  if (!props.familyTree) return false
  
  const familyMembers = Array.isArray(props.familyTree.familyMembers) ? props.familyTree.familyMembers : []
  const externalMembers = Array.isArray(props.familyTree.externalMembers) ? props.familyTree.externalMembers : []
  const parents = Array.isArray(props.familyTree.parents) ? props.familyTree.parents : []
  const children = Array.isArray(props.familyTree.children) ? props.familyTree.children : []
  const siblings = Array.isArray(props.familyTree.siblings) ? props.familyTree.siblings : []
  const grandparents = Array.isArray(props.familyTree.grandparents) ? props.familyTree.grandparents : []
  const grandchildren = Array.isArray(props.familyTree.grandchildren) ? props.familyTree.grandchildren : []
  
  return familyMembers.length > 0 || 
         externalMembers.length > 0 || 
         parents.length > 0 || 
         children.length > 0 || 
         siblings.length > 0 || 
         grandparents.length > 0 || 
         grandchildren.length > 0 ||
         props.familyTree.spouse !== null
})

// Your existing computed properties with safety checks
const internalMembers = computed(() => {
  if (!Array.isArray(props.familyTree?.familyMembers)) {
    return []
  }
  return props.familyTree.familyMembers.filter(member => !member.is_external)
})

const externalMembersOnly = computed(() => {
  if (!Array.isArray(props.familyTree?.familyMembers)) {
    return []
  }
  return props.familyTree.familyMembers.filter(member => member.is_external)
})
</script>
