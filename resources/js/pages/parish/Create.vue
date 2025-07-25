<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { List } from 'lucide-vue-next';
import { type BreadcrumbItem, type SharedData } from '@/types';

interface Props {
  parish?: {
    id: number;
    deanery: string;
    name: string;
    code?: string;
    address?: string;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Parishes',
    href: '/parish/index',
  },
  {
    title: props.parish ? 'Edit Parish' : 'Create Parish',
    href: props.parish ? `/parish/edit/${props.parish.id}` : '/parish/create',
  },
];

const page = usePage<SharedData>();

const form = useForm({
  id: props.parish?.id || '',
  deanery: props.parish?.deanery || '',
  name: props.parish?.name || '',
  code: props.parish?.code || '',
  address: props.parish?.address || '',
});

const submit = () => {
  const routeName = props.parish?.id ? 'parish.update' : 'parish.store';
  const method = props.parish?.id ? 'put' : 'post';

  form[method](route(routeName, { id: props.parish?.id }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.parish ? 'Edit Parish' : 'Create Parish'" />
    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">{{ props.parish ? 'Edit Parish' : 'Create Parish' }}</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/parish/index">
            <component :is="List" />
            <span>Parish List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-2">
          <Label for="deanery">Deanery</Label>
          <Input id="deanery" v-model="form.deanery" class="mt-1 block w-full" placeholder="Enter deanery" required />
          <InputError class="mt-2" :message="form.errors.deanery" />
        </div>
        <div class="grid gap-2">
          <Label for="name">Parish Name</Label>
          <Input id="name" v-model="form.name" class="mt-1 block w-full" placeholder="Enter parish name" required />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="grid gap-2">
          <Label for="code">Code</Label>
          <Input id="code" v-model="form.code" class="mt-1 block w-full" placeholder="Code" />
          <InputError class="mt-2" :message="form.errors.code" />
        </div>
        <div class="grid gap-2">
          <Label for="address">Address</Label>
          <Input id="address" v-model="form.address" class="mt-1 block w-full" placeholder="Address" />
          <InputError class="mt-2" :message="form.errors.address" />
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
