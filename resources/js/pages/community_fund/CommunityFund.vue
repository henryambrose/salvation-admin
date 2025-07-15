<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SelectInput } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Member, type BreadcrumbItem } from '@/types';
import { List } from 'lucide-vue-next';
import FormHeader from '@/components/FormHeader.vue';
import FormBody from '@/components/FormBody.vue';

const props = defineProps<{
  communityFund?: {
    id?: number;
    member_id?: number;
    amount?: number;
    fund_date?: string;
    description?: string;
  };
  members: Member[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Community Funds', href: '/community-fund/index' },
  {
    title: props.communityFund?.id ? 'Edit Community Fund' : 'Add Community Fund',
    href: props.communityFund?.id
      ? `/community-fund/edit/${props.communityFund.id}`
      : '/community-fund/create',
  },
];

const memberOptions = props.members.map(m => ({
  id: m.id,
  name: m.first_name + ' ' + (m.middle_name ? m.middle_name + ' ' : '') + (m.last_name || ''),
}));

const form = useForm({
  id: props.communityFund?.id ?? '',
  member_id: props.communityFund?.member_id ?? '',
  amount: props.communityFund?.amount ?? '',
  fund_date: props.communityFund?.fund_date ?? '',
  description: props.communityFund?.description ?? '',
});

const submit = () => {
  const routeName = props.communityFund?.id ? 'community-fund.update' : 'community-fund.store';
  const method = props.communityFund?.id ? 'put' : 'post';
  form[method](route(routeName, props.communityFund?.id ? { communityFund: props.communityFund.id } : {}), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.communityFund?.id ? 'Edit Community Fund' : 'Add Community Fund'" />
    <FormHeader>
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">
          {{ props.communityFund?.id ? 'Edit Community Fund' : 'Add Community Fund' }}
        </h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/community-fund/index" class="btn btn-secondary">
            <component :is="List" />
            <span>Community Fund List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="grid gap-2">
            <Label for="member_id">Member</Label>
            <SelectInput
              id="member_id"
              v-model="form.member_id"
              :options="memberOptions"
              class="mt-1 block w-full"
              placeholder="Select Member"
            />
            <InputError class="mt-2" :message="form.errors.member_id" />
          </div>
          <div class="grid gap-2">
            <Label for="amount">Amount</Label>
            <Input
              id="amount"
              v-model="form.amount"
              type="number"
              class="mt-1 block w-full"
              placeholder="Enter Amount"
              required
            />
            <InputError class="mt-2" :message="form.errors.amount" />
          </div>
          <div class="grid gap-2">
            <Label for="fund_date">Fund Date</Label>
            <Input
              id="fund_date"
              v-model="form.fund_date"
              type="date"
              class="mt-1 block w-full"
              placeholder="Select Fund Date"
              required
            />
            <InputError class="mt-2" :message="form.errors.fund_date" />
          </div>
          <div class="grid gap-2 md:col-span-2">
            <Label for="description">Description</Label>
            <Input
              id="description"
              v-model="form.description"
              type="text"
              class="mt-1 block w-full"
              placeholder="Enter Description"
            />
            <InputError class="mt-2" :message="form.errors.description" />
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
