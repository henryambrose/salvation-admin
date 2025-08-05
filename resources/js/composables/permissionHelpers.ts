import { usePage } from '@inertiajs/vue3';

export function permissionHelpers() {
  const page = usePage();

  const can = (permission: any) => {
    if (!page.props.auth || !page.props.auth.permissions) {
      return false;
    }

    const roles = page.props.auth?.roles || [];
    
    // Superadmin has all permissions
    if (roles.includes('superadmin')) {
      return true;
    }

    // For other roles, check specific permissions
    return page.props.auth?.permissions?.includes(permission);
  };

  return {
    can,
  };
}
