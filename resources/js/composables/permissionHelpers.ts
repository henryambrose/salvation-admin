import { usePage } from '@inertiajs/vue3';


export function permissionHelpers() {
  const page = usePage()

  const can = (permission: any) => {
    if (!page.props.auth || !page.props.auth.permissions) {
      return false
    }

    const roles = page.props.auth?.roles || [];
    if (roles.includes('superadmin')) {
        return true;
    }

    return page.props.auth?.permissions?.includes(permission)// || role.permission.includes(permission)
  }

  return {
    can,
  }
}
