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
  relationship?: {
    id: number;
    name: string;
    description?: string;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Relationships',
    href: '/relationship/index',
  },
  {
    title: props.relationship ? 'Edit Relationship' : 'Create Relationship',
    href: props.relationship ? `/relationship/edit/${props.relationship.id}` : '/relationship/create',
  },
];

const page = usePage<SharedData>();

const form = useForm({
  id: props.relationship?.id || '',
  name: props.relationship?.name || '',
  description: props.relationship?.description || '',
});

const submit = () => {
  const routeName = props.relationship?.id ? 'relationship.update' : 'relationship.store';
  const method = props.relationship?.id ? 'put' : 'post';

  form[method](route(routeName, { id: props.relationship?.id }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.relationship ? 'Edit Relationship' : 'Create Relationship'" />
    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">{{ props.relationship ? 'Edit Relationship' : 'Create Relationship' }}</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/relationship/index">
            <component :is="List" />
            <span>Relationship List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-2">
          <Label for="name">Relationship Name</Label>
          <Input id="name" v-model="form.name" class="mt-1 block w-full" placeholder="Enter relationship name" required />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="grid gap-2">
          <Label for="description">Description</Label>
          <Input id="description" v-model="form.description" class="mt-1 block w-full" placeholder="Description" />
          <InputError class="mt-2" :message="form.errors.description" />
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
