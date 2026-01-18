<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';

interface Props {
  open: boolean;
  title?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'PDF Options',
});

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
  (e: 'confirm', options: PdfOptions): void;
  (e: 'cancel'): void;
}>();

export interface PdfOptions {
  includeRemark: boolean;
  signBy: 'parish_priest' | 'for_parish_priest';
  signeeName: string;
  printDate: string;
}

// Form state
const includeRemark = ref<'yes' | 'no'>('yes');
const signBy = ref<'parish_priest' | 'for_parish_priest'>('parish_priest');
const signeeName = ref('');
const printDate = ref(new Date().toISOString().split('T')[0]);

const isOpen = computed({
  get: () => props.open,
  set: (value) => emit('update:open', value),
});

function handleConfirm() {
  const options: PdfOptions = {
    includeRemark: includeRemark.value === 'yes',
    signBy: signBy.value,
    signeeName: signeeName.value.trim(),
    printDate: printDate.value,
  };
  emit('confirm', options);
  resetForm();
}

function handleCancel() {
  emit('cancel');
  emit('update:open', false);
  resetForm();
}

function resetForm() {
  includeRemark.value = 'yes';
  signBy.value = 'parish_priest';
  signeeName.value = '';
  printDate.value = new Date().toISOString().split('T')[0];
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
        <DialogDescription>
          Configure the PDF output options before generating.
        </DialogDescription>
      </DialogHeader>

      <div class="grid gap-4 py-4">
        <!-- Include Remark -->
        <div class="grid gap-2">
          <Label>Include Remark</Label>
          <div class="flex gap-4">
            <label class="flex items-center space-x-2 cursor-pointer">
              <input
                type="radio"
                v-model="includeRemark"
                value="yes"
                class="h-4 w-4 text-primary border-gray-300 focus:ring-primary"
              />
              <span class="text-sm font-normal">Yes</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
              <input
                type="radio"
                v-model="includeRemark"
                value="no"
                class="h-4 w-4 text-primary border-gray-300 focus:ring-primary"
              />
              <span class="text-sm font-normal">No</span>
            </label>
          </div>
          <p class="text-xs text-muted-foreground">
            When "No" is selected, remarks will show as "---"
          </p>
        </div>

        <!-- Sign By -->
        <div class="grid gap-2">
          <Label for="sign-by">Sign By</Label>
          <select
            id="sign-by"
            v-model="signBy"
            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
          >
            <option value="parish_priest">Parish Priest</option>
            <option value="for_parish_priest">For Parish Priest</option>
          </select>
        </div>

        <!-- Signee Name -->
        <div class="grid gap-2">
          <Label for="signee-name">Signee Name</Label>
          <Input
            id="signee-name"
            v-model="signeeName"
            placeholder="Enter name (optional)"
          />
          <p class="text-xs text-muted-foreground">
            Leave blank to use default
          </p>
        </div>

        <!-- Print Date -->
        <div class="grid gap-2">
          <Label for="print-date">Print Date</Label>
          <Input
            id="print-date"
            v-model="printDate"
            type="date"
          />
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" @click="handleCancel">
          Cancel
        </Button>
        <Button @click="handleConfirm">
          Generate PDF
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
