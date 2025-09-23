<template>
  <div class="family-photo-upload">
    <!-- Display current family photo -->
    <div v-if="currentPhoto" class="mb-6">
      <div class="text-center">
        <div class="relative inline-block">
          <img
            :src="currentPhoto.url"
            :alt="`Family ${familyNo} photo`"
            class="w-full max-w-md h-auto object-cover rounded-lg border border-gray-200 shadow-sm"
          />
          <button
            v-if="canDelete"
            @click="deletePhoto"
            :disabled="isDeleting"
            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 disabled:opacity-50 shadow-md"
            title="Delete photo"
          >
            <X class="w-5 h-5" />
          </button>
        </div>
        <div class="mt-3 text-center">
          <p class="text-sm font-medium text-gray-900">Family Photo</p>
          <p class="text-xs text-gray-500">{{ currentPhoto.original_filename }}</p>
          <p class="text-xs text-gray-500">{{ formatFileSize(currentPhoto.file_size) }}</p>
        </div>
      </div>
    </div>

    <!-- Upload area -->
    <div v-if="!currentPhoto || isReplacing" class="upload-area">
      <div
        @drop="handleDrop"
        @dragover.prevent
        @dragenter.prevent
        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors"
        :class="{ 'border-blue-400 bg-blue-50': isDragging }"
      >
        <Camera class="mx-auto h-12 w-12 text-gray-400" />
        <div class="mt-4">
          <label
            for="family-photo-input"
            class="cursor-pointer text-blue-600 hover:text-blue-500 font-medium"
          >
            {{ currentPhoto ? 'Replace family photo' : 'Upload family photo' }}
          </label>
          <input
            id="family-photo-input"
            ref="fileInput"
            type="file"
            class="sr-only"
            accept="image/jpeg,image/png,image/gif,image/jpg"
            @change="handleFileSelect"
          />
          <p class="text-sm text-gray-500 mt-1">
            or drag and drop
          </p>
        </div>
        <p class="text-xs text-gray-500 mt-2">
          PNG, JPG, GIF up to 20MB
        </p>
      </div>
    </div>

    <!-- Upload progress -->
    <div v-if="isUploading" class="mt-4">
      <div class="flex items-center space-x-2">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
        <span class="text-sm text-gray-600">Uploading...</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
        <div
          class="bg-blue-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: `${uploadProgress}%` }"
        ></div>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
      <div class="flex">
        <AlertTriangle class="h-5 w-5 text-red-400" />
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>
      </div>
    </div>

    <!-- Success message -->
    <div v-if="successMessage" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
      <div class="flex">
        <CheckCircle class="h-5 w-5 text-green-400" />
        <div class="ml-3">
          <p class="text-sm text-green-700">{{ successMessage }}</p>
        </div>
      </div>
    </div>

    <!-- Action buttons -->
    <div v-if="currentPhoto && !isReplacing" class="mt-4 flex space-x-2">
      <button
        @click="isReplacing = true"
        class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        Replace Photo
      </button>
    </div>

    <div v-if="isReplacing" class="mt-4 flex space-x-2">
      <button
        @click="cancelReplace"
        class="px-3 py-2 text-sm bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
      >
        Cancel
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Camera, X, AlertTriangle, CheckCircle } from 'lucide-vue-next'
import axios from 'axios'

const props = defineProps({
  familyNo: {
    type: String,
    required: true
  },
  canEdit: {
    type: Boolean,
    default: true
  },
  canDelete: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['photoUploaded', 'photoDeleted'])

// State
const currentPhoto = ref(null)
const isUploading = ref(false)
const isDeleting = ref(false)
const isReplacing = ref(false)
const isDragging = ref(false)
const uploadProgress = ref(0)
const errorMessage = ref('')
const successMessage = ref('')
const fileInput = ref(null)

// Load current photo on mount
onMounted(async () => {
  await loadCurrentPhoto()
})

// Methods
const loadCurrentPhoto = async () => {
  try {
    const response = await axios.get(`/member/family-photo/${props.familyNo}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    if (response.data.success) {
      currentPhoto.value = {
        ...response.data.data.photo,
        url: response.data.data.url
      }
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      console.error('Error loading family photo:', error)
    }
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    uploadPhoto(file)
  }
}

const handleDrop = (event) => {
  event.preventDefault()
  isDragging.value = false

  const files = event.dataTransfer.files
  if (files.length > 0) {
    uploadPhoto(files[0])
  }
}

const uploadPhoto = async (file) => {
  // Reset messages
  errorMessage.value = ''
  successMessage.value = ''

  // Validate file
  if (!file.type.startsWith('image/')) {
    errorMessage.value = 'Please select an image file'
    return
  }

  if (file.size > 20 * 1024 * 1024) {
    errorMessage.value = 'File size must be less than 20MB'
    return
  }

  isUploading.value = true
  uploadProgress.value = 0

  try {
    const formData = new FormData()
    formData.append('photo', file)

    const response = await axios.post(
      `/member/family-photo/upload/${props.familyNo}`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
          'X-Requested-With': 'XMLHttpRequest'
        },
        onUploadProgress: (progressEvent) => {
          uploadProgress.value = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          )
        }
      }
    )

    if (response.data.success) {
      currentPhoto.value = {
        ...response.data.data.photo,
        url: response.data.data.url
      }
      successMessage.value = response.data.message
      isReplacing.value = false
      emit('photoUploaded', response.data.data)

      // Clear success message after 3 seconds
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
  } catch (error) {
    console.error('Upload error:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to upload photo'
  } finally {
    isUploading.value = false
    uploadProgress.value = 0
    // Clear file input
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }
}

const deletePhoto = async () => {
  if (!confirm('Are you sure you want to delete this family photo?')) {
    return
  }

  isDeleting.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await axios.delete(`/member/family-photo/${props.familyNo}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    if (response.data.success) {
      currentPhoto.value = null
      successMessage.value = response.data.message
      emit('photoDeleted')

      // Clear success message after 3 seconds
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
  } catch (error) {
    console.error('Delete error:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to delete photo'
  } finally {
    isDeleting.value = false
  }
}

const cancelReplace = () => {
  isReplacing.value = false
  errorMessage.value = ''
}

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown'

  const units = ['B', 'KB', 'MB', 'GB']
  let size = bytes
  let unitIndex = 0

  while (size >= 1024 && unitIndex < units.length - 1) {
    size /= 1024
    unitIndex++
  }

  return `${size.toFixed(2)} ${units[unitIndex]}`
}

// Expose methods for parent component
defineExpose({
  loadCurrentPhoto,
  uploadPhoto
})
</script>

<style scoped>
.family-photo-upload {
  @apply max-w-md;
}

.upload-area {
  @apply relative;
}

.upload-area input[type="file"] {
  @apply sr-only;
}
</style>