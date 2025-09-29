import { ref, reactive } from 'vue'

// Global reactive state for certificate filters
const certificateState = reactive({
  search: '',
  certificateType: '',
  sort: 'created_at',
  direction: 'desc',
  perPage: 10,
  currentPage: 1,
})

// Helper to preserve filter state
export function useCertificateState() {
  const saveState = (filters: any) => {
    if (filters.search !== undefined) certificateState.search = filters.search
    if (filters.type !== undefined) certificateState.certificateType = filters.type
    if (filters.certificate_type_id !== undefined) certificateState.certificateType = filters.certificate_type_id
    if (filters.sort !== undefined) certificateState.sort = filters.sort
    if (filters.direction !== undefined) certificateState.direction = filters.direction
    if (filters.per_page !== undefined) certificateState.perPage = filters.per_page
    if (filters.page !== undefined) certificateState.currentPage = filters.page
  }

  const getState = () => {
    return {
      search: certificateState.search,
      type: certificateState.certificateType,
      sort: certificateState.sort,
      direction: certificateState.direction,
      per_page: certificateState.perPage,
      page: certificateState.currentPage,
    }
  }

  const resetState = () => {
    certificateState.search = ''
    certificateState.certificateType = ''
    certificateState.sort = 'created_at'
    certificateState.direction = 'desc'
    certificateState.perPage = 10
    certificateState.currentPage = 1
  }

  return {
    certificateState,
    saveState,
    getState,
    resetState,
  }
}