<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { CheckCircle, XCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

const { toasts, removeToast } = useToast();

const getIcon = (type: string) => {
  switch (type) {
    case 'success':
      return CheckCircle;
    case 'error':
      return XCircle;
    case 'warning':
      return AlertTriangle;
    case 'info':
      return Info;
    default:
      return Info;
  }
};

const getColorClasses = (type: string) => {
  switch (type) {
    case 'success':
      return 'bg-green-500 text-white border-green-600';
    case 'error':
      return 'bg-red-500 text-white border-red-600';
    case 'warning':
      return 'bg-yellow-500 text-white border-yellow-600';
    case 'info':
      return 'bg-blue-500 text-white border-blue-600';
    default:
      return 'bg-gray-500 text-white border-gray-600';
  }
};
</script>

<template>
  <div class="fixed top-20 right-4 z-[9999] space-y-2">
    <TransitionGroup
      name="toast"
      tag="div"
      enter-active-class="transition ease-out duration-300"
      enter-from-class="transform opacity-0 translate-x-full"
      enter-to-class="transform opacity-100 translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="transform opacity-100 translate-x-0"
      leave-to-class="transform opacity-0 translate-x-full"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'flex items-center space-x-3 px-4 py-3 rounded-lg shadow-lg border min-w-[300px] max-w-[500px]',
          getColorClasses(toast.type)
        ]"
      >
        <component :is="getIcon(toast.type)" class="h-5 w-5 flex-shrink-0" />
        <span class="flex-grow text-sm">{{ toast.message }}</span>
        <button
          @click="removeToast(toast.id)"
          class="text-white hover:text-gray-200 transition-colors flex-shrink-0"
        >
          <X class="h-4 w-4" />
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-move,
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>