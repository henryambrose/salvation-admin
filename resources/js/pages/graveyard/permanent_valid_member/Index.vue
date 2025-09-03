&lt;script setup lang="ts"&gt; import DatatableHeader from '@/components/DatatableHeader.vue'; import { Button } from '@/components/ui/button'; import
{ Input } from '@/components/ui/input'; import { Checkbox } from '@/components/ui/checkbox'; import { Tooltip, TooltipContent, TooltipProvider,
TooltipTrigger } from '@/components/ui/tooltip'; import AppLayout from '@/layouts/AppLayout.vue'; import { Head, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash, ZapIcon } from 'lucide-vue-next'; import { computed, nextTick, ref, watch } from 'vue'; import { permissionHelpers }
from '@/composables/permissionHelpers'; const { can } = permissionHelpers(); const props = defineProps({ permanentValidMembers: { type: Object,
default: () => ({ data: [] }), }, filters: Object, fetchUrl: String, }); const partialOnly = ['permanentValidMembers', 'filters']; const searchTimeout
= ref&lt;number | null&gt;(null); const search = ref(props.filters?.search || ''); const perPage = ref(props.filters?.perPage || 10); const sort =
ref(props.filters?.sort || ''); const direction = ref(props.filters?.direction || 'asc'); const isArchived = ref(String(props.filters?.isArchived) ===
'true'); const serverArchived = computed(() => String(props.filters?.isArchived) === 'true'); const highlightedRowId = ref&lt;number|null&gt;(null);
const columns = [ { key: 'id', label: 'Id', sortable: true }, { key: 'first_name', label: 'First Name', sortable: true }, { key: 'last_name', label:
'Last Name', sortable: true }, { key: 'aadhar_no', label: 'Aadhar No', sortable: true }, { key: 'contact_no', label: 'Contact No', sortable: true }, {
key: 'created_at', label: 'Created At', sortable: true }, { key: 'updated_at', label: 'Updated At', sortable: true }, ]; const breadcrumbs = [{ title:
'Permanent Valid Members', href: '/graveyard/permanent-valid-members' }]; function editMember(member: any) {
router.get(route('graveyard.permanent-valid-members.edit', member.id)); } function addNewMember() {
router.get(route('graveyard.permanent-valid-members.create')); } const showDeleteModal = ref(false); const deletingMember = ref&lt;any&gt;(null);
function openDeleteModal(member: any) { deletingMember.value = member; showDeleteModal.value = true; } function restoreMember(id: number) {
router.post( route('graveyard.permanent-valid-members.restore', id), {}, { preserveScroll: true, only: partialOnly, onSuccess: () => {
isArchived.value = false; }, }, ); } function confirmDelete() { if (!deletingMember.value) return; const deletedId = deletingMember.value.id;
router.delete(route('graveyard.permanent-valid-members.destroy', deletedId), { preserveScroll: true, only: partialOnly, onSuccess: () => {
showDeleteModal.value = false; deletingMember.value = null; highlightedRowId.value = deletedId + 1; nextTick(() => scrollToRow(deletedId + 1)); }, });
} function scrollToRow(id: number) { const el = document.getElementById(`member-row-${id}`); if (el) { el.scrollIntoView({ behavior: 'smooth', block:
'center' }); } } watch( [search, perPage, sort, direction, isArchived], () => { clearTimeout(searchTimeout.value); searchTimeout.value = setTimeout(()
=> { if (props.fetchUrl) { router.get( props.fetchUrl, { search: search.value, perPage: perPage.value, sort: sort.value, direction: direction.value,
isArchived: isArchived.value ? 'true' : 'false', }, { preserveState: true, replace: true, only: partialOnly, }, ); } }, 300); }, { deep: true }, );
const enhancedMembers = computed(() => { let members = props.permanentValidMembers?.data.map((item: any) => ({ ...item, created_at: item.created_at ?
new Date(item.created_at).toLocaleDateString() : '', updated_at: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '', })); return {
...props.permanentValidMembers, data: members, }; }); &lt;/script&gt; &lt;template&gt; &lt;AppLayout&gt; &lt;Head title="Permanent Valid Members"
/&gt; &lt;div class="container mx-auto py-6"&gt; &lt;div class="mb-6"&gt; &lt;DatatableHeader :breadcrumbs="breadcrumbs"&gt; &lt;template #left&gt;
&lt;div class="flex items-center gap-4"&gt; &lt;h1 class="text-3xl font-semibold text-gray-900"&gt; Permanent Valid Members &lt;/h1&gt; &lt;/div&gt;
&lt;/template&gt; &lt;template #right&gt; &lt;div class="flex items-center gap-4"&gt; &lt;Button @click="addNewMember" class="gap-2"&gt; &lt;Plus
class="h-4 w-4" /&gt; Add New &lt;/Button&gt; &lt;/div&gt; &lt;/template&gt; &lt;/DatatableHeader&gt; &lt;div class="mb-4 flex flex-wrap items-center
justify-between gap-4"&gt; &lt;div class="flex items-center gap-4"&gt; &lt;Input v-model="search" type="search" placeholder="Search members..."
class="w-64" /&gt; &lt;label class="flex items-center gap-2"&gt; &lt;Tooltip&gt; &lt;TooltipTrigger asChild&gt; &lt;Checkbox v-model="isArchived"
class="switch-checkbox" /&gt; &lt;/TooltipTrigger&gt; &lt;TooltipContent&gt; &lt;p&gt;Show archived members in the list&lt;/p&gt;
&lt;/TooltipContent&gt; &lt;/Tooltip&gt; &lt;span class="text-sm font-medium"&gt;Show Archived&lt;/span&gt; &lt;/label&gt; &lt;/div&gt; &lt;select
v-model="perPage" class="rounded border px-2 py-1 text-sm"&gt; &lt;option :value="10"&gt;10 per page&lt;/option&gt; &lt;option :value="25"&gt;25 per
page&lt;/option&gt; &lt;option :value="50"&gt;50 per page&lt;/option&gt; &lt;option :value="100"&gt;100 per page&lt;/option&gt; &lt;/select&gt;
&lt;/div&gt; &lt;div class="datatable mt-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-xl"&gt; &lt;div class="overflow-x-auto rounded-xl
border border-gray-100"&gt; &lt;table class="w-full border-collapse text-left"&gt; &lt;thead&gt; &lt;tr class="bg-blue-50"&gt; &lt;th class="border-b
p-3 font-semibold whitespace-nowrap text-gray-700"&gt;Actions&lt;/th&gt; &lt;th v-for="col in columns" :key="col.key" @click="col.sortable ?
changeSort(col.key) : null" class="cursor-pointer border-b p-3 font-semibold whitespace-nowrap text-gray-700 transition hover:bg-blue-100" &gt;
{{ col.label }}
&lt;span v-if="col.sortable && sort === col.key"&gt;
{{ direction === 'asc' ? '▲' : '▼' }}
&lt;/span&gt; &lt;/th&gt; &lt;th v-if="!serverArchived" class="border-b p-3 font-semibold whitespace-nowrap text-gray-700"&gt;Delete&lt;/th&gt;
&lt;/tr&gt; &lt;/thead&gt; &lt;tbody&gt; &lt;tr v-for="member in enhancedMembers.data" :key="member.id" :id="`member-row-${member.id}`"
:class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === member.id ? 'highlight-row' : '']" &gt; &lt;td class="p-2
whitespace-nowrap"&gt; &lt;div class="flex gap-2"&gt; &lt;template v-if="!serverArchived"&gt; &lt;Tooltip&gt; &lt;TooltipTrigger asChild&gt;
&lt;Button @click="editMember(member)" class="rounded-full bg-yellow-100 text-yellow-700 transition hover:bg-yellow-200"&gt; &lt;component
:is="Pencil" /&gt; &lt;/Button&gt; &lt;/TooltipTrigger&gt; &lt;TooltipContent&gt; &lt;p&gt;Edit this member&lt;/p&gt; &lt;/TooltipContent&gt;
&lt;/Tooltip&gt; &lt;/template&gt; &lt;template v-else&gt; &lt;Tooltip&gt; &lt;TooltipTrigger asChild&gt; &lt;Button @click="restoreMember(member.id)"
class="rounded-full bg-green-100 text-green-700 transition hover:bg-green-200"&gt; Restore &lt;/Button&gt; &lt;/TooltipTrigger&gt;
&lt;TooltipContent&gt; &lt;p&gt;Restore this member&lt;/p&gt; &lt;/TooltipContent&gt; &lt;/Tooltip&gt; &lt;/template&gt; &lt;/div&gt; &lt;/td&gt;
&lt;td v-for="col in columns" :key="col.key" class="overflow-hidden p-2 whitespace-nowrap"&gt;
{{ member[col.key] }}
&lt;/td&gt; &lt;td v-if="!serverArchived" class="p-2"&gt; &lt;Button @click="openDeleteModal(member)" variant="destructive" class="rounded-full
bg-red-100 text-red-700 hover:bg-red-200 transition" &gt; &lt;component :is="Trash" /&gt; &lt;span&gt;Delete&lt;/span&gt; &lt;/Button&gt; &lt;/td&gt;
&lt;/tr&gt; &lt;/tbody&gt; &lt;/table&gt; &lt;/div&gt; &lt;/div&gt; &lt;!-- Delete Confirmation Modal --&gt; &lt;transition name="fade"&gt; &lt;div
v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"&gt; &lt;div class="w-full max-w-md
rounded-lg bg-white p-6 shadow-xl"&gt; &lt;h2 class="mb-4 text-xl font-semibold"&gt;Confirm Delete&lt;/h2&gt; &lt;p class="mb-4 text-gray-600"&gt; Are
you sure you want to delete this member? This action cannot be undone. &lt;/p&gt; &lt;div class="flex justify-end gap-4"&gt; &lt;Button
@click="showDeleteModal = false" variant="outline" class="hover:bg-gray-100" &gt; Cancel &lt;/Button&gt; &lt;Button @click="confirmDelete"
variant="destructive" class="bg-red-600 text-white hover:bg-red-700" &gt; Delete &lt;/Button&gt; &lt;/div&gt; &lt;/div&gt; &lt;/div&gt;
&lt;/transition&gt; &lt;/div&gt; &lt;/div&gt; &lt;/AppLayout&gt; &lt;/template&gt;
