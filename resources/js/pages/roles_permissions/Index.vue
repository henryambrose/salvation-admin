<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Roles, Modules, Permissions } from '@/types';

const props = defineProps<{
  roles: Roles;
  modules: Modules;
  permissions: Permissions;
  modulesIdWise: Record<number, any>;
}>();

const actions = ['create', 'read', 'update', 'delete', 'list'];

const selectedRoleId = ref(props.roles.length > 0 ? props.roles[0].id : 0);
const permissionState = ref(props.permissions[selectedRoleId.value] ? JSON.parse(JSON.stringify(props.permissions[selectedRoleId.value])) : {});

watch(selectedRoleId, (newRoleId) => {
  if (newRoleId && props.permissions[newRoleId]) {
    permissionState.value = JSON.parse(JSON.stringify(props.permissions[newRoleId]));
  } else {
    permissionState.value = {};
  }
});

function togglePermission(moduleId: number, actionId: number) {
  if (permissionState.value[moduleId] && typeof permissionState.value[moduleId][actionId] !== 'undefined') {
    permissionState.value[moduleId][actionId] = permissionState.value[moduleId][actionId] ? 0 : 1;
  }
}

function savePermissions() {
  router.post('/roles-permissions/update', {
    role_id: selectedRoleId.value,
    permissions: permissionState.value,
  });
}
</script>

<template>
  <AppLayout>
    <Head title="Role Permissions" />
    <div class="mx-auto max-w-4xl py-8">
      <h2 class="mb-6 text-2xl font-bold">Role Permissions</h2>
      <div class="mb-4">
        <label class="mb-1 block font-semibold">Select Role</label>
        <select v-model="selectedRoleId" class="w-full rounded-full border border-gray-200 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200">
          <option v-for="role in props.roles" :key="role.id" :value="role.id">{{ (role as { id: number; name: string }).name }}</option>
        </select>
      </div>
      <div class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
          <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Module</th>
                <th v-for="action in actions" :key="action" class="border-b p-3 font-semibold text-gray-700 capitalize">{{ action }}</th>
            </tr>
          </thead>
          <tbody>
              <tr v-for="module in props.modules" :key="(module as { id: number }).id" class="even:bg-gray-50 hover:bg-blue-50 transition">
                <td class="border-b p-3 font-semibold">{{ (module as { name: string }).name }}</td>
                <td v-for="action in (module as { actions: { id: number; name: string }[] }).actions" :key="action.id" class="border-b p-3 text-center">
                  <input type="checkbox" :checked="permissionState[module.id]?.[action.id] === 1" @change="togglePermission(module.id, action.id)" class="accent-blue-600 w-5 h-5 rounded-full border-gray-300 focus:ring-2 focus:ring-blue-200" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-6 flex justify-end">
          <Button class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2" @click="savePermissions">
            Save Permissions
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
