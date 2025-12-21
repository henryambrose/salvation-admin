<template>
  <Dialog :open="isOpen" @update:open="handleCancel">
    <DialogContent class="max-w-md">
      <DialogHeader>
        <DialogTitle :class="titleClass">{{ dialogState?.title }}</DialogTitle>
      </DialogHeader>

      <div class="py-4">
        <p class="text-gray-700">{{ dialogState?.message }}</p>
      </div>

      <DialogFooter class="flex gap-3 justify-end">
        <Button
          variant="outline"
          @click="handleCancel"
          class="px-6"
        >
          {{ dialogState?.cancelText || 'Cancel' }}
        </Button>
        <Button
          :variant="buttonVariant"
          @click="handleConfirm"
          class="px-6"
        >
          {{ dialogState?.confirmText || 'Confirm' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { useConfirm } from '@/composables/useConfirm';

const { dialogState, isOpen, handleConfirm, handleCancel } = useConfirm();

const titleClass = computed(() => {
  const type = dialogState.value?.type || 'warning';
  const classMap = {
    danger: 'text-red-700',
    warning: 'text-yellow-700',
    info: 'text-blue-700',
  };
  return classMap[type];
});

const buttonVariant = computed(() => {
  const type = dialogState.value?.type || 'warning';
  if (type === 'danger') return 'destructive';
  return 'default';
});
</script>
