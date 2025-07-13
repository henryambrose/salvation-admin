<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    roles: Array,
    modules: Array,
    permissions: Object,
    modulesIdWise: Object,
});

const actions = ['create', 'read', 'update', 'delete', 'list'];

const selectedRoleId = ref(props.roles[0]?.id || null);

const permissionState = ref({ ...props.permissions[selectedRoleId.value] });

watch(selectedRoleId, (newRoleId) => {
    permissionState.value = { ...props.permissions[newRoleId] };
});

function togglePermission(moduleId: number, actionId: number) {
    // Defensive: Ensure module and action exist
    if (
        permissionState.value[moduleId] &&
        typeof permissionState.value[moduleId][actionId] !== 'undefined'
    ) {
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
        <div class="max-w-3xl mx-auto py-8">
            <h2 class="text-2xl font-bold mb-6">Role Permissions</h2>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Select Role</label>
                <select v-model="selectedRoleId" class="border rounded px-3 py-2 w-full">
                    <option v-for="role in props.roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Module</th>
                            <th v-for="action in actions" :key="action" class="border px-4 py-2 capitalize">{{ action }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="module in props.modules" :key="module.id">
                            <td class="border px-4 py-2 font-semibold">{{ module.name }}</td>
                            <td v-for="action in module.actions" :key="action.id" class="border px-4 py-2 text-center">
                                <input
                                    type="checkbox"
                                    :checked="permissionState[module.id][action.id] === 1"
                                    @change="togglePermission(module.id, action.id)"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-end">
                <Button class="btn btn-primary" @click="savePermissions">Save Permissions</Button>
            </div>
        </div>
    </AppLayout>
</template>
