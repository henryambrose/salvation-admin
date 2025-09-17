import { ref } from 'vue';

interface ToastMessage {
  id: number;
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
  duration?: number;
}

const toasts = ref<ToastMessage[]>([]);
let toastId = 0;

export function useToast() {
  const addToast = (type: ToastMessage['type'], message: string, duration = 5000) => {
    const id = ++toastId;
    const toast: ToastMessage = {
      id,
      type,
      message,
      duration,
    };

    toasts.value.push(toast);

    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        removeToast(id);
      }, duration);
    }

    return id;
  };

  const removeToast = (id: number) => {
    const index = toasts.value.findIndex(toast => toast.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message: string, duration?: number) => {
    return addToast('success', message, duration);
  };

  const error = (message: string, duration?: number) => {
    return addToast('error', message, duration);
  };

  const warning = (message: string, duration?: number) => {
    return addToast('warning', message, duration);
  };

  const info = (message: string, duration?: number) => {
    return addToast('info', message, duration);
  };

  const clear = () => {
    toasts.value = [];
  };

  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    warning,
    info,
    clear,
  };
}