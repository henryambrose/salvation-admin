import { ref } from 'vue';

interface ConfirmDialog {
  title: string;
  message: string;
  onConfirm: () => void;
  onCancel?: () => void;
  confirmText?: string;
  cancelText?: string;
  type?: 'danger' | 'warning' | 'info';
}

const dialogState = ref<ConfirmDialog | null>(null);
const isOpen = ref(false);

export function useConfirm() {
  const confirm = (options: {
    title: string;
    message: string;
    onConfirm: () => void;
    onCancel?: () => void;
    confirmText?: string;
    cancelText?: string;
    type?: 'danger' | 'warning' | 'info';
  }): Promise<boolean> => {
    return new Promise((resolve) => {
      dialogState.value = {
        title: options.title,
        message: options.message,
        onConfirm: () => {
          options.onConfirm();
          close();
          resolve(true);
        },
        onCancel: () => {
          options.onCancel?.();
          close();
          resolve(false);
        },
        confirmText: options.confirmText || 'Confirm',
        cancelText: options.cancelText || 'Cancel',
        type: options.type || 'warning',
      };
      isOpen.value = true;
    });
  };

  const close = () => {
    isOpen.value = false;
    dialogState.value = null;
  };

  const handleConfirm = () => {
    dialogState.value?.onConfirm();
  };

  const handleCancel = () => {
    dialogState.value?.onCancel?.();
    close();
  };

  return {
    dialogState,
    isOpen,
    confirm,
    close,
    handleConfirm,
    handleCancel,
  };
}
