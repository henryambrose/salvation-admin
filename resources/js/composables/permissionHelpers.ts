import { usePage } from '@inertiajs/vue3';


export function permissionHelpers() {
  const page = usePage()

  const can = (permission: any) => {
    if (!page.props.auth || !page.props.auth.permissions) {
      return false
    }
    return page.props.auth?.permissions?.includes(permission)
  }

  return {
    can,
  }
}
