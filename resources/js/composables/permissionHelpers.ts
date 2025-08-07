import { usePage } from '@inertiajs/vue3';

export function permissionHelpers() {
  const page = usePage();

  const can = (permission: any) => {
    const auth = page.props.auth as any;
    if (!auth || !auth.permissions) {
      return false;
    }

    const roles = auth.roles || [];
    
    // Superadmin has all permissions
    if (roles.includes('superadmin')) {
      return true;
    }

    // For other roles, check specific permissions
    return auth.permissions?.includes(permission);
  };

  return {
    can,
  };
}
