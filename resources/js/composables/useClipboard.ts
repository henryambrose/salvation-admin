import { ref } from 'vue'

interface CopyState {
  id: string
  type: string
}

export function useClipboard() {
  const copiedItem = ref<CopyState | null>(null)

  // Enhanced clipboard function that works in production nginx environments
  const copyToClipboard = async (text: string): Promise<void> => {
    try {
      // Check if we're in a secure context (HTTPS or localhost)
      if (navigator.clipboard && window.isSecureContext) {
        // Use modern Clipboard API (requires HTTPS in production)
        await navigator.clipboard.writeText(text)
      } else {
        // Fallback for non-HTTPS or older browsers
        await copyToClipboardLegacy(text)
      }
    } catch (error) {
      // If modern API fails, try legacy fallback
      await copyToClipboardLegacy(text)
    }
  }

  // Legacy fallback using document.execCommand
  const copyToClipboardLegacy = (text: string): Promise<void> => {
    return new Promise((resolve, reject) => {
      const textArea = document.createElement('textarea')
      textArea.value = text

      // Make the textarea invisible but accessible
      textArea.style.position = 'fixed'
      textArea.style.left = '-999999px'
      textArea.style.top = '-999999px'
      textArea.style.opacity = '0'

      document.body.appendChild(textArea)

      try {
        textArea.focus()
        textArea.select()
        textArea.setSelectionRange(0, 99999) // For mobile devices

        const successful = document.execCommand('copy')
        document.body.removeChild(textArea)

        if (successful) {
          resolve()
        } else {
          reject(new Error('execCommand copy failed'))
        }
      } catch (err) {
        document.body.removeChild(textArea)
        reject(err)
      }
    })
  }

  // Show success toast notification
  const showSuccessToast = (type: string) => {
    const toast = document.createElement('div')
    toast.className =
      'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-[9999] transform transition-all duration-300 flex items-center gap-2'
    toast.innerHTML = `
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
      ${type} copied to clipboard!
    `
    document.body.appendChild(toast)

    // Auto remove after 2 seconds
    setTimeout(() => {
      if (document.body.contains(toast)) {
        toast.remove()
      }
    }, 2000)
  }

  // Show error toast notification
  const showErrorToast = (type: string, text: string) => {
    const toast = document.createElement('div')
    toast.className =
      'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-[9999] transform transition-all duration-300 flex items-center gap-2 max-w-sm'
    toast.innerHTML = `
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
      <div>
        <div class="font-medium">Copy failed</div>
        <div class="text-sm opacity-90">Please copy manually: <span class="font-mono bg-red-600 px-1 rounded">${text}</span></div>
      </div>
    `
    document.body.appendChild(toast)

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (document.body.contains(toast)) {
        toast.remove()
      }
    }, 5000)
  }

  // Main copy function with visual feedback
  const copyWithFeedback = async (text: string, type: string, memberId: number) => {
    if (!text || text === '—') return

    try {
      await copyToClipboard(text)

      // Set copied state for visual feedback
      copiedItem.value = { id: `${type}-${memberId}`, type }

      // Show success toast
      showSuccessToast(type)

      // Clear copied state after animation
      setTimeout(() => {
        copiedItem.value = null
      }, 1000)

    } catch (error) {
      console.error('Failed to copy:', error)
      showErrorToast(type, text)
    }
  }

  return {
    copiedItem,
    copyWithFeedback,
    copyToClipboard
  }
}