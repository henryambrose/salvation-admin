<script setup lang="ts">
import { computed } from 'vue';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Clock, AlertTriangle } from 'lucide-vue-next';

const props = defineProps<{
  open: boolean;
  minutesRemaining?: number;
}>();

const emit = defineEmits<{
  (e: 'continue'): void;
  (e: 'close'): void;
}>();

const timeDisplay = computed(() => {
  if (!props.minutesRemaining) return 'soon';
  if (props.minutesRemaining < 1) return 'in less than a minute';
  return `in ${props.minutesRemaining} minute${props.minutesRemaining !== 1 ? 's' : ''}`;
});
</script>

<template>
  <Dialog :open="open" @update:open="(val) => !val && emit('close')">
    <DialogContent class="max-w-md">
      <DialogHeader>
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 rounded-full bg-amber-100 dark:bg-amber-900">
            <AlertTriangle class="h-6 w-6 text-amber-600 dark:text-amber-400" />
          </div>
          <DialogTitle class="text-xl">Session Expiring Soon</DialogTitle>
        </div>

        <DialogDescription class="text-base space-y-3 pt-2">
          <p class="flex items-center gap-2">
            <Clock class="h-4 w-4 text-amber-600" />
            <span>Your session will expire <strong>{{ timeDisplay }}</strong></span>
          </p>

          <p class="text-sm">
            If your session expires, you'll lose any unsaved changes. Click "Continue Working" to keep your session active.
          </p>
        </DialogDescription>
      </DialogHeader>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button
          variant="outline"
          @click="emit('close')"
        >
          Let it expire
        </Button>
        <Button
          @click="emit('continue')"
          class="bg-blue-600 hover:bg-blue-700"
        >
          Continue Working
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
